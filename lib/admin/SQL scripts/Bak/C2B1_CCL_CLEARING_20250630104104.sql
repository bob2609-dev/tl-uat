USE `tl_uat`;

-- Enhanced TestLink Import SQL Script
-- Generated on 2025-06-30 11:41:08
-- Test Suite ID: 115575

START TRANSACTION;


-- Test Case 1: CCL-SI-TC1
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC1', 3, 100); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC1', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 1, 1, 1, 
'<p>Create and save the outward clearing transaction with a cheque amount above 10M and transaction currency TZS</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the outward clearing transaction with a cheque amount above 10M and transaction currency TZS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the outward clearing transaction with a cheque amount above 10M and transaction currency TZS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 2: CCL-SI-TC2
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC2', 3, 101); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC2', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 2, 1, 1, 
'<p>Create and save the outward clearing transaction with a cheque amount above 10K and transaction currency USD</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the outward clearing transaction with a cheque amount above 10K and transaction currency USD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the outward clearing transaction with a cheque amount above 10K and transaction currency USD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 3: CCL-SI-TC3
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC3', 3, 102); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC3', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 3, 1, 1, 
'<p>Create and save the outward clearing transaction with account having dormant status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the outward clearing transaction with account having dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the outward clearing transaction with account having dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 4: CCL-SI-TC4
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC4', 3, 103); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC4', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 4, 1, 1, 
'<p>Create and save the outward clearing transaction with account having frozen status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the outward clearing transaction with account having frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the outward clearing transaction with account having frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 5: CCL-SI-TC5
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC5', 3, 104); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC5', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 5, 1, 1, 
'<p>Create and save the outward clearing transaction with account having no_credit status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the outward clearing transaction with account having no_credit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the outward clearing transaction with account having no_credit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 6: CCL-SI-TC6
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC6', 3, 105); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC6', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 6, 1, 1, 
'<p>Create and save the outward clearing transaction with account class CA29 - NMB  AGENCY BANKING  FLOAT ACCOUNTS</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the outward clearing transaction with account class CA29 - NMB  AGENCY BANKING  FLOAT ACCOUNTS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the outward clearing transaction with account class CA29 - NMB  AGENCY BANKING  FLOAT ACCOUNTS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 7: CCL-SI-TC7
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC7', 3, 106); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC7', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 7, 1, 1, 
'<p>Delete created authorized outward clearing transaction</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGSOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Summary');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Delete');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Delete created authorized outward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Delete created authorized outward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 8: CCL-SI-TC8
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC8', 3, 107); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC8', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 8, 1, 1, 
'<p>Unlock and modify authorized outward clearing transaction</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGSOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Summary');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Modify');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Unlock and modify authorized outward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Unlock and modify authorized outward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 9: CCL-SI-TC9
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC9', 3, 108); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC9', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 9, 1, 1, 
'<p>Create and save the inward clearing transaction with a cheque amount above 10M and transaction currency TZS</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with a cheque amount above 10M and transaction currency TZS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with a cheque amount above 10M and transaction currency TZS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 10: CCL-SI-TC10
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC10', 3, 109); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC10', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 10, 1, 1, 
'<p>Create and save the inward clearing transaction with a cheque amount above 10K and transaction currency USD</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with a cheque amount above 10K and transaction currency USD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with a cheque amount above 10K and transaction currency USD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 11: CCL-SI-TC11
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC11', 3, 110); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC11', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 11, 1, 1, 
'<p>Create and save the inward clearing transaction with account having dormant status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with account having dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with account having dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 12: CCL-SI-TC12
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC12', 3, 111); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC12', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 12, 1, 1, 
'<p>Create and save the inward clearing transaction with account having frozen status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with account having frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with account having frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 13: CCL-SI-TC13
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC13', 3, 112); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC13', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 13, 1, 1, 
'<p>Create and save the inward clearing transaction with account having no_credit status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with account having no_credit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with account having no_credit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 14: CCL-SI-TC14
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC14', 3, 113); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC14', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 14, 1, 1, 
'<p>Create and save the inward clearing transaction with account having insufficient balance</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with account having insufficient balance');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with account having insufficient balance');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 15: CCL-SI-TC15
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC15', 3, 114); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC15', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 15, 1, 1, 
'<p>Create and save the inward clearing transaction with amount above account overdraw amount limit</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with amount above account overdraw amount limit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with amount above account overdraw amount limit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 16: CCL-SI-TC16
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC16', 3, 115); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC16', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 16, 1, 1, 
'<p>Create and save the inward clearing transaction with account in which the cheque marked with ''stop pay'' status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with account in which the cheque marked with ''stop pay'' status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with account in which the cheque marked with ''stop pay'' status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 17: CCL-SI-TC17
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC17', 3, 116); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC17', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 17, 1, 1, 
'<p>Create and save the inward clearing transaction with account in which the checklist not maintained</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with account in which the checklist not maintained');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with account in which the checklist not maintained');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 18: CCL-SI-TC18
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC18', 3, 117); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC18', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 18, 1, 1, 
'<p>Create and save the inward clearing transaction with the cheque presented cheque details details differ from checklist</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with the cheque presented cheque details details differ from checklist');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with the cheque presented cheque details details differ from checklist');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 19: CCL-SI-TC19
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC19', 3, 118); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC19', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 19, 1, 1, 
'<p>Create and save the inward clearing transaction with account class CA29 - NMB  AGENCY BANKING  FLOAT ACCOUNTS</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save the inward clearing transaction with account class CA29 - NMB  AGENCY BANKING  FLOAT ACCOUNTS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save the inward clearing transaction with account class CA29 - NMB  AGENCY BANKING  FLOAT ACCOUNTS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 20: CCL-SI-TC20
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC20', 3, 119); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC20', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 20, 1, 1, 
'<p>Delete authorized inward outward clearing transaction</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGSOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Delete');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Delete authorized inward outward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Delete authorized inward outward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 21: CCL-SI-TC21
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC21', 3, 120); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC21', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 21, 1, 1, 
'<p>Unlock and modify authorized inward clearing transaction</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGSOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Modify');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Unlock and modify authorized inward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Unlock and modify authorized inward clearing transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 22: CCL-SI-TC22
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC22', 3, 121); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC22', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 22, 1, 1, 
'<p>Create and save Instrument payment  transaction with the cheque having stop payment status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PIDINSPY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Instrument payment detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save Instrument payment  transaction with the cheque having stop payment status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save Instrument payment  transaction with the cheque having stop payment status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Payment Inbound > Cross Border ISO > Cross Border Inbound FI to FI Customer Credit Transfer Input Detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 23: CCL-SI-TC23
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC23', 3, 122); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC23', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 23, 1, 1, 
'<p>Create and save Instrument payment  transaction with the cheque having changes status from  ''unused'' status to ''used''.</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PIDINSPY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Instrument payment detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and save Instrument payment  transaction with the cheque having changes status from  ''unused'' status to ''used''.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Create and save Instrument payment  transaction with the cheque having changes status from  ''unused'' status to ''used''.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Payment Inbound > Cross Border ISO > Cross Border Inbound FI to FI Customer Credit Transfer Input Detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 24: CCL-SI-TC24
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC24', 3, 123); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC24', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 24, 1, 1, 
'<p>Return clearing transaction to return account with No Debit status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOURTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Out clearing return detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with No Debit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Return clearing transaction to return account with No Debit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Out Clearing Return Detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 25: CCL-SI-TC25
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC25', 3, 124); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC25', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 25, 1, 1, 
'<p>Return clearing transaction to return account with Frozen status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOURTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Out clearing return detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with Frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Return clearing transaction to return account with Frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Out Clearing Return Detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 26: CCL-SI-TC26
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC26', 3, 125); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC26', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 26, 1, 1, 
'<p>Return clearing transaction to return account with Dormant status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOURTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Out clearing return detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with Dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Return clearing transaction to return account with Dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Out Clearing Return Detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, ' ');


