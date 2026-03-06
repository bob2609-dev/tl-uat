USE `tl_uat`;

-- Enhanced TestLink Import SQL Script
-- Generated on 2026-01-03 13:13:05
-- Test Suite ID: 350136

START TRANSACTION;


-- Test Case 1: EOD_CARD-TC01
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC01', 3, 100); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC01', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 1, 1, 1, 
'<p>After EOD run, confirm that the system date has been changed</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Date Change');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'After EOD run, confirm that the system date has been changed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Confirm that the system date has been changed after EOD run');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Log into Flexcube 14.7>confirm system date');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'System date has been changed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');


-- Test Case 2: EOD_CARD-TC02
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC02', 3, 101); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC02', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 2, 1, 1, 
'<p>For a card that has been produced and dispatched and not activated for the past six month, verify if after EOD status has been changed to overstayed</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Status Change');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For a card that has been produced and dispatched and not activated for the past six month, verify if after EOD status has been changed to overstayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Confirm after EOD run card that has been produced, dispatched and not activated for the past six month has been changed status to overstayed');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Cards >  Maintenance > Card Master Summary>confirm card status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Card status should automatically be changed status after EOD run');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');


-- Test Case 3: EOD_CARD-TC03
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC03', 3, 102); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC03', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 3, 1, 1, 
'<p>For a card that has reached its expiry date, verify if after EOD status has been changed to Expired</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Status Change');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For a card that has reached its expiry date, verify if after EOD status has been changed to Expired');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Confirm after EOD run card that has eached its expiry date card status is changed to Expiry ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'Cards >  Maintenance > Card Master Summary>confirm card status');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Card status should automatically be changed status after EOD run');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');


-- Test Case 4: EOD_CARD-TC04
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC04', 3, 103); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC04', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 4, 1, 1, 
'<p>For a periodic charges, after EOQ run confirm annual card fees have been collected from customer''s account and proper distribution has been done</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Fees Collection');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For a periodic charges, after EOQ run confirm annual card fees have been collected from customer''s account and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Confirm after EOQ run annual card fees have been collected from customer''s account and proper distribution has been done ');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'BI>Customer statement>confirm charges');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Annual card fees have been collected and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');


-- Test Case 5: EOD_CARD-TC05
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC05', 3, 104); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC05', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 5, 1, 1, 
'<p>For a online charges, after EOD run confirm Card Transfer Fees have been collected from customer''s account and proper distribution has been done</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Fees Collection');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For a online charges, after EOD run confirm Card Transfer Fees have been collected from customer''s account and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'For a online charges, after EOD run confirm Card Transfer Fees have been collected from customer''s account and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'BI>Customer statement>confirm charges');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Card Transfer fees have been collected and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');


-- Test Case 6: EOD_CARD-TC06
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC06', 3, 105); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC06', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 6, 1, 1, 
'<p>For a online charges, after EOD run confirm Card Overstayed Fees  have been collected from customer''s account and proper distribution has been done</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Fees Collection');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For a online charges, after EOD run confirm Card Overstayed Fees  have been collected from customer''s account and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'For a online charges, after EOD run confirm Card Overstayed Fees  have been collected from customer''s account and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'BI>Customer statement>confirm charges');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Card Overstayed fees have been collected and proper distribution has been done');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');


-- Test Case 7: EOD_CARD-TC07
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC07', 3, 106); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC07', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 7, 1, 1, 
'<p>For a transaction that has been done during EOD run via ATM</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Withdraw via ATM');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For a transaction that has been done during EOD run via ATM');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Attempt to withdraw any amount  via ATM during EOD run and Confirm the transaction has been properly captured after EOD run');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'ATM>Withdraw>Enter amount');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction is posted with appropriate dates');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');


-- Test Case 8: EOD_CARD-TC08
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (350136, 'EOD_CARD-TC08', 3, 107); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, 'EOD_CARD-TC08', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, 8, 1, 1, 
'<p>For a transaction that has been done during EOD run via POS</p>', 
2, 1, NOW(), NOW(), 
7, 1, 1);

-- Insert custom field values
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (1, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (2, @tcversion_id, 'Purchase via POS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (3, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (4, @tcversion_id, 'For a transaction that has been done during EOD run via POS');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (5, @tcversion_id, 'Positive');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (6, @tcversion_id, 'Attempt to purchase any amount  via POS during EOD run and Confirm the transaction has been properly captured after EOD run');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (7, @tcversion_id, 'POS>Purchase');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (8, @tcversion_id, 'Transaction is posted with appropriate dates');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (9, @tcversion_id, '_');
INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) VALUES (15, @tcversion_id, 'CRITICAL');

COMMIT;
