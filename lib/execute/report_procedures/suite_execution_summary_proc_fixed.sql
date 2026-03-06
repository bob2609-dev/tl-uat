USE testlink_db;
DELIMITER $$

DROP PROCEDURE IF EXISTS suite_execution_summary $$
CREATE PROCEDURE suite_execution_summary(
    IN p_project_id INT,
    IN p_testplan_id INT,
    IN p_build_id INT,
    IN p_status VARCHAR(1),
    IN p_execution_path VARCHAR(1000),
    IN p_start_date DATETIME,
    IN p_end_date DATETIME
)
BEGIN
  -- Build hierarchy path on-the-fly instead of relying on pre-populated table
  SELECT
      CONCAT(
        COALESCE(suite3.name, ''),
        IF(suite3.name IS NOT NULL AND suite2.name IS NOT NULL, ' -> ', ''),
        COALESCE(suite2.name, ''),
        IF(suite2.name IS NOT NULL AND suite1.name IS NOT NULL, ' -> ', ''),
        COALESCE(suite1.name, ''),
        IF(suite1.name IS NOT NULL AND parent_nh.name IS NOT NULL, ' -> ', ''),
        parent_nh.name
      ) AS test_path,

      COUNT(DISTINCT tcv.id) AS total_testcases,
      COUNT(DISTINCT tcv.id) AS testcase_count,

      SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
      SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
      SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
      SUM(
          CASE 
            WHEN e.status IS NULL OR e.status = 'n' THEN 1
            WHEN e.status NOT IN ('p','f','b','n') THEN 1
            ELSE 0
          END
      ) AS not_run_count,

      CASE 
        WHEN SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END) > 0
          THEN ROUND(
            (SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) /
             SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2
          )
        ELSE 0.00
      END AS pass_rate,

      CASE 
        WHEN SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END) > 0
          THEN ROUND(
            (SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) /
             SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2
          )
        ELSE 0.00
      END AS fail_rate,

      CASE 
        WHEN COUNT(DISTINCT tcv.id) > 0
          THEN ROUND(
            (SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(DISTINCT tcv.id)) * 100, 2
          )
        ELSE 0.00
      END AS block_rate,

      CASE 
        WHEN (COUNT(DISTINCT tcv.id) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END)) > 0
          THEN ROUND(
            (SUM(CASE WHEN e.status IS NULL OR e.status = 'n' OR e.status NOT IN ('p','f','b','n') THEN 1 ELSE 0 END) /
             (COUNT(DISTINCT tcv.id) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END))) * 100, 2
          )
        ELSE 0.00
      END AS pending_rate

  FROM testplan_tcversions tptcv
  JOIN tcversions tcv               ON tptcv.tcversion_id = tcv.id
  JOIN nodes_hierarchy nh_tcv       ON tcv.id = nh_tcv.id
  JOIN nodes_hierarchy nh_tc        ON nh_tcv.parent_id = nh_tc.id
  JOIN nodes_hierarchy parent_nh    ON nh_tc.parent_id = parent_nh.id
  LEFT JOIN nodes_hierarchy suite1  ON parent_nh.parent_id = suite1.id AND suite1.node_type_id = 2
  LEFT JOIN nodes_hierarchy suite2  ON suite1.parent_id = suite2.id AND suite2.node_type_id = 2
  LEFT JOIN nodes_hierarchy suite3  ON suite2.parent_id = suite3.id AND suite3.node_type_id = 2
  JOIN testplans tp                 ON tptcv.testplan_id = tp.id
  JOIN testprojects tproj           ON tp.testproject_id = tproj.id
  LEFT JOIN (
    SELECT e1.tcversion_id, e1.testplan_id, e1.build_id, e1.status, e1.execution_ts
    FROM executions e1
    JOIN (
      SELECT tcversion_id, testplan_id, build_id, MAX(execution_ts) AS latest_exec_ts
      FROM executions
      GROUP BY tcversion_id, testplan_id, build_id
    ) latest_e
      ON  e1.tcversion_id = latest_e.tcversion_id
      AND e1.testplan_id  = latest_e.testplan_id
      AND e1.build_id     = latest_e.build_id
      AND e1.execution_ts = latest_e.latest_exec_ts
  ) e ON tcv.id = e.tcversion_id AND tptcv.testplan_id = e.testplan_id
  LEFT JOIN builds b ON e.build_id = b.id

  WHERE 1=1
    AND ((p_project_id   IS NULL OR p_project_id   = 0) OR tp.testproject_id     = p_project_id)
    AND ((p_testplan_id  IS NULL OR p_testplan_id  = 0) OR tptcv.testplan_id     = p_testplan_id)
    AND ((p_build_id     IS NULL OR p_build_id     = 0) OR (e.build_id = p_build_id OR e.build_id IS NULL))
    AND (
         p_status IS NULL OR p_status = '' OR
         (p_status = 'n'  AND (e.status IS NULL OR e.status = 'n')) OR
         (p_status <> 'n' AND  e.status = p_status)
    )
    AND (
         (p_execution_path IS NULL OR p_execution_path = '') 
         OR CONCAT(
              COALESCE(suite3.name, ''),
              IF(suite3.name IS NOT NULL AND suite2.name IS NOT NULL, ' -> ', ''),
              COALESCE(suite2.name, ''),
              IF(suite2.name IS NOT NULL AND suite1.name IS NOT NULL, ' -> ', ''),
              COALESCE(suite1.name, ''),
              IF(suite1.name IS NOT NULL AND parent_nh.name IS NOT NULL, ' -> ', ''),
              parent_nh.name
            ) LIKE CONCAT('%', p_execution_path, '%')
    )
    AND (p_start_date IS NULL OR (e.execution_ts IS NULL OR e.execution_ts >= p_start_date))
    AND (p_end_date   IS NULL OR (e.execution_ts IS NULL OR e.execution_ts <= p_end_date))

  GROUP BY parent_nh.id, suite1.id, suite2.id, suite3.id, tptcv.testplan_id
  ORDER BY test_path;

END $$
DELIMITER ;
