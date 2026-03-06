-- Sample Data Template for Custom Bug Tracking Integration
-- Use this template to create sample data after the schema is installed
-- Replace the placeholder IDs with actual values from your TestLink database

-- First, find your actual user ID and project IDs by running:
-- SELECT id, login FROM users WHERE login = 'your_username';
-- SELECT id, name FROM testprojects;

-- Sample Integrations (replace user_id with actual user ID from your database)
INSERT INTO `custom_bugtrack_integrations` 
(`name`, `type`, `url`, `api_key`, `username`, `project_key`, `default_priority`, `created_by`) 
VALUES 
('Default Redmine Integration', 'REDMINE', 'https://redmine.example.com', 'your-api-key-here', NULL, 'test-project', 'Normal', 1),
('Production Redmine', 'REDMINE', 'https://prod-redmine.company.com', 'prod-api-key', 'testlink_user', 'production', 'High', 1),
('Jira Cloud Integration', 'JIRA', 'https://company.atlassian.net', 'jira-api-token', NULL, 'TEST', 'Medium', 1);

-- Sample Project Mappings (replace tproject_id with actual project IDs from your database)
-- First, get your actual project IDs:
-- SELECT id, name FROM testprojects;

-- Then uncomment and modify these lines with your actual project IDs:
/*
INSERT INTO `custom_bugtrack_project_mapping` 
(`tproject_id`, `integration_id`, `created_by`) 
VALUES 
(1, 1, 1), -- Map project 1 to default Redmine
(2, 2, 1), -- Map project 2 to production Redmine
(3, 3, 1); -- Map project 3 to Jira
*/

-- To find the correct IDs for your setup, run these queries:
-- 1. Get user IDs:
--    SELECT id, login FROM users ORDER BY id;
--
-- 2. Get project IDs:
--    SELECT id, name FROM testprojects ORDER BY id;
--
-- 3. After creating integrations, get integration IDs:
--    SELECT id, name FROM custom_bugtrack_integrations ORDER BY id;
--
-- Then update the sample data above with your actual IDs.
