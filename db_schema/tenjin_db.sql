-- DROP SCHEMA public;

CREATE SCHEMA public AUTHORIZATION pg_database_owner;

-- DROP SEQUENCE public.agent_sequence_generator;

CREATE SEQUENCE public.agent_sequence_generator
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_ytc_files_id_seq;

CREATE SEQUENCE public.ai_ytc_files_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_yts_design_scenario_order_seq;

CREATE SEQUENCE public.ai_yts_design_scenario_order_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_yts_design_testcase_id_seq;

CREATE SEQUENCE public.ai_yts_design_testcase_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_yts_design_testcase_order_seq;

CREATE SEQUENCE public.ai_yts_design_testcase_order_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_yts_design_teststeps_id_seq;

CREATE SEQUENCE public.ai_yts_design_teststeps_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_yts_design_transactions_id_seq;

CREATE SEQUENCE public.ai_yts_design_transactions_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_yts_file_design_mapper_id_seq;

CREATE SEQUENCE public.ai_yts_file_design_mapper_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.ai_yts_llm_behaviour_id_seq;

CREATE SEQUENCE public.ai_yts_llm_behaviour_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.assisted_sequence_generator;

CREATE SEQUENCE public.assisted_sequence_generator
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 200
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.aut_user_sequence_generator;

CREATE SEQUENCE public.aut_user_sequence_generator
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.complexty_sequence_generator;

CREATE SEQUENCE public.complexty_sequence_generator
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.deleted_agent_log_sequence_generator;

CREATE SEQUENCE public.deleted_agent_log_sequence_generator
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.deleted_backlog_sequence_generator;

CREATE SEQUENCE public.deleted_backlog_sequence_generator
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.deleted_case_log_sequence_generator;

CREATE SEQUENCE public.deleted_case_log_sequence_generator
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.deleted_ext_interface_log_sequence_generator;

CREATE SEQUENCE public.deleted_ext_interface_log_sequence_generator
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.deleted_steps_log_sequence_generator;

CREATE SEQUENCE public.deleted_steps_log_sequence_generator
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.exe_test_failures_id_seq;

CREATE SEQUENCE public.exe_test_failures_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 2147483647
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.exe_test_result_platforms_id_seq;

CREATE SEQUENCE public.exe_test_result_platforms_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.exe_test_results_id_seq;

CREATE SEQUENCE public.exe_test_results_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.folder_deleted_log_sequence_generator;

CREATE SEQUENCE public.folder_deleted_log_sequence_generator
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.hibernate_sequence;

CREATE SEQUENCE public.hibernate_sequence
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.license_sequence_generator;

CREATE SEQUENCE public.license_sequence_generator
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.prj_backlog_testcase_links_id_seq;

CREATE SEQUENCE public.prj_backlog_testcase_links_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.prj_labels_id_seq;

CREATE SEQUENCE public.prj_labels_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.project_sequence_generator;

CREATE SEQUENCE public.project_sequence_generator
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_agent_command_log;

CREATE SEQUENCE public.seq_agent_command_log
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_api_request_body;

CREATE SEQUENCE public.seq_api_request_body
	INCREMENT BY 15
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 2000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_api_response_body;

CREATE SEQUENCE public.seq_api_response_body
	INCREMENT BY 15
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 3000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_api_token_sequence_generator;

CREATE SEQUENCE public.seq_api_token_sequence_generator
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_apioperation;

CREATE SEQUENCE public.seq_apioperation
	INCREMENT BY 15
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_application;

CREATE SEQUENCE public.seq_application
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_attachment;

CREATE SEQUENCE public.seq_attachment
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1500
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_auto_test_bots;

CREATE SEQUENCE public.seq_auto_test_bots
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_backlog_contributor;

CREATE SEQUENCE public.seq_backlog_contributor
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_backlog_qa_analysis;

CREATE SEQUENCE public.seq_backlog_qa_analysis
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_backlog_risk_analysis;

CREATE SEQUENCE public.seq_backlog_risk_analysis
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_comments;

CREATE SEQUENCE public.seq_comments
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_def_defect_linkage;

CREATE SEQUENCE public.seq_def_defect_linkage
	INCREMENT BY 20
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_deleted_functions_log;

CREATE SEQUENCE public.seq_deleted_functions_log
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_email_configuration;

CREATE SEQUENCE public.seq_email_configuration
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_exe_cloud_providers;

CREATE SEQUENCE public.seq_exe_cloud_providers
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_exe_defect_linkage;

CREATE SEQUENCE public.seq_exe_defect_linkage
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_exe_summary;

CREATE SEQUENCE public.seq_exe_summary
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_exe_test_run_targets;

CREATE SEQUENCE public.seq_exe_test_run_targets
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_exe_test_runs;

CREATE SEQUENCE public.seq_exe_test_runs
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_executable_iteration_run;

CREATE SEQUENCE public.seq_executable_iteration_run
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 21000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_exedefects;

CREATE SEQUENCE public.seq_exedefects
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_ext_external_entities;

CREATE SEQUENCE public.seq_ext_external_entities
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_ext_interface;

CREATE SEQUENCE public.seq_ext_interface
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_ext_project_integrations;

CREATE SEQUENCE public.seq_ext_project_integrations
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_extdefect;

CREATE SEQUENCE public.seq_extdefect
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_extdefectlist_integrated;

CREATE SEQUENCE public.seq_extdefectlist_integrated
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_field;

CREATE SEQUENCE public.seq_field
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_fieldlocator;

CREATE SEQUENCE public.seq_fieldlocator
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 3500
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_functions;

CREATE SEQUENCE public.seq_functions
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 2000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_learner_audit;

CREATE SEQUENCE public.seq_learner_audit
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 5000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_manage_aut_url;

CREATE SEQUENCE public.seq_manage_aut_url
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_milestone_check_list;

CREATE SEQUENCE public.seq_milestone_check_list
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 3000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_milestone_links;

CREATE SEQUENCE public.seq_milestone_links
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_milestones;

CREATE SEQUENCE public.seq_milestones
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 20000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_msg;

CREATE SEQUENCE public.seq_msg
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_pagearea;

CREATE SEQUENCE public.seq_pagearea
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 6000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_pagelocator;

CREATE SEQUENCE public.seq_pagelocator
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 3000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_password_reset_requests;

CREATE SEQUENCE public.seq_password_reset_requests
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_priority;

CREATE SEQUENCE public.seq_priority
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_prj_backlog;

CREATE SEQUENCE public.seq_prj_backlog
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_program;

CREATE SEQUENCE public.seq_program
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_project_aut_files;

CREATE SEQUENCE public.seq_project_aut_files
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_project_role;

CREATE SEQUENCE public.seq_project_role
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_project_role_permissions;

CREATE SEQUENCE public.seq_project_role_permissions
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_projectusers_list;

CREATE SEQUENCE public.seq_projectusers_list
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 18000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_pym_msgs;

CREATE SEQUENCE public.seq_pym_msgs
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_report_configuration;

CREATE SEQUENCE public.seq_report_configuration
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1300
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_reports;

CREATE SEQUENCE public.seq_reports
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_runs;

CREATE SEQUENCE public.seq_runs
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 10000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_runtime_field_value;

CREATE SEQUENCE public.seq_runtime_field_value
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_runtime_screenshot;

CREATE SEQUENCE public.seq_runtime_screenshot
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_scheduled_task;

CREATE SEQUENCE public.seq_scheduled_task
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_sys_users;

CREATE SEQUENCE public.seq_sys_users
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_system_role;

CREATE SEQUENCE public.seq_system_role
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_tdes_classification_types;

CREATE SEQUENCE public.seq_tdes_classification_types
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_tdes_folder;

CREATE SEQUENCE public.seq_tdes_folder
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 13000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_tdes_test_cases;

CREATE SEQUENCE public.seq_tdes_test_cases
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 12000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_tdes_test_cycles;

CREATE SEQUENCE public.seq_tdes_test_cycles
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_tdes_test_packs;

CREATE SEQUENCE public.seq_tdes_test_packs
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_tdes_test_suites;

CREATE SEQUENCE public.seq_tdes_test_suites
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_tdes_test_types;

CREATE SEQUENCE public.seq_tdes_test_types
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_test_data_upload;

CREATE SEQUENCE public.seq_test_data_upload
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 20000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_test_instance;

CREATE SEQUENCE public.seq_test_instance
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_test_step;

CREATE SEQUENCE public.seq_test_step
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 100
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_test_suite;

CREATE SEQUENCE public.seq_test_suite
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_testcaseattempt;

CREATE SEQUENCE public.seq_testcaseattempt
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_teststepattempt;

CREATE SEQUENCE public.seq_teststepattempt
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_two_factor_auth_setting;

CREATE SEQUENCE public.seq_two_factor_auth_setting
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_validation_result;

CREATE SEQUENCE public.seq_validation_result
	INCREMENT BY 10
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_workflow;

CREATE SEQUENCE public.seq_workflow
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.seq_workflowstatustransition;

CREATE SEQUENCE public.seq_workflowstatustransition
	INCREMENT BY 50
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.sys_events_id_seq;

CREATE SEQUENCE public.sys_events_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.sys_users_password_history_id_seq;

CREATE SEQUENCE public.sys_users_password_history_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.tdes_test_cases_classification;

CREATE SEQUENCE public.tdes_test_cases_classification
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 12000
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE public.user_audit_logs_id_seq;

CREATE SEQUENCE public.user_audit_logs_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;-- public.agent_command_log definition

-- Drop table

-- DROP TABLE public.agent_command_log;

CREATE TABLE public.agent_command_log (
	id int8 NOT NULL,
	automation_token varchar(4000) NULL,
	command_data oid NOT NULL,
	completed_on timestamp NULL,
	created_on timestamp NOT NULL,
	priority bool NOT NULL,
	response_data oid NULL,
	status varchar(255) NOT NULL,
	updated_on timestamp NULL,
	CONSTRAINT agent_command_log_pkey PRIMARY KEY (id)
);


-- public.ai_ytc_files definition

-- Drop table

-- DROP TABLE public.ai_ytc_files;

CREATE TABLE public.ai_ytc_files (
	id int4 NOT NULL,
	file_id varchar(255) NULL,
	doc_type varchar(255) NOT NULL,
	"name" varchar(255) NOT NULL,
	raw_data bytea NULL,
	url_path text NULL,
	"delimiter" varchar(255) NULL,
	sheet varchar(255) NULL,
	created_on timestamp NULL,
	is_delete bool NULL,
	CONSTRAINT ai_ytc_files_pkey PRIMARY KEY (id)
);


-- public.ai_yts_design_transactions definition

-- Drop table

-- DROP TABLE public.ai_yts_design_transactions;

CREATE TABLE public.ai_yts_design_transactions (
	id int4 NOT NULL,
	unique_id varchar(255) NOT NULL,
	application_id varchar(255) NULL,
	vector_id varchar(255) NULL,
	output_language varchar(255) NULL,
	"name" varchar(255) NULL,
	description text NULL,
	approx_scen_count text NULL,
	parameters text NULL,
	context text NULL,
	elapsed_time text NULL,
	status varchar(255) NULL,
	created_on timestamp NULL,
	is_delete varchar(255) NULL,
	meta text NULL,
	created_by varchar(255) NULL,
	CONSTRAINT ai_yts_design_transactions_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_design_transactions_unique_id_name_application_id_is_key UNIQUE (unique_id, name, application_id, is_delete)
);


-- public.ai_yts_llm_behaviour definition

-- Drop table

-- DROP TABLE public.ai_yts_llm_behaviour;

CREATE TABLE public.ai_yts_llm_behaviour (
	id int4 NOT NULL,
	model varchar(255) NOT NULL,
	temperature varchar(255) NULL,
	prompt text NOT NULL,
	i_o_logs text NULL,
	origin varchar(255) NULL,
	is_defect int4 NULL,
	level_type varchar(255) NULL,
	design_id int4 NULL,
	is_upload bool NULL,
	created_on timestamp NULL,
	CONSTRAINT ai_yts_llm_behaviour_pkey PRIMARY KEY (id)
);


-- public.ai_yts_llm_source definition

-- Drop table

-- DROP TABLE public.ai_yts_llm_source;

CREATE TABLE public.ai_yts_llm_source (
	id varchar(255) NOT NULL,
	"source" text NULL,
	created_on timestamp NULL,
	CONSTRAINT ai_yts_llm_source_pkey PRIMARY KEY (id)
);


-- public.aut_api_request definition

-- Drop table

-- DROP TABLE public.aut_api_request;

CREATE TABLE public.aut_api_request (
	id int8 NOT NULL,
	content_type varchar(255) NULL,
	request oid NULL,
	CONSTRAINT aut_api_request_pkey PRIMARY KEY (id)
);


-- public.aut_message_format definition

-- Drop table

-- DROP TABLE public.aut_message_format;

CREATE TABLE public.aut_message_format (
	id int8 NOT NULL,
	field_label varchar(255) NULL,
	format varchar(255) NULL,
	message_type varchar(255) NULL,
	tag_name varchar(255) NULL,
	CONSTRAINT aut_message_format_pkey PRIMARY KEY (id)
);


-- public.defect_list_integrated definition

-- Drop table

-- DROP TABLE public.defect_list_integrated;

CREATE TABLE public.defect_list_integrated (
	id int8 NOT NULL,
	defect_id int8 NULL,
	defect_key varchar(255) NULL,
	instance_id int8 NULL,
	test_cycle_id int8 NULL,
	test_instance_id int8 NOT NULL,
	test_pack_id int8 NULL,
	test_step_id int8 NULL,
	test_suite_id int8 NULL,
	CONSTRAINT defect_list_integrated_pkey PRIMARY KEY (id)
);


-- public.exe_summary definition

-- Drop table

-- DROP TABLE public.exe_summary;

CREATE TABLE public.exe_summary (
	id int8 NOT NULL,
	entity_id int8 NOT NULL,
	entity_type varchar(255) NOT NULL,
	execution_summary_data oid NULL,
	CONSTRAINT exe_summary_pkey PRIMARY KEY (id)
);


-- public.exe_test_failures definition

-- Drop table

-- DROP TABLE public.exe_test_failures;

