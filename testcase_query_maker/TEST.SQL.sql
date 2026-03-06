-- Enhanced TestLink Import SQL Script
-- Generated on 2025-06-15 20:27:03
-- Test Suite ID: 2

START TRANSACTION;


-- Test Case 1: NO_TC_0
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_0', 3, 1020); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_0', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 1, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system  allow to proceed with creation of contract  without settlement instruction of the Borrower being set');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Failed to save contract');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 2: NO_TC_1
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_1', 3, 1021); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_1', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 2, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system  allow to proceed with creation of contract  with settlement instruction of the Borrower being set');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully  saved the contract');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 3: NO_TC_2
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_2', 3, 1022); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_2', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 3, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can sytem allow user to select borrower facility product from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System shoould successfully generate contract reference, Product description and Branch Code.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 4: NO_TC_3
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_3', 3, 1023); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_3', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 4, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system  allow user to select Counterparty  from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected counterparty');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 5: NO_TC_4
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_4', 3, 1024); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_4', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 5, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system display borrower name after selecting counterparty from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully borrower name displayed/customer name');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 6: NO_TC_5
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_5', 3, 1025); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_5', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 6, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select facility currency from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully currency selected');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 7: NO_TC_6
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_6', 3, 1026); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_6', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 7, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture  Facility Amount');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully captured Facility Amount');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 8: NO_TC_7
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_7', 3, 1027); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_7', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 8, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system  allow user  to select Facility Start date and End date');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selecting Facility Start date and End date');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 9: NO_TC_8
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_8', 3, 1028); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_8', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 9, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select Internal Agency ID (i.e  Select the CIF Id of a bank or its entity to play the role of a self participant in the syndicated loan');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selecting Internal Agency ID and displaying name of Agency');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 10: NO_TC_9
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_9', 3, 1029); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_9', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 10, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'System to be able to use  Internal Agency ID selected as the settlement party of the corresponding commitment/loan contract.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully use of Internal Agency ID selected as the settlement party of the corresponding commitment/loan contract.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 11: NO_TC_10
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_10', 3, 1030); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_10', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 11, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Syetem not to allow to  use  Internal Agency ID of differenct agent from contaract as the settlement party of the corresponding commitment/loan contract.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Failed to use  Internal Agency ID of differenct agent from contaract as the settlement party of the corresponding commitment/loan contract.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 12: NO_TC_11
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_11', 3, 1031); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_11', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 12, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Tranche');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture  Start date and End date of tranche');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully specified  Start date and End date of tranche');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 13: NO_TC_12
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_12', 3, 1032); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_12', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 13, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Tranche');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture capture Minimum and Maximum Amount');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully specified  Minimum and Maximum Amount of tranche');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 14: NO_TC_13
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_13', 3, 1033); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_13', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 14, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Named Agent');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to select Named Agent');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully display named agent name');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 15: NO_TC_14
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_14', 3, 1034); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_14', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 15, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Agency type');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select agent type either Lead or Participation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully allow user to select agent type either Lead or Participation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 16: NO_TC_15
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_15', 3, 1035); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_15', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 16, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Administrator details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to  to capture  Primary Administrator ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully display Name of Primary Administrator');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 17: NO_TC_16
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_16', 3, 1036); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_16', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 17, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Administrator details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to  to capture Secondary administrator');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully display Name of Secondary Administrator');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 18: NO_TC_17
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_17', 3, 1037); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_17', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 18, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Reporting Details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture tranche curency ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully capture tranche currency');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 19: NO_TC_18
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_18', 3, 1038); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_18', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 19, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Agreements details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to capture Purpose of Sysndication facility  ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfuly captured Purpose of Sysndication facility  ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 20: NO_TC_19
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_19', 3, 1039); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_19', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 20, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Agreements details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to capture  Internal remarks');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfuly captured Internal Remarks');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 21: NO_TC_20
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_20', 3, 1040); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_20', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 21, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Agent CIF');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture Agent CIF number(if agent has an account in NMB bank)-Not Mandatory');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully capture  Agent CIF number(if agent has an account in NMB bank)-Not Mandatory');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 22: NO_TC_21
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_21', 3, 1041); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_21', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 22, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Currency sub screen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'System should allow user  to capture allowed currencies for tranche and drawdown created under borrower facilty ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully  populate currency description');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 23: NO_TC_22
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_22', 3, 1042); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_22', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 23, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Assignment fee sub screen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'System should be able to displays the latest assignment fee amount for the facility/product in the tranche currency');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully displayed  latest assignment fee amount for the facility/product in the tranche currency');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 24: NO_TC_23
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_23', 3, 1043); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_23', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 24, 1, 1, 
'<p>Processing Fee at -Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Processing Fee at -Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select  Processing fee component from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected Processing Fee from List of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 25: NO_TC_24
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_24', 3, 1044); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_24', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 25, 1, 1, 
'<p>Commitment Fee at Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Commitment Fee at Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select  Commitment Fee component from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected commitment Fee component from List of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 26: NO_TC_25
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_25', 3, 1045); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_25', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 26, 1, 1, 
'<p>Agency Fee at Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Agency Fee at Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select Agency Fee component from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected Agency fee component from List of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 27: NO_TC_26
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_26', 3, 1046); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_26', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 27, 1, 1, 
'<p>Security agency Fee at Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Security agency Fee at Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select Security agency Fee component from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected security agency fee component from List of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 28: NO_TC_27
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_27', 3, 1047); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_27', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 28, 1, 1, 
'<p>Arrangement Fee at Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Arrangement Fee at Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select  Arrangement Fee component from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected Arrangement  fee component from List of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 29: NO_TC_28
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_28', 3, 1048); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_28', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 29, 1, 1, 
'<p>Fee Shedule at Borrower Facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Fee Shedule at Borrower Facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to specify start date of the fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully specified Start date of the fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 30: NO_TC_29
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_29', 3, 1049); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_29', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 30, 1, 1, 
'<p>Fee Shedule at Borrower Facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Fee Shedule at Borrower Facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to specify End date of the fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully specified End date of the fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 31: NO_TC_30
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_30', 3, 1050); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_30', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 31, 1, 1, 
'<p>Fee Shedule at Borrower Facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Fee Shedule at Borrower Facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user capture number ( Number of times fee component  can be collected)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully captured number of times for fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 32: NO_TC_31
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_31', 3, 1051); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_31', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 32, 1, 1, 
'<p>Fee Shedule at Borrower Facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Fee Shedule at Borrower Facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to be able to select frequency of  Fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected frequency as Daily,monthly ,quarterly,semi-annual,yearly and Bullet');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 33: NO_TC_32
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_32', 3, 1052); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_32', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 33, 1, 1, 
'<p>Fee Shedule at Borrower Facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Fee Shedule at Borrower Facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user capture unit component ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully captured unit component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 34: NO_TC_33
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_33', 3, 1053); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_33', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 34, 1, 1, 
'<p>Fee Shedule at Borrower Facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Fee Schedule subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Fee Shedule at Borrower Facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to be able to capture amount of  Fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully capture amount of fee component');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 35: NO_TC_34
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_34', 3, 1054); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_34', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 35, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System  populate all details of borrower from settlement instruction of the borrower (Borrower,Borrower Name,SSI,Counterparty,Counterparty Name,Nature of SSI,Netting Preferences,Blocked');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfull populate all details of borrower (Borrower,Borrower Name,SSI,Counterparty,Counterparty Name,Nature of SSI,Netting Preferences,Blocked');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 36: NO_TC_35
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_35', 3, 1055); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_35', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 36, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to  select SSI Mnemonic of the standing instraction of the borrower');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully select SSI Mnemonic of the standing instraction of the borrower');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 37: NO_TC_36
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_36', 3, 1056); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_36', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 37, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can sytem allow user to select Entity details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfuly  populate Entity details (Entity,Entity Description,Primary)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 38: NO_TC_37
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_37', 3, 1057); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_37', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 38, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select  Drawdown Product from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selection of Drawdown Product from list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 39: NO_TC_38
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_38', 3, 1058); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_38', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 39, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select  currency-wise drawing Limit based on drawdown product');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully select currency wise drawing Limit and capture limit amount.');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 40: NO_TC_39
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_39', 3, 1059); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_39', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 40, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture  Borrower Limit at Facility Level');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully capture  Borrower Limit at Facility Level (Borrower Limit,Available Amount,Utilized Amount,Matured Amount');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 41: NO_TC_40
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_40', 3, 1060); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_40', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 41, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system be able to populate Borrower details,Tranchee details, drawdown detail on Borrower utilization at facility level capture Borrower Limit at Facility Level');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully populate Borrower details,Tranchee details, drawdown detail on Borrower utilization at facility levelcapture Borrower Limit at Facility Level');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 42: NO_TC_41
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_41', 3, 1061); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_41', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 42, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user to select particpants from the list of value');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected Participant and partcipant name displayed
');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 43: NO_TC_42
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_42', 3, 1062); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_42', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 43, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to  capture asset ratio of the participant');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfuly capturing asset ratio of the participants');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 44: NO_TC_43
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_43', 3, 1063); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_43', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 44, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'User should be able to  indicate if the lead is a self participant ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully indicated');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 45: NO_TC_44
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_44', 3, 1064); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_44', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 45, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'User should be able to indicate if the lead is originator');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully indicated');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 46: NO_TC_45
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_45', 3, 1065); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_45', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 46, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select  participation type from list of values');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected participant type (Lender,Affiliate,Borrower Consent,Agent Consent)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 47: NO_TC_46
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_46', 3, 1066); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_46', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 47, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to upload participant details  from file ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully uploading participant details from the file');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 48: NO_TC_47
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_47', 3, 1067); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_47', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 48, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select currency  from settlement details subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully currency selected');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 49: NO_TC_48
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_48', 3, 1068); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_48', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 49, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select  Mnemonics details  from settlement details subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully Mnemonics details selected');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 50: NO_TC_49
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_49', 3, 1069); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_49', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 50, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to upload Settlement  details  from file ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully uploading settlement details from the file');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 51: NO_TC_50
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_50', 3, 1070); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_50', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 51, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to  select Entity details including Entity ID from Entity subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfull Entinty details selected');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 52: NO_TC_51
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_51', 3, 1071); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_51', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 52, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to upload Entity details  from file ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully uploading Entity details from the file');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 53: NO_TC_52
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_52', 3, 1072); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_52', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 53, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System  populate fee components for each participants at component details tab');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully fee component populated');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 54: NO_TC_53
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_53', 3, 1073); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_53', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 54, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Participants Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System  allow user to amend/change participant ratio at component details tab');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully amend/change participant ratio');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 55: NO_TC_54
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_54', 3, 1074); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_54', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 55, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Party Type Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can systme allow user to select whether party involved in syndication are either Lead, participant,arranger, Originator or Borrower');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully selected syndication involvement (Lead, participant,arranger, Originator or Borrower)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 56: NO_TC_55
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_55', 3, 1075); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_55', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 56, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Fee Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system be able to display all the fees associated with components and model of liquidation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully  fees associated with components and model of liquidation displayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 57: NO_TC_56
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_56', 3, 1076); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_56', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 57, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Fee Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to change fee distribution ratio at ratio sub tab');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully amendment of fee distribution ratio ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 58: NO_TC_57
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_57', 3, 1077); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_57', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 58, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Settlement Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, ' System should be able to populate all settlement details  maintained');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully displayed all settlement details ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 59: NO_TC_58
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_58', 3, 1078); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_58', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 59, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'MIS/UDF');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture /select customer risk grading (NMB_AAA, NMB_AA+,NMB_AA,NMB_AA-,NMB_A+) etc ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Customer risk grade captured successful ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 60: NO_TC_59
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_59', 3, 1079); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_59', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 60, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'MIS/UDF');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture/select from list of value  sector as per BOT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Sector selected successfully ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 61: NO_TC_60
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_60', 3, 1080); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_60', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 61, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'MIS/UDF');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to capture/select customers main business physical location');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Business physical location successfully captured ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 62: NO_TC_61
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_61', 3, 1081); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_61', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 62, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'MIS/UDF');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user link created property collateral  with physical risk associated with property (Wildfire,Drought ,Extreme Heat,Landslide,River Flood,Urban Flood,Coastal Flood,Water scarcity,Cyclone ,Not Applicable');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully collateral linked to a facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 63: NO_TC_62
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_62', 3, 1082); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_62', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 63, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'MIS/UDF');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can System allow user link created Machinery collateral and mark physical risk associated with property (Wildfire,Drought ,Extreme Heat,Landslide,River Flood,Urban Flood,Coastal Flood,Water scarcity,Cyclone ,Not Applicable');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully collateral linked to a facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 64: NO_TC_63
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_63', 3, 1083); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_63', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 64, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'MIS/UDF');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can sytem allow user to select  Green and Social Assets sector from the list of value (Pollution Prevention,Sustainable water and wastewater Management,Clean Transportation,Green Buildings,Climate Change Adaptation,Living Natural Resources and Land Use ,Affordable Housing,Access to Essential services,Employment Generation,Socio-economic Advancement and empowerment,
Food Security,efficiency )on  risk report');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Green and social sector successful selected ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 65: NO_TC_64
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_64', 3, 1084); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_64', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 65, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system allow user to select from list of value  Environmental and Social risk categorization  (i.e high risk, Medium and Low risk)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful selection of Environmental and Social risk ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 66: NO_TC_65
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_65', 3, 1085); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_65', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 66, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'System should allow user to save the contract upon complition to capture all mandatory information');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Contract saved successfully');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 67: NO_TC_66
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_66', 3, 1086); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_66', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 67, 1, 1, 
'<p>Creation of a loan contract  -  Loan Syndication (FC01)</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'New');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Creation of a loan contract  -  Loan Syndication (FC01)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'System should allow user to authorize the contract upon capturing rekey fields');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Contract authorized successfully');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 68: NO_TC_67
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_67', 3, 1087); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_67', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 68, 1, 1, 
'<p>Collection of Processing Fee at -Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'OLDEVENT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Collection of Processing Fee at -Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system collect Processing fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful  collect Processing fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 69: NO_TC_68
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_68', 3, 1088); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_68', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 69, 1, 1, 
'<p>Collection ofCommitment Fee at -Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'OLDEVENT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Collection ofCommitment Fee at -Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system collect Commitment Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful  collect Commitment Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 70: NO_TC_69
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_69', 3, 1089); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_69', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 70, 1, 1, 
'<p>Collection of Agency Fee at -Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'OLDEVENT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Collection of Agency Fee at -Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system collect Agency Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful  collect Agency Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 71: NO_TC_70
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_70', 3, 1090); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_70', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 71, 1, 1, 
'<p>Collection of Security agency Fee at -Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'OLDEVENT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Collection of Security agency Fee at -Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system collect Security agency Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful  collect Security agency Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 72: NO_TC_71
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_71', 3, 1091); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_71', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 72, 1, 1, 
'<p>Collection of Arrangement Fee at -Borrower facility-FC01</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'OLDEVENT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Collection of Arrangement Fee at -Borrower facility-FC01');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system collect Arrangement Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful  collect Arrangement Fee (Amount) by debiting Borrower''s account and credit respective GL ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 73: NO_TC_72
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_72', 3, 1092); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_72', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 73, 1, 1, 
'<p>Party Summary</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'FCDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Loan syndication  Facility contract online');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Borrower Facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Party Summary');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'From the Borrower facility can system create related participant as per each participant ration with all details on the facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Sucessful generated refence number/account number for each Participants ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 74: NO_TC_73
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_73', 3, 1093); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_73', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 74, 1, 1, 
'<p>Participant Contract View</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Participant Contract View');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can the sytem allow user to fetch/capture Contract Reference');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful display/populate Branch ,Partcipant Facility Product Code ,Participant Product Description, Borrower Facility Custom Reference Number,User Reference');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 75: NO_TC_74
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_74', 3, 1094); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_74', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 75, 1, 1, 
'<p>Participant Contract View</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Participant Contract View');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can the system populate Contract Details (Counterparty,Counterparty Description
,Currency,Currency Description,Amount,Booking Date,Borrower Contract Ref No,Facility Reference Number,Drawdown Number,Value Date');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully display contract details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 76: NO_TC_75
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_75', 3, 1095); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_75', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 76, 1, 1, 
'<p>Participant Contract View</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Participant Contract View');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system populate Reporting Details (Reporting Amount
Reporting Currency,Remarks)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully reporting details displayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 77: NO_TC_76
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_76', 3, 1096); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_76', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 77, 1, 1, 
'<p>Tax details</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Tax details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system populate Tax Details (Tax Rule,Value Date,Tax Currency,Tax Amount)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful tax details displayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 78: NO_TC_77
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_77', 3, 1097); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_77', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 78, 1, 1, 
'<p>Settlement details</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input-Settlement subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Settlement details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system populate Settlement Details ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful settelement details displayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 79: NO_TC_78
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_78', 3, 1098); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_78', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 79, 1, 1, 
'<p>Events</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'OLDACENT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input-Events subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Events');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system pulate Events details (Accounting Entries-Fees and charges for the participants with respect to the ratio captured at borrower facility)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfull accounting entries displayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 80: NO_TC_79
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_79', 3, 1099); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_79', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 80, 1, 1, 
'<p>Events</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'OLDACENT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input-Events subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Events');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system populate Events details (Messages)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful dispay Messages details (Message Type,SWIFT Message Type,SWIFT MX Type,Medium,Message Status,Authorization Status,Receiver,Receiver Name,DCN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 81: NO_TC_80
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_80', 3, 1100); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_80', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 81, 1, 1, 
'<p>UDF/MIS</p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input-UDF/MIS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'UDF/MIS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system populate UDF/MIS captured at borrower''s facility');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 82: NO_TC_81
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_81', 3, 1101); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_81', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 82, 1, 1, 
'<p>Syndication Details </p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input-LS Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Syndication Details ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system populate Syndication Details ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successfully display syndication details (Borrower Details and Participant Details)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 83: NO_TC_82
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (2, 'NO_TC_82', 3, 1102); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'NO_TC_82', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 83, 1, 1, 
'<p>Participant Rate History </p>', 
2, 1, NOW(), NOW(), 
1, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'LPDCOMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Participant Contract Input ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Participant Facility Contract Input-Rate History Subscreen');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Participant Rate History ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Can system populate Interest Components');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'TEMPLATE_EMPTY');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Successful Interest component ( Margin Details and Margin Rate) of participants displayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');

COMMIT;
