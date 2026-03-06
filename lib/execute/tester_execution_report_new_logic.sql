/* ===== build the base dataset once ===== */
SELECT *
FROM (
    SELECT
        u.id AS tester_id,
        CONCAT(u.first, ' ', u.last) AS tester_name,

        IFNULL(a.assigned_cnt, 0) AS assigned_testcases,
        IFNULL(e.executed_cnt, 0) AS executed_testcases,
        IFNULL(e.pass_cnt, 0) AS passed_testcases,
        IFNULL(e.fail_cnt, 0) AS failed_testcases,

        GREATEST(IFNULL(a.assigned_cnt,0) - IFNULL(e.executed_cnt,0), 0)
            AS assigned_not_run,

        ROUND(
            CASE
                WHEN IFNULL(e.pass_cnt,0) + IFNULL(e.fail_cnt,0) = 0 THEN 0
                ELSE (e.pass_cnt / (e.pass_cnt + e.fail_cnt)) * 100
            END, 2
        ) AS pass_rate_percent,

        e.last_execution_date

    FROM users u

    LEFT JOIN (
        SELECT user_id, COUNT(DISTINCT feature_id) assigned_cnt
        FROM user_assignments
        GROUP BY user_id
    ) a ON a.user_id = u.id

    LEFT JOIN (
        SELECT
            tester_id,
            COUNT(*) executed_cnt,
            SUM(status='p') pass_cnt,
            SUM(status='f') fail_cnt,
            MAX(execution_ts) last_execution_date
        FROM executions
        GROUP BY tester_id
    ) e ON e.tester_id = u.id

    WHERE a.user_id IS NOT NULL OR e.tester_id IS NOT NULL
) report


UNION ALL


/* ===== totals row ===== */
SELECT
    NULL,
    'TOTAL',

    SUM(assigned_testcases),
    SUM(executed_testcases),
    SUM(passed_testcases),
    SUM(failed_testcases),
    SUM(assigned_not_run),

    ROUND(
        CASE
            WHEN SUM(passed_testcases + failed_testcases) = 0 THEN 0
            ELSE SUM(passed_testcases) /
                 SUM(passed_testcases + failed_testcases) * 100
        END, 2
    ),

    MAX(last_execution_date)

FROM (
    SELECT
        IFNULL(a.assigned_cnt, 0) AS assigned_testcases,
        IFNULL(e.executed_cnt, 0) AS executed_testcases,
        IFNULL(e.pass_cnt, 0) AS passed_testcases,
        IFNULL(e.fail_cnt, 0) AS failed_testcases,
        GREATEST(IFNULL(a.assigned_cnt,0) - IFNULL(e.executed_cnt,0), 0) AS assigned_not_run,
        e.last_execution_date
    FROM users u
    LEFT JOIN (
        SELECT user_id, COUNT(DISTINCT feature_id) assigned_cnt
        FROM user_assignments GROUP BY user_id
    ) a ON a.user_id = u.id
    LEFT JOIN (
        SELECT tester_id,
               COUNT(*) executed_cnt,
               SUM(status='p') pass_cnt,
               SUM(status='f') fail_cnt,
               MAX(execution_ts) last_execution_date
        FROM executions GROUP BY tester_id
    ) e ON e.tester_id = u.id
    WHERE a.user_id IS NOT NULL OR e.tester_id IS NOT NULL
) totals;
