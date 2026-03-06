USE `tl_uat`;

-- Enhanced TestLink Import SQL Script
-- Generated on 2025-07-17 12:36:04
-- Test Suite ID: 148468

START TRANSACTION;


-- Test Case 1: CX-DD
BC-TC1
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC1', 3, 100); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC1', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 1, 1, 1, 
'<p>customer can easily ask informaton about cheque Withdraw</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Availability of cheque withdraw information on Jiran');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Jirani');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'discover');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer can easily ask informaton about cheque Withdraw');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '1.Confirm  new customers get enough knowledge on filling out the open cheques');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Customers will fill out cheques correctly ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Customers will fill out cheques correctly ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 2: CX-DD
BC-TC2
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC2', 3, 101); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC2', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 2, 1, 1, 
'<p>Customer handles cheque and Id to teller</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Cheque withdraw');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Customer handles cheque and Id to teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '3.confirm customers account can be verified,signature match,date validity,sufficient balance,stop payment or post dated flags');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Ask Jiran on availability of cheque withdraw');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, '361 view of customers ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 3: CX-DD
BC-TC3
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC3', 3, 102); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC3', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 3, 1, 1, 
'<p>confirm if system can provide the ability to capture extra data requirements with regards to the customers’ withdrawal
Teller posts the transaction and gives cash </p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Cheque withdraw');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'confirm if system can provide the ability to capture extra data requirements with regards to the customers’ withdrawal
Teller posts the transaction and gives cash ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '4.confirm that customers are debited correctly and narration are well captured');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller > Customer Transaction > Cheque withdrawal');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customers are debited correctly and narration are well captured');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 4: CX-DD
BC-TC4
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC4', 3, 103); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC4', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 4, 1, 1, 
'<p>Authorization(if needed)</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Cheque withdraw');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Authorization(if needed)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '5.High value cheques may require supervisor override,confirm dual control via cbs ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller > Customer Transaction > Cheque withdrawal');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Approval workflow ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 5: CX-DD
BC-TC5
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC5', 3, 104); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC5', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 5, 1, 1, 
'<p>customer receives sms or email alerts</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Transaction notifications');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Mkononi platform');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'discover');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer receives sms or email alerts');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '6.confirm if  customers receive notification upon transaction completion');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller > elecronic journal > Cheque withdrawal');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'transaction completetion notification');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 6: CX-DD
BC-TC6
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC6', 3, 105); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC6', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 6, 1, 1, 
'<p>customer can easily ask informaton about cheque deposit</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Availability of cheque deposit information on Jiran');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Jirani');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'discover');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer can easily ask informaton about cheque deposit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '7.check if customer can get information about check deposit at  Jirani');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Customer tab->Search by');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer gets pre information before service');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 7: CX-DD
BC-TC7
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC7', 3, 106); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC7', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 7, 1, 1, 
'<p>availability of explained products and services on website</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'WEBSITE');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'website');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'discover');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'availability of explained products and services on website');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '8.verify if customer can get all information about cheque deposit on website');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Ask Jiran on availability of cheque deposit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer is able to get full information of the products');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 8: CX-DD
BC-TC8
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC8', 3, 107); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC8', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 8, 1, 1, 
'<p>product knowledge</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Products & Services');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'FCUB');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'discover');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'product knowledge');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '9.verify if branch staff have knowledge about cheque depost and can assist all customers with cheque deposit requests');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'availability of information on website https://www.nmbbank.co.tz/');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer is able to get clarity from staff about the product');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 9: CX-DD
BC-TC9
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC9', 3, 108); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC9', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 9, 1, 1, 
'<p>A new customer is able to fill out cheque deposit slip at front desk</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'BRANCH - Information / details on cheque');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'No system required');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'discover');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'A new customer is able to fill out cheque deposit slip at front desk');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '10.Confirm  new customers have enough knowledge on filling out the cheques slip');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Sharepoints->Documents->products & services');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer fills cheque deposit correctly');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 10: CX-DD
BC-TC10
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC10', 3, 109); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC10', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 10, 1, 1, 
'<p>Submission of cheque at branch/ teller</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Scaning');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'SYBRIN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Submission of cheque at branch/ teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '11.confrim if the cheque is genuine for payments');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'well knowlegable staff to guide customers');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'verification is done effectively');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 11: CX-DD
BC-TC11
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC11', 3, 110); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC11', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 11, 1, 1, 
'<p>image scanning</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Scaning');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'SYBRIN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'image scanning');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '12.confirm if  Teller can scan cheque image on sybring ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'sybrin->Scan cheque');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'visibility of image on the system');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 12: CX-DD
BC-TC12
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC12', 3, 111); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC12', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 12, 1, 1, 
'<p>cutomer account is credited (transaction within limit)</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'ACDOPTN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'cutomer account is credited (transaction within limit)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '13.confim if customers account is credited');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'sybrin->Scan cheque');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Debit and credit entry is done correctly');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 13: CX-DD
BC-TC13
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC13', 3, 112); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC13', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 13, 1, 1, 
'<p>''Dual control ( transaction above limit)</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Authorization');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'SYBRIN');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, '''Dual control ( transaction above limit)');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '14.confirm if authoriation is done for transactions above tellers limit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Customer account->customer account view');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'authorization is done timetly');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 14: CX-DD
BC-TC14
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC14', 3, 113); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC14', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 14, 1, 1, 
'<p>For other bank cheques with amount over limit,amount appears as pending until cleared by clearing house</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Account balance');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For other bank cheques with amount over limit,amount appears as pending until cleared by clearing house');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '15.confirm if customers are credited on their account after cleared ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Authorize transactions above limit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Debit and credit entry is done correctly');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 15: CX-DD
BC-TC15
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC15', 3, 114); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC15', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 15, 1, 1, 
'<p>customer raises a concern or seek help with a cheque deposit</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Support / Complaint');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer raises a concern or seek help with a cheque deposit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '16.confirm and retrive transaction history 
');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'teller>customer service>account balance inquiry');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Confirmation of the transaction');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 16: CX-DD
BC-TC16
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC16', 3, 115); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC16', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 16, 1, 1, 
'<p>customer raises a concern or seek help with a cheque deposit</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'New ticket');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'NMB CURE');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Support / Complaint');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer raises a concern or seek help with a cheque deposit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '17.complaint registered via NMB cure');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Sharepoints->Systems->BI & Reporting tools->Folders->Public folders->BankBI NMB->Branch onlineReport->customer reports');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'registration is successful registered');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 17: CX-DD
BC-TC17
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC17', 3, 116); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC17', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 17, 1, 1, 
'<p>customer raises a concern or seek help with a cheque deposit</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'DEDTRONL');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'FCUB');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Support / Complaint');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer raises a concern or seek help with a cheque deposit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '18.confirm manual correction or reversal can be performed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'NMB cure portal->New ticket');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer complaint is resolved within a short period of time');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 18: CX-DD
BC-TC18
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC18', 3, 117); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC18', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 18, 1, 1, 
'<p>Bill payments</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Bill payments');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Bill payments');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '19.confirm if bill payment can be done by cheque ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'FCUB->Accounting & IMS->Journal operations->Single entry');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'bill payment should be successful by cheques');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 19: CX-DD
BC-TC19
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC19', 3, 118); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC19', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 19, 1, 1, 
'<p>validation of billers</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Account validation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'validation of billers');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '20.confirm if billers from other bank can be validated ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller>bill payment>bill payment by other modes');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'account validation ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 20: CX-DD
BC-TC20
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC20', 3, 119); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC20', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 20, 1, 1, 
'<p>Notifications </p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Unpaid cheque notifications');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Mkononi platform');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Choose / Apply');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Notifications ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '21.confirm if customer can receive notifications for unpaid cheques for both inwards and inhouse cheques  through email or message');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller>bill payment>bill payment by other modes');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer should receive an sms for unpaid cheque ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 21: CX-DD
BC-TC21
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC21', 3, 120); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC21', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 21, 1, 1, 
'<p>Request of cheque book via internet banking</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Unpaid cheque notifications');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Internet Banking');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Choose / Apply');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Request of cheque book via internet banking');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '21.confirm if customer can request cheque book via internet banking');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Customer tab->Search by');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer should receive an sms for unpaid cheque ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 22: CX-DD
BC-TC22
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC22', 3, 121); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC22', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 22, 1, 1, 
'<p>checklist validation</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'checklist validation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'checklist validation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'negative');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '22.confirm the validation of checklist during transaction operation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Customer tab->Search by');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'system should prevent the transaction if checlist is not uploaded or mismatch with the cheque details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 23: CX-DD
BC-TC23
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC23', 3, 122); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC23', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 23, 1, 1, 
'<p>cheque settlement </p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'cheque settlement ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'cheque settlement ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '23.confirm settlement is done before crediting customers account');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller transaction->cheque withdraw');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customer should not have balance mismatch');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 24: CX-DD
BC-TC24
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC24', 3, 123); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC24', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 24, 1, 1, 
'<p>Name validation </p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'checklist validation');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Obbrn/Teller');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'use');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'Name validation ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '24.confirm  both debit and credit account account name validation is done ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'waiting for intergration');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'system should validate account names .');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 25: CX-DD
BC-TC25
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC25', 3, 124); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC25', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 25, 1, 1, 
'<p>customer can initiate cheque book renew on mkononi</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Cheque book request');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Mkononi platform');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Renew');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer can initiate cheque book renew on mkononi');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '25. confirm if customer can request cheque book through mkononi platform');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller transaction->cheque withdraw/deposit');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'cusomer can submitt the request successful');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 26: CX-DD
BC-TC26
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC26', 3, 125); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC26', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 26, 1, 1, 
'<p>customer can request cheque book on mkononi</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Cheque book request');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Mkononi platform');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Onboard / Set-up');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer can request cheque book on mkononi');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '25. confirm if customer can request cheque book through mkononi platform');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'NMB APP/ USSD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'cusomer can submitt the request successful');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 27: CX-DD
BC-TC27
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC27', 3, 126); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC27', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 27, 1, 1, 
'<p>cheque book request at branch</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'CADCHBOO');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'FCUB');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Onboard / Set-up');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'cheque book request at branch');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '25. confirm if customer can request cheque book at branch');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'NMB APP/ USSD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'cusomer can submitt the request successful');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 28: CX-DD
BC-TC28
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC28', 3, 127); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC28', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 28, 1, 1, 
'<p>cheque book request at branch</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'CADCHBOO');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'FCUB');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Onboard / Set-up');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'cheque book request at branch');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Confirm if the checque book can be requested after ticking the check box for cheque book during account opening');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'teller/common maintainance/cheque book details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'system should pick customer details and process the request successful');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 29: CX-DD
BC-TC29
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC29', 3, 128); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC29', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 29, 1, 1, 
'<p>customer can visit any branch for cheque book request</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'CADCHBOO');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'FCUB');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'Renew');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'customer can visit any branch for cheque book request');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '26. confirm if customer can request cheque book at any branch');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Customer Accounts > customer account Operations > Customer Accounts Input');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'cusomer can submitt the request successful');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 30: CX-DD
BC-TC30
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC30', 3, 129); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC30', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 30, 1, 1, 
'<p>stop cheque </p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'Stop cheque payment');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Mkononi platform');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'close');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'stop cheque ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '27.confirm if customer can initiate stop payment through nmb mkononi');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Teller->Common matainance->Cheque book details');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customers cheque book shouldnot work after being stopped');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');


-- Test Case 31: CX-DD
BC-TC31
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (148468, 'CX-DD
BC-TC31', 3, 130); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'CX-DD
BC-TC31', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 31, 1, 1, 
'<p>stop cheque </p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, 'CADSPMNT');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'FCUB');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, 'close');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'stop cheque ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, '28. confirm if customers cheque can be stoped');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'NMB APP/ USSD');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'customers cheque book shouldnot work after being stopped');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, 'TEMPLATE_EMPTY');

COMMIT;