-- Test Case 27: CCL-SI-TC27
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC27', 3, 126); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC27', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 27, 1, 1, 
'<p>Return clearing transaction to return account with No Credit status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDINRTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Return/reversal of inbound clearing');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with No Credit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Return clearing transaction to return account with No Credit status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing View Summary > Return or Reversal of Inbound Clearing ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 28: CCL-SI-TC28
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC28', 3, 127); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC28', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 28, 1, 1, 
'<p>Return clearing transaction to return account with Frozen status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDINRTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Return/reversal of inbound clearing');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with Frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Return clearing transaction to return account with Frozen status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing View Summary > Return or Reversal of Inbound Clearing ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 29: CCL-SI-TC29
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC29', 3, 128); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC29', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 29, 1, 1, 
'<p>Return clearing transaction to return account with Dormant status</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDINRTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Return/reversal of inbound clearing');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with Dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Return clearing transaction to return account with Dormant status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing View Summary > Return or Reversal of Inbound Clearing ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System should reject transaction and pop error message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, ' ');


-- Test Case 30: CCL-SI-TC30
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC30', 3, 129); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC30', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 30, 1, 1, 
'<p>Create and authorize the outward clearing transaction within amount limit - maker checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOTONL/PGSOTONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and authorize the outward clearing transaction within amount limit - maker checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker -Create and Checker-authorize the outward clearing transaction within amount limit currency TZS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Clearing Transaction Input Detailed > Outbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 31: CCL-SI-TC31
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC31', 3, 130); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC31', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 31, 1, 1, 
'<p>Create and authorize the inward clearing transaction within amount limit - maker checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDITONL/PGSITONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and authorize the inward clearing transaction within amount limit - maker checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker -Create and Checker-authorize the inward clearing transaction within amount limit currency USD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing Transaction Input Detailed > Inbound Clearing Transaction Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 32: CCL-SI-TC32
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC32', 3, 131); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC32', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 32, 1, 1, 
'<p>Return clearing transaction to return account with valid details - maker checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDOURTN/PGSOURTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Out clearing return detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with valid details - maker checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker- create and Checker - authorize Return clearing transaction with valid details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Out Clearing Return Detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 33: CCL-SI-TC33
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC33', 3, 132); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC33', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 33, 1, 1, 
'<p>Return clearing transaction to return account with valid details - maker checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PGDINRTN/PGSINRTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Return/reversal of inbound clearing');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Return clearing transaction to return account with valid details - maker checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker- create and Checker - authorize Return clearing transaction with valid details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Inbound > Operations > Clearing View Summary > Return or Reversal of Inbound Clearing ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 34: CCL-SI-TC34
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC34', 3, 133); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC34', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 34, 1, 1, 
'<p>Issue Instument with valid details - Maker/checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PIDINSIS/PISINSIS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Instrument Issue detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Issue Instument with valid details - Maker/checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker - Issue Instument with valid details and to be authorized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Instrument Issue Detailed ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 35: CCL-SI-TC35
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC35', 3, 134); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC35', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 35, 1, 1, 
'<p>Create and authorize Instrument payment  transaction with valid details - maker/checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PIDINSPY/PISINSPY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Instrument payment detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Create and authorize Instrument payment  transaction with valid details - maker/checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker - Create and Checker to authorize Instrument payment  transaction with valid details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Instrument Payment Detailed ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 36: CCL-SI-TC36
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC36', 3, 135); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC36', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 36, 1, 1, 
'<p>Impose and authorize stop pay Instrument by correct instrument details - maker/checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PIDSTPAY/PISSTPAY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Instrument Stop Pay');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Impose and authorize stop pay Instrument by correct instrument details - maker/checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker - Impose stop pay Insturment by correct instrument details and to be authorized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Instrument Stop Pay Detailed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 37: CCL-SI-TC37
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (115575, 'CCL-SI-TC37', 3, 136); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CCL-SI-TC37', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 37, 1, 1, 
'<p>Change Unused Instument status with valid details - maker checker control</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'PIDUNUSE/PISUNUSE');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Unused Instrument Status Change');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Change Unused Instument status with valid details - maker checker control');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Maker - Change Unused Instument status  with valid details and checker to authorize');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Clearing Outbound > Operations > Unused Instruments Status Change ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction to be created by Maker and Finalized by Checker');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');

COMMIT;
