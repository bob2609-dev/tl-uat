#!/usr/bin/env python3
"""
TestLink Optimized Execution Module - Standalone Launcher
All dependencies and configuration embedded.

Usage:
    1. python TestLinkOptimizedExecution.py          # extract files & start
    2. pip install -r requirements.txt               # if not already installed
    3. python main.py                                # start directly next time
"""

import sys
import os

# ── Embedded file contents ─────────────────────────────────────────────────────

_ENV = """# TestLink Optimized Execution Module - Production Configuration
# Database Server: localhost (Database Only)

DB_HOST=10.200.224.21
DB_PORT=4053
DB_USER=tl_uat
DB_PASSWORD=tl_uat269
DB_NAME=tl_uat

# Server Configuration
HOST=0.0.0.0
PORT=8000
DEBUG=false
RELOAD=false
LOG_LEVEL=warning

# CORS Configuration
CORS_ORIGINS=http://localhost:8000,http://127.0.0.1:8000,http://test-management.nmbtz.com:9443,https://test-management.nmbtz.com:9443
CORS_ALLOW_CREDENTIALS=true
CORS_ALLOW_METHODS=GET,POST,PUT,DELETE,OPTIONS
CORS_ALLOW_HEADERS=Content-Type,Authorization
"""

_MAIN = """from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import uvicorn
from config import config
from database import DatabaseManager

app = FastAPI(
    title="TestLink Optimized Execution API",
    description="High-performance test execution backend",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost:8000",
        "http://127.0.0.1:8000",
        "http://test-management.nmbtz.com:9443",
        "https://test-management.nmbtz.com:9443",
    ],
    allow_credentials=True,
    allow_methods=["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    allow_headers=["Content-Type", "Authorization"],
)

@app.get("/health")
async def health_check():
    return {"status": "healthy", "database": "connected"}

@app.get("/")
async def root():
    return {"message": "TestLink Optimized Execution API", "version": "1.0.0"}

if __name__ == "__main__":
    print("Starting TestLink Optimized Execution API...")
    print("API Documentation: http://localhost:8000/docs")
    print("Health Check:      http://localhost:8000/health")
    uvicorn.run(app, host="0.0.0.0", port=8000)
"""

_CONFIG = """import os

class Config:
    def __init__(self):
        self.DB_HOST     = os.getenv("DB_HOST", "10.200.224.21")
        self.DB_PORT     = int(os.getenv("DB_PORT", "4053"))
        self.DB_USER     = os.getenv("DB_USER", "tl_uat")
        self.DB_PASSWORD = os.getenv("DB_PASSWORD", "tl_uat269")
        self.DB_NAME     = os.getenv("DB_NAME", "tl_uat")
        self.HOST        = os.getenv("HOST", "0.0.0.0")
        self.PORT        = int(os.getenv("PORT", "8000"))

config = Config()
"""

_DATABASE = """import mysql.connector
from mysql.connector import pooling, Error

class DatabaseManager:
    def __init__(self):
        self.pool = None

    def create_connection_pool(self):
        try:
            self.pool = mysql.connector.pooling.MySQLConnectionPool(
                pool_name="testlink_pool",
                pool_size=10,
                host="10.200.224.21",
                port=4053,
                user="tl_uat",
                password="tl_uat269",
                database="tl_uat",
            )
            return True
        except Error as e:
            print(f"Database connection error: {e}")
            return False
"""

_REQUIREMENTS = """fastapi==0.104.1
uvicorn[standard]==0.24.0
mysql-connector-python==8.2.0
python-dotenv==1.0.0
"""

# ── Helpers ────────────────────────────────────────────────────────────────────

def _write(filename, content):
    with open(filename, "w", encoding="utf-8") as f:
        f.write(content)
    print(f"  created {filename}")


def extract_files():
    print("Extracting application files...")
    _write(".env",             _ENV)
    _write("main.py",         _MAIN)
    _write("config.py",       _CONFIG)
    _write("database.py",     _DATABASE)
    _write("requirements.txt", _REQUIREMENTS)
    print("All files extracted.")


def main():
    extract_files()

    print()
    print("Next steps:")
    print("  pip install -r requirements.txt")
    print("  python main.py")
    print()
    print("Or start now if dependencies are already installed:")

    try:
        import uvicorn  # noqa: F401
        import fastapi  # noqa: F401
    except ImportError:
        print("  Dependencies missing. Run: pip install -r requirements.txt")
        return 1

    # Start directly
    print("Starting TestLink Optimized Execution API...")
    print("API Documentation: http://localhost:8000/docs")
    print("Health Check:      http://localhost:8000/health")

    # Load .env before importing the app
    from dotenv import load_dotenv
    load_dotenv()

    import uvicorn
    from main import app
    uvicorn.run(app, host="0.0.0.0", port=8000)
    return 0


if __name__ == "__main__":
    sys.exit(main())
