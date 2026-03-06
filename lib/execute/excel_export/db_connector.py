#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Database connector for TestLink test execution export script.
This module handles database connections and query execution.
"""

import pymysql
import sys
import os
from script_DB_config import DB_CONFIG


class DatabaseConnector:
    """
    Class for handling database connections and queries.
    """
    def __init__(self, config=None):
        """Initialize the database connector with configuration."""
        self.config = config or DB_CONFIG
        self.connection = None
        self.cursor = None

    def connect(self):
        """Establish a connection to the database."""
        try:
            # Add cursor_class for dictionary results (equivalent to dictionary=True)
            self.config['cursorclass'] = pymysql.cursors.DictCursor
            self.connection = pymysql.connect(**self.config)
            self.cursor = self.connection.cursor()
            print("Connected to the database successfully.")
            return True
        except pymysql.Error as err:
            print(f"Error connecting to the database: {err}")
            return False

    def disconnect(self):
        """Close the database connection."""
        if self.cursor:
            self.cursor.close()
        if self.connection:
            self.connection.close()
            print("Disconnected from the database.")

    def execute_query(self, query, params=None):
        """
        Execute a SQL query with optional parameters.
        
        Args:
            query (str): SQL query to execute
            params (tuple, optional): Parameters for the query
            
        Returns:
            list: Results as a list of dictionaries
        """
        if not self.connection or not hasattr(self.connection, 'open') or not self.connection.open:
            if not self.connect():
                return None

        try:
            self.cursor.execute(query, params or ())
            results = self.cursor.fetchall()
            return results
        except pymysql.Error as err:
            print(f"Error executing query: {err}")
            print(f"Query: {query}")
            if params:
                print(f"Parameters: {params}")
            return None

    def get_projects(self):
        """Get a list of all test projects."""
        query = """
        SELECT 
            id, 
            notes AS name, 
            active
        FROM 
            testprojects
        WHERE 
            active = 1
        ORDER BY 
            name
        """
        return self.execute_query(query)

    def get_test_plans(self, project_id=None):
        """
        Get test plans, optionally filtered by project.
        
        Args:
            project_id (int, optional): Project ID to filter by
            
        Returns:
            list: Test plans as a list of dictionaries
        """
        query = """
        SELECT 
            id, 
            testproject_id,
            notes AS name, 
            active
        FROM 
            testplans
        WHERE 
            active = 1
        """
        
        params = None
        if project_id:
            query += " AND testproject_id = %s"
            params = (project_id,)
            
        query += " ORDER BY name"
        
        return self.execute_query(query, params)

    def get_builds(self, testplan_id=None):
        """
        Get builds, optionally filtered by test plan.
        
        Args:
            testplan_id (int, optional): Test plan ID to filter by
            
        Returns:
            list: Builds as a list of dictionaries
        """
        query = """
        SELECT 
            id,
            testplan_id, 
            name, 
            notes,
            active
        FROM 
            builds
        WHERE 
            active = 1
        """
        
        params = None
        if testplan_id:
            query += " AND testplan_id = %s"
            params = (testplan_id,)
            
        query += " ORDER BY name"
        
        return self.execute_query(query, params)
