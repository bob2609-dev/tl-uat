SELECT 
            ROW_NUMBER() OVER (ORDER BY u.first, u.last) AS serial_no,
            u.id AS tester_id,
            CONCAT(u.first, ' ', u.last) AS tester_name,
            COUNT(tptc.id) AS total_assigned,
            COUNT(CASE WHEN e.id IS NOT NULL THEN tptc.id END) AS total_executions,
            COUNT(CASE WHEN e.status = 'p' THEN tptc.id END) AS passed,
            COUNT(CASE WHEN e.status = 'f' THEN tptc.id END) AS failed,
            COUNT(CASE WHEN e.status = 'b' THEN tptc.id END) AS blocked,
            COUNT(CASE WHEN e.id IS NULL THEN tptc.id END) AS not_run,
            MAX(e.execution_ts) AS last_execution
        FROM 
            -- Start with all active users in the system
            users u
            -- LEFT JOIN with user_assignments to get assignments to testplan_tcversions (if any)
            LEFT JOIN user_assignments ua ON ua.user_id = u.id 
                AND ua.type IN (1, 2) 
                AND ua.status = 1
            -- LEFT JOIN with testplan_tcversions using the correct relationship (ua.feature_id = tptc.id)
            LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
            -- LEFT JOIN with testplans and testprojects for filtering (only for assigned test cases)
            LEFT JOIN testplans tp ON tptc.testplan_id = tp.id AND tp.testproject_id = 242099
            -- LEFT JOIN with executions to find actual executions (if any)
            LEFT JOIN executions e ON e.tcversion_id = tptc.tcversion_id 
                AND e.testplan_id = tptc.testplan_id 
                AND e.platform_id = tptc.platform_id
                AND (ua.build_id = e.build_id OR ua.build_id = 0 OR ua.build_id IS NULL)
        WHERE 1=1 
            AND u.active = 1  -- Only active users
            -- Include users with no assignments OR users with assignments in the specified test project
            AND (ua.id IS NULL OR tp.testproject_id = 242099)
        GROUP BY u.id, u.login, u.first, u.last
        
        UNION ALL
        
        SELECT 
            NULL AS serial_no,
            NULL AS tester_id,
            'TOTAL' AS tester_name,
            COUNT(tptc.id) AS total_assigned,
            COUNT(CASE WHEN e.id IS NOT NULL THEN tptc.id END) AS total_executions,
            COUNT(CASE WHEN e.status = 'p' THEN tptc.id END) AS passed,
            COUNT(CASE WHEN e.status = 'f' THEN tptc.id END) AS failed,
            COUNT(CASE WHEN e.status = 'b' THEN tptc.id END) AS blocked,
            COUNT(CASE WHEN e.id IS NULL THEN tptc.id END) AS not_run,
            MAX(e.execution_ts) AS last_execution
        FROM 
            -- Start with all active users in the system
            users u
            -- LEFT JOIN with user_assignments to get assignments to testplan_tcversions (if any)
            LEFT JOIN user_assignments ua ON ua.user_id = u.id 
                AND ua.type IN (1, 2) 
                AND ua.status = 1
            -- LEFT JOIN with testplan_tcversions using the correct relationship (ua.feature_id = tptc.id)
            LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
            -- LEFT JOIN with testplans and testprojects for filtering (only for assigned test cases)
            LEFT JOIN testplans tp ON tptc.testplan_id = tp.id AND tp.testproject_id = 242099
            -- LEFT JOIN with executions to find actual executions (if any)
            LEFT JOIN executions e ON e.tcversion_id = tptc.tcversion_id 
                AND e.testplan_id = tptc.testplan_id 
                AND e.platform_id = tptc.platform_id
                AND (ua.build_id = e.build_id OR ua.build_id = 0 OR ua.build_id IS NULL)
        WHERE 1=1 
            AND u.active = 1  -- Only active users
            -- Include users with no assignments OR users with assignments in the specified test project
            AND (ua.id IS NULL OR tp.testproject_id = 242099)
        
        ORDER BY 
            CASE WHEN tester_name = 'TOTAL' THEN 2 ELSE 1 END,
            tester_name
