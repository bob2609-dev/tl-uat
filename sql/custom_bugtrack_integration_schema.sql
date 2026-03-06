-- Custom Bug Tracking Integration Schema for TestLink
-- Supports multiple Redmine, Jira, Bugzilla integrations per project
-- Created: 2025-02-23

-- Table for storing bug tracking integration configurations
CREATE TABLE IF NOT EXISTS `custom_bugtrack_integrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Human-readable name for the integration',
  `type` enum('REDMINE','JIRA','BUGZILLA') NOT NULL DEFAULT 'REDMINE' COMMENT 'Integration type',
  `url` varchar(255) NOT NULL COMMENT 'Base URL of the bug tracker',
  `api_key` varchar(255) DEFAULT NULL COMMENT 'API key/token for authentication',
  `username` varchar(100) DEFAULT NULL COMMENT 'Username for basic auth (if needed)',
  `password` varchar(255) DEFAULT NULL COMMENT 'Password for basic auth (if needed)',
  `project_key` varchar(100) DEFAULT NULL COMMENT 'Project identifier in bug tracker',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether this integration is enabled',
  `default_priority` varchar(50) DEFAULT 'Normal' COMMENT 'Default priority for new issues',
  `default_tracker_id` int(10) DEFAULT NULL COMMENT 'Default tracker type ID (Redmine)',
  `default_status_id` int(10) DEFAULT NULL COMMENT 'Default status ID for new issues',
  `custom_fields` text DEFAULT NULL COMMENT 'JSON encoded custom field mappings',
  `created_by` int(10) unsigned NOT NULL COMMENT 'User ID who created this integration',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(10) unsigned DEFAULT NULL COMMENT 'User ID who last updated',
  `updated_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`),
  KEY `idx_type` (`type`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Custom bug tracking integration configurations';

-- Table for mapping TestLink projects to bug tracking integrations
CREATE TABLE IF NOT EXISTS `custom_bugtrack_project_mapping` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tproject_id` int(10) unsigned NOT NULL COMMENT 'TestLink test project ID',
  `integration_id` int(10) unsigned NOT NULL COMMENT 'Integration ID from custom_bugtrack_integrations',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether this mapping is active',
  `created_by` int(10) unsigned NOT NULL COMMENT 'User ID who created this mapping',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(10) unsigned DEFAULT NULL COMMENT 'User ID who last updated',
  `updated_on` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_project_integration` (`tproject_id`, `integration_id`),
  KEY `idx_tproject` (`tproject_id`),
  KEY `idx_integration` (`integration_id`),
  KEY `idx_active` (`is_active`),
  FOREIGN KEY (`tproject_id`) REFERENCES `testprojects` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`integration_id`) REFERENCES `custom_bugtrack_integrations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mapping between TestLink projects and bug tracking integrations';

-- Table for storing integration usage logs and audit trail
CREATE TABLE IF NOT EXISTS `custom_bugtrack_integration_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `integration_id` int(10) unsigned NOT NULL COMMENT 'Integration ID used',
  `tproject_id` int(10) unsigned DEFAULT NULL COMMENT 'TestLink project ID (if applicable)',
  `tplan_id` int(10) unsigned DEFAULT NULL COMMENT 'TestLink test plan ID (if applicable)',
  `tc_id` int(10) unsigned DEFAULT NULL COMMENT 'TestLink test case ID (if applicable)',
  `execution_id` int(10) unsigned DEFAULT NULL COMMENT 'TestLink execution ID (if applicable)',
  `action` varchar(50) NOT NULL COMMENT 'Action performed (CREATE_ISSUE, UPDATE_ISSUE, etc)',
  `issue_id` varchar(100) DEFAULT NULL COMMENT 'External issue ID',
  `request_data` text DEFAULT NULL COMMENT 'JSON encoded request data',
  `response_data` text DEFAULT NULL COMMENT 'JSON encoded response data',
  `status` varchar(20) NOT NULL COMMENT 'SUCCESS, ERROR, TIMEOUT',
  `error_message` text DEFAULT NULL COMMENT 'Error message if status is ERROR',
  `execution_time_ms` int(10) unsigned DEFAULT NULL COMMENT 'Execution time in milliseconds',
  `created_by` int(10) unsigned NOT NULL COMMENT 'User ID who performed the action',
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_integration` (`integration_id`),
  KEY `idx_tproject` (`tproject_id`),
  KEY `idx_action` (`action`),
  KEY `idx_status` (`status`),
  KEY `idx_created_on` (`created_on`),
  FOREIGN KEY (`integration_id`) REFERENCES `custom_bugtrack_integrations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for bug tracking integration usage';

-- Insert sample data for testing
INSERT INTO `custom_bugtrack_integrations` 
(`name`, `type`, `url`, `api_key`, `username`, `project_key`, `default_priority`, `created_by`) 
VALUES 
('Default Redmine Integration', 'REDMINE', 'https://redmine.example.com', 'your-api-key-here', NULL, 'test-project', 'Normal', 1),
('Production Redmine', 'REDMINE', 'https://prod-redmine.company.com', 'prod-api-key', 'testlink_user', 'production', 'High', 1),
('Jira Cloud Integration', 'JIRA', 'https://company.atlassian.net', 'jira-api-token', NULL, 'TEST', 'Medium', 1);

-- Insert sample project mappings
INSERT INTO `custom_bugtrack_project_mapping` 
(`tproject_id`, `integration_id`, `created_by`) 
VALUES 
(1, 1, 1), -- Map project 1 to default Redmine
(2, 2, 1), -- Map project 2 to production Redmine
(3, 3, 1); -- Map project 3 to Jira

-- Create indexes for better performance
CREATE INDEX `idx_integration_log_lookup` ON `custom_bugtrack_integration_log` (`integration_id`, `tproject_id`, `created_on`);
CREATE INDEX `idx_project_mapping_lookup` ON `custom_bugtrack_project_mapping` (`tproject_id`, `is_active`);