CREATE TABLE public.exe_test_failures (
	id int4 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START 1 CACHE 1 NO CYCLE) NOT NULL,
	full_stack_trace oid NULL,
	reason oid NULL,
	skip_caused_by varchar(255) NULL,
	CONSTRAINT exe_test_failures_pkey PRIMARY KEY (id)
);


-- public.exe_test_result_platforms definition

-- Drop table

-- DROP TABLE public.exe_test_result_platforms;

CREATE TABLE public.exe_test_result_platforms (
	id int8 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
	application_platform varchar(255) NULL,
	browser varchar(255) NULL,
	browser_version varchar(255) NULL,
	os varchar(255) NULL,
	os_version varchar(255) NULL,
	CONSTRAINT exe_test_result_platforms_pkey PRIMARY KEY (id)
);


-- public.executable_iteration_run definition

-- Drop table

-- DROP TABLE public.executable_iteration_run;

CREATE TABLE public.executable_iteration_run (
	id int8 NOT NULL,
	actual_response_code int4 NULL,
	tstep_message varchar(4000) NULL,
	aut_credential_type varchar(4000) NULL,
	execution_blocks oid NULL,
	bot_type varchar(255) NULL,
	test_case_attempt_id int8 NULL,
	clear_session_flag bool NULL,
	dataset_dependencies oid NULL,
	tstep_tduid varchar(255) NULL,
	dataset_details oid NULL,
	end_time timestamp NULL,
	executed_query varchar(4000) NULL,
	execution_manifest oid NULL,
	expected_result varchar(4000) NULL,
	credential_details oid NULL,
	function_details oid NULL,
	logout_actions oid NULL,
	login_actions oid NULL,
	agent_task_mode varchar(255) NULL,
	step_notes varchar(255) NULL,
	recover_actions oid NULL,
	recover_actions_new oid NULL,
	api_req_log oid NULL,
	api_request oid NULL,
	expected_response_code int4 NULL,
	api_response oid NULL,
	response_time varchar(255) NULL,
	execution_sequence int8 NULL,
	session_signature varchar(500) NULL,
	start_time timestamp NULL,
	tstep_result varchar(255) NULL,
	step_action_results oid NULL,
	tstep_attempt_id int8 NULL,
	iteration_no int4 NULL,
	step_type varchar(255) NULL,
	test_bot_id int8 NULL,
	test_bot_details oid NULL,
	test_data_group_id varchar(255) NULL,
	run_id int8 NULL,
	test_run_target_id int8 NULL,
	teststep_id varchar(255) NULL,
	test_suite_id int8 NULL,
	test_suite_instance_id int8 NULL,
	step_dependencies oid NULL,
	CONSTRAINT executable_iteration_run_pkey PRIMARY KEY (id)
);


-- public.ext_defect definition

-- Drop table

-- DROP TABLE public.ext_defect;

CREATE TABLE public.ext_defect (
	id int8 NOT NULL,
	defect_key varchar(255) NULL,
	instance_id int8 NULL,
	posted bool NOT NULL,
	priority varchar(255) NULL,
	raised_by varchar(255) NULL,
	raised_on varchar(255) NULL,
	status varchar(255) NULL,
	summary varchar(255) NULL,
	CONSTRAINT ext_defect_pkey PRIMARY KEY (id)
);


-- public.page_area_locator definition

-- Drop table

-- DROP TABLE public.page_area_locator;

CREATE TABLE public.page_area_locator (
	id int8 NOT NULL,
	page_locator_altered bool NULL,
	page_locator_type varchar(255) NULL,
	page_locator_value varchar(4000) NULL,
	CONSTRAINT page_area_locator_pkey PRIMARY KEY (id)
);


-- public.prj_labels definition

-- Drop table

-- DROP TABLE public.prj_labels;

CREATE TABLE public.prj_labels (
	id int8 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
	description varchar(255) NULL,
	"external" bool NOT NULL,
	"name" varchar(255) NULL,
	CONSTRAINT prj_labels_pkey PRIMARY KEY (id),
	CONSTRAINT uk_5byep0ogv1s6g2krjb1ui2dhg UNIQUE (name)
);


-- public.prj_testcase_sequence definition

-- Drop table

-- DROP TABLE public.prj_testcase_sequence;

CREATE TABLE public.prj_testcase_sequence (
	project_id int8 NOT NULL,
	"next" int8 NULL,
	"version" int8 NULL,
	CONSTRAINT prj_testcase_sequence_pkey PRIMARY KEY (project_id)
);


-- public.sys_events definition

-- Drop table

-- DROP TABLE public.sys_events;

CREATE TABLE public.sys_events (
	id int8 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
	event_name varchar(255) NULL,
	payload oid NULL,
	publish_time timestamp NULL,
	CONSTRAINT sys_events_pkey PRIMARY KEY (id)
);


-- public.sys_system_roles definition

-- Drop table

-- DROP TABLE public.sys_system_roles;

CREATE TABLE public.sys_system_roles (
	id int8 NOT NULL,
	description varchar(255) NULL,
	"name" varchar(255) NOT NULL,
	CONSTRAINT sys_system_roles_pkey PRIMARY KEY (id)
);


-- public.tdes_classification_types definition

-- Drop table

-- DROP TABLE public.tdes_classification_types;

CREATE TABLE public.tdes_classification_types (
	id int8 NOT NULL,
	classification_type_id varchar(255) NULL,
	description varchar(4000) NULL,
	icon varchar(255) NULL,
	"name" varchar(48) NOT NULL,
	CONSTRAINT tdes_classification_types_pkey PRIMARY KEY (id),
	CONSTRAINT uk_9gmp1kysy843wo1p277tkx82f UNIQUE (name)
);


-- public.tdes_test_type definition

-- Drop table

-- DROP TABLE public.tdes_test_type;

CREATE TABLE public.tdes_test_type (
	id int8 NOT NULL,
	"name" varchar(32) NOT NULL,
	CONSTRAINT tdes_test_type_pkey PRIMARY KEY (id),
	CONSTRAINT uk_8hva3jclmfed1gsikp71t3qlj UNIQUE (name)
);


-- public.tdes_work_flow_status definition

-- Drop table

-- DROP TABLE public.tdes_work_flow_status;

CREATE TABLE public.tdes_work_flow_status (
	id int8 NOT NULL,
	baselined bool NULL,
	color varchar(255) NULL,
	default_status bool NULL,
	"name" varchar(255) NULL,
	reviewed bool NULL,
	CONSTRAINT tdes_work_flow_status_pkey PRIMARY KEY (id)
);


-- public.testcase_classifications definition

-- Drop table

-- DROP TABLE public.testcase_classifications;

CREATE TABLE public.testcase_classifications (
	id int8 NOT NULL,
	classification_id int8 NULL,
	classification_type varchar(255) NULL,
	classification_value varchar(255) NULL,
	folder_id int8 NULL,
	testcase_id int8 NULL,
	CONSTRAINT testcase_classifications_pkey PRIMARY KEY (id)
);


-- public.ai_ytc_api_specification definition

-- Drop table

-- DROP TABLE public.ai_ytc_api_specification;

CREATE TABLE public.ai_ytc_api_specification (
	id int4 NOT NULL,
	file_id int4 NULL,
	file_name varchar(255) NULL,
	function_chunk text NULL,
	created_on timestamp NULL,
	process_id varchar NULL,
	CONSTRAINT ai_ytc_api_specification_pkey PRIMARY KEY (id),
	CONSTRAINT ai_ytc_api_specification_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.ai_ytc_files(id)
);


-- public.ai_yts_design_process definition

-- Drop table

-- DROP TABLE public.ai_yts_design_process;

CREATE TABLE public.ai_yts_design_process (
	id varchar(255) NOT NULL,
	description text NULL,
	short_name varchar(255) NULL,
	origin varchar(255) NULL,
	is_select bool NULL,
	imported bool NULL,
	created_on timestamp NULL,
	feedback bool NULL,
	"comment" text NULL,
	design_id int4 NULL,
	CONSTRAINT ai_yts_design_process_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_design_process_design_id_fkey FOREIGN KEY (design_id) REFERENCES public.ai_yts_design_transactions(id)
);


-- public.ai_yts_design_scenario definition

-- Drop table

-- DROP TABLE public.ai_yts_design_scenario;

CREATE TABLE public.ai_yts_design_scenario (
	id varchar(255) NOT NULL,
	is_select bool NULL,
	imported bool NULL,
	short_name varchar(255) NULL,
	"order" int4 NOT NULL,
	description text NULL,
	traceability text NULL,
	requirement_type varchar(255) NULL,
	origin varchar(255) NULL,
	feedback bool NULL,
	"comment" text NULL,
	created_on timestamp NULL,
	process_id varchar(255) NULL,
	design_id int4 NULL,
	CONSTRAINT ai_yts_design_scenario_order_key UNIQUE ("order"),
	CONSTRAINT ai_yts_design_scenario_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_design_scenario_design_id_fkey FOREIGN KEY (design_id) REFERENCES public.ai_yts_design_transactions(id),
	CONSTRAINT ai_yts_design_scenario_process_id_fkey FOREIGN KEY (process_id) REFERENCES public.ai_yts_design_process(id)
);


-- public.ai_yts_design_testcase definition

-- Drop table

-- DROP TABLE public.ai_yts_design_testcase;

CREATE TABLE public.ai_yts_design_testcase (
	id int4 NOT NULL,
	"order" int4 NOT NULL,
	description text NULL,
	expected_value text NULL,
	testcase_type varchar(255) NULL,
	origin varchar(255) NULL,
	requirement_type varchar(255) NULL,
	is_select bool NULL,
	imported bool NULL,
	feedback bool NULL,
	"comment" text NULL,
	created_on timestamp NULL,
	scenario_id varchar(255) NULL,
	design_id int4 NULL,
	CONSTRAINT ai_yts_design_testcase_order_key UNIQUE ("order"),
	CONSTRAINT ai_yts_design_testcase_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_design_testcase_design_id_fkey FOREIGN KEY (design_id) REFERENCES public.ai_yts_design_transactions(id),
	CONSTRAINT ai_yts_design_testcase_scenario_id_fkey FOREIGN KEY (scenario_id) REFERENCES public.ai_yts_design_scenario(id)
);


-- public.ai_yts_design_teststeps definition

-- Drop table

-- DROP TABLE public.ai_yts_design_teststeps;

CREATE TABLE public.ai_yts_design_teststeps (
	id int4 NOT NULL,
	description text NULL,
	expected_value text NULL,
	teststep_type varchar(255) NULL,
	created_on timestamp NULL,
	testcase_id int4 NULL,
	CONSTRAINT ai_yts_design_teststeps_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_design_teststeps_testcase_id_fkey FOREIGN KEY (testcase_id) REFERENCES public.ai_yts_design_testcase(id)
);


-- public.ai_yts_file_design_mapper definition

-- Drop table

-- DROP TABLE public.ai_yts_file_design_mapper;

CREATE TABLE public.ai_yts_file_design_mapper (
	id int4 NOT NULL,
	file_id int4 NULL,
	design_id int4 NULL,
	CONSTRAINT ai_yts_file_design_mapper_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_file_design_mapper_design_id_fkey FOREIGN KEY (design_id) REFERENCES public.ai_yts_design_transactions(id),
	CONSTRAINT ai_yts_file_design_mapper_file_id_fkey FOREIGN KEY (file_id) REFERENCES public.ai_ytc_files(id)
);


-- public.ai_yts_llm_assistants definition

-- Drop table

-- DROP TABLE public.ai_yts_llm_assistants;

CREATE TABLE public.ai_yts_llm_assistants (
	id varchar(255) NOT NULL,
	model varchar(255) NULL,
	temperature varchar(255) NULL,
	"type" varchar(255) NULL,
	design_id int4 NULL,
	CONSTRAINT ai_yts_llm_assistants_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_llm_assistants_design_id_fkey FOREIGN KEY (design_id) REFERENCES public.ai_yts_design_transactions(id)
);


-- public.ai_yts_llm_threads definition

-- Drop table

-- DROP TABLE public.ai_yts_llm_threads;

CREATE TABLE public.ai_yts_llm_threads (
	id varchar(255) NOT NULL,
	"type" varchar(255) NULL,
	parent_id varchar(255) NULL,
	assistant_id varchar(255) NULL,
	design_id int4 NULL,
	CONSTRAINT ai_yts_llm_threads_pkey PRIMARY KEY (id),
	CONSTRAINT ai_yts_llm_threads_assistant_id_fkey FOREIGN KEY (assistant_id) REFERENCES public.ai_yts_llm_assistants(id),
	CONSTRAINT ai_yts_llm_threads_design_id_fkey FOREIGN KEY (design_id) REFERENCES public.ai_yts_design_transactions(id)
);


-- public.aut_api_operation definition

-- Drop table

-- DROP TABLE public.aut_api_operation;

CREATE TABLE public.aut_api_operation (
	id int8 NOT NULL,
	api_type varchar(255) NOT NULL,
	end_point_url varchar(255) NOT NULL,
	method_type varchar(255) NOT NULL,
	request_body int8 NULL,
	"source" varchar(255) NULL,
	CONSTRAINT aut_api_operation_pkey PRIMARY KEY (id),
	CONSTRAINT fks5cjds9osjklfn885hhuqb6i1 FOREIGN KEY (request_body) REFERENCES public.aut_api_request(id)
);


-- public.aut_api_response definition

-- Drop table

-- DROP TABLE public.aut_api_response;

CREATE TABLE public.aut_api_response (
	id int8 NOT NULL,
	content_type varchar(255) NULL,
	response oid NOT NULL,
	status int4 NULL,
	response_body int8 NOT NULL,
	CONSTRAINT aut_api_response_pkey PRIMARY KEY (id),
	CONSTRAINT fksj5afu92ketvxvdv7qyr9n7dt FOREIGN KEY (response_body) REFERENCES public.aut_api_operation(id)
);


-- public.runtime_field_value definition

-- Drop table

-- DROP TABLE public.runtime_field_value;

