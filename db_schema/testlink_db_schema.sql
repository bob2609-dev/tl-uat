-- MariaDB dump 10.17  Distrib 10.4.14-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: testlink_db
-- ------------------------------------------------------
-- Server version	10.4.14-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `assignment_status`
--

DROP TABLE IF EXISTS `assignment_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignment_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assignment_types`
--

DROP TABLE IF EXISTS `assignment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignment_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_table` varchar(30) DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_id` int(10) unsigned NOT NULL DEFAULT 0,
  `fk_table` varchar(250) DEFAULT '',
  `title` varchar(250) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `file_name` varchar(250) NOT NULL DEFAULT '',
  `file_path` varchar(250) DEFAULT '',
  `file_size` int(11) NOT NULL DEFAULT 0,
  `file_type` varchar(250) NOT NULL DEFAULT '',
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` longblob DEFAULT NULL,
  `compression_type` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `attachments_idx1` (`fk_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14413 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `baseline_l1l2_context`
--

DROP TABLE IF EXISTS `baseline_l1l2_context`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `baseline_l1l2_context` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `platform_id` int(10) unsigned NOT NULL DEFAULT 0,
  `being_exec_ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_exec_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx1` (`testplan_id`,`platform_id`,`creation_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `baseline_l1l2_details`
--

DROP TABLE IF EXISTS `baseline_l1l2_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `baseline_l1l2_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `context_id` int(10) unsigned NOT NULL,
  `top_tsuite_id` int(10) unsigned NOT NULL DEFAULT 0,
  `child_tsuite_id` int(10) unsigned NOT NULL DEFAULT 0,
  `status` char(1) DEFAULT NULL,
  `qty` int(10) unsigned NOT NULL DEFAULT 0,
  `total_tc` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx1` (`context_id`,`top_tsuite_id`,`child_tsuite_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `builds`
--

DROP TABLE IF EXISTS `builds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `builds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT 'undefined',
  `notes` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `release_date` date DEFAULT NULL,
  `closed_on_date` date DEFAULT NULL,
  `commit_id` varchar(64) DEFAULT NULL,
  `tag` varchar(64) DEFAULT NULL,
  `branch` varchar(64) DEFAULT NULL,
  `release_candidate` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`testplan_id`,`name`),
  KEY `testplan_id` (`testplan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Available builds';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfield_build_design_values`
--

DROP TABLE IF EXISTS `cfield_build_design_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cfield_build_design_values` (
  `field_id` int(10) NOT NULL DEFAULT 0,
  `node_id` int(10) NOT NULL DEFAULT 0,
  `value` varchar(4000) NOT NULL DEFAULT '',
  PRIMARY KEY (`field_id`,`node_id`),
  KEY `idx_cfield_build_design_values` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfield_design_values`
--

DROP TABLE IF EXISTS `cfield_design_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cfield_design_values` (
  `field_id` int(10) NOT NULL DEFAULT 0,
  `node_id` int(10) NOT NULL DEFAULT 0,
  `value` varchar(4000) NOT NULL DEFAULT '',
  PRIMARY KEY (`field_id`,`node_id`),
  KEY `idx_cfield_design_values` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfield_execution_values`
--

DROP TABLE IF EXISTS `cfield_execution_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cfield_execution_values` (
  `field_id` int(10) NOT NULL DEFAULT 0,
  `execution_id` int(10) NOT NULL DEFAULT 0,
  `testplan_id` int(10) NOT NULL DEFAULT 0,
  `tcversion_id` int(10) NOT NULL DEFAULT 0,
  `value` varchar(4000) NOT NULL DEFAULT '',
  PRIMARY KEY (`field_id`,`execution_id`,`testplan_id`,`tcversion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfield_node_types`
--

DROP TABLE IF EXISTS `cfield_node_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cfield_node_types` (
  `field_id` int(10) NOT NULL DEFAULT 0,
  `node_type_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`field_id`,`node_type_id`),
  KEY `idx_custom_fields_assign` (`node_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfield_testplan_design_values`
--

DROP TABLE IF EXISTS `cfield_testplan_design_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cfield_testplan_design_values` (
  `field_id` int(10) NOT NULL DEFAULT 0,
  `link_id` int(10) NOT NULL DEFAULT 0 COMMENT 'point to testplan_tcversion id',
  `value` varchar(4000) NOT NULL DEFAULT '',
  PRIMARY KEY (`field_id`,`link_id`),
  KEY `idx_cfield_tplan_design_val` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cfield_testprojects`
--

DROP TABLE IF EXISTS `cfield_testprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cfield_testprojects` (
  `field_id` int(10) unsigned NOT NULL DEFAULT 0,
  `testproject_id` int(10) unsigned NOT NULL DEFAULT 0,
  `display_order` smallint(5) unsigned NOT NULL DEFAULT 1,
  `location` smallint(5) unsigned NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `required_on_design` tinyint(1) NOT NULL DEFAULT 0,
  `required_on_execution` tinyint(1) NOT NULL DEFAULT 0,
  `monitorable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`field_id`,`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `codetrackers`
--

DROP TABLE IF EXISTS `codetrackers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `codetrackers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` int(10) DEFAULT 0,
  `cfg` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codetrackers_uidx1` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_fields`
--

DROP TABLE IF EXISTS `custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_fields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `label` varchar(64) NOT NULL DEFAULT '' COMMENT 'label to display on user interface',
  `type` smallint(6) NOT NULL DEFAULT 0,
  `possible_values` varchar(4000) NOT NULL DEFAULT '',
  `default_value` varchar(4000) NOT NULL DEFAULT '',
  `valid_regexp` varchar(255) NOT NULL DEFAULT '',
  `length_min` int(10) NOT NULL DEFAULT 0,
  `length_max` int(10) NOT NULL DEFAULT 0,
  `show_on_design` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '1=> show it during specification design',
  `enable_on_design` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '1=> user can write/manage it during specification design',
  `show_on_execution` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '1=> show it during test case execution',
  `enable_on_execution` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '1=> user can write/manage it during test case execution',
  `show_on_testplan_design` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `enable_on_testplan_design` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_custom_fields_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `db_version`
--

DROP TABLE IF EXISTS `db_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_version` (
  `version` varchar(50) NOT NULL DEFAULT 'unknown',
  `upgrade_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(10) unsigned NOT NULL DEFAULT 0,
  `log_level` smallint(5) unsigned NOT NULL DEFAULT 0,
  `source` varchar(45) DEFAULT NULL,
  `description` text NOT NULL,
  `fired_at` int(10) unsigned NOT NULL DEFAULT 0,
  `activity` varchar(45) DEFAULT NULL,
  `object_id` int(10) unsigned DEFAULT NULL,
  `object_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `fired_at` (`fired_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4396166 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `exec_by_date_time`
--

DROP TABLE IF EXISTS `exec_by_date_time`;
/*!50001 DROP VIEW IF EXISTS `exec_by_date_time`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `exec_by_date_time` (
  `testplan_name` tinyint NOT NULL,
  `yyyy_mm_dd` tinyint NOT NULL,
  `yyyy_mm` tinyint NOT NULL,
  `hh` tinyint NOT NULL,
  `hour` tinyint NOT NULL,
  `id` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `tester_id` tinyint NOT NULL,
  `execution_ts` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `tcversion_id` tinyint NOT NULL,
  `tcversion_number` tinyint NOT NULL,
  `platform_id` tinyint NOT NULL,
  `execution_type` tinyint NOT NULL,
  `execution_duration` tinyint NOT NULL,
  `notes` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `execution_bugs`
--

DROP TABLE IF EXISTS `execution_bugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `execution_bugs` (
  `execution_id` int(10) unsigned NOT NULL DEFAULT 0,
  `bug_id` varchar(64) NOT NULL DEFAULT '0',
  `tcstep_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`execution_id`,`bug_id`,`tcstep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `execution_tcsteps`
--

DROP TABLE IF EXISTS `execution_tcsteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `execution_tcsteps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `execution_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcstep_id` int(10) unsigned NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `execution_tcsteps_idx1` (`execution_id`,`tcstep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `execution_tcsteps_wip`
--

DROP TABLE IF EXISTS `execution_tcsteps_wip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `execution_tcsteps_wip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tcstep_id` int(10) unsigned NOT NULL DEFAULT 0,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `platform_id` int(10) unsigned NOT NULL DEFAULT 0,
  `build_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tester_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `execution_tcsteps_wip_idx1` (`tcstep_id`,`testplan_id`,`platform_id`,`build_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `executions`
--

DROP TABLE IF EXISTS `executions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `executions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `build_id` int(10) NOT NULL DEFAULT 0,
  `tester_id` int(10) unsigned DEFAULT NULL,
  `execution_ts` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_number` smallint(5) unsigned NOT NULL DEFAULT 1,
  `platform_id` int(10) unsigned NOT NULL DEFAULT 0,
  `execution_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 -> manual, 2 -> automated',
  `execution_duration` decimal(6,2) DEFAULT NULL COMMENT 'NULL will be considered as NO DATA Provided by user',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `executions_idx1` (`testplan_id`,`tcversion_id`,`platform_id`,`build_id`),
  KEY `executions_idx2` (`execution_type`),
  KEY `executions_idx3` (`tcversion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50643 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `executions_094_only_backup_20250416`
--

DROP TABLE IF EXISTS `executions_094_only_backup_20250416`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `executions_094_only_backup_20250416` (
  `id` int(10) unsigned NOT NULL DEFAULT 0,
  `build_id` int(10) NOT NULL DEFAULT 0,
  `tester_id` int(10) unsigned DEFAULT NULL,
  `execution_ts` datetime DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8 DEFAULT NULL,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_number` smallint(5) unsigned NOT NULL DEFAULT 1,
  `platform_id` int(10) unsigned NOT NULL DEFAULT 0,
  `execution_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 -> manual, 2 -> automated',
  `execution_duration` decimal(6,2) DEFAULT NULL COMMENT 'NULL will be considered as NO DATA Provided by user',
  `notes` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `executions_backup_20250416`
--

DROP TABLE IF EXISTS `executions_backup_20250416`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `executions_backup_20250416` (
  `id` int(10) unsigned NOT NULL DEFAULT 0,
  `build_id` int(10) NOT NULL DEFAULT 0,
  `tester_id` int(10) unsigned DEFAULT NULL,
  `execution_ts` datetime DEFAULT NULL,
  `status` char(1) CHARACTER SET utf8 DEFAULT NULL,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_number` smallint(5) unsigned NOT NULL DEFAULT 1,
  `platform_id` int(10) unsigned NOT NULL DEFAULT 0,
  `execution_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 -> manual, 2 -> automated',
  `execution_duration` decimal(6,2) DEFAULT NULL COMMENT 'NULL will be considered as NO DATA Provided by user',
  `notes` text CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testproject_id` int(10) unsigned NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `ipaddress` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `modification_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `inventory_idx1` (`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `issuetrackers`
--

DROP TABLE IF EXISTS `issuetrackers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issuetrackers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` int(10) DEFAULT 0,
  `cfg` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issuetrackers_uidx1` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keywords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) NOT NULL DEFAULT '',
  `testproject_id` int(10) unsigned NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword_testproject_id` (`keyword`,`testproject_id`),
  KEY `testproject_id` (`testproject_id`),
  KEY `keyword` (`keyword`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `latest_exec_by_context`
--

DROP TABLE IF EXISTS `latest_exec_by_context`;
/*!50001 DROP VIEW IF EXISTS `latest_exec_by_context`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_exec_by_context` (
  `tcversion_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `platform_id` tinyint NOT NULL,
  `id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `latest_exec_by_testplan`
--

DROP TABLE IF EXISTS `latest_exec_by_testplan`;
/*!50001 DROP VIEW IF EXISTS `latest_exec_by_testplan`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_exec_by_testplan` (
  `tcversion_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `latest_exec_by_testplan_plat`
--

DROP TABLE IF EXISTS `latest_exec_by_testplan_plat`;
/*!50001 DROP VIEW IF EXISTS `latest_exec_by_testplan_plat`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_exec_by_testplan_plat` (
  `tcversion_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `platform_id` tinyint NOT NULL,
  `id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `latest_req_version`
--

DROP TABLE IF EXISTS `latest_req_version`;
/*!50001 DROP VIEW IF EXISTS `latest_req_version`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_req_version` (
  `req_id` tinyint NOT NULL,
  `version` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `latest_req_version_id`
--

DROP TABLE IF EXISTS `latest_req_version_id`;
/*!50001 DROP VIEW IF EXISTS `latest_req_version_id`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_req_version_id` (
  `req_id` tinyint NOT NULL,
  `version` tinyint NOT NULL,
  `req_version_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `latest_rspec_revision`
--

DROP TABLE IF EXISTS `latest_rspec_revision`;
/*!50001 DROP VIEW IF EXISTS `latest_rspec_revision`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_rspec_revision` (
  `req_spec_id` tinyint NOT NULL,
  `testproject_id` tinyint NOT NULL,
  `revision` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `latest_tcase_version_id`
--

DROP TABLE IF EXISTS `latest_tcase_version_id`;
/*!50001 DROP VIEW IF EXISTS `latest_tcase_version_id`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_tcase_version_id` (
  `testcase_id` tinyint NOT NULL,
  `version` tinyint NOT NULL,
  `tcversion_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `latest_tcase_version_number`
--

DROP TABLE IF EXISTS `latest_tcase_version_number`;
/*!50001 DROP VIEW IF EXISTS `latest_tcase_version_number`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `latest_tcase_version_number` (
  `testcase_id` tinyint NOT NULL,
  `version` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `milestones`
--

DROP TABLE IF EXISTS `milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `milestones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `target_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `a` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `b` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `c` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) NOT NULL DEFAULT 'undefined',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_testplan_id` (`name`,`testplan_id`),
  KEY `testplan_id` (`testplan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `new_col_update_tb`
--

DROP TABLE IF EXISTS `new_col_update_tb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `new_col_update_tb` (
  `name` varchar(50) DEFAULT NULL,
  `tc_id` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `script` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `node_hierarchy_paths_v2`
--

DROP TABLE IF EXISTS `node_hierarchy_paths_v2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node_hierarchy_paths_v2` (
  `node_id` int(11) NOT NULL,
  `level1_id` int(11) DEFAULT NULL,
  `level1_name` varchar(100) DEFAULT NULL,
  `level2_id` int(11) DEFAULT NULL,
  `level2_name` varchar(100) DEFAULT NULL,
  `level3_id` int(11) DEFAULT NULL,
  `level3_name` varchar(100) DEFAULT NULL,
  `level4_id` int(11) DEFAULT NULL,
  `level4_name` varchar(100) DEFAULT NULL,
  `level5_id` int(11) DEFAULT NULL,
  `level5_name` varchar(100) DEFAULT NULL,
  `full_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`node_id`),
  KEY `idx_l1` (`level1_id`),
  KEY `idx_l2` (`level2_id`),
  KEY `idx_l3` (`level3_id`),
  KEY `idx_l4` (`level4_id`),
  KEY `idx_l5` (`level5_id`),
  KEY `idx_hierarchy_path_fullpath` (`full_path`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `node_types`
--

DROP TABLE IF EXISTS `node_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL DEFAULT 'testproject',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodes_hierarchy`
--

DROP TABLE IF EXISTS `nodes_hierarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes_hierarchy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `node_type_id` int(10) unsigned NOT NULL DEFAULT 1,
  `node_order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid_m_nodeorder` (`parent_id`,`node_order`),
  KEY `nodes_hierarchy_node_type_id` (`node_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=232649 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_keywords`
--

DROP TABLE IF EXISTS `object_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_keywords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_id` int(10) unsigned NOT NULL DEFAULT 0,
  `fk_table` varchar(30) DEFAULT '',
  `keyword_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx01_object_keywords` (`fk_id`,`fk_table`,`keyword_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parent_update_table`
--

DROP TABLE IF EXISTS `parent_update_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parent_update_table` (
  `name` varchar(50) DEFAULT NULL,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parent_update_table_1`
--

DROP TABLE IF EXISTS `parent_update_table_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parent_update_table_1` (
  `name` varchar(50) DEFAULT NULL,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parent_update_table_2`
--

DROP TABLE IF EXISTS `parent_update_table_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parent_update_table_2` (
  `name` varchar(50) DEFAULT NULL,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `platforms`
--

DROP TABLE IF EXISTS `platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `testproject_id` int(10) unsigned NOT NULL,
  `notes` text NOT NULL,
  `enable_on_design` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `enable_on_execution` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_platforms` (`testproject_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `basename` varchar(100) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugins_configuration`
--

DROP TABLE IF EXISTS `plugins_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `testproject_id` int(11) NOT NULL,
  `config_key` varchar(255) NOT NULL,
  `config_type` int(11) NOT NULL,
  `config_value` varchar(255) NOT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `req_coverage`
--

DROP TABLE IF EXISTS `req_coverage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `req_coverage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `req_id` int(10) NOT NULL,
  `req_version_id` int(10) NOT NULL,
  `testcase_id` int(10) NOT NULL,
  `tcversion_id` int(10) NOT NULL,
  `link_status` int(11) NOT NULL DEFAULT 1,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `review_requester_id` int(10) unsigned DEFAULT NULL,
  `review_request_ts` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `req_coverage_full_link` (`req_id`,`req_version_id`,`testcase_id`,`tcversion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='relation test case version ** requirement version';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `req_monitor`
--

DROP TABLE IF EXISTS `req_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `req_monitor` (
  `req_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `testproject_id` int(11) NOT NULL,
  PRIMARY KEY (`req_id`,`user_id`,`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `req_relations`
--

DROP TABLE IF EXISTS `req_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `req_relations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_id` int(10) unsigned NOT NULL,
  `destination_id` int(10) unsigned NOT NULL,
  `relation_type` smallint(5) unsigned NOT NULL DEFAULT 1,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `req_revisions`
--

DROP TABLE IF EXISTS `req_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `req_revisions` (
  `parent_id` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `revision` smallint(5) unsigned NOT NULL DEFAULT 1,
  `req_doc_id` varchar(64) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `scope` text DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'V',
  `type` char(1) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `expected_coverage` int(10) NOT NULL DEFAULT 1,
  `log_message` text DEFAULT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifier_id` int(10) unsigned DEFAULT NULL,
  `modification_ts` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `req_revisions_uidx1` (`parent_id`,`revision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `req_specs`
--

DROP TABLE IF EXISTS `req_specs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `req_specs` (
  `id` int(10) unsigned NOT NULL,
  `testproject_id` int(10) unsigned NOT NULL,
  `doc_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `req_spec_uk1` (`doc_id`,`testproject_id`),
  KEY `testproject_id` (`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dev. Documents (e.g. System Requirements Specification)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `req_specs_revisions`
--

DROP TABLE IF EXISTS `req_specs_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `req_specs_revisions` (
  `parent_id` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `revision` smallint(5) unsigned NOT NULL DEFAULT 1,
  `doc_id` varchar(64) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `scope` text DEFAULT NULL,
  `total_req` int(10) NOT NULL DEFAULT 0,
  `status` int(10) unsigned DEFAULT 1,
  `type` char(1) DEFAULT NULL,
  `log_message` text DEFAULT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifier_id` int(10) unsigned DEFAULT NULL,
  `modification_ts` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `req_specs_revisions_uidx1` (`parent_id`,`revision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `req_versions`
--

DROP TABLE IF EXISTS `req_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `req_versions` (
  `id` int(10) unsigned NOT NULL,
  `version` smallint(5) unsigned NOT NULL DEFAULT 1,
  `revision` smallint(5) unsigned NOT NULL DEFAULT 1,
  `scope` text DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'V',
  `type` char(1) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `expected_coverage` int(10) NOT NULL DEFAULT 1,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifier_id` int(10) unsigned DEFAULT NULL,
  `modification_ts` datetime NOT NULL DEFAULT current_timestamp(),
  `log_message` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reqmgrsystems`
--

DROP TABLE IF EXISTS `reqmgrsystems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reqmgrsystems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` int(10) DEFAULT 0,
  `cfg` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reqmgrsystems_uidx1` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `requirements`
--

DROP TABLE IF EXISTS `requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requirements` (
  `id` int(10) unsigned NOT NULL,
  `srs_id` int(10) unsigned NOT NULL,
  `req_doc_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requirements_req_doc_id` (`srs_id`,`req_doc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rights`
--

DROP TABLE IF EXISTS `rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rights` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rights_descr` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `risk_assignments`
--

DROP TABLE IF EXISTS `risk_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_assignments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `node_id` int(10) unsigned NOT NULL DEFAULT 0,
  `risk` char(1) NOT NULL DEFAULT '2',
  `importance` char(1) NOT NULL DEFAULT 'M',
  PRIMARY KEY (`id`),
  UNIQUE KEY `risk_assignments_tplan_node_id` (`testplan_id`,`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_rights`
--

DROP TABLE IF EXISTS `role_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_rights` (
  `role_id` int(10) NOT NULL DEFAULT 0,
  `right_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`role_id`,`right_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL DEFAULT '',
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_rights_roles_descr` (`description`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tcsteps`
--

DROP TABLE IF EXISTS `tcsteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tcsteps` (
  `id` int(10) unsigned NOT NULL,
  `step_number` int(11) NOT NULL DEFAULT 1,
  `actions` text DEFAULT NULL,
  `expected_results` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `execution_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 -> manual, 2 -> automated',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tcversions`
--

DROP TABLE IF EXISTS `tcversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tcversions` (
  `id` int(10) unsigned NOT NULL,
  `tc_external_id` int(10) unsigned DEFAULT NULL,
  `version` smallint(5) unsigned NOT NULL DEFAULT 1,
  `layout` smallint(5) unsigned NOT NULL DEFAULT 1,
  `status` smallint(5) unsigned NOT NULL DEFAULT 1,
  `summary` text DEFAULT NULL,
  `preconditions` text DEFAULT NULL,
  `importance` smallint(5) unsigned NOT NULL DEFAULT 2,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `updater_id` int(10) unsigned DEFAULT NULL,
  `modification_ts` datetime NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `execution_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 -> manual, 2 -> automated',
  `estimated_exec_duration` decimal(6,2) DEFAULT NULL COMMENT 'NULL will be considered as NO DATA Provided by user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `tcversions_without_keywords`
--

DROP TABLE IF EXISTS `tcversions_without_keywords`;
/*!50001 DROP VIEW IF EXISTS `tcversions_without_keywords`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `tcversions_without_keywords` (
  `testcase_id` tinyint NOT NULL,
  `id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `tcversions_without_platforms`
--

DROP TABLE IF EXISTS `tcversions_without_platforms`;
/*!50001 DROP VIEW IF EXISTS `tcversions_without_platforms`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `tcversions_without_platforms` (
  `testcase_id` tinyint NOT NULL,
  `id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `temp_tc_update`
--

DROP TABLE IF EXISTS `temp_tc_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_tc_update` (
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `script` varchar(256) DEFAULT NULL,
  `tc_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `test_execution_hierarchy_summary_path_based`
--

DROP TABLE IF EXISTS `test_execution_hierarchy_summary_path_based`;
/*!50001 DROP VIEW IF EXISTS `test_execution_hierarchy_summary_path_based`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `test_execution_hierarchy_summary_path_based` (
  `execution_id` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `execution_status_name` tinyint NOT NULL,
  `execution_notes` tinyint NOT NULL,
  `execution_date` tinyint NOT NULL,
  `tc_id` tinyint NOT NULL,
  `tc_name` tinyint NOT NULL,
  `tc_version` tinyint NOT NULL,
  `tc_summary` tinyint NOT NULL,
  `suite_id` tinyint NOT NULL,
  `suite_name` tinyint NOT NULL,
  `suite_path` tinyint NOT NULL,
  `level1_id` tinyint NOT NULL,
  `level1_name` tinyint NOT NULL,
  `level2_id` tinyint NOT NULL,
  `level2_name` tinyint NOT NULL,
  `level3_id` tinyint NOT NULL,
  `level3_name` tinyint NOT NULL,
  `level4_id` tinyint NOT NULL,
  `level4_name` tinyint NOT NULL,
  `level5_id` tinyint NOT NULL,
  `level5_name` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `testplan_name` tinyint NOT NULL,
  `project_id` tinyint NOT NULL,
  `project_name` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `build_name` tinyint NOT NULL,
  `platform_id` tinyint NOT NULL,
  `platform_name` tinyint NOT NULL,
  `tester_id` tinyint NOT NULL,
  `tester_login` tinyint NOT NULL,
  `tester_name` tinyint NOT NULL,
  `time_to_execution` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `test_path_metrics_view`
--

DROP TABLE IF EXISTS `test_path_metrics_view`;
/*!50001 DROP VIEW IF EXISTS `test_path_metrics_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `test_path_metrics_view` (
  `test_path` tinyint NOT NULL,
  `testcase_count` tinyint NOT NULL,
  `passed_count` tinyint NOT NULL,
  `failed_count` tinyint NOT NULL,
  `blocked_count` tinyint NOT NULL,
  `not_run_count` tinyint NOT NULL,
  `pass_rate` tinyint NOT NULL,
  `fail_rate` tinyint NOT NULL,
  `block_rate` tinyint NOT NULL,
  `pending_rate` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `testcase_keywords`
--

DROP TABLE IF EXISTS `testcase_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testcase_keywords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testcase_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_id` int(10) unsigned NOT NULL DEFAULT 0,
  `keyword_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx01_testcase_keywords` (`testcase_id`,`tcversion_id`,`keyword_id`),
  KEY `idx02_testcase_keywords` (`tcversion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testcase_platforms`
--

DROP TABLE IF EXISTS `testcase_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testcase_platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testcase_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_id` int(10) unsigned NOT NULL DEFAULT 0,
  `platform_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx01_testcase_platform` (`testcase_id`,`tcversion_id`,`platform_id`),
  KEY `idx02_testcase_platform` (`tcversion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testcase_relations`
--

DROP TABLE IF EXISTS `testcase_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testcase_relations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_id` int(10) unsigned NOT NULL,
  `destination_id` int(10) unsigned NOT NULL,
  `link_status` tinyint(1) NOT NULL DEFAULT 1,
  `relation_type` smallint(5) unsigned NOT NULL DEFAULT 1,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testcase_script_links`
--

DROP TABLE IF EXISTS `testcase_script_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testcase_script_links` (
  `tcversion_id` int(10) unsigned NOT NULL DEFAULT 0,
  `project_key` varchar(64) NOT NULL,
  `repository_name` varchar(64) NOT NULL,
  `code_path` varchar(255) NOT NULL,
  `branch_name` varchar(64) DEFAULT NULL,
  `commit_id` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`tcversion_id`,`project_key`,`repository_name`,`code_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testplan_platforms`
--

DROP TABLE IF EXISTS `testplan_platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testplan_platforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testplan_id` int(10) unsigned NOT NULL,
  `platform_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_testplan_platforms` (`testplan_id`,`platform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Connects a testplan with platforms';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testplan_tcversions`
--

DROP TABLE IF EXISTS `testplan_tcversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testplan_tcversions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testplan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tcversion_id` int(10) unsigned NOT NULL DEFAULT 0,
  `node_order` int(10) unsigned NOT NULL DEFAULT 1,
  `urgency` smallint(5) NOT NULL DEFAULT 2,
  `platform_id` int(10) unsigned NOT NULL DEFAULT 0,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `testplan_tcversions_tplan_tcversion` (`testplan_id`,`tcversion_id`,`platform_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71865 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testplans`
--

DROP TABLE IF EXISTS `testplans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testplans` (
  `id` int(10) unsigned NOT NULL,
  `testproject_id` int(10) unsigned NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `api_key` varchar(64) NOT NULL DEFAULT '829a2ded3ed0829a2dedd8ab81dfa2c77e8235bc3ed0d8ab81dfa2c77e8235bc',
  PRIMARY KEY (`id`),
  UNIQUE KEY `testplans_api_key` (`api_key`),
  KEY `testplans_testproject_id_active` (`testproject_id`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testproject_codetracker`
--

DROP TABLE IF EXISTS `testproject_codetracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testproject_codetracker` (
  `testproject_id` int(10) unsigned NOT NULL,
  `codetracker_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testproject_issuetracker`
--

DROP TABLE IF EXISTS `testproject_issuetracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testproject_issuetracker` (
  `testproject_id` int(10) unsigned NOT NULL,
  `issuetracker_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testproject_reqmgrsystem`
--

DROP TABLE IF EXISTS `testproject_reqmgrsystem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testproject_reqmgrsystem` (
  `testproject_id` int(10) unsigned NOT NULL,
  `reqmgrsystem_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testprojects`
--

DROP TABLE IF EXISTS `testprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testprojects` (
  `id` int(10) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `color` varchar(12) NOT NULL DEFAULT '#9BD',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `option_reqs` tinyint(1) NOT NULL DEFAULT 0,
  `option_priority` tinyint(1) NOT NULL DEFAULT 0,
  `option_automation` tinyint(1) NOT NULL DEFAULT 0,
  `options` text DEFAULT NULL,
  `prefix` varchar(16) NOT NULL,
  `tc_counter` int(10) unsigned NOT NULL DEFAULT 0,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `issue_tracker_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `code_tracker_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `reqmgr_integration_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `api_key` varchar(64) NOT NULL DEFAULT '0d8ab81dfa2c77e8235bc829a2ded3edfa2c78235bc829a27eded3ed0d8ab81d',
  PRIMARY KEY (`id`),
  UNIQUE KEY `testprojects_prefix` (`prefix`),
  UNIQUE KEY `testprojects_api_key` (`api_key`),
  KEY `testprojects_id_active` (`id`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testsuites`
--

DROP TABLE IF EXISTS `testsuites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testsuites` (
  `id` int(10) unsigned NOT NULL,
  `details` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `text_templates`
--

DROP TABLE IF EXISTS `text_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `text_templates` (
  `id` int(10) unsigned NOT NULL,
  `type` smallint(5) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `template_data` text DEFAULT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `idx_text_templates` (`type`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Global Project Templates';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_point` varchar(45) NOT NULL DEFAULT '',
  `start_time` int(10) unsigned NOT NULL DEFAULT 0,
  `end_time` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `session_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=241475 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `tsuites_tree_depth_2`
--

DROP TABLE IF EXISTS `tsuites_tree_depth_2`;
/*!50001 DROP VIEW IF EXISTS `tsuites_tree_depth_2`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `tsuites_tree_depth_2` (
  `prefix` tinyint NOT NULL,
  `testproject_name` tinyint NOT NULL,
  `level1_name` tinyint NOT NULL,
  `level2_name` tinyint NOT NULL,
  `testproject_id` tinyint NOT NULL,
  `level1_id` tinyint NOT NULL,
  `level2_id` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `update_test_results`
--

DROP TABLE IF EXISTS `update_test_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_test_results` (
  `name` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tester` varchar(50) DEFAULT NULL,
  `execution_ts` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_test_results_new`
--

DROP TABLE IF EXISTS `update_test_results_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_test_results_new` (
  `name` varchar(50) DEFAULT NULL,
  `test_data` varchar(64) DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  `tester` varchar(50) DEFAULT NULL,
  `execution_ts` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tc_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_test_results_new2`
--

DROP TABLE IF EXISTS `update_test_results_new2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_test_results_new2` (
  `name` varchar(50) DEFAULT NULL,
  `test_data` varchar(64) DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  `tester` varchar(50) DEFAULT NULL,
  `execution_ts` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tc_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_test_results_new3`
--

DROP TABLE IF EXISTS `update_test_results_new3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_test_results_new3` (
  `name` varchar(50) DEFAULT NULL,
  `test_data` varchar(50) DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  `tester` varchar(50) DEFAULT NULL,
  `execution_ts` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tc_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_test_results_new4`
--

DROP TABLE IF EXISTS `update_test_results_new4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_test_results_new4` (
  `name` varchar(50) DEFAULT NULL,
  `test_data` varchar(64) DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  `tester` varchar(50) DEFAULT NULL,
  `execution_ts` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tc_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update_test_results_new5`
--

DROP TABLE IF EXISTS `update_test_results_new5`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update_test_results_new5` (
  `name` varchar(50) DEFAULT NULL,
  `test_data` varchar(50) DEFAULT NULL,
  `notes` varchar(50) DEFAULT NULL,
  `tester` varchar(50) DEFAULT NULL,
  `execution_ts` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tc_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_assignments`
--

DROP TABLE IF EXISTS `user_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_assignments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned NOT NULL DEFAULT 1,
  `feature_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned DEFAULT 0,
  `build_id` int(10) unsigned DEFAULT 0,
  `deadline_ts` datetime DEFAULT NULL,
  `assigner_id` int(10) unsigned DEFAULT 0,
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_assignments_feature_id` (`feature_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65061 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_group` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group_assign`
--

DROP TABLE IF EXISTS `user_group_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_assign` (
  `usergroup_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `idx_user_group_assign` (`usergroup_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_testplan_roles`
--

DROP TABLE IF EXISTS `user_testplan_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_testplan_roles` (
  `user_id` int(10) NOT NULL DEFAULT 0,
  `testplan_id` int(10) NOT NULL DEFAULT 0,
  `role_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`testplan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_testproject_roles`
--

DROP TABLE IF EXISTS `user_testproject_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_testproject_roles` (
  `user_id` int(10) NOT NULL DEFAULT 0,
  `testproject_id` int(10) NOT NULL DEFAULT 0,
  `role_id` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`testproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `role_id` int(10) unsigned NOT NULL DEFAULT 0,
  `email` varchar(100) NOT NULL DEFAULT '',
  `first` varchar(50) NOT NULL DEFAULT '',
  `last` varchar(50) NOT NULL DEFAULT '',
  `locale` varchar(10) NOT NULL DEFAULT 'en_GB',
  `default_testproject_id` int(10) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `script_key` varchar(32) DEFAULT NULL,
  `cookie_string` varchar(64) NOT NULL DEFAULT '',
  `auth_method` varchar(10) DEFAULT '',
  `creation_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiration_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_login` (`login`),
  UNIQUE KEY `users_cookie_string` (`cookie_string`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COMMENT='User information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `vw_execution_bugs`
--

DROP TABLE IF EXISTS `vw_execution_bugs`;
/*!50001 DROP VIEW IF EXISTS `vw_execution_bugs`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_execution_bugs` (
  `execution_id` tinyint NOT NULL,
  `bug_id` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `execution_ts` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `testcase_id` tinyint NOT NULL,
  `testcase_name` tinyint NOT NULL,
  `build_name` tinyint NOT NULL,
  `testplan_name` tinyint NOT NULL,
  `tester_login` tinyint NOT NULL,
  `execution_notes` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_latest_executions`
--

DROP TABLE IF EXISTS `vw_latest_executions`;
/*!50001 DROP VIEW IF EXISTS `vw_latest_executions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_latest_executions` (
  `tcversion_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `execution_id` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `execution_ts` tinyint NOT NULL,
  `tester_id` tinyint NOT NULL,
  `Test_Data` tinyint NOT NULL,
  `Priority` tinyint NOT NULL,
  `Execution_Status_CF` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_new_testcase_execution_details`
--

DROP TABLE IF EXISTS `vw_new_testcase_execution_details`;
/*!50001 DROP VIEW IF EXISTS `vw_new_testcase_execution_details`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_new_testcase_execution_details` (
  `execution_id` tinyint NOT NULL,
  `tcversion_id` tinyint NOT NULL,
  `execution_notes` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `testcase_id` tinyint NOT NULL,
  `testcase_name` tinyint NOT NULL,
  `testcase_parent_id` tinyint NOT NULL,
  `testcase_parent_name` tinyint NOT NULL,
  `testcase_summary` tinyint NOT NULL,
  `testcase_preconditions` tinyint NOT NULL,
  `testcase_version` tinyint NOT NULL,
  `scenario_id` tinyint NOT NULL,
  `primary_module` tinyint NOT NULL,
  `sub_scenario` tinyint NOT NULL,
  `test_data` tinyint NOT NULL,
  `expected_results` tinyint NOT NULL,
  `priority` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_test_execution_summary`
--

DROP TABLE IF EXISTS `vw_test_execution_summary`;
/*!50001 DROP VIEW IF EXISTS `vw_test_execution_summary`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_test_execution_summary` (
  `execution_id` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `execution_status_name` tinyint NOT NULL,
  `execution_notes` tinyint NOT NULL,
  `execution_date` tinyint NOT NULL,
  `tc_id` tinyint NOT NULL,
  `tc_name` tinyint NOT NULL,
  `tc_version` tinyint NOT NULL,
  `tc_summary` tinyint NOT NULL,
  `suite_id` tinyint NOT NULL,
  `suite_name` tinyint NOT NULL,
  `suite_path` tinyint NOT NULL,
  `level1_id` tinyint NOT NULL,
  `level1_name` tinyint NOT NULL,
  `level2_id` tinyint NOT NULL,
  `level2_name` tinyint NOT NULL,
  `level3_id` tinyint NOT NULL,
  `level3_name` tinyint NOT NULL,
  `level4_id` tinyint NOT NULL,
  `level4_name` tinyint NOT NULL,
  `level5_id` tinyint NOT NULL,
  `level5_name` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `testplan_name` tinyint NOT NULL,
  `project_id` tinyint NOT NULL,
  `project_name` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `build_name` tinyint NOT NULL,
  `platform_id` tinyint NOT NULL,
  `platform_name` tinyint NOT NULL,
  `tester_id` tinyint NOT NULL,
  `tester_login` tinyint NOT NULL,
  `tester_name` tinyint NOT NULL,
  `time_to_execution` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_testcase_customfields`
--

DROP TABLE IF EXISTS `vw_testcase_customfields`;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_customfields`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_testcase_customfields` (
  `testcase_id` tinyint NOT NULL,
  `testcase_name` tinyint NOT NULL,
  `tcversion_id` tinyint NOT NULL,
  `version` tinyint NOT NULL,
  `summary` tinyint NOT NULL,
  `preconditions` tinyint NOT NULL,
  `testsuite_id` tinyint NOT NULL,
  `testsuite_name` tinyint NOT NULL,
  `execution_id` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `execution_ts` tinyint NOT NULL,
  `tester_id` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `Scenario_ID` tinyint NOT NULL,
  `Primary_Module` tinyint NOT NULL,
  `Sub_Scenario` tinyint NOT NULL,
  `Test_Case_Description` tinyint NOT NULL,
  `Test_Type` tinyint NOT NULL,
  `Test_Script` tinyint NOT NULL,
  `Test_Execution_Path` tinyint NOT NULL,
  `Expected_Results` tinyint NOT NULL,
  `E_R_Process` tinyint NOT NULL,
  `Executed_By` tinyint NOT NULL,
  `Test_Data` tinyint NOT NULL,
  `Priority` tinyint NOT NULL,
  `Execution_Status_CF` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_testcase_customfields_new`
--

DROP TABLE IF EXISTS `vw_testcase_customfields_new`;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_customfields_new`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_testcase_customfields_new` (
  `testcase_id` tinyint NOT NULL,
  `testcase_name` tinyint NOT NULL,
  `tcversion_id` tinyint NOT NULL,
  `version` tinyint NOT NULL,
  `tc_external_id` tinyint NOT NULL,
  `summary` tinyint NOT NULL,
  `preconditions` tinyint NOT NULL,
  `importance` tinyint NOT NULL,
  `execution_type` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `testsuite_id` tinyint NOT NULL,
  `testsuite_name` tinyint NOT NULL,
  `execution_id` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `execution_ts` tinyint NOT NULL,
  `tester_id` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `platform_id` tinyint NOT NULL,
  `execution_duration` tinyint NOT NULL,
  `Scenario_ID` tinyint NOT NULL,
  `Primary_Module` tinyint NOT NULL,
  `Sub_Scenario` tinyint NOT NULL,
  `Test_Case_Description` tinyint NOT NULL,
  `Test_Type` tinyint NOT NULL,
  `Test_Script` tinyint NOT NULL,
  `Test_Execution_Path` tinyint NOT NULL,
  `Expected_Results` tinyint NOT NULL,
  `E_R_Process` tinyint NOT NULL,
  `Executed_By` tinyint NOT NULL,
  `Test_Data` tinyint NOT NULL,
  `Priority` tinyint NOT NULL,
  `Execution_Status_CF` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_testcase_execution_details`
--

DROP TABLE IF EXISTS `vw_testcase_execution_details`;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_execution_details`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_testcase_execution_details` (
  `execution_id` tinyint NOT NULL,
  `tcversion_id` tinyint NOT NULL,
  `execution_notes` tinyint NOT NULL,
  `execution_status` tinyint NOT NULL,
  `build_id` tinyint NOT NULL,
  `testplan_id` tinyint NOT NULL,
  `testcase_id` tinyint NOT NULL,
  `testcase_name` tinyint NOT NULL,
  `parent_id` tinyint NOT NULL,
  `parent_name` tinyint NOT NULL,
  `testcase_summary` tinyint NOT NULL,
  `testcase_preconditions` tinyint NOT NULL,
  `testcase_version` tinyint NOT NULL,
  `execution_ts` tinyint NOT NULL,
  `scenario_id` tinyint NOT NULL,
  `primary_module` tinyint NOT NULL,
  `sub_scenario` tinyint NOT NULL,
  `test_data` tinyint NOT NULL,
  `expected_results` tinyint NOT NULL,
  `priority` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_testcase_summary`
--

DROP TABLE IF EXISTS `vw_testcase_summary`;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_summary`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vw_testcase_summary` (
  `testcase_id` tinyint NOT NULL,
  `testcase_name` tinyint NOT NULL,
  `tcversion_id` tinyint NOT NULL,
  `version` tinyint NOT NULL,
  `summary` tinyint NOT NULL,
  `preconditions` tinyint NOT NULL,
  `creation_ts` tinyint NOT NULL,
  `modification_ts` tinyint NOT NULL,
  `importance` tinyint NOT NULL,
  `testsuite_id` tinyint NOT NULL,
  `testsuite_name` tinyint NOT NULL,
  `testproject_id` tinyint NOT NULL,
  `testproject_name` tinyint NOT NULL,
  `Scenario_ID` tinyint NOT NULL,
  `Primary_Module` tinyint NOT NULL,
  `Sub_Scenario` tinyint NOT NULL,
  `Test_Case_Description` tinyint NOT NULL,
  `Test_Type` tinyint NOT NULL,
  `Test_Script` tinyint NOT NULL,
  `Test_Execution_Path` tinyint NOT NULL,
  `Expected_Results` tinyint NOT NULL,
  `E_R_Process` tinyint NOT NULL,
  `Executed_By` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `exec_by_date_time`
--

/*!50001 DROP TABLE IF EXISTS `exec_by_date_time`*/;
/*!50001 DROP VIEW IF EXISTS `exec_by_date_time`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `exec_by_date_time` AS (select `nhtpl`.`name` AS `testplan_name`,date_format(`e`.`execution_ts`,'%Y-%m-%d') AS `yyyy_mm_dd`,date_format(`e`.`execution_ts`,'%Y-%m') AS `yyyy_mm`,date_format(`e`.`execution_ts`,'%H') AS `hh`,date_format(`e`.`execution_ts`,'%k') AS `hour`,`e`.`id` AS `id`,`e`.`build_id` AS `build_id`,`e`.`tester_id` AS `tester_id`,`e`.`execution_ts` AS `execution_ts`,`e`.`status` AS `status`,`e`.`testplan_id` AS `testplan_id`,`e`.`tcversion_id` AS `tcversion_id`,`e`.`tcversion_number` AS `tcversion_number`,`e`.`platform_id` AS `platform_id`,`e`.`execution_type` AS `execution_type`,`e`.`execution_duration` AS `execution_duration`,`e`.`notes` AS `notes` from ((`executions` `e` join `testplans` `tpl` on(`tpl`.`id` = `e`.`testplan_id`)) join `nodes_hierarchy` `nhtpl` on(`nhtpl`.`id` = `tpl`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_exec_by_context`
--

/*!50001 DROP TABLE IF EXISTS `latest_exec_by_context`*/;
/*!50001 DROP VIEW IF EXISTS `latest_exec_by_context`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_exec_by_context` AS select `executions`.`tcversion_id` AS `tcversion_id`,`executions`.`testplan_id` AS `testplan_id`,`executions`.`build_id` AS `build_id`,`executions`.`platform_id` AS `platform_id`,max(`executions`.`id`) AS `id` from `executions` group by `executions`.`tcversion_id`,`executions`.`testplan_id`,`executions`.`build_id`,`executions`.`platform_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_exec_by_testplan`
--

/*!50001 DROP TABLE IF EXISTS `latest_exec_by_testplan`*/;
/*!50001 DROP VIEW IF EXISTS `latest_exec_by_testplan`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_exec_by_testplan` AS select `executions`.`tcversion_id` AS `tcversion_id`,`executions`.`testplan_id` AS `testplan_id`,max(`executions`.`id`) AS `id` from `executions` group by `executions`.`tcversion_id`,`executions`.`testplan_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_exec_by_testplan_plat`
--

/*!50001 DROP TABLE IF EXISTS `latest_exec_by_testplan_plat`*/;
/*!50001 DROP VIEW IF EXISTS `latest_exec_by_testplan_plat`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_exec_by_testplan_plat` AS select `executions`.`tcversion_id` AS `tcversion_id`,`executions`.`testplan_id` AS `testplan_id`,`executions`.`platform_id` AS `platform_id`,max(`executions`.`id`) AS `id` from `executions` group by `executions`.`tcversion_id`,`executions`.`testplan_id`,`executions`.`platform_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_req_version`
--

/*!50001 DROP TABLE IF EXISTS `latest_req_version`*/;
/*!50001 DROP VIEW IF EXISTS `latest_req_version`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_req_version` AS select `rq`.`id` AS `req_id`,max(`rqv`.`version`) AS `version` from ((`nodes_hierarchy` `nhrqv` join `requirements` `rq` on(`rq`.`id` = `nhrqv`.`parent_id`)) join `req_versions` `rqv` on(`rqv`.`id` = `nhrqv`.`id`)) group by `rq`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_req_version_id`
--

/*!50001 DROP TABLE IF EXISTS `latest_req_version_id`*/;
/*!50001 DROP VIEW IF EXISTS `latest_req_version_id`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_req_version_id` AS select `lrqvn`.`req_id` AS `req_id`,`lrqvn`.`version` AS `version`,`reqv`.`id` AS `req_version_id` from ((`latest_req_version` `lrqvn` join `nodes_hierarchy` `nhrqv` on(`nhrqv`.`parent_id` = `lrqvn`.`req_id`)) join `req_versions` `reqv` on(`reqv`.`id` = `nhrqv`.`id` and `reqv`.`version` = `lrqvn`.`version`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_rspec_revision`
--

/*!50001 DROP TABLE IF EXISTS `latest_rspec_revision`*/;
/*!50001 DROP VIEW IF EXISTS `latest_rspec_revision`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_rspec_revision` AS select `rsr`.`parent_id` AS `req_spec_id`,`rs`.`testproject_id` AS `testproject_id`,max(`rsr`.`revision`) AS `revision` from (`req_specs_revisions` `rsr` join `req_specs` `rs` on(`rs`.`id` = `rsr`.`parent_id`)) group by `rsr`.`parent_id`,`rs`.`testproject_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_tcase_version_id`
--

/*!50001 DROP TABLE IF EXISTS `latest_tcase_version_id`*/;
/*!50001 DROP VIEW IF EXISTS `latest_tcase_version_id`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_tcase_version_id` AS select `ltcvn`.`testcase_id` AS `testcase_id`,`ltcvn`.`version` AS `version`,`tcv`.`id` AS `tcversion_id` from ((`latest_tcase_version_number` `ltcvn` join `nodes_hierarchy` `nhtcv` on(`nhtcv`.`parent_id` = `ltcvn`.`testcase_id`)) join `tcversions` `tcv` on(`tcv`.`id` = `nhtcv`.`id` and `tcv`.`version` = `ltcvn`.`version`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `latest_tcase_version_number`
--

/*!50001 DROP TABLE IF EXISTS `latest_tcase_version_number`*/;
/*!50001 DROP VIEW IF EXISTS `latest_tcase_version_number`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `latest_tcase_version_number` AS select `nh_tc`.`id` AS `testcase_id`,max(`tcv`.`version`) AS `version` from ((`nodes_hierarchy` `nh_tc` join `nodes_hierarchy` `nh_tcv` on(`nh_tcv`.`parent_id` = `nh_tc`.`id`)) join `tcversions` `tcv` on(`nh_tcv`.`id` = `tcv`.`id`)) group by `nh_tc`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `tcversions_without_keywords`
--

/*!50001 DROP TABLE IF EXISTS `tcversions_without_keywords`*/;
/*!50001 DROP VIEW IF EXISTS `tcversions_without_keywords`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `tcversions_without_keywords` AS select `nhtcv`.`parent_id` AS `testcase_id`,`nhtcv`.`id` AS `id` from `nodes_hierarchy` `nhtcv` where `nhtcv`.`node_type_id` = 4 and !exists(select 1 from `testcase_keywords` `tck` where `tck`.`tcversion_id` = `nhtcv`.`id` limit 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `tcversions_without_platforms`
--

/*!50001 DROP TABLE IF EXISTS `tcversions_without_platforms`*/;
/*!50001 DROP VIEW IF EXISTS `tcversions_without_platforms`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `tcversions_without_platforms` AS select `nhtcv`.`parent_id` AS `testcase_id`,`nhtcv`.`id` AS `id` from `nodes_hierarchy` `nhtcv` where `nhtcv`.`node_type_id` = 4 and !exists(select 1 from `testcase_platforms` `tcpl` where `tcpl`.`tcversion_id` = `nhtcv`.`id` limit 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `test_execution_hierarchy_summary_path_based`
--

/*!50001 DROP TABLE IF EXISTS `test_execution_hierarchy_summary_path_based`*/;
/*!50001 DROP VIEW IF EXISTS `test_execution_hierarchy_summary_path_based`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `test_execution_hierarchy_summary_path_based` AS with recursive node_hierarchy_path as (select `nodes_hierarchy`.`id` AS `id`,`nodes_hierarchy`.`name` AS `name`,`nodes_hierarchy`.`parent_id` AS `parent_id`,`nodes_hierarchy`.`name` AS `path` from `nodes_hierarchy` where `nodes_hierarchy`.`parent_id` is null or `nodes_hierarchy`.`parent_id` = 0 union all select `nh`.`id` AS `id`,`nh`.`name` AS `name`,`nh`.`parent_id` AS `parent_id`,concat(`nhp`.`path`,' > ',`nh`.`name`) AS `path` from (`nodes_hierarchy` `nh` join `node_hierarchy_path` `nhp` on(`nh`.`parent_id` = `nhp`.`id`)))select `e`.`id` AS `execution_id`,`e`.`status` AS `execution_status`,case `e`.`status` when 'p' then 'passed' when 'f' then 'failed' when 'b' then 'blocked' when 'n' then 'not run' when 'i' then 'incomplete' else `e`.`status` end AS `execution_status_name`,`e`.`notes` AS `execution_notes`,`e`.`execution_ts` AS `execution_date`,`nh_tc`.`id` AS `tc_id`,`nh_tc`.`name` AS `tc_name`,`tcv`.`version` AS `tc_version`,replace(replace(`tcv`.`summary`,'<p>',''),'</p>','') AS `tc_summary`,`suite`.`id` AS `suite_id`,`suite`.`name` AS `suite_name`,case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else 'No Suite' end AS `suite_path`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',1) limit 1) AS `level1_id`,substring_index(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else 'No Suite' end,' > ',1) AS `level1_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when locate(' > ',case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) > 0 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',2),' > ',-1) else NULL end) limit 1) AS `level2_id`,case when locate(' > ',case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) > 0 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',2),' > ',-1) else NULL end AS `level2_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 4 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',3),' > ',-1) else NULL end) limit 1) AS `level3_id`,case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 4 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',3),' > ',-1) else NULL end AS `level3_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 6 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',4),' > ',-1) else NULL end) limit 1) AS `level4_id`,case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 6 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',4),' > ',-1) else NULL end AS `level4_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 8 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',5),' > ',-1) else `suite`.`name` end) limit 1) AS `level5_id`,case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 8 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',5),' > ',-1) else `suite`.`name` end AS `level5_name`,`e`.`testplan_id` AS `testplan_id`,replace(replace(`tp`.`notes`,'<p>',''),'</p>','') AS `testplan_name`,`tp`.`testproject_id` AS `project_id`,replace(replace(`tproj`.`notes`,'<p>',''),'</p>','') AS `project_name`,`e`.`build_id` AS `build_id`,`b`.`name` AS `build_name`,`e`.`platform_id` AS `platform_id`,`p`.`name` AS `platform_name`,`e`.`tester_id` AS `tester_id`,`u`.`login` AS `tester_login`,concat(`u`.`first`,' ',`u`.`last`) AS `tester_name`,timestampdiff(SECOND,`tcv`.`creation_ts`,`e`.`execution_ts`) AS `time_to_execution` from (((((((((`executions` `e` join `tcversions` `tcv` on(`e`.`tcversion_id` = `tcv`.`id`)) join `nodes_hierarchy` `nh_tcv` on(`tcv`.`id` = `nh_tcv`.`id`)) join `nodes_hierarchy` `nh_tc` on(`nh_tcv`.`parent_id` = `nh_tc`.`id`)) join `testplans` `tp` on(`e`.`testplan_id` = `tp`.`id`)) join `builds` `b` on(`e`.`build_id` = `b`.`id`)) left join `platforms` `p` on(`e`.`platform_id` = `p`.`id`)) left join `users` `u` on(`e`.`tester_id` = `u`.`id`)) left join `nodes_hierarchy` `suite` on(`nh_tc`.`parent_id` = `suite`.`id`)) join `testprojects` `tproj` on(`tp`.`testproject_id` = `tproj`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `test_path_metrics_view`
--

/*!50001 DROP TABLE IF EXISTS `test_path_metrics_view`*/;
/*!50001 DROP VIEW IF EXISTS `test_path_metrics_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `test_path_metrics_view` AS with all_testcase_versions as (select `nh_tc`.`id` AS `tc_id`,`nh_tc`.`parent_id` AS `suite_id`,`nh_tcv`.`id` AS `tcversion_id`,`tcv`.`tc_external_id` AS `tc_external_id`,`tcv`.`version` AS `version` from ((`testlink_db`.`nodes_hierarchy` `nh_tc` join `testlink_db`.`nodes_hierarchy` `nh_tcv` on(`nh_tcv`.`parent_id` = `nh_tc`.`id`)) join `testlink_db`.`tcversions` `tcv` on(`tcv`.`id` = `nh_tcv`.`id`)) where `nh_tc`.`node_type_id` = 3 and `tcv`.`id` = (select max(`testlink_db`.`tcversions`.`id`) from `testlink_db`.`tcversions` where `testlink_db`.`tcversions`.`tc_external_id` = `tcv`.`tc_external_id`)), latest_executions as (select `atcv`.`tc_id` AS `tc_id`,`atcv`.`suite_id` AS `suite_id`,`atcv`.`tcversion_id` AS `tcversion_id`,`atcv`.`tc_external_id` AS `tc_external_id`,`e`.`id` AS `execution_id`,`e`.`build_id` AS `build_id`,`e`.`testplan_id` AS `testplan_id`,`e`.`tester_id` AS `tester_id`,`e`.`status` AS `status`,`e`.`execution_ts` AS `execution_ts` from (`all_testcase_versions` `atcv` left join (select `e`.`tcversion_id` AS `tcversion_id`,`e`.`id` AS `id`,`e`.`status` AS `status`,`e`.`testplan_id` AS `testplan_id`,`e`.`build_id` AS `build_id`,`e`.`tester_id` AS `tester_id`,`e`.`execution_ts` AS `execution_ts` from (`testlink_db`.`executions` `e` join (select `testlink_db`.`executions`.`tcversion_id` AS `tcversion_id`,`testlink_db`.`executions`.`build_id` AS `build_id`,`testlink_db`.`executions`.`testplan_id` AS `testplan_id`,max(`testlink_db`.`executions`.`execution_ts`) AS `latest_exec_ts` from `testlink_db`.`executions` group by `testlink_db`.`executions`.`tcversion_id`,`testlink_db`.`executions`.`build_id`,`testlink_db`.`executions`.`testplan_id`) `latest` on(`e`.`tcversion_id` = `latest`.`tcversion_id` and `e`.`build_id` = `latest`.`build_id` and `e`.`testplan_id` = `latest`.`testplan_id` and `e`.`execution_ts` = `latest`.`latest_exec_ts`))) `e` on(`atcv`.`tcversion_id` = `e`.`tcversion_id`))), path_metrics as (select coalesce(`nhp`.`full_path`,`parent_nhp`.`full_path`) AS `test_path`,count(distinct `le`.`tc_id`) AS `testcase_count`,sum(case when `le`.`status` = 'p' then 1 else 0 end) AS `passed_count`,sum(case when `le`.`status` = 'f' then 1 else 0 end) AS `failed_count`,sum(case when `le`.`status` = 'b' then 1 else 0 end) AS `blocked_count`,sum(case when (`le`.`status` is null or `le`.`status` not in ('p','f','b')) then 1 else 0 end) AS `not_run_count` from (((`latest_executions` `le` join `testlink_db`.`nodes_hierarchy` `suite` on(`suite`.`id` = `le`.`suite_id`)) left join `testlink_db`.`node_hierarchy_paths_v2` `nhp` on(`nhp`.`node_id` = `suite`.`id`)) left join `testlink_db`.`node_hierarchy_paths_v2` `parent_nhp` on(`parent_nhp`.`node_id` = `suite`.`parent_id` and `nhp`.`node_id` is null)) group by coalesce(`nhp`.`full_path`,`parent_nhp`.`full_path`))select `pm`.`test_path` AS `test_path`,`pm`.`testcase_count` AS `testcase_count`,`pm`.`passed_count` AS `passed_count`,`pm`.`failed_count` AS `failed_count`,`pm`.`blocked_count` AS `blocked_count`,`pm`.`not_run_count` AS `not_run_count`,case when `pm`.`testcase_count` = 0 then 0 else round(`pm`.`passed_count` / `pm`.`testcase_count` * 100,2) end AS `pass_rate`,case when `pm`.`testcase_count` = 0 then 0 else round(`pm`.`failed_count` / `pm`.`testcase_count` * 100,2) end AS `fail_rate`,case when `pm`.`testcase_count` = 0 then 0 else round(`pm`.`blocked_count` / `pm`.`testcase_count` * 100,2) end AS `block_rate`,case when `pm`.`testcase_count` = 0 then 0 else greatest(0,round(100 - round(`pm`.`passed_count` / `pm`.`testcase_count` * 100,2) - round(`pm`.`failed_count` / `pm`.`testcase_count` * 100,2) - round(`pm`.`blocked_count` / `pm`.`testcase_count` * 100,2),2)) end AS `pending_rate` from `path_metrics` `pm` where `pm`.`testcase_count` > 0 order by `pm`.`test_path` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `tsuites_tree_depth_2`
--

/*!50001 DROP TABLE IF EXISTS `tsuites_tree_depth_2`*/;
/*!50001 DROP VIEW IF EXISTS `tsuites_tree_depth_2`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `tsuites_tree_depth_2` AS select `tprj`.`prefix` AS `prefix`,`nhtprj`.`name` AS `testproject_name`,`nhts_l1`.`name` AS `level1_name`,`nhts_l2`.`name` AS `level2_name`,`nhtprj`.`id` AS `testproject_id`,`nhts_l1`.`id` AS `level1_id`,`nhts_l2`.`id` AS `level2_id` from (((`testprojects` `tprj` join `nodes_hierarchy` `nhtprj` on(`tprj`.`id` = `nhtprj`.`id`)) left join `nodes_hierarchy` `nhts_l1` on(`nhts_l1`.`parent_id` = `nhtprj`.`id`)) left join `nodes_hierarchy` `nhts_l2` on(`nhts_l2`.`parent_id` = `nhts_l1`.`id`)) where `nhtprj`.`node_type_id` = 1 and `nhts_l1`.`node_type_id` = 2 and `nhts_l2`.`node_type_id` = 2 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_execution_bugs`
--

/*!50001 DROP TABLE IF EXISTS `vw_execution_bugs`*/;
/*!50001 DROP VIEW IF EXISTS `vw_execution_bugs`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_execution_bugs` AS select `eb`.`execution_id` AS `execution_id`,`eb`.`bug_id` AS `bug_id`,`e`.`status` AS `execution_status`,`e`.`execution_ts` AS `execution_ts`,`e`.`build_id` AS `build_id`,`e`.`testplan_id` AS `testplan_id`,`nh_tc`.`id` AS `testcase_id`,`nh_tc`.`name` AS `testcase_name`,`b`.`name` AS `build_name`,`nh_tpl`.`name` AS `testplan_name`,`u`.`login` AS `tester_login`,`e`.`notes` AS `execution_notes` from ((((((((`execution_bugs` `eb` join `executions` `e` on(`eb`.`execution_id` = `e`.`id`)) join `tcversions` `tcv` on(`e`.`tcversion_id` = `tcv`.`id`)) join `nodes_hierarchy` `nh_tcv` on(`tcv`.`id` = `nh_tcv`.`id`)) join `nodes_hierarchy` `nh_tc` on(`nh_tcv`.`parent_id` = `nh_tc`.`id`)) left join `builds` `b` on(`e`.`build_id` = `b`.`id`)) left join `testplans` `tpl` on(`e`.`testplan_id` = `tpl`.`id`)) left join `nodes_hierarchy` `nh_tpl` on(`tpl`.`id` = `nh_tpl`.`id`)) left join `users` `u` on(`e`.`tester_id` = `u`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_latest_executions`
--

/*!50001 DROP TABLE IF EXISTS `vw_latest_executions`*/;
/*!50001 DROP VIEW IF EXISTS `vw_latest_executions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_latest_executions` AS select `e`.`tcversion_id` AS `tcversion_id`,`e`.`testplan_id` AS `testplan_id`,`e`.`build_id` AS `build_id`,`e`.`id` AS `execution_id`,`e`.`status` AS `execution_status`,`e`.`execution_ts` AS `execution_ts`,`e`.`tester_id` AS `tester_id`,max(case when `cf`.`id` = 10 then `cfev`.`value` end) AS `Test_Data`,max(case when `cf`.`id` = 11 then `cfev`.`value` end) AS `Priority`,max(case when `cf`.`id` = 13 then `cfev`.`value` end) AS `Execution_Status_CF` from ((`executions` `e` left join `custom_fields` `cf` on(`cf`.`enable_on_execution` = 1)) left join `cfield_execution_values` `cfev` on(`cfev`.`field_id` = `cf`.`id` and `cfev`.`execution_id` = `e`.`id` and `cfev`.`tcversion_id` = `e`.`tcversion_id`)) where `e`.`id` = (select max(`executions`.`id`) from `executions` where `executions`.`tcversion_id` = `e`.`tcversion_id` and `executions`.`testplan_id` = `e`.`testplan_id` and `executions`.`build_id` = `e`.`build_id`) group by `e`.`tcversion_id`,`e`.`testplan_id`,`e`.`build_id`,`e`.`id`,`e`.`status`,`e`.`execution_ts`,`e`.`tester_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_new_testcase_execution_details`
--

/*!50001 DROP TABLE IF EXISTS `vw_new_testcase_execution_details`*/;
/*!50001 DROP VIEW IF EXISTS `vw_new_testcase_execution_details`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_new_testcase_execution_details` AS select `e`.`id` AS `execution_id`,`e`.`tcversion_id` AS `tcversion_id`,`e`.`notes` AS `execution_notes`,`e`.`status` AS `execution_status`,`e`.`build_id` AS `build_id`,`e`.`testplan_id` AS `testplan_id`,`nh_tc`.`id` AS `testcase_id`,`nh_tc`.`name` AS `testcase_name`,`nh_tc`.`parent_id` AS `testcase_parent_id`,`nh_parent`.`name` AS `testcase_parent_name`,`tv`.`summary` AS `testcase_summary`,`tv`.`preconditions` AS `testcase_preconditions`,`tv`.`version` AS `testcase_version`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 1),`nh_tc`.`name`,'No Scenario ID Available') AS `scenario_id`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 2),'No Primary Module Available') AS `primary_module`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 3),'No Sub-Scenario Available') AS `sub_scenario`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 10),`e`.`notes`,'No Test Data Available') AS `test_data`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 8),'No Expected Results Available') AS `expected_results`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 11),'P3') AS `priority` from ((((`executions` `e` join `nodes_hierarchy` `nh_tcv` on(`e`.`tcversion_id` = `nh_tcv`.`id`)) join `nodes_hierarchy` `nh_tc` on(`nh_tcv`.`parent_id` = `nh_tc`.`id`)) left join `nodes_hierarchy` `nh_parent` on(`nh_tc`.`parent_id` = `nh_parent`.`id`)) join `tcversions` `tv` on(`nh_tcv`.`id` = `tv`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_test_execution_summary`
--

/*!50001 DROP TABLE IF EXISTS `vw_test_execution_summary`*/;
/*!50001 DROP VIEW IF EXISTS `vw_test_execution_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_test_execution_summary` AS with recursive node_hierarchy_path as (select `nodes_hierarchy`.`id` AS `id`,`nodes_hierarchy`.`name` AS `name`,`nodes_hierarchy`.`parent_id` AS `parent_id`,`nodes_hierarchy`.`name` AS `path` from `nodes_hierarchy` where `nodes_hierarchy`.`parent_id` is null or `nodes_hierarchy`.`parent_id` = 0 union all select `nh`.`id` AS `id`,`nh`.`name` AS `name`,`nh`.`parent_id` AS `parent_id`,concat(`nhp`.`path`,' > ',`nh`.`name`) AS `path` from (`nodes_hierarchy` `nh` join `node_hierarchy_path` `nhp` on(`nh`.`parent_id` = `nhp`.`id`)))select `e`.`id` AS `execution_id`,`e`.`status` AS `execution_status`,case `e`.`status` when 'p' then 'passed' when 'f' then 'failed' when 'b' then 'blocked' when 'n' then 'not run' when 'i' then 'incomplete' else `e`.`status` end AS `execution_status_name`,`e`.`notes` AS `execution_notes`,`e`.`execution_ts` AS `execution_date`,`nh_tc`.`id` AS `tc_id`,`nh_tc`.`name` AS `tc_name`,`tcv`.`version` AS `tc_version`,replace(replace(`tcv`.`summary`,'<p>',''),'</p>','') AS `tc_summary`,`suite`.`id` AS `suite_id`,`suite`.`name` AS `suite_name`,case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else 'No Suite' end AS `suite_path`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',1) limit 1) AS `level1_id`,substring_index(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else 'No Suite' end,' > ',1) AS `level1_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when locate(' > ',case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) > 0 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',2),' > ',-1) else NULL end) limit 1) AS `level2_id`,case when locate(' > ',case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) > 0 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',2),' > ',-1) else NULL end AS `level2_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 4 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',3),' > ',-1) else NULL end) limit 1) AS `level3_id`,case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 4 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',3),' > ',-1) else NULL end AS `level3_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 6 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',4),' > ',-1) else NULL end) limit 1) AS `level4_id`,case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 6 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',4),' > ',-1) else NULL end AS `level4_name`,(select `nodes_hierarchy`.`id` from `nodes_hierarchy` where `nodes_hierarchy`.`name` = (case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 8 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',5),' > ',-1) else `suite`.`name` end) limit 1) AS `level5_id`,case when octet_length(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end) - octet_length(replace(case when `suite`.`id` is not null then (select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`) else '' end,' > ','')) > 8 then substring_index(substring_index((select `node_hierarchy_path`.`path` from `node_hierarchy_path` where `node_hierarchy_path`.`id` = `suite`.`id`),' > ',5),' > ',-1) else `suite`.`name` end AS `level5_name`,`e`.`testplan_id` AS `testplan_id`,replace(replace(`tp`.`notes`,'<p>',''),'</p>','') AS `testplan_name`,`tp`.`testproject_id` AS `project_id`,replace(replace(`tproj`.`notes`,'<p>',''),'</p>','') AS `project_name`,`e`.`build_id` AS `build_id`,`b`.`name` AS `build_name`,`e`.`platform_id` AS `platform_id`,`p`.`name` AS `platform_name`,`e`.`tester_id` AS `tester_id`,`u`.`login` AS `tester_login`,concat(`u`.`first`,' ',`u`.`last`) AS `tester_name`,timestampdiff(SECOND,`tcv`.`creation_ts`,`e`.`execution_ts`) AS `time_to_execution` from (((((((((`executions` `e` join `tcversions` `tcv` on(`e`.`tcversion_id` = `tcv`.`id`)) join `nodes_hierarchy` `nh_tcv` on(`tcv`.`id` = `nh_tcv`.`id`)) join `nodes_hierarchy` `nh_tc` on(`nh_tcv`.`parent_id` = `nh_tc`.`id`)) join `testplans` `tp` on(`e`.`testplan_id` = `tp`.`id`)) join `builds` `b` on(`e`.`build_id` = `b`.`id`)) left join `platforms` `p` on(`e`.`platform_id` = `p`.`id`)) left join `users` `u` on(`e`.`tester_id` = `u`.`id`)) left join `nodes_hierarchy` `suite` on(`nh_tc`.`parent_id` = `suite`.`id`)) join `testprojects` `tproj` on(`tp`.`testproject_id` = `tproj`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_testcase_customfields`
--

/*!50001 DROP TABLE IF EXISTS `vw_testcase_customfields`*/;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_customfields`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_testcase_customfields` AS select `nh_tc`.`id` AS `testcase_id`,`nh_tc`.`name` AS `testcase_name`,`tcv`.`id` AS `tcversion_id`,`tcv`.`version` AS `version`,`tcv`.`summary` AS `summary`,`tcv`.`preconditions` AS `preconditions`,`ts`.`id` AS `testsuite_id`,`nh_ts`.`name` AS `testsuite_name`,`e`.`id` AS `execution_id`,`e`.`status` AS `execution_status`,`e`.`execution_ts` AS `execution_ts`,`e`.`tester_id` AS `tester_id`,`e`.`build_id` AS `build_id`,`e`.`testplan_id` AS `testplan_id`,max(case when `cf`.`id` = 1 then `cfdv`.`value` end) AS `Scenario_ID`,max(case when `cf`.`id` = 2 then `cfdv`.`value` end) AS `Primary_Module`,max(case when `cf`.`id` = 3 then `cfdv`.`value` end) AS `Sub_Scenario`,max(case when `cf`.`id` = 4 then `cfdv`.`value` end) AS `Test_Case_Description`,max(case when `cf`.`id` = 5 then `cfdv`.`value` end) AS `Test_Type`,max(case when `cf`.`id` = 6 then `cfdv`.`value` end) AS `Test_Script`,max(case when `cf`.`id` = 7 then `cfdv`.`value` end) AS `Test_Execution_Path`,max(case when `cf`.`id` = 8 then `cfdv`.`value` end) AS `Expected_Results`,max(case when `cf`.`id` = 9 then `cfdv`.`value` end) AS `E_R_Process`,max(case when `cf`.`id` = 12 then `cfdv`.`value` end) AS `Executed_By`,max(case when `cf`.`id` = 10 then `cfev`.`value` end) AS `Test_Data`,max(case when `cf`.`id` = 11 then `cfev`.`value` end) AS `Priority`,max(case when `cf`.`id` = 13 then `cfev`.`value` end) AS `Execution_Status_CF` from (((((((`nodes_hierarchy` `nh_tc` join `tcversions` `tcv` on(`tcv`.`id` = `nh_tc`.`id`)) join `nodes_hierarchy` `nh_ts` on(`nh_tc`.`parent_id` = `nh_ts`.`id`)) left join `testsuites` `ts` on(`ts`.`id` = `nh_ts`.`id`)) left join `executions` `e` on(`tcv`.`id` = `e`.`tcversion_id`)) left join `custom_fields` `cf` on(1 = 1)) left join `cfield_design_values` `cfdv` on(`cfdv`.`field_id` = `cf`.`id` and `cfdv`.`node_id` = `tcv`.`id` and `cf`.`enable_on_design` = 1)) left join `cfield_execution_values` `cfev` on(`cfev`.`field_id` = `cf`.`id` and `cfev`.`execution_id` = `e`.`id` and `cfev`.`tcversion_id` = `tcv`.`id` and `cf`.`enable_on_execution` = 1)) where `nh_tc`.`node_type_id` = 4 and `tcv`.`active` = 1 group by `nh_tc`.`id`,`nh_tc`.`name`,`tcv`.`id`,`tcv`.`version`,`tcv`.`summary`,`tcv`.`preconditions`,`ts`.`id`,`nh_ts`.`name`,`e`.`id`,`e`.`status`,`e`.`execution_ts`,`e`.`tester_id`,`e`.`build_id`,`e`.`testplan_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_testcase_customfields_new`
--

/*!50001 DROP TABLE IF EXISTS `vw_testcase_customfields_new`*/;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_customfields_new`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_testcase_customfields_new` AS select `nh_tc`.`id` AS `testcase_id`,`nh_tc`.`name` AS `testcase_name`,`tcv`.`id` AS `tcversion_id`,`tcv`.`version` AS `version`,`tcv`.`tc_external_id` AS `tc_external_id`,`tcv`.`summary` AS `summary`,`tcv`.`preconditions` AS `preconditions`,`tcv`.`importance` AS `importance`,`tcv`.`execution_type` AS `execution_type`,`tcv`.`status` AS `status`,`nh_ts`.`id` AS `testsuite_id`,`nh_ts`.`name` AS `testsuite_name`,`e`.`id` AS `execution_id`,`e`.`status` AS `execution_status`,`e`.`execution_ts` AS `execution_ts`,`e`.`tester_id` AS `tester_id`,`e`.`build_id` AS `build_id`,`e`.`testplan_id` AS `testplan_id`,`e`.`platform_id` AS `platform_id`,`e`.`execution_duration` AS `execution_duration`,max(case when `cf`.`id` = 1 then `cfdv`.`value` end) AS `Scenario_ID`,max(case when `cf`.`id` = 2 then `cfdv`.`value` end) AS `Primary_Module`,max(case when `cf`.`id` = 3 then `cfdv`.`value` end) AS `Sub_Scenario`,max(case when `cf`.`id` = 4 then `cfdv`.`value` end) AS `Test_Case_Description`,max(case when `cf`.`id` = 5 then `cfdv`.`value` end) AS `Test_Type`,max(case when `cf`.`id` = 6 then `cfdv`.`value` end) AS `Test_Script`,max(case when `cf`.`id` = 7 then `cfdv`.`value` end) AS `Test_Execution_Path`,max(case when `cf`.`id` = 8 then `cfdv`.`value` end) AS `Expected_Results`,max(case when `cf`.`id` = 9 then `cfdv`.`value` end) AS `E_R_Process`,max(case when `cf`.`id` = 12 then `cfdv`.`value` end) AS `Executed_By`,max(case when `cf`.`id` = 10 then `cfev`.`value` end) AS `Test_Data`,max(case when `cf`.`id` = 11 then `cfev`.`value` end) AS `Priority`,max(case when `cf`.`id` = 13 then `cfev`.`value` end) AS `Execution_Status_CF` from ((((((((`tcversions` `tcv` join `nodes_hierarchy` `nh_tc` on(`tcv`.`id` = `nh_tc`.`id`)) join `nodes_hierarchy` `nh_parent` on(`nh_tc`.`parent_id` = `nh_parent`.`id`)) join `nodes_hierarchy` `nh_ts` on(`nh_parent`.`parent_id` = `nh_ts`.`id`)) left join `testsuites` `ts` on(`nh_ts`.`id` = `ts`.`id`)) left join `executions` `e` on(`tcv`.`id` = `e`.`tcversion_id`)) left join `custom_fields` `cf` on(1 = 1)) left join `cfield_design_values` `cfdv` on(`cfdv`.`field_id` = `cf`.`id` and `cfdv`.`node_id` = `tcv`.`id`)) left join `cfield_execution_values` `cfev` on(`cfev`.`field_id` = `cf`.`id` and `cfev`.`execution_id` = `e`.`id` and `cfev`.`tcversion_id` = `tcv`.`id` and `cfev`.`testplan_id` = `e`.`testplan_id`)) where `tcv`.`active` = 1 group by `nh_tc`.`id`,`nh_tc`.`name`,`tcv`.`id`,`tcv`.`version`,`tcv`.`tc_external_id`,`tcv`.`summary`,`tcv`.`preconditions`,`tcv`.`importance`,`tcv`.`execution_type`,`tcv`.`status`,`nh_ts`.`id`,`nh_ts`.`name`,`e`.`id`,`e`.`status`,`e`.`execution_ts`,`e`.`tester_id`,`e`.`build_id`,`e`.`testplan_id`,`e`.`platform_id`,`e`.`execution_duration` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_testcase_execution_details`
--

/*!50001 DROP TABLE IF EXISTS `vw_testcase_execution_details`*/;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_execution_details`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_testcase_execution_details` AS select `e`.`id` AS `execution_id`,`e`.`tcversion_id` AS `tcversion_id`,`e`.`notes` AS `execution_notes`,`e`.`status` AS `execution_status`,`e`.`build_id` AS `build_id`,`e`.`testplan_id` AS `testplan_id`,`nh_tc`.`id` AS `testcase_id`,`nh_tc`.`name` AS `testcase_name`,`nh_tc`.`parent_id` AS `parent_id`,`nh_parent`.`name` AS `parent_name`,`tv`.`summary` AS `testcase_summary`,`tv`.`preconditions` AS `testcase_preconditions`,`tv`.`version` AS `testcase_version`,`e`.`execution_ts` AS `execution_ts`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 1),`nh_tc`.`name`,'No Scenario ID Available') AS `scenario_id`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 2),'No Primary Module Available') AS `primary_module`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 3),'No Sub-Scenario Available') AS `sub_scenario`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 10),`e`.`notes`,'No Test Data Available') AS `test_data`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 8),'No Expected Results Available') AS `expected_results`,coalesce((select `cfv`.`value` from `cfield_execution_values` `cfv` where `cfv`.`execution_id` = `e`.`id` and `cfv`.`field_id` = 11),'P3') AS `priority` from ((((`executions` `e` join `nodes_hierarchy` `nh_tcv` on(`e`.`tcversion_id` = `nh_tcv`.`id`)) join `nodes_hierarchy` `nh_tc` on(`nh_tcv`.`parent_id` = `nh_tc`.`id`)) join `tcversions` `tv` on(`nh_tcv`.`id` = `tv`.`id`)) left join `nodes_hierarchy` `nh_parent` on(`nh_tc`.`parent_id` = `nh_parent`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_testcase_summary`
--

/*!50001 DROP TABLE IF EXISTS `vw_testcase_summary`*/;
/*!50001 DROP VIEW IF EXISTS `vw_testcase_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_testcase_summary` AS select `nh_tc`.`id` AS `testcase_id`,`nh_tc`.`name` AS `testcase_name`,`tcv`.`id` AS `tcversion_id`,`tcv`.`version` AS `version`,`tcv`.`summary` AS `summary`,`tcv`.`preconditions` AS `preconditions`,`tcv`.`creation_ts` AS `creation_ts`,`tcv`.`modification_ts` AS `modification_ts`,`tcv`.`importance` AS `importance`,`ts`.`id` AS `testsuite_id`,`nh_ts`.`name` AS `testsuite_name`,`tp`.`id` AS `testproject_id`,`nh_tp`.`name` AS `testproject_name`,max(case when `cf`.`id` = 1 then `cfdv`.`value` end) AS `Scenario_ID`,max(case when `cf`.`id` = 2 then `cfdv`.`value` end) AS `Primary_Module`,max(case when `cf`.`id` = 3 then `cfdv`.`value` end) AS `Sub_Scenario`,max(case when `cf`.`id` = 4 then `cfdv`.`value` end) AS `Test_Case_Description`,max(case when `cf`.`id` = 5 then `cfdv`.`value` end) AS `Test_Type`,max(case when `cf`.`id` = 6 then `cfdv`.`value` end) AS `Test_Script`,max(case when `cf`.`id` = 7 then `cfdv`.`value` end) AS `Test_Execution_Path`,max(case when `cf`.`id` = 8 then `cfdv`.`value` end) AS `Expected_Results`,max(case when `cf`.`id` = 9 then `cfdv`.`value` end) AS `E_R_Process`,max(case when `cf`.`id` = 12 then `cfdv`.`value` end) AS `Executed_By` from (((((((`nodes_hierarchy` `nh_tc` join `tcversions` `tcv` on(`tcv`.`id` = `nh_tc`.`id`)) join `nodes_hierarchy` `nh_ts` on(`nh_tc`.`parent_id` = `nh_ts`.`id`)) left join `testsuites` `ts` on(`ts`.`id` = `nh_ts`.`id`)) join `nodes_hierarchy` `nh_tp` on(`nh_ts`.`parent_id` = `nh_tp`.`id`)) left join `testprojects` `tp` on(`tp`.`id` = `nh_tp`.`id`)) left join `custom_fields` `cf` on(`cf`.`enable_on_design` = 1)) left join `cfield_design_values` `cfdv` on(`cfdv`.`field_id` = `cf`.`id` and `cfdv`.`node_id` = `tcv`.`id`)) where `nh_tc`.`node_type_id` = 4 and `tcv`.`active` = 1 group by `nh_tc`.`id`,`nh_tc`.`name`,`tcv`.`id`,`tcv`.`version`,`tcv`.`summary`,`tcv`.`preconditions`,`tcv`.`creation_ts`,`tcv`.`modification_ts`,`tcv`.`importance`,`ts`.`id`,`nh_ts`.`name`,`tp`.`id`,`nh_tp`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-27  9:39:34
