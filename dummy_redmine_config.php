<?php
/**
 * Dummy Redmine Configuration
 * 
 * This file allows easy configuration of the dummy Redmine API server.
 * Include this file in your TestLink custom configuration to use the dummy API.
 */

// Main configuration
$REDMINE_CONFIG = [
    // Set to true to use the dummy API instead of the real Redmine server
    'use_dummy_api' => true,
    
    // URL to the dummy API (when use_dummy_api is true)
    'dummy_api_url' => '/dummy_redmine_api.php', // Use a server-root relative path
    
    // Real Redmine server URL (when use_dummy_api is false)
    'real_api_url' => 'https://support.profinch.com',
    
    // API key for the real Redmine server
    'api_key' => 'a597e200f8923a85484e81ca81d731827b8dbf3d',
    
    // Default project ID
    'project_id' => 'nmb-fcubs-14-7-uat2',
    
    // Default assignee ID
    'assignee_id' => 2635
];

// Helper function to get the current API URL based on configuration
function get_redmine_api_url() {
    global $REDMINE_CONFIG;
    return $REDMINE_CONFIG['use_dummy_api'] ? 
        $REDMINE_CONFIG['dummy_api_url'] : 
        $REDMINE_CONFIG['real_api_url'];
}

// Helper function to get the API key
function get_redmine_api_key() {
    global $REDMINE_CONFIG;
    return $REDMINE_CONFIG['api_key'];
}

// Helper function to get the project ID
function get_redmine_project_id() {
    global $REDMINE_CONFIG;
    return $REDMINE_CONFIG['project_id'];
}

// Helper function to get the assignee ID
function get_redmine_assignee_id() {
    global $REDMINE_CONFIG;
    return $REDMINE_CONFIG['assignee_id'];
}