CREATE TABLE public.runtime_field_value (
	id int8 NOT NULL,
	detail_no varchar(255) NULL,
	field_name varchar(255) NULL,
	field_value varchar(4000) NULL,
	iteration int4 NOT NULL,
	page_name varchar(255) NULL,
	user_id int8 NULL,
	exe_iterable_run_id int8 NOT NULL,
	CONSTRAINT runtime_field_value_pkey PRIMARY KEY (id),
	CONSTRAINT fkclj0sg0xwxo5454o34el8p341 FOREIGN KEY (exe_iterable_run_id) REFERENCES public.executable_iteration_run(id)
);


-- public.runtime_screenshot definition

-- Drop table

-- DROP TABLE public.runtime_screenshot;

CREATE TABLE public.runtime_screenshot (
	id int8 NOT NULL,
	iteration int4 NOT NULL,
	screenshot_data oid NULL,
	screenshot_type varchar(255) NULL,
	"sequence" int4 NOT NULL,
	exe_iterable_run_id int8 NOT NULL,
	captured_at varchar(255) NULL,
	message varchar(4000) NULL,
	CONSTRAINT runtime_screenshot_pkey PRIMARY KEY (id),
	CONSTRAINT fkmndsv37c6c3td9wy7vsrdom4c FOREIGN KEY (exe_iterable_run_id) REFERENCES public.executable_iteration_run(id)
);


-- public.sys_users definition

-- Drop table

-- DROP TABLE public.sys_users;

CREATE TABLE public.sys_users (
	id int8 NOT NULL,
	created_on timestamp NOT NULL,
	email varchar(255) NOT NULL,
	failed_attempt int4 NULL,
	first_name varchar(255) NOT NULL,
	activation_status bool NULL,
	is_locked bool NOT NULL,
	onboard_status bool NULL,
	last_name varchar(255) NOT NULL,
	full_name varchar(255) NOT NULL,
	"password" varchar(255) NOT NULL,
	reason varchar(1000) NULL,
	updated_on timestamp NULL,
	created_by int8 NULL,
	role_id int8 NULL,
	updated_by int8 NULL,
	last_login_time timestamp NULL,
	two_factor_enabled bool DEFAULT false NOT NULL,
	two_factor_setup_complete bool DEFAULT false NOT NULL,
	two_factor_secret varchar(255) NULL,
	CONSTRAINT sys_users_pkey PRIMARY KEY (id),
	CONSTRAINT uk_33q8s4g9uijscjhylutntc72e UNIQUE (email),
	CONSTRAINT fk5q1f7lu3sny989e9oq2cr3gw4 FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk8u301loxffiipewl48iyt7rof FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkkuct3edfwdvbveegll1yhj08e FOREIGN KEY (role_id) REFERENCES public.sys_system_roles(id)
);


-- public.sys_users_password_history definition

-- Drop table

-- DROP TABLE public.sys_users_password_history;

CREATE TABLE public.sys_users_password_history (
	id int8 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
	password_history varchar(3000) NOT NULL,
	updated_at timestamp NULL,
	user_id int8 NOT NULL,
	CONSTRAINT sys_users_password_history_pkey PRIMARY KEY (id),
	CONSTRAINT fkclxciu7e8ada7qxx2ace9rr6p FOREIGN KEY (user_id) REFERENCES public.sys_users(id)
);


-- public.tc_complexity definition

-- Drop table

-- DROP TABLE public.tc_complexity;

