#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Database configuration for TestLink test execution export script.
Edit this file to provide your database credentials.
"""

# Database connection parameters
DB_CONFIG = {
    'host': 'localhost',        # Database host
    'port': 3306,               # Database port (default MySQL port)
    'user': 'testlink_user',    # Database username
    'password': 'password',     # Database password
    'database': 'testlink_db',  # Database name
    'charset': 'utf8mb4',       # Character set
    'use_unicode': True         # Use Unicode
}