CREATE TABLE public.tc_complexity (
	id int8 NOT NULL,
	complexity_created_on timestamp NULL,
	complexity_updated_on timestamp NULL,
	complexity_description varchar(4000) NULL,
	complexity_name varchar(255) NOT NULL,
	complexity_created_by int8 NOT NULL,
	complexity_updated_by int8 NULL,
	CONSTRAINT tc_complexity_pkey PRIMARY KEY (id),
	CONSTRAINT uk_ktuh8dlmx3oq1u7n10r0q61dm UNIQUE (complexity_name),
	CONSTRAINT fk9e2f1y6wqymtqc0kf9v0alw6c FOREIGN KEY (complexity_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkl6ah4ei3t6xbd8etyinw4xngr FOREIGN KEY (complexity_updated_by) REFERENCES public.sys_users(id)
);


-- public.tenjin_license definition

-- Drop table

-- DROP TABLE public.tenjin_license;

CREATE TABLE public.tenjin_license (
	id int8 NOT NULL,
	license_key oid NULL,
	updated_on timestamp NULL,
	license_updated_by int8 NULL,
	CONSTRAINT tenjin_license_pkey PRIMARY KEY (id),
	CONSTRAINT fktjlefmg3xr6d9pyr18k0pdp75 FOREIGN KEY (license_updated_by) REFERENCES public.sys_users(id)
);


-- public.test_suite_instance definition

-- Drop table

-- DROP TABLE public.test_suite_instance;

CREATE TABLE public.test_suite_instance (
	id int8 NOT NULL,
	added_on timestamp NULL,
	archived_date timestamp NULL,
	cycle_id int8 NULL,
	execution_status int4 NULL,
	is_archived bool NOT NULL,
	last_executed_on timestamp NULL,
	last_run_type int4 NULL,
	sequence_id int8 NULL,
	test_case_id int8 NULL,
	test_instance_id int8 NULL,
	test_suite_id int8 NULL,
	added_by int8 NULL,
	archived_by int8 NULL,
	assigned_to int8 NULL,
	last_executed_by int8 NULL,
	CONSTRAINT test_suite_instance_pkey PRIMARY KEY (id),
	CONSTRAINT fk1sqose9ymcxeblvtgnigdyy5n FOREIGN KEY (last_executed_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk8cfo56h4yo6dvxxj2a0hbegnl FOREIGN KEY (assigned_to) REFERENCES public.sys_users(id),
	CONSTRAINT fkqds0i5koq928gvt8y4lnh01s2 FOREIGN KEY (archived_by) REFERENCES public.sys_users(id),
	CONSTRAINT fks8vu7id8569qq0b765d9gqtv7 FOREIGN KEY (added_by) REFERENCES public.sys_users(id)
);


-- public.two_factor_auth_setting definition

-- Drop table

-- DROP TABLE public.two_factor_auth_setting;

CREATE TABLE public.two_factor_auth_setting (
	id int8 NOT NULL,
	last_audit_details varchar(4000) NULL,
	two_factor_action varchar(255) NOT NULL,
	updated_on timestamp NULL,
	updated_by int8 NULL,
	CONSTRAINT two_factor_auth_setting_pkey PRIMARY KEY (id),
	CONSTRAINT fkqbkcj4i6buoij9mbuxpv3djkk FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.user_audit_logs definition

-- Drop table

-- DROP TABLE public.user_audit_logs;

CREATE TABLE public.user_audit_logs (
	id int8 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
	device_info varchar(1024) NULL,
	login_time timestamp NOT NULL,
	logout_time timestamp NULL,
	user_id int8 NOT NULL,
	CONSTRAINT user_audit_logs_pkey PRIMARY KEY (id),
	CONSTRAINT fk6e84oqxxa3xfa5cmvrm3i8l9t FOREIGN KEY (user_id) REFERENCES public.sys_users(id)
);


-- public.user_custom_attributes definition

-- Drop table

-- DROP TABLE public.user_custom_attributes;

CREATE TABLE public.user_custom_attributes (
	user_id int8 NOT NULL,
	description varchar(250) NULL,
	"key" varchar(50) NULL,
	value varchar(1000) NULL,
	CONSTRAINT fka7ye9knvueuqpxrj9xbr0j2sj FOREIGN KEY (user_id) REFERENCES public.sys_users(id)
);


-- public.validation_result definition

-- Drop table

-- DROP TABLE public.validation_result;

CREATE TABLE public.validation_result (
	id int8 NOT NULL,
	actual_value varchar(255) NULL,
	detail_no varchar(255) NULL,
	expected_value varchar(255) NULL,
	field_name varchar(255) NULL,
	iteration_no int4 NOT NULL,
	page_name varchar(255) NULL,
	remarks varchar(255) NULL,
	"result" varchar(255) NULL,
	exe_iterable_run_id int8 NOT NULL,
	CONSTRAINT validation_result_pkey PRIMARY KEY (id),
	CONSTRAINT fk9vgwygauuuy1onbpq3mkgjmhs FOREIGN KEY (exe_iterable_run_id) REFERENCES public.executable_iteration_run(id)
);


-- public.workflow_status_transition definition

-- Drop table

-- DROP TABLE public.workflow_status_transition;

CREATE TABLE public.workflow_status_transition (
	id int8 NOT NULL,
	created_on timestamp NULL,
	updated_on timestamp NULL,
	reviewer_remark_required bool NULL,
	transi_tion_id int8 NULL,
	transition varchar(255) NULL,
	testcase_created_by int8 NULL,
	testcase_updated_by int8 NULL,
	workflow_status_id int8 NULL,
	CONSTRAINT workflow_status_transition_pkey PRIMARY KEY (id),
	CONSTRAINT fkkxbsitbxcoy6xbhkrcgyc0xtv FOREIGN KEY (testcase_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkn9x9i8hcsnnhys1rakpuqbrfx FOREIGN KEY (workflow_status_id) REFERENCES public.tdes_work_flow_status(id),
	CONSTRAINT fkpq207wbs0shwhh3oqq8vy09d8 FOREIGN KEY (testcase_updated_by) REFERENCES public.sys_users(id)
);


-- public.agents definition

-- Drop table

-- DROP TABLE public.agents;

CREATE TABLE public.agents (
	id int8 NOT NULL,
	agent_created_on timestamp NOT NULL,
	agent_updated_on timestamp NULL,
	automation_token varchar(4000) NULL,
	agent_device_farm_flag bool NULL,
	agent_device_farm_key varchar(255) NULL,
	agent_device_farm_password varchar(255) NULL,
	agent_device_farm_username varchar(255) NULL,
	agent_host_name varchar(255) NULL,
	last_ping_time timestamp NULL,
	agent_name varchar(255) NOT NULL,
	agent_os_type varchar(255) NULL,
	agent_port varchar(255) NULL,
	status varchar(255) NOT NULL,
	agent_created_by int8 NULL,
	agent_updated_by int8 NULL,
	CONSTRAINT agents_pkey PRIMARY KEY (id),
	CONSTRAINT uk_o331sjwmx9kd1c9nd2ddjhxd9 UNIQUE (agent_name),
	CONSTRAINT fk3vfivpg101138tnkaogevca85 FOREIGN KEY (agent_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkpyd984dhsk1nfktreiq7wjw6n FOREIGN KEY (agent_updated_by) REFERENCES public.sys_users(id)
);


-- public.ai_yts_llm_memorys definition

-- Drop table

-- DROP TABLE public.ai_yts_llm_memorys;

CREATE TABLE public.ai_yts_llm_memorys (
	thread_id varchar(255) NOT NULL,
	memory text NULL,
	created_on timestamp NULL,
	CONSTRAINT ai_yts_llm_memorys_pkey PRIMARY KEY (thread_id),
	CONSTRAINT ai_yts_llm_memorys_thread_id_fkey FOREIGN KEY (thread_id) REFERENCES public.ai_yts_llm_threads(id)
);


-- public.application definition

-- Drop table

-- DROP TABLE public.application;

CREATE TABLE public.application (
	id int8 NOT NULL,
	adapter varchar(255) NULL,
	app_activity varchar(255) NULL,
	app_package varchar(255) NULL,
	created_on timestamp NULL,
	updated_on timestamp NULL,
	browser varchar(255) NULL,
	app_bundle_id varchar(255) NULL,
	connection_host_url varchar(255) NULL,
	connection_type int8 NULL,
	database_key varchar(255) NULL,
	database_port int8 NULL,
	database_service_sid varchar(255) NULL,
	database_type varchar(255) NULL,
	database_user_name varchar(255) NULL,
	date_format varchar(255) NULL,
	description varchar(4000) NULL,
	"name" varchar(255) NOT NULL,
	parallel_sessions_allowed bool NOT NULL,
	pause_location varchar(255) NULL,
	pause_time int4 NULL,
	server_type int8 NULL,
	ttd_password varchar(255) NULL,
	"type" varchar(255) NOT NULL,
	url varchar(255) NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	file_path varchar(255) NULL,
	CONSTRAINT application_pkey PRIMARY KEY (id),
	CONSTRAINT uk_lspnba25gpku3nx3oecprrx8c UNIQUE (name),
	CONSTRAINT fk663b4314jtq5aifqwfp64yo8i FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fktoepe9h8h9pqm7h0wqk2ui4w7 FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.application_operation definition

-- Drop table

-- DROP TABLE public.application_operation;

CREATE TABLE public.application_operation (
	application_id int8 NOT NULL,
	operation varchar(255) NULL,
	CONSTRAINT fkhfx3dt2opa2ppppy4ljiwxbof FOREIGN KEY (application_id) REFERENCES public.application(id)
);


-- public.application_user_type definition

-- Drop table

-- DROP TABLE public.application_user_type;

CREATE TABLE public.application_user_type (
	application_id int8 NOT NULL,
	user_type varchar(255) NULL,
	CONSTRAINT fkrw8v2bbdlxwra2fy2sd7oukox FOREIGN KEY (application_id) REFERENCES public.application(id)
);


-- public.archived_steps_suite definition

-- Drop table

-- DROP TABLE public.archived_steps_suite;

CREATE TABLE public.archived_steps_suite (
	test_suite_instance_id int8 NOT NULL,
	archived_by int8 NULL,
	archived_on timestamp NULL,
	step_id int8 NULL,
	CONSTRAINT fkbc7bfetpylsg95qghwqtj67ay FOREIGN KEY (archived_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkclxg6bdk21w2lw6g3aj3u0ff2 FOREIGN KEY (test_suite_instance_id) REFERENCES public.test_suite_instance(id)
);


-- public.aut_functions definition

-- Drop table

-- DROP TABLE public.aut_functions;

CREATE TABLE public.aut_functions (
	id int8 NOT NULL,
	created_on timestamp NULL,
	date_format varchar(255) NULL,
	function_code varchar(255) NOT NULL,
	function_menu varchar(4000) NULL,
	function_name varchar(255) NOT NULL,
	login_required bool NULL,
	query_param varchar(255) NULL,
	updated_on timestamp NULL,
	api_operation_id int8 NULL,
	application int8 NOT NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	CONSTRAINT aut_functions_pkey PRIMARY KEY (id),
	CONSTRAINT fk5d8xjtdbvu9w5ixjrmreda0sf FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk8756ytey72kf8rpcxei8ksab7 FOREIGN KEY (application) REFERENCES public.application(id),
	CONSTRAINT fkevphi694bq9yevluake19an3s FOREIGN KEY (api_operation_id) REFERENCES public.aut_api_operation(id),
	CONSTRAINT fkljs30uhndix3jfon4ncv7vmu1 FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.aut_pageareas definition

-- Drop table

-- DROP TABLE public.aut_pageareas;

CREATE TABLE public.aut_pageareas (
	id int8 NOT NULL,
	app_id int8 NULL,
	created_on timestamp NULL,
	function_id varchar(255) NULL,
	multi_record_block bool NOT NULL,
	"path" varchar(255) NULL,
	"sequence" int8 NULL,
	title varchar(255) NOT NULL,
	"type" varchar(255) NULL,
	updated_on timestamp NULL,
	uri varchar(255) NULL,
	created_by int8 NULL,
	page_locator int8 NULL,
	updated_by int8 NULL,
	CONSTRAINT aut_pageareas_pkey PRIMARY KEY (id),
	CONSTRAINT fk6t48wncnryai7osfndos1lrv FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkj3l84mp2jy7un1rnipp37d6e0 FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkqfi4188ya1fkou6mwmjtvpjb5 FOREIGN KEY (page_locator) REFERENCES public.page_area_locator(id)
);


-- public.aut_pym_message definition

-- Drop table

-- DROP TABLE public.aut_pym_message;

CREATE TABLE public.aut_pym_message (
	id int8 NOT NULL,
	message_created_on timestamp NULL,
	file_content oid NULL,
	filename varchar(255) NOT NULL,
	format varchar(255) NOT NULL,
	message_name varchar(255) NOT NULL,
	message_type varchar(255) NOT NULL,
	message_updated_on timestamp NULL,
	application_id int8 NOT NULL,
	message_created_by int8 NOT NULL,
	message_updated_by int8 NULL,
	CONSTRAINT aut_pym_message_pkey PRIMARY KEY (id),
	CONSTRAINT fk1hv4uuq5mk4psc0b0b33ih9lt FOREIGN KEY (message_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkja0762wk6fy7u8l6fgw6mj7ve FOREIGN KEY (message_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkkd300neuqr19xvj0gm7s3ydgv FOREIGN KEY (application_id) REFERENCES public.application(id)
);


-- public.aut_user_credentials definition

-- Drop table

-- DROP TABLE public.aut_user_credentials;

CREATE TABLE public.aut_user_credentials (
	id int8 NOT NULL,
	app_login_key varchar(4000) NOT NULL,
	app_login_name varchar(255) NOT NULL,
	app_transacation_key varchar(255) NULL,
	created_on timestamp NOT NULL,
	updated_on timestamp NULL,
	user_type varchar(255) NOT NULL,
	app_id int8 NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	user_id int8 NULL,
	CONSTRAINT aut_user_credentials_pkey PRIMARY KEY (id),
	CONSTRAINT fkh3y3upnph10dql75dys5x6m51 FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkhljy78tiivc605ac5j7mqtnc0 FOREIGN KEY (user_id) REFERENCES public.sys_users(id),
	CONSTRAINT fknq0ulo5u636wrym7qgo971e4s FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkppt3xeydov18085ht1npt4118 FOREIGN KEY (app_id) REFERENCES public.application(id)
);


-- public.backlog_qa_analysis definition

-- Drop table

-- DROP TABLE public.backlog_qa_analysis;

CREATE TABLE public.backlog_qa_analysis (
	id int8 NOT NULL,
	answered_on timestamp NULL,
	backlog_id int8 NOT NULL,
	qa_response_id varchar(255) NOT NULL,
	answered_by int8 NULL,
	CONSTRAINT backlog_qa_analysis_pkey PRIMARY KEY (id),
	CONSTRAINT fk973fk3qxnmr193hly021wcaxc FOREIGN KEY (answered_by) REFERENCES public.sys_users(id)
);


-- public.backlog_risk_analysis definition

-- Drop table

-- DROP TABLE public.backlog_risk_analysis;

CREATE TABLE public.backlog_risk_analysis (
	id int8 NOT NULL,
	answered_on timestamp NULL,
	backlog_id int8 NOT NULL,
	response_id varchar(255) NULL,
	answered_by int8 NULL,
	CONSTRAINT backlog_risk_analysis_pkey PRIMARY KEY (id),
	CONSTRAINT fk94duxoxkukctst9robdv3osq0 FOREIGN KEY (answered_by) REFERENCES public.sys_users(id)
);


-- public.deleted_agents_log definition

-- Drop table

-- DROP TABLE public.deleted_agents_log;

CREATE TABLE public.deleted_agents_log (
	id int8 NOT NULL,
	agent_id int8 NOT NULL,
	agent_name varchar(255) NULL,
	deleted_on timestamp NULL,
	deleted_by int8 NULL,
	CONSTRAINT deleted_agents_log_pkey PRIMARY KEY (id),
	CONSTRAINT uk_86dfm5s1b0jsl4fpsp4gm4qwd UNIQUE (agent_id),
	CONSTRAINT fk47r528y7l7r1wy957897wqmuj FOREIGN KEY (deleted_by) REFERENCES public.sys_users(id)
);


-- public.deleted_backlog_audit definition

-- Drop table

-- DROP TABLE public.deleted_backlog_audit;

CREATE TABLE public.deleted_backlog_audit (
	id int8 NOT NULL,
	backlog_id int8 NULL,
	deleted_on timestamp NULL,
	identifier varchar(255) NULL,
	project_id int8 NULL,
	project_name varchar(255) NULL,
	deleted_by int8 NULL,
	CONSTRAINT deleted_backlog_audit_pkey PRIMARY KEY (id),
	CONSTRAINT fk50oxa7p2rlv1s37ktj6v6xt4g FOREIGN KEY (deleted_by) REFERENCES public.sys_users(id)
);


-- public.deleted_cases_log definition

-- Drop table

-- DROP TABLE public.deleted_cases_log;

CREATE TABLE public.deleted_cases_log (
	id int8 NOT NULL,
	deleted_on timestamp NOT NULL,
	project_name varchar(255) NOT NULL,
	summary varchar(255) NULL,
	testcase_id varchar(255) NOT NULL,
	deleted_by int8 NULL,
	CONSTRAINT deleted_cases_log_pkey PRIMARY KEY (id),
	CONSTRAINT fkn8vtwn9301ohrljorcws75uya FOREIGN KEY (deleted_by) REFERENCES public.sys_users(id)
);


-- public.deleted_external_interface_audit definition

-- Drop table

-- DROP TABLE public.deleted_external_interface_audit;

CREATE TABLE public.deleted_external_interface_audit (
	id int8 NOT NULL,
	deleted_on timestamp NULL,
	ext_interface_id int8 NOT NULL,
	ext_interface_name varchar(255) NULL,
	project_id int8 NULL,
	deleted_by int8 NULL,
	CONSTRAINT deleted_external_interface_audit_pkey PRIMARY KEY (id),
	CONSTRAINT fkkjcxpb8w1othl8pltdcjepolb FOREIGN KEY (deleted_by) REFERENCES public.sys_users(id)
);


-- public.deleted_folder_log definition

-- Drop table

-- DROP TABLE public.deleted_folder_log;

CREATE TABLE public.deleted_folder_log (
	id int8 NOT NULL,
	deleted_on timestamp NOT NULL,
	folder_name varchar(255) NOT NULL,
	project_name varchar(255) NOT NULL,
	deleted_by int8 NULL,
	CONSTRAINT deleted_folder_log_pkey PRIMARY KEY (id),
	CONSTRAINT fkieaikxso5ugjgni8mspyxlsjp FOREIGN KEY (deleted_by) REFERENCES public.sys_users(id)
);


-- public.deleted_functions_log definition

-- Drop table

-- DROP TABLE public.deleted_functions_log;

CREATE TABLE public.deleted_functions_log (
	id int8 NOT NULL,
	confirmation_msg varchar(255) NULL,
	deleted_on timestamp NULL,
	function_code varchar(255) NULL,
	function_id int8 NULL,
	deleted_by int8 NULL,
	CONSTRAINT deleted_functions_log_pkey PRIMARY KEY (id),
	CONSTRAINT fkdf8itboq41bsmy7x3e0hospk6 FOREIGN KEY (deleted_by) REFERENCES public.sys_users(id)
);


-- public.deleted_steps_log definition

-- Drop table

-- DROP TABLE public.deleted_steps_log;

CREATE TABLE public.deleted_steps_log (
	id int8 NOT NULL,
	deleted_on timestamp NOT NULL,
	project_name varchar(255) NOT NULL,
	testcase_id varchar(255) NOT NULL,
	deleted_by int8 NULL,
	CONSTRAINT deleted_steps_log_pkey PRIMARY KEY (id),
	CONSTRAINT fk6dt28cxhp7eokhvjc4dng89vn FOREIGN KEY (deleted_by) REFERENCES public.sys_users(id)
);


-- public.email_configuration definition

-- Drop table

-- DROP TABLE public.email_configuration;

CREATE TABLE public.email_configuration (
	id int8 NOT NULL,
	authentication_required bool NULL,
	created_on timestamp NULL,
	email varchar(255) NOT NULL,
	"password" varchar(4000) NULL,
	port int8 NOT NULL,
	smtp_server varchar(255) NOT NULL,
	updated_on timestamp NULL,
	user_name varchar(255) NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	CONSTRAINT email_configuration_pkey PRIMARY KEY (id),
	CONSTRAINT fk9cnc6brqeqx4vh7wk027dsm28 FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkpbcims7d1qleylo6v35jety92 FOREIGN KEY (created_by) REFERENCES public.sys_users(id)
);


-- public.exe_test_runs definition

-- Drop table

-- DROP TABLE public.exe_test_runs;

CREATE TABLE public.exe_test_runs (
	id int8 NOT NULL,
	archived_on timestamp NULL,
	created_on timestamp NOT NULL,
	elapsed_time int8 NULL,
	end_time timestamp NULL,
	execution_entity varchar(255) NULL,
	execution_entity_id int8 NULL,
	is_archived bool NOT NULL,
	project_id int8 NULL,
	"result" varchar(255) NOT NULL,
	start_time timestamp NULL,
	completion_status varchar(255) NOT NULL,
	test_cycle_id int8 NULL,
	archived_by int8 NULL,
	started_by int8 NOT NULL,
	cloud_provider_configuration text NULL,
	automation_framework varchar(255) NULL,
	is_external bool NULL,
	"name" varchar(255) NULL,
	scm_info oid NULL,
	CONSTRAINT exe_test_runs_pkey PRIMARY KEY (id),
	CONSTRAINT fk773eoux5a4kaa0awkpifi3aom FOREIGN KEY (started_by) REFERENCES public.sys_users(id),
	CONSTRAINT fklmmfl4q1869hfi4nkn71qm93b FOREIGN KEY (archived_by) REFERENCES public.sys_users(id)
);


-- public.external_interface definition

-- Drop table

-- DROP TABLE public.external_interface;

CREATE TABLE public.external_interface (
	id int8 NOT NULL,
	created_on timestamp NULL,
	email varchar(255) NULL,
	"name" varchar(255) NOT NULL,
	"password" varchar(4000) NOT NULL,
	updated_on timestamp NULL,
	url varchar(255) NOT NULL,
	username varchar(255) NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	avatar varchar(1000) NULL,
	"type" varchar(50) NOT NULL,
	CONSTRAINT external_interface_pkey PRIMARY KEY (id),
	CONSTRAINT uk_p89jf95lnvjhmo3c8pgacma2x UNIQUE (name),
	CONSTRAINT fkn4re7t3xbifxvyd48brq1s13l FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkn8y3xg7ki0jfcqdhyfbxcht0b FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.function_user_defined_values definition

-- Drop table

-- DROP TABLE public.function_user_defined_values;

CREATE TABLE public.function_user_defined_values (
	function_id int8 NOT NULL,
	description varchar(250) NULL,
	"key" varchar(50) NOT NULL,
	value varchar(1000) NOT NULL,
	CONSTRAINT fke5h7cspkd50c4isnndoi0yo48 FOREIGN KEY (function_id) REFERENCES public.aut_functions(id)
);


-- public.mastest_runs definition

-- Drop table

-- DROP TABLE public.mastest_runs;

CREATE TABLE public.mastest_runs (
	id int8 NOT NULL,
	browser_type varchar(255) NULL,
	parent_run_id int8 NULL,
	run_created_on timestamp NOT NULL,
	run_end_time timestamp NULL,
	run_start_time timestamp NULL,
	run_updated_on timestamp NULL,
	status varchar(255) NULL,
	task_type varchar(255) NULL,
	agent int8 NULL,
	app_id int8 NOT NULL,
	run_initiated_by int8 NULL,
	run_updated_by int8 NULL,
	executed_by int8 NULL,
	agent_snapshot oid NULL,
	agent_copy int8 NULL,
	CONSTRAINT mastest_runs_pkey PRIMARY KEY (id),
	CONSTRAINT fkcj6xjfnfhw6fluyvy55j55y31 FOREIGN KEY (agent) REFERENCES public.agents(id),
	CONSTRAINT fkd7hyge2dt49jtcso9k97ll65w FOREIGN KEY (run_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkgm51q9375g7uft99dq8r0p4vj FOREIGN KEY (run_initiated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkk59bd32k1i1tue8mxgs9upqtj FOREIGN KEY (app_id) REFERENCES public.application(id),
	CONSTRAINT fkocrw18c0yp8ej2a90il4grp0g FOREIGN KEY (executed_by) REFERENCES public.sys_users(id)
);


-- public.password_reset_requests definition

-- Drop table

-- DROP TABLE public.password_reset_requests;

CREATE TABLE public.password_reset_requests (
	id int8 NOT NULL,
	completed bool NOT NULL,
	expires_at timestamp NULL,
	requested_at timestamp NULL,
	reset_token varchar(255) NOT NULL,
	user_id int8 NULL,
	CONSTRAINT password_reset_requests_pkey PRIMARY KEY (id),
	CONSTRAINT uk_6pfbf82kk9dgir9ldts9fy1jb UNIQUE (reset_token),
	CONSTRAINT fkltrhaqnrgdiqpfxjjuf3c5bbv FOREIGN KEY (user_id) REFERENCES public.sys_users(id)
);


-- public.prj_execution_application definition

-- Drop table

-- DROP TABLE public.prj_execution_application;

CREATE TABLE public.prj_execution_application (
	id int8 NOT NULL,
	application_id int8 NULL,
	created_on timestamp NULL,
	entity_id int8 NULL,
	entity_type varchar(255) NULL,
	updated_on timestamp NULL,
	custom_url varchar(255) NOT NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	CONSTRAINT prj_execution_application_pkey PRIMARY KEY (id),
	CONSTRAINT fk8xantqd3kfn47ds74enb3yceq FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fknw1ilaklj3wsybmwtjihlr5ej FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.prj_priority definition

-- Drop table

-- DROP TABLE public.prj_priority;

CREATE TABLE public.prj_priority (
	id int8 NOT NULL,
	description varchar(4000) NULL,
	priority_created_on timestamp NOT NULL,
	priority_name varchar(255) NOT NULL,
	priority_updated_on timestamp NULL,
	priority_created_by int8 NULL,
	priority_updated_by int8 NULL,
	CONSTRAINT prj_priority_pkey PRIMARY KEY (id),
	CONSTRAINT uk_j0ljyup4deun0656ibe9t86l7 UNIQUE (priority_name),
	CONSTRAINT fk6kx5e8x83dqebuf0l1dmntxf7 FOREIGN KEY (priority_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkeg68kd9tla7lkpygo2c9q8cls FOREIGN KEY (priority_updated_by) REFERENCES public.sys_users(id)
);


-- public.prj_program definition

-- Drop table

-- DROP TABLE public.prj_program;

CREATE TABLE public.prj_program (
	id int8 NOT NULL,
	description varchar(4000) NULL,
	end_date date NULL,
	program_created_on timestamp NOT NULL,
	program_name varchar(255) NOT NULL,
	program_updated_on timestamp NULL,
	program_project_count int8 NULL,
	start_date date NULL,
	"owner" int8 NULL,
	program_created_by int8 NULL,
	program_updated_by int8 NULL,
	CONSTRAINT prj_program_pkey PRIMARY KEY (id),
	CONSTRAINT uk_7hhbpr5rpa5qovmi21qcgnmd6 UNIQUE (program_name),
	CONSTRAINT fk70b1yniat9uam8ldqmd0eyvaj FOREIGN KEY (program_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk9m77c4o8753y7p8jgju7hln68 FOREIGN KEY (program_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkthbtfr8ya3uojap46cdoycq2j FOREIGN KEY ("owner") REFERENCES public.sys_users(id)
);


-- public.prj_project definition

-- Drop table

-- DROP TABLE public.prj_project;

CREATE TABLE public.prj_project (
	id int8 NOT NULL,
	created_on timestamp NULL,
	prj_name varchar(255) NOT NULL,
	prj_key varchar(255) NOT NULL,
	repopath varchar(255) NULL,
	status bool NULL,
	updated_on timestamp NULL,
	prj_created_by int8 NOT NULL,
	prj_ext_interface int8 NULL,
	prj_lead int8 NULL,
	prj_program int8 NULL,
	prj_updated_by int8 NULL,
	CONSTRAINT prj_project_pkey PRIMARY KEY (id),
	CONSTRAINT uk_65mxr7juf6o386mw5r4oeomuj UNIQUE (prj_key),
	CONSTRAINT fk49mmq8i0kt13o7cgin99kq21l FOREIGN KEY (prj_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fka9q3kb0tf3myq18ojraxlh4uo FOREIGN KEY (prj_lead) REFERENCES public.sys_users(id),
	CONSTRAINT fkinmtyk8wrrrcd7c3jjkh3ur6n FOREIGN KEY (prj_program) REFERENCES public.prj_program(id),
	CONSTRAINT fkpy3t7t444y509i4im9ippajhy FOREIGN KEY (prj_ext_interface) REFERENCES public.external_interface(id),
	CONSTRAINT fkswtfvga0qy2uumkj2aejfwpju FOREIGN KEY (prj_updated_by) REFERENCES public.sys_users(id)
);


-- public.prj_reports definition

-- Drop table

-- DROP TABLE public.prj_reports;

CREATE TABLE public.prj_reports (
	id int8 NOT NULL,
	created_on timestamp NULL,
	description varchar(4000) NULL,
	entity varchar(255) NOT NULL,
	manifest oid NOT NULL,
	test_cycle_id int8 NULL,
	title varchar(255) NOT NULL,
	updated_on timestamp NULL,
	created_by int8 NULL,
	project_id int8 NOT NULL,
	updated_by int8 NULL,
	entity_id int8 NULL,
	CONSTRAINT prj_reports_pkey PRIMARY KEY (id),
	CONSTRAINT fk3yljwin8kwfcpev1jm7ikpest FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkov7gll8lev4vccaoe7la7ix4p FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkppa18n9ta12109hfejbapfn5j FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.prj_roles definition

-- Drop table

-- DROP TABLE public.prj_roles;

CREATE TABLE public.prj_roles (
	id int8 NOT NULL,
	created_on timestamp NOT NULL,
	description varchar(255) NULL,
	project_role_name varchar(255) NULL,
	updated_on timestamp NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	CONSTRAINT prj_roles_pkey PRIMARY KEY (id),
	CONSTRAINT uk_515l8tiy8gejjpj3ma78omfuy UNIQUE (project_role_name),
	CONSTRAINT fk8qnffubu82bfb5cc1sfelov9k FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkq3ecql6vx6qwc0y8n8u2v42ur FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.prj_testdata_upload_list definition

-- Drop table

-- DROP TABLE public.prj_testdata_upload_list;

CREATE TABLE public.prj_testdata_upload_list (
	id int8 NOT NULL,
	app_id int8 NOT NULL,
	app_name varchar(255) NOT NULL,
	download_url varchar(255) NULL,
	file_content oid NULL,
	file_name varchar(255) NULL,
	function_code varchar(255) NULL,
	function_id int8 NULL,
	modified_on timestamp NULL,
	uploaded_on timestamp NULL,
	project_id int8 NOT NULL,
	remarks_error_message varchar(255) NULL,
	state varchar(255) NULL,
	test_bot_id int8 NULL,
	"type" varchar(255) NULL,
	user_id int8 NOT NULL,
	application_id int8 NULL,
	CONSTRAINT prj_testdata_upload_list_pkey PRIMARY KEY (id),
	CONSTRAINT fk1jj6ug0eci1ailkrw6etd0n7x FOREIGN KEY (application_id) REFERENCES public.application(id)
);


-- public.prj_users definition

-- Drop table

-- DROP TABLE public.prj_users;

CREATE TABLE public.prj_users (
	id int8 NOT NULL,
	prj_mapped int8 NULL,
	prj_role_mapped int8 NULL,
	prj_user_mapped int8 NULL,
	CONSTRAINT prj_users_pkey PRIMARY KEY (id),
	CONSTRAINT fk5uciv43ndxcxstxbep2uwtnru FOREIGN KEY (prj_mapped) REFERENCES public.prj_project(id),
	CONSTRAINT fkc25jd7ah2dweeumebq81lvjyy FOREIGN KEY (prj_role_mapped) REFERENCES public.prj_roles(id),
	CONSTRAINT fkdp6e7ycyq1ln1x8yd88atq5cj FOREIGN KEY (prj_user_mapped) REFERENCES public.sys_users(id)
);


-- public.project_aut_files definition

-- Drop table

-- DROP TABLE public.project_aut_files;

CREATE TABLE public.project_aut_files (
	id int8 NOT NULL,
	cloud_url varchar(255) NULL,
	file_hash varchar(255) NULL,
	file_path varchar(255) NULL,
	is_uploaded bool NULL,
	uploaded_by varchar(255) NULL,
	uploaded_on timestamp NULL,
	application_id int8 NOT NULL,
	project_id int8 NOT NULL,
	CONSTRAINT project_aut_files_pkey PRIMARY KEY (id),
	CONSTRAINT fkk3kp7kno1kr25hqn546k7c3ky FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkmyco00i17mhouwt65wogoqrpq FOREIGN KEY (application_id) REFERENCES public.application(id)
);


-- public.project_roles_permissions definition

-- Drop table

-- DROP TABLE public.project_roles_permissions;

CREATE TABLE public.project_roles_permissions (
	id int8 NOT NULL,
	prjrolepermission varchar(1000) NULL,
	projectrole_id int8 NULL,
	project_id int8 NULL,
	CONSTRAINT project_roles_permissions_pkey PRIMARY KEY (id),
	CONSTRAINT fkg7mvikrmmuqi4igbvt0x4389f FOREIGN KEY (projectrole_id) REFERENCES public.prj_roles(id),
	CONSTRAINT fkp2b4yv3pg5u0l9vjr3v7x000x FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public.qa_analysis definition

-- Drop table

-- DROP TABLE public.qa_analysis;

CREATE TABLE public.qa_analysis (
	backlog_qa_analysis_id int8 NOT NULL,
	answer oid NULL,
	id varchar(255) NULL,
	question oid NULL,
	CONSTRAINT fk1ca0ryluylwmr9dnbbexuicyh FOREIGN KEY (backlog_qa_analysis_id) REFERENCES public.backlog_qa_analysis(id)
);


-- public.report_configuration definition

-- Drop table

-- DROP TABLE public.report_configuration;

CREATE TABLE public.report_configuration (
	id int8 NOT NULL,
	updated_on timestamp NULL,
	project_id int8 NOT NULL,
	updated_by int8 NULL,
	CONSTRAINT report_configuration_pkey PRIMARY KEY (id),
	CONSTRAINT fk3ux8h16wwbad6wlhr79m6is1c FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkije985abvrjcwqo4guqppru83 FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public.report_configuration_classification_types definition

-- Drop table

-- DROP TABLE public.report_configuration_classification_types;

CREATE TABLE public.report_configuration_classification_types (
	report_configuration_id int8 NOT NULL,
	classification_type varchar(255) NULL,
	CONSTRAINT fkpsxhjkkywqybsssx1dl0yhftu FOREIGN KEY (report_configuration_id) REFERENCES public.report_configuration(id)
);


-- public.report_configuration_properties definition

-- Drop table

-- DROP TABLE public.report_configuration_properties;

CREATE TABLE public.report_configuration_properties (
	report_configuration_id int8 NOT NULL,
	property varchar(255) NULL,
	CONSTRAINT fkqamn83n1vedrf86q7s34gue5t FOREIGN KEY (report_configuration_id) REFERENCES public.report_configuration(id)
);


-- public.risk_analysis definition

-- Drop table

-- DROP TABLE public.risk_analysis;

CREATE TABLE public.risk_analysis (
	backlog_risk_analysis_id int8 NOT NULL,
	id varchar(255) NULL,
	risk_level int4 NULL,
	question oid NULL,
	reason oid NULL,
	risk_status int4 NULL,
	"type" varchar(255) NULL,
	CONSTRAINT fkqf1toto0s233p07pua5ragy0l FOREIGN KEY (backlog_risk_analysis_id) REFERENCES public.backlog_risk_analysis(id)
);


-- public.sys_apitokens definition

-- Drop table

-- DROP TABLE public.sys_apitokens;

CREATE TABLE public.sys_apitokens (
	id int8 NOT NULL,
	"label" varchar(50) NOT NULL,
	"token" varchar(4000) NOT NULL,
	token_created timestamp NOT NULL,
	user_id int8 NULL,
	CONSTRAINT sys_apitokens_pkey PRIMARY KEY (id),
	CONSTRAINT uk_mrdrgeahalr816f2nurc66193 UNIQUE (token),
	CONSTRAINT fkd4ygnuo9owpn14ssgdug6njui FOREIGN KEY (user_id) REFERENCES public.sys_users(id)
);


-- public.sys_backlog_contributors definition

-- Drop table

-- DROP TABLE public.sys_backlog_contributors;

CREATE TABLE public.sys_backlog_contributors (
	id int8 NOT NULL,
	external_avatars oid NULL,
	email varchar(255) NULL,
	"external" bool NOT NULL,
	external_identifier varchar(255) NULL,
	"name" varchar(255) NULL,
	internal_user_id int8 NULL,
	CONSTRAINT sys_backlog_contributors_pkey PRIMARY KEY (id),
	CONSTRAINT fk1kf36yudxktn2s26m4ps534kv FOREIGN KEY (internal_user_id) REFERENCES public.sys_users(id)
);


-- public.tdes_folder definition

-- Drop table

-- DROP TABLE public.tdes_folder;

CREATE TABLE public.tdes_folder (
	id int8 NOT NULL,
	created_on timestamp NULL,
	description varchar(4000) NULL,
	exec_productivity_indicator int8 NULL,
	likelihood_indicator int4 NULL,
	"name" varchar(255) NOT NULL,
	risk_indicator int4 NULL,
	risk_score int4 NULL,
	updated_on timestamp NULL,
	fld_created_by int8 NULL,
	parent_id int8 NULL,
	project_id int8 NOT NULL,
	classification int8 NOT NULL,
	fld_updated_by int8 NULL,
	"path" varchar(255) NULL,
	CONSTRAINT tdes_folder_pkey PRIMARY KEY (id),
	CONSTRAINT fk3vemgbp7pvvwu71u6kap7l2fd FOREIGN KEY (parent_id) REFERENCES public.tdes_folder(id),
	CONSTRAINT fkcv6t3pg1ep4r3xf4jswng7rjf FOREIGN KEY (fld_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkfvqqwoqt01qfep2r3l2xey20l FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkjbomfur058n0haeautnbk8kde FOREIGN KEY (fld_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkm7ok9w3d2gtljohfa521oj7rv FOREIGN KEY (classification) REFERENCES public.tdes_classification_types(id)
);


-- public.tdes_test_cases definition

-- Drop table

-- DROP TABLE public.tdes_test_cases;

CREATE TABLE public.tdes_test_cases (
	id int8 NOT NULL,
	created_on timestamp NULL,
	updated_on timestamp NULL,
	description varchar(4000) NULL,
	run_type int4 NULL,
	pre_requisites varchar(4000) NULL,
	record_id varchar(255) NULL,
	risk_score int4 NULL,
	summary varchar(255) NULL,
	test_case_id varchar(255) NULL,
	test_case_type varchar(255) NULL,
	testcase_created_by int8 NULL,
	testcase_updated_by int8 NULL,
	project_id int8 NULL,
	aut int8 NULL,
	complexity_id int8 NULL,
	parent_folder_id int8 NULL,
	priority_id int8 NULL,
	test_type_id int8 NULL,
	work_flow_status_id int8 NULL,
	automation_source_identifier varchar(255) NULL,
	is_external bool DEFAULT false NULL,
	project_sequence int8 NULL,
	CONSTRAINT tdes_test_cases_pkey PRIMARY KEY (id),
	CONSTRAINT fk3598sg167r93i6ndeo37ppq0n FOREIGN KEY (testcase_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk5mjp2yj99crq4umlmnqd2f4l6 FOREIGN KEY (complexity_id) REFERENCES public.tc_complexity(id),
	CONSTRAINT fk62bu3ljsynchd3wrfg5fxojgw FOREIGN KEY (testcase_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkbo7ju9eyssy7ntkysopj8cv9a FOREIGN KEY (work_flow_status_id) REFERENCES public.tdes_work_flow_status(id),
	CONSTRAINT fkbrgte743cqueipoi53fobwbes FOREIGN KEY (parent_folder_id) REFERENCES public.tdes_folder(id),
	CONSTRAINT fketa7lft7a3fdttn3elswestqo FOREIGN KEY (test_type_id) REFERENCES public.tdes_test_type(id),
	CONSTRAINT fkjs5slh8pvq1ln5mjsa3bcdcff FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkopyr1tmr33s8i93segh71ymll FOREIGN KEY (aut) REFERENCES public.application(id),
	CONSTRAINT fkq077bprfbnwtb3wdy5psj7br1 FOREIGN KEY (priority_id) REFERENCES public.prj_priority(id)
);


-- public.tdes_test_packs definition

-- Drop table

-- DROP TABLE public.tdes_test_packs;

CREATE TABLE public.tdes_test_packs (
	id int8 NOT NULL,
	archive bool NOT NULL,
	created_on timestamp NULL,
	description varchar(4000) NULL,
	execution_status int4 NULL,
	test_pack_name varchar(255) NOT NULL,
	non_editable bool NOT NULL,
	updated_on timestamp NULL,
	created_by int8 NULL,
	project_id int8 NOT NULL,
	test_type_id int8 NULL,
	updated_by int8 NULL,
	archived_on timestamp NULL,
	archived_by int8 NULL,
	CONSTRAINT tdes_test_packs_pkey PRIMARY KEY (id),
	CONSTRAINT fk8e03kskc9klb3ypgd1w06jbt0 FOREIGN KEY (test_type_id) REFERENCES public.tdes_test_type(id),
	CONSTRAINT fkc5nqyivdo3whpe6aaxu4oeoc4 FOREIGN KEY (archived_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkcwqa4r22h2cmcaxn3fk6g6s72 FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkder8gc42nnilgll3xbaxpxre FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkf30eunchhw83195boe5stkuwj FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public.test_case_label definition

-- Drop table

-- DROP TABLE public.test_case_label;

CREATE TABLE public.test_case_label (
	test_case_id int8 NOT NULL,
	"label" varchar(255) NULL,
	CONSTRAINT fkew8l2g7pxx5r4y4c1rf1dnlk9 FOREIGN KEY (test_case_id) REFERENCES public.tdes_test_cases(id)
);


-- public.test_step_deletion_records definition

-- Drop table

-- DROP TABLE public.test_step_deletion_records;

CREATE TABLE public.test_step_deletion_records (
	test_step_deletion_audit_history_id int8 NOT NULL,
	summary varchar(255) NULL,
	test_step_id varchar(255) NOT NULL,
	CONSTRAINT fkfn27tbavsnve1dscmyu0ukxoq FOREIGN KEY (test_step_deletion_audit_history_id) REFERENCES public.deleted_steps_log(id)
);


-- public.assisted_data definition

-- Drop table

-- DROP TABLE public.assisted_data;

CREATE TABLE public.assisted_data (
	record_id int8 NOT NULL,
	"data" varchar(255) NULL,
	field_name varchar(255) NULL,
	identified_by varchar(255) NULL,
	assisted_updated_on timestamp NULL,
	page_area_name varchar(255) NULL,
	"sequence" int8 NULL,
	app_id_id int8 NULL,
	function_code_id int8 NULL,
	assisted_updated_by int8 NULL,
	CONSTRAINT assisted_data_pkey PRIMARY KEY (record_id),
	CONSTRAINT fk6x5o53lnhmvliw4slaki2tu3v FOREIGN KEY (function_code_id) REFERENCES public.aut_functions(id),
	CONSTRAINT fko57gfqqx6w2k33h1ltgk3becu FOREIGN KEY (assisted_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fktp7ayjqibywakcqs1dk0l7hu5 FOREIGN KEY (app_id_id) REFERENCES public.application(id)
);


-- public.attachments definition

-- Drop table

-- DROP TABLE public.attachments;

CREATE TABLE public.attachments (
	id int8 NOT NULL,
	created_on timestamp NULL,
	updated_on timestamp NULL,
	attachment_uploaded_on timestamp NOT NULL,
	"data" oid NULL,
	entity varchar(255) NULL,
	entity_id int8 NULL,
	filename varchar(255) NULL,
	filetype varchar(255) NULL,
	testcase_created_by int8 NULL,
	testcase_updated_by int8 NULL,
	attachment_uploaded_by int8 NULL,
	project_id int8 NULL,
	attachment_id varchar(255) NULL,
	author varchar(255) NULL,
	CONSTRAINT attachments_pkey PRIMARY KEY (id),
	CONSTRAINT fk398uflwfen9q6bguteq7pmfmg FOREIGN KEY (testcase_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk7ntchvp2j91meq1n7alph424w FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkkmfbt03acq1omotnjr9hgcuuv FOREIGN KEY (testcase_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkmbaxovai2rcs7g2srdkekv1w1 FOREIGN KEY (attachment_uploaded_by) REFERENCES public.sys_users(id)
);


-- public.aut_fields definition

-- Drop table

-- DROP TABLE public.aut_fields;

CREATE TABLE public.aut_fields (
	id int8 NOT NULL,
	app_id int8 NULL,
	"attributes" oid NULL,
	created_on timestamp NULL,
	current_activity varchar(255) NULL,
	drop_values varchar(4000) NULL,
	function_id varchar(255) NULL,
	html_id varchar(255) NULL,
	html_name varchar(255) NULL,
	ismrbelement bool NOT NULL,
	is_mandatory bool NOT NULL,
	"label" varchar(255) NOT NULL,
	"name" varchar(255) NULL,
	parent_frame varchar(255) NULL,
	"password" bool NULL,
	"sequence" int8 NULL,
	strategy varchar(255) NULL,
	strategy_value varchar(255) NULL,
	tag varchar(255) NULL,
	title varchar(255) NULL,
	"type" varchar(255) NULL,
	updated_on timestamp NULL,
	user_remarks varchar(255) NULL,
	created_by int8 NULL,
	updated_by int8 NULL,
	fields int8 NULL,
	device_context varchar(255) NULL,
	CONSTRAINT aut_fields_pkey PRIMARY KEY (id),
	CONSTRAINT fkew6ennb0ph2utmawuhq2n5nxt FOREIGN KEY (fields) REFERENCES public.aut_pageareas(id),
	CONSTRAINT fkr5t37ndaaij9v1js6t0xiokng FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkrlcpg9px4hu4pu41lyvgawe16 FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.auto_test_bots definition

-- Drop table

-- DROP TABLE public.auto_test_bots;

CREATE TABLE public.auto_test_bots (
	id int8 NOT NULL,
	created_on timestamp NOT NULL,
	description varchar(4000) NULL,
	bot_flow oid NULL,
	"name" varchar(255) NOT NULL,
	test_bot_key varchar(255) NULL,
	test_bot_type varchar(255) NULL,
	updated_on timestamp NOT NULL,
	application_id int8 NOT NULL,
	created_by int8 NOT NULL,
	function_id int8 NULL,
	project_id int8 NULL,
	updated_by int8 NOT NULL,
	CONSTRAINT auto_test_bots_pkey PRIMARY KEY (id),
	CONSTRAINT fk13stvoiqilo665dhuswmlusso FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk24wx3gu28s58sb0xnh4112lgb FOREIGN KEY (function_id) REFERENCES public.aut_functions(id),
	CONSTRAINT fkj4urbx8473yj7hnw398jv9ptt FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkl27i82pb5ydgro3axrsdnglm4 FOREIGN KEY (application_id) REFERENCES public.application(id),
	CONSTRAINT fkpidjnvro4jc3j2fsyi6k8654t FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public."comments" definition

-- Drop table

-- DROP TABLE public."comments";

CREATE TABLE public."comments" (
	id int8 NOT NULL,
	created_on timestamp NULL,
	updated_on timestamp NULL,
	"content" oid NULL,
	entity varchar(255) NULL,
	entity_id int8 NULL,
	testcase_created_by int8 NULL,
	testcase_updated_by int8 NULL,
	project_id int8 NULL,
	author varchar(255) NULL,
	comment_id int8 NULL,
	CONSTRAINT comments_pkey PRIMARY KEY (id),
	CONSTRAINT fk4fgeqn3vts831sd6y1jqf2hh5 FOREIGN KEY (testcase_created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkf5oqaoyso6was1v568r4jax9e FOREIGN KEY (testcase_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkhu1gonv9t61ei7ajvcrg6xwdx FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public.common_project_role_permissions definition

-- Drop table

-- DROP TABLE public.common_project_role_permissions;

CREATE TABLE public.common_project_role_permissions (
	project_role_id int8 NOT NULL,
	prjpermission varchar(255) NULL,
	CONSTRAINT fk2dgn1ae6vnwu2uq3r4kfbqcad FOREIGN KEY (project_role_id) REFERENCES public.prj_roles(id)
);


-- public.exe_cloud_providers definition

-- Drop table

-- DROP TABLE public.exe_cloud_providers;

CREATE TABLE public.exe_cloud_providers (
	id int8 NOT NULL,
	access_key varchar(4000) NOT NULL,
	created_by varchar(255) NULL,
	created_on timestamp NULL,
	data_center int4 NULL,
	provider_type varchar(255) NOT NULL,
	updated_by varchar(255) NULL,
	updated_on timestamp NULL,
	user_name varchar(255) NOT NULL,
	project_id int8 NOT NULL,
	CONSTRAINT exe_cloud_providers_pkey PRIMARY KEY (id),
	CONSTRAINT fk58oixcbpjku6sgm59obbuetpm FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public.exe_defect definition

-- Drop table

-- DROP TABLE public.exe_defect;

CREATE TABLE public.exe_defect (
	id int8 NOT NULL,
	defect_key varchar(255) NULL,
	defect_url varchar(255) NULL,
	posted bool NOT NULL,
	priority varchar(255) NULL,
	raised_by varchar(255) NULL,
	raised_on varchar(255) NULL,
	status varchar(255) NULL,
	summary varchar(255) NULL,
	external_interface_id int8 NULL,
	CONSTRAINT exe_defect_pkey PRIMARY KEY (id),
	CONSTRAINT fk7gunvxuaw8r1kbxjltbndkpg1 FOREIGN KEY (external_interface_id) REFERENCES public.external_interface(id)
);


-- public.exe_scheduled_tasks definition

-- Drop table

-- DROP TABLE public.exe_scheduled_tasks;

CREATE TABLE public.exe_scheduled_tasks (
	id int8 NOT NULL,
	browser varchar(255) NULL,
	schedule_created_on timestamp NOT NULL,
	end_date timestamp NULL,
	entity varchar(255) NULL,
	entity_id int8 NULL,
	parent_task_id int8 NULL,
	recurrence_interval varchar(255) NOT NULL,
	remark varchar(255) NULL,
	settings varchar(255) NULL,
	start_date timestamp NOT NULL,
	status varchar(255) NOT NULL,
	task_name varchar(255) NOT NULL,
	test_run_id int8 NULL,
	schedule_updated_on timestamp NULL,
	schedule_created_by int8 NULL,
	project_id int8 NOT NULL,
	schedule_updated_by int8 NULL,
	CONSTRAINT exe_scheduled_tasks_pkey PRIMARY KEY (id),
	CONSTRAINT fk7gxanwqhwyvwc4h31x7pyywfy FOREIGN KEY (schedule_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkow6ww516wmdmir4xrh93mwisj FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkpefx00x9di1kxugn4olvbe9rw FOREIGN KEY (schedule_created_by) REFERENCES public.sys_users(id)
);


-- public.exe_test_run_targets definition

-- Drop table

-- DROP TABLE public.exe_test_run_targets;

CREATE TABLE public.exe_test_run_targets (
	id int8 NOT NULL,
	browser varchar(255) NULL,
	device varchar(255) NULL,
	host_name varchar(255) NOT NULL,
	port int8 NULL,
	agent_id int8 NULL,
	test_run_id int8 NOT NULL,
	agent_snapshot oid NULL,
	agent_copy int8 NULL,
	CONSTRAINT exe_test_run_targets_pkey PRIMARY KEY (id),
	CONSTRAINT fk1wymh04g7ebtycprsxeoohd0l FOREIGN KEY (agent_id) REFERENCES public.agents(id),
	CONSTRAINT fkcaw5tqqc23x3hfrr98ufwul6t FOREIGN KEY (test_run_id) REFERENCES public.exe_test_runs(id)
);


-- public.ext_external_entities definition

-- Drop table

-- DROP TABLE public.ext_external_entities;

CREATE TABLE public.ext_external_entities (
	id int8 NOT NULL,
	identifier varchar(255) NULL,
	last_sync_time timestamp NULL,
	project_id int8 NULL,
	raw_data oid NULL,
	sync_hash varchar(255) NULL,
	source_integration_id int8 NULL,
	CONSTRAINT ext_external_entities_pkey PRIMARY KEY (id),
	CONSTRAINT fk1yqeuicixblw5k2wadt1hge1d FOREIGN KEY (source_integration_id) REFERENCES public.external_interface(id)
);


-- public.ext_project_integrations definition

-- Drop table

-- DROP TABLE public.ext_project_integrations;

CREATE TABLE public.ext_project_integrations (
	id int8 NOT NULL,
	"attributes" oid NULL,
	created_on timestamp NULL,
	last_sync_remark oid NULL,
	status varchar(255) NULL,
	"type" varchar(255) NULL,
	updated_on timestamp NULL,
	prj_ext_interface int8 NULL,
	project_id int8 NOT NULL,
	is_two_way_sync bool NULL,
	CONSTRAINT ext_project_integrations_pkey PRIMARY KEY (id),
	CONSTRAINT uk1w95c9xa982ekvxpar8oqefrn UNIQUE (project_id),
	CONSTRAINT fkf5dbu5aux8gmh14r8fu3ia8w FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkp08efm0g6rgi00kcfugveggdy FOREIGN KEY (prj_ext_interface) REFERENCES public.external_interface(id)
);


-- public.field_locator definition

-- Drop table

-- DROP TABLE public.field_locator;

CREATE TABLE public.field_locator (
	id int8 NOT NULL,
	locator_altered bool NULL,
	locator_type varchar(255) NULL,
	locator_value varchar(4000) NULL,
	field_locator int8 NULL,
	CONSTRAINT field_locator_pkey PRIMARY KEY (id),
	CONSTRAINT fkdey744biv1xjrarjxqhb9xbp5 FOREIGN KEY (field_locator) REFERENCES public.aut_fields(id)
);


-- public.import_jobs definition

-- Drop table

-- DROP TABLE public.import_jobs;

CREATE TABLE public.import_jobs (
	id int8 NOT NULL,
	end_time timestamp NULL,
	file_name varchar(255) NOT NULL,
	issues_json oid NULL,
	processed_rows int8 NULL,
	remarks varchar(4000) NULL,
	"source" varchar(255) NULL,
	source_project varchar(255) NULL,
	status varchar(255) NULL,
	start_time timestamp NOT NULL,
	total_rows int8 NULL,
	started_by varchar(255) NULL,
	project_id int8 NULL,
	CONSTRAINT import_jobs_pkey PRIMARY KEY (id),
	CONSTRAINT fks70ni2157cwh7fktqjlessrji FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public.learner_audit_trail definition

-- Drop table

-- DROP TABLE public.learner_audit_trail;

CREATE TABLE public.learner_audit_trail (
	id int8 NOT NULL,
	fields_learnt_count int8 NULL,
	learning_created_on timestamp NOT NULL,
	learning_updated_on timestamp NULL,
	lrnr_end_time timestamp NULL,
	lrnr_start_time timestamp NULL,
	message varchar(4000) NULL,
	status varchar(255) NULL,
	user_type varchar(255) NULL,
	agent int8 NULL,
	function_id int8 NOT NULL,
	learning_initiated_by int8 NULL,
	learning_updated_by int8 NULL,
	run_id int8 NOT NULL,
	executed_by int8 NULL,
	agent_snapshot oid NULL,
	agent_copy int8 NULL,
	CONSTRAINT learner_audit_trail_pkey PRIMARY KEY (id),
	CONSTRAINT fk2w1oj4481ixbb8pkt6q0dyg9h FOREIGN KEY (run_id) REFERENCES public.mastest_runs(id),
	CONSTRAINT fk5dbjy3u4mhgwoph4vignr4j6x FOREIGN KEY (executed_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk8n2hnc65typd9695fqm5rlvy4 FOREIGN KEY (learning_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkcq0w4q7ik9n5r7docn3e9s2e1 FOREIGN KEY (function_id) REFERENCES public.aut_functions(id),
	CONSTRAINT fke5b4njnp5kh2wg974r6687975 FOREIGN KEY (learning_initiated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkhwmrpgm17xqub8ql2yrfxaqy9 FOREIGN KEY (agent) REFERENCES public.agents(id)
);


-- public.mile_stones definition

-- Drop table

-- DROP TABLE public.mile_stones;

CREATE TABLE public.mile_stones (
	id int8 NOT NULL,
	achievement_status varchar(255) NULL,
	actual_end_date date NULL,
	actual_start_date date NULL,
	actual_velocity int8 NULL,
	days_elapsed int8 NULL,
	days_left int8 NULL,
	milestone_created_on timestamp NOT NULL,
	milestone_goal int8 NULL,
	milestone_goal_achieved int8 NULL,
	milestone_type varchar(255) NULL,
	milestone_updated_on timestamp NULL,
	milestone_name varchar(255) NOT NULL,
	planned_end_date date NULL,
	planned_start_date date NULL,
	milestone_progress int8 NULL,
	required_velocity int8 NULL,
	milestone_status varchar(255) NULL,
	milestone_created_by int8 NULL,
	milestone_updated_by int8 NULL,
	project_id int8 NULL,
	CONSTRAINT mile_stones_pkey PRIMARY KEY (id),
	CONSTRAINT fkcdy1wog4u7gd1d6p5aayrwgu7 FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fke2iv8afvk1gk6q8pta11jq0qe FOREIGN KEY (milestone_updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkok7pdyl2ripasy7xuicjqdn50 FOREIGN KEY (milestone_created_by) REFERENCES public.sys_users(id)
);


-- public.milestone_check_list definition

-- Drop table

-- DROP TABLE public.milestone_check_list;

CREATE TABLE public.milestone_check_list (
	id int8 NOT NULL,
	check_list_remarks varchar(255) NULL,
	check_list_status varchar(255) NULL,
	check_list_summary varchar(255) NULL,
	milestone_id int8 NULL,
	CONSTRAINT milestone_check_list_pkey PRIMARY KEY (id),
	CONSTRAINT fkaqhws7a9m5tijq8x6eijsqki FOREIGN KEY (milestone_id) REFERENCES public.mile_stones(id)
);


-- public.milestone_links definition

-- Drop table

-- DROP TABLE public.milestone_links;

CREATE TABLE public.milestone_links (
	id int8 NOT NULL,
	linked_milestone_id int8 NULL,
	milestone_id int8 NULL,
	CONSTRAINT milestone_links_pkey PRIMARY KEY (id),
	CONSTRAINT fkm6rqcfnuevebqae9gttbh4b7 FOREIGN KEY (milestone_id) REFERENCES public.mile_stones(id),
	CONSTRAINT fkpbpbm07d67ow61wd6a794t82c FOREIGN KEY (linked_milestone_id) REFERENCES public.mile_stones(id)
);


-- public.prj_backlogs definition

-- Drop table

-- DROP TABLE public.prj_backlogs;

CREATE TABLE public.prj_backlogs (
	id int8 NOT NULL,
	created_on timestamp NULL,
	description oid NULL,
	due_date timestamp NULL,
	"external" bool NOT NULL,
	identifier varchar(255) NOT NULL,
	invest_score oid NULL,
	issue_url varchar(255) NULL,
	project_sequence int8 NULL,
	status varchar(255) NULL,
	summary varchar(1000) NULL,
	"type" varchar(255) NULL,
	updated_on timestamp NULL,
	assigned_to int8 NULL,
	creator int8 NULL,
	external_reference int8 NULL,
	priority_id int8 NULL,
	project_id int8 NOT NULL,
	CONSTRAINT prj_backlogs_pkey PRIMARY KEY (id),
	CONSTRAINT fk2akwoy2yllaeigy82nhweet8q FOREIGN KEY (creator) REFERENCES public.sys_backlog_contributors(id),
	CONSTRAINT fkkpo02rid2arda48c1q743yjgq FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkm20tupi4kasrlvp5t33m4wxc6 FOREIGN KEY (assigned_to) REFERENCES public.sys_backlog_contributors(id),
	CONSTRAINT fkpk0cpchoxibx3eu3oatlaue4k FOREIGN KEY (external_reference) REFERENCES public.ext_external_entities(id),
	CONSTRAINT fkrn52d8wc0mspwrd7oes5kqs1s FOREIGN KEY (priority_id) REFERENCES public.prj_priority(id)
);


-- public.scheduled_task_agents definition

-- Drop table

-- DROP TABLE public.scheduled_task_agents;

CREATE TABLE public.scheduled_task_agents (
	scheduled_task_id int8 NOT NULL,
	agent_id int8 NULL,
	"name" varchar(255) NULL,
	CONSTRAINT fkg5x62o0i00r3nhix4rnkqes1g FOREIGN KEY (scheduled_task_id) REFERENCES public.exe_scheduled_tasks(id)
);


-- public.tdes_test_cycles definition

-- Drop table

-- DROP TABLE public.tdes_test_cycles;

CREATE TABLE public.tdes_test_cycles (
	id int8 NOT NULL,
	actual_end_date date NULL,
	actual_start_date date NULL,
	archive bool NOT NULL,
	completion_status int4 NULL,
	created_on timestamp NULL,
	description varchar(4000) NULL,
	test_cycle_name varchar(255) NOT NULL,
	planned_end_date date NULL,
	planned_start_date date NULL,
	test_cycle_plan_json_data oid NULL,
	updated_on timestamp NULL,
	created_by int8 NULL,
	mile_stone_id int8 NULL,
	project_id int8 NOT NULL,
	test_type_id int8 NULL,
	updated_by int8 NULL,
	archived_on timestamp NULL,
	archived_by int8 NULL,
	CONSTRAINT tdes_test_cycles_pkey PRIMARY KEY (id),
	CONSTRAINT fk9bnq1kavkl8t3uld2rsjt7wqy FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkbpkfll5spvn12vxoqmevwqfa3 FOREIGN KEY (archived_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkfymk0cqkkc1942nxwg6v5j2r4 FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkphmthkbux26sfsco9lcbsbgfd FOREIGN KEY (test_type_id) REFERENCES public.tdes_test_type(id),
	CONSTRAINT fkrra6605mmg8bryjlar7i15tfb FOREIGN KEY (mile_stone_id) REFERENCES public.mile_stones(id),
	CONSTRAINT fktiinyxgumebysp22risscw3g8 FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.tdes_test_steps definition

-- Drop table

-- DROP TABLE public.tdes_test_steps;

CREATE TABLE public.tdes_test_steps (
	id int8 NOT NULL,
	actual_result varchar(4000) NULL,
	aut_type varchar(255) NULL,
	bot_type varchar(255) NULL,
	created_on timestamp NULL,
	description varchar(4000) NULL,
	expected_result varchar(4000) NULL,
	file_name varchar(255) NULL,
	file_path varchar(255) NULL,
	step_mode varchar(255) NULL,
	msg varchar(255) NULL,
	msg_format varchar(255) NULL,
	operation varchar(255) NULL,
	response_code int4 NULL,
	rows_to_execute int8 NULL,
	sequence_id int4 NULL,
	show_for_rerun bool NOT NULL,
	step_type varchar(255) NULL,
	summary varchar(255) NULL,
	test_data_id varchar(255) NULL,
	test_step_id varchar(255) NOT NULL,
	updated_on timestamp NULL,
	app_id int8 NULL,
	created_by int8 NOT NULL,
	fun_id int8 NULL,
	message_id int8 NULL,
	test_bot_id int8 NULL,
	test_case_id int8 NOT NULL,
	updated_by int8 NULL,
	CONSTRAINT tdes_test_steps_pkey PRIMARY KEY (id),
	CONSTRAINT fk12abgt9wbb2i8c5tjs2wawp4 FOREIGN KEY (test_bot_id) REFERENCES public.auto_test_bots(id),
	CONSTRAINT fk73v01djgmbxga82svxdc8j1wb FOREIGN KEY (app_id) REFERENCES public.application(id),
	CONSTRAINT fk8lbp2xnfurr7boh8406nnoylu FOREIGN KEY (test_case_id) REFERENCES public.tdes_test_cases(id),
	CONSTRAINT fk9f4ewpe5s1hgucyvn482dsjx7 FOREIGN KEY (message_id) REFERENCES public.aut_pym_message(id),
	CONSTRAINT fkamdygms3gafirb47pmpdobqmy FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkkx8ql04fkeb238a0t6jmj9e8j FOREIGN KEY (fun_id) REFERENCES public.aut_functions(id),
	CONSTRAINT fksl79yb87a0luo21y8cvalqj0y FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.tdes_test_suites definition

-- Drop table

-- DROP TABLE public.tdes_test_suites;

CREATE TABLE public.tdes_test_suites (
	id int8 NOT NULL,
	archive bool NOT NULL,
	created_on timestamp NULL,
	description varchar(4000) NULL,
	execution_status int4 NULL,
	test_suite_name varchar(255) NOT NULL,
	updated_on timestamp NULL,
	created_by int8 NULL,
	project_id int8 NOT NULL,
	test_cycle_id int8 NULL,
	test_type_id int8 NULL,
	updated_by int8 NULL,
	archived_on timestamp NULL,
	archived_by int8 NULL,
	CONSTRAINT tdes_test_suites_pkey PRIMARY KEY (id),
	CONSTRAINT fkbpo7ixvxq7sy82s261eugp7kd FOREIGN KEY (test_type_id) REFERENCES public.tdes_test_type(id),
	CONSTRAINT fkbrt000jjcbl1356sqscmt147r FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkidxhon22e5b3kvfmyw70g9ual FOREIGN KEY (archived_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkl56yr2ai052i63f1y0eskfc5l FOREIGN KEY (test_cycle_id) REFERENCES public.tdes_test_cycles(id),
	CONSTRAINT fkl61dpcpxwung49ckyoy6yyv49 FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkn6scxe7eu159o55q4l1wfbyxy FOREIGN KEY (updated_by) REFERENCES public.sys_users(id)
);


-- public.test_instance definition

-- Drop table

-- DROP TABLE public.test_instance;

CREATE TABLE public.test_instance (
	id int8 NOT NULL,
	archived_date timestamp NULL,
	available_resources varchar(255) NULL,
	created_on timestamp NULL,
	execution_status int4 NULL,
	is_archived bool NOT NULL,
	last_attempted_id int8 NULL,
	last_executed_on timestamp NULL,
	last_run_type int4 NULL,
	number_of_attempts int4 DEFAULT 0 NULL,
	planned_execution_date timestamp NULL,
	project_id int8 NULL,
	"sequence" int8 NULL,
	updated_on timestamp NULL,
	archived_by int8 NULL,
	assigned_to int8 NULL,
	created_by int8 NULL,
	last_executed_by int8 NULL,
	test_case_id int8 NULL,
	test_cycle_id int8 NULL,
	test_pack_id int8 NULL,
	updated_by int8 NULL,
	CONSTRAINT test_instance_pkey PRIMARY KEY (id),
	CONSTRAINT fk4k9u6sj69xrth4dyosiv97fr1 FOREIGN KEY (archived_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk5m4nxngdi93vy6ark5nssni48 FOREIGN KEY (assigned_to) REFERENCES public.sys_users(id),
	CONSTRAINT fk85p2b3i300qdyf8iu468cls2v FOREIGN KEY (updated_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkb1r0j3iikil8tantl2urpn6s8 FOREIGN KEY (last_executed_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkd4duni183rv9uurqx006o9hb7 FOREIGN KEY (test_case_id) REFERENCES public.tdes_test_cases(id),
	CONSTRAINT fkfuwpip6pgtx4fevulqvibckcu FOREIGN KEY (created_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkotgen7j80u7m0k9r65emuhfna FOREIGN KEY (test_pack_id) REFERENCES public.tdes_test_packs(id),
	CONSTRAINT fkrhjxr6lbat9bduduqkeqmax2g FOREIGN KEY (test_cycle_id) REFERENCES public.tdes_test_cycles(id)
);


-- public.test_step_data_sets definition

-- Drop table

-- DROP TABLE public.test_step_data_sets;

CREATE TABLE public.test_step_data_sets (
	test_step_id int8 NOT NULL,
	data_sets varchar(255) NULL,
	CONSTRAINT fk3782acod014mgon28c1usue4c FOREIGN KEY (test_step_id) REFERENCES public.tdes_test_steps(id)
);


-- public.test_step_dependency definition

-- Drop table

-- DROP TABLE public.test_step_dependency;

CREATE TABLE public.test_step_dependency (
	test_step_id int8 NOT NULL,
	dependency_rule int4 NULL,
	dependent_id int8 NULL,
	dependent_step_id varchar(255) NULL,
	id int8 NULL,
	CONSTRAINT fk7jrhjqxnmjomk5uogt0u4ygat FOREIGN KEY (test_step_id) REFERENCES public.tdes_test_steps(id)
);


-- public.archived_steps definition

-- Drop table

-- DROP TABLE public.archived_steps;

CREATE TABLE public.archived_steps (
	test_instance_id int8 NOT NULL,
	archived_by int8 NULL,
	archived_on timestamp NULL,
	step_id int8 NULL,
	CONSTRAINT fk67kpb12h9664qnsfk4ierkxrx FOREIGN KEY (test_instance_id) REFERENCES public.test_instance(id),
	CONSTRAINT fkdx2ioxgx5pmbwyhf6qo1oy18 FOREIGN KEY (archived_by) REFERENCES public.sys_users(id)
);


-- public.def_defect_linkage definition

-- Drop table

-- DROP TABLE public.def_defect_linkage;

CREATE TABLE public.def_defect_linkage (
	id int8 NOT NULL,
	linked_on timestamp NOT NULL,
	test_step_id int8 NULL,
	defect_id int8 NOT NULL,
	linked_by int8 NOT NULL,
	project_id int8 NOT NULL,
	test_cycle_id int8 NULL,
	test_instance_id int8 NOT NULL,
	test_pack_id int8 NULL,
	test_suite_id int8 NULL,
	CONSTRAINT def_defect_linkage_pkey PRIMARY KEY (id),
	CONSTRAINT fk2e8vr7weu7hfpusph06hmlohj FOREIGN KEY (test_suite_id) REFERENCES public.tdes_test_suites(id),
	CONSTRAINT fk95tsvwixekeujqx0hsl8kbuc FOREIGN KEY (defect_id) REFERENCES public.ext_defect(id),
	CONSTRAINT fkc0mqtr76jc9wac3ogvq15ina6 FOREIGN KEY (test_instance_id) REFERENCES public.test_instance(id),
	CONSTRAINT fkescbh5t5bmu5329kopm47ocxq FOREIGN KEY (test_pack_id) REFERENCES public.tdes_test_packs(id),
	CONSTRAINT fkikjdxnx42pwdo64tt63xdlpx9 FOREIGN KEY (linked_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkq6ws14mfm4pilexsu5q2i9gk FOREIGN KEY (test_cycle_id) REFERENCES public.tdes_test_cycles(id),
	CONSTRAINT fktk9mvk1wcijmc9aig3w9ert16 FOREIGN KEY (project_id) REFERENCES public.prj_project(id)
);


-- public.exe_defect_linkage definition

-- Drop table

-- DROP TABLE public.exe_defect_linkage;

CREATE TABLE public.exe_defect_linkage (
	id int8 NOT NULL,
	linked_on timestamp NOT NULL,
	test_step_id int8 NULL,
	defects_id int8 NULL,
	linked_by int8 NOT NULL,
	project_id int8 NOT NULL,
	test_cycle_id int8 NULL,
	test_instance_id int8 NULL,
	test_pack_id int8 NULL,
	test_suite_id int8 NULL,
	test_suite_instance_id int8 NULL,
	backlog_id int8 NULL,
	CONSTRAINT exe_defect_linkage_pkey PRIMARY KEY (id),
	CONSTRAINT fk4d36pdm7vd8papyb41envoc8m FOREIGN KEY (test_pack_id) REFERENCES public.tdes_test_packs(id),
	CONSTRAINT fk8nljx6iwnkydn69nmwcrer74u FOREIGN KEY (test_suite_instance_id) REFERENCES public.test_suite_instance(id),
	CONSTRAINT fkf9qt62wrl4ksayxio98gmn89f FOREIGN KEY (test_suite_id) REFERENCES public.tdes_test_suites(id),
	CONSTRAINT fkgrudx56f1ijxrmpfkc0bgl4nf FOREIGN KEY (test_cycle_id) REFERENCES public.tdes_test_cycles(id),
	CONSTRAINT fkhvvwcjhdr6in9mpv06rj3pdt8 FOREIGN KEY (project_id) REFERENCES public.prj_project(id),
	CONSTRAINT fkk7beqhdc7i9gy9kfhq07oaioi FOREIGN KEY (backlog_id) REFERENCES public.prj_backlogs(id),
	CONSTRAINT fkpbdddtxgmxfb7b2kbqpd0qjxa FOREIGN KEY (test_instance_id) REFERENCES public.test_instance(id),
	CONSTRAINT fkqna8dlfiukibeyarjj19lu9e9 FOREIGN KEY (defects_id) REFERENCES public.exe_defect(id),
	CONSTRAINT fktdvdb1833i4ttuxb9kov72ii FOREIGN KEY (linked_by) REFERENCES public.sys_users(id)
);


-- public.prj_backlog_labels definition

-- Drop table

-- DROP TABLE public.prj_backlog_labels;

CREATE TABLE public.prj_backlog_labels (
	backlog_id int8 NOT NULL,
	label_id int8 NOT NULL,
	CONSTRAINT prj_backlog_labels_pkey PRIMARY KEY (backlog_id, label_id),
	CONSTRAINT fk98m1xyaapb9v24ym9pb4enqks FOREIGN KEY (label_id) REFERENCES public.prj_labels(id),
	CONSTRAINT fkbppd4lnqw767wdooj7d9msnha FOREIGN KEY (backlog_id) REFERENCES public.prj_backlogs(id)
);


-- public.prj_backlog_testcase_links definition

-- Drop table

-- DROP TABLE public.prj_backlog_testcase_links;

CREATE TABLE public.prj_backlog_testcase_links (
	id int8 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
	created_on timestamp NOT NULL,
	backlog_id int8 NOT NULL,
	created_by int8 NULL,
	test_case_id int8 NOT NULL,
	CONSTRAINT prj_backlog_testcase_links_pkey PRIMARY KEY (id),
	CONSTRAINT fk7kurleyd95rhqnehirjqp6a7y FOREIGN KEY (backlog_id) REFERENCES public.prj_backlogs(id),
	CONSTRAINT fkc93ubspn3htak4ppms4c1strh FOREIGN KEY (test_case_id) REFERENCES public.tdes_test_cases(id),
	CONSTRAINT fkrv8qpne01f7sa4kxgbl6i9hju FOREIGN KEY (created_by) REFERENCES public.sys_users(id)
);


-- public.test_case_attempt definition

-- Drop table

-- DROP TABLE public.test_case_attempt;

CREATE TABLE public.test_case_attempt (
	id int8 NOT NULL,
	elapsed_time varchar(255) NULL,
	end_time timestamp NULL,
	executed_on timestamp NULL,
	execution_mode int4 NULL,
	start_time timestamp NULL,
	status int4 NOT NULL,
	summary varchar(255) NULL,
	test_case_name varchar(255) NULL,
	test_pack_id int8 NULL,
	test_run_id int8 NULL,
	test_suite_id int8 NULL,
	test_suite_instance_id int8 NULL,
	testcase_id int8 NOT NULL,
	testcycle_id int8 NULL,
	executed_by int8 NOT NULL,
	test_case_mapped int8 NULL,
	testinstance_id int8 NULL,
	is_deleted bool NOT NULL,
	test_case_snapshot oid NULL,
	automation_framework varchar(255) NULL,
	duration int8 NULL,
	natural_group varchar(255) NULL,
	CONSTRAINT test_case_attempt_pkey PRIMARY KEY (id),
	CONSTRAINT fk1a2321eqmhcwyi3moc14162i FOREIGN KEY (executed_by) REFERENCES public.sys_users(id),
	CONSTRAINT fk6q3pf0iref9aa4nxm0jgan9fb FOREIGN KEY (testinstance_id) REFERENCES public.test_instance(id),
	CONSTRAINT fkoacb9m1owkr3o8qls9smfw8ne FOREIGN KEY (test_case_mapped) REFERENCES public.tdes_test_cases(id)
);


-- public.test_step_attempt definition

-- Drop table

-- DROP TABLE public.test_step_attempt;

CREATE TABLE public.test_step_attempt (
	id int8 NOT NULL,
	actual_result varchar(4000) NULL,
	aut_credential_type varchar(255) NULL,
	bot_type varchar(255) NULL,
	executed_on timestamp NULL,
	execution_mode int4 NULL,
	expected_result varchar(4000) NULL,
	agent_task_mode varchar(255) NULL,
	response_code int4 NULL,
	rows_to_execute int8 NULL,
	status int4 NOT NULL,
	step_type varchar(255) NULL,
	td_gid varchar(255) NULL,
	test_instance_id int8 NULL,
	test_run_id int8 NULL,
	test_step_id int8 NULL,
	test_suite_id int8 NULL,
	test_suite_instance_id int8 NULL,
	executed_by int8 NULL,
	function_id int8 NULL,
	test_bot_id int8 NULL,
	testcaseattempt_id int8 NULL,
	test_step_mapped int8 NULL,
	is_deleted bool NULL,
	test_step_sanpshot oid NULL,
	url varchar(255) NULL,
	automation_framework varchar(255) NULL,
	CONSTRAINT test_step_attempt_pkey PRIMARY KEY (id),
	CONSTRAINT fk9q4vdytark42n22naa74wvl9e FOREIGN KEY (test_bot_id) REFERENCES public.auto_test_bots(id),
	CONSTRAINT fkg74c9cgn5ur2n75gx79w74fp0 FOREIGN KEY (executed_by) REFERENCES public.sys_users(id),
	CONSTRAINT fkggc2w52pncygu6u2358mfvn71 FOREIGN KEY (testcaseattempt_id) REFERENCES public.test_case_attempt(id),
	CONSTRAINT fkjxexyahd6a22mmyobl1lf8bgd FOREIGN KEY (function_id) REFERENCES public.aut_functions(id),
	CONSTRAINT fkn0dcnbcrjhf9ks00rc6v00u4v FOREIGN KEY (test_step_mapped) REFERENCES public.tdes_test_steps(id)
);


-- public.exe_test_results definition

-- Drop table

-- DROP TABLE public.exe_test_results;

CREATE TABLE public.exe_test_results (
	id int8 GENERATED BY DEFAULT AS IDENTITY( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
	duration int8 NOT NULL,
	end_time timestamp NULL,
	execution_mode varchar(255) NULL,
	framework_specific_data oid NULL,
	parameters oid NULL,
	platform_info oid NULL,
	"result" varchar(255) NULL,
	start_time timestamp NULL,
	steps oid NULL,
	test_cycle_id int8 NULL,
	test_instance_id int8 NULL,
	test_run_id int8 NULL,
	failure_id int4 NULL,
	platform_id int8 NULL,
	test_case_attempt_id int8 NULL,
	test_step_attempt_id int8 NULL,
	CONSTRAINT exe_test_results_pkey PRIMARY KEY (id),
	CONSTRAINT fk61gxpvvui8ugfpw70kg05vaoc FOREIGN KEY (test_step_attempt_id) REFERENCES public.test_step_attempt(id),
	CONSTRAINT fk63iox6mkn2qsys1crmhq2pyy FOREIGN KEY (test_case_attempt_id) REFERENCES public.test_case_attempt(id),
	CONSTRAINT fkckshpfy8s57qsqtjr9gavuw05 FOREIGN KEY (failure_id) REFERENCES public.exe_test_failures(id),
	CONSTRAINT fkrmc9xlr838m8y5fi8cgnxnv5k FOREIGN KEY (platform_id) REFERENCES public.exe_test_result_platforms(id)
);