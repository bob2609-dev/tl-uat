#!/usr/bin/env python3
"""
Startup Script for TestLink Optimized Execution Module
Handles environment validation and server startup
"""

import os
import sys
import logging
from pathlib import Path

def check_environment():
    """Check if environment is properly configured"""
    print("🔍 Checking environment...")
    
    # Check if .env file exists
    env_file = Path(".env")
    if not env_file.exists():
        print("❌ .env file not found!")
        print("📝 Please copy .env.example to .env and configure your settings")
        return False
    
    # Check required directories
    log_dir = Path("logs")
    if not log_dir.exists():
        print("📁 Creating logs directory...")
        log_dir.mkdir(exist_ok=True)
    
    # Check Python version
    if sys.version_info < (3, 8):
        print("❌ Python 3.8+ is required!")
        print(f"   Current version: {sys.version}")
        return False
    
    print("✅ Environment check passed")
    return True

def check_dependencies():
    """Check if required dependencies are installed"""
    print("🔍 Checking dependencies...")
    
    required_packages = [
        "fastapi",
        "uvicorn", 
        "mysql.connector",
        "pydantic",
        "dotenv"
    ]
    
    missing_packages = []
    
    for package in required_packages:
        try:
            if package == "mysql.connector":
                import mysql.connector
            elif package == "dotenv":
                import dotenv
            else:
                __import__(package)
        except ImportError:
            missing_packages.append(package)
    
    if missing_packages:
        print("❌ Missing dependencies:")
        for package in missing_packages:
            print(f"   - {package}")
        print("\n💡 Install with: pip install -r requirements.txt")
        return False
    
    print("✅ Dependencies check passed")
    return True

def test_database_connection():
    """Test database connection"""
    print("🔍 Testing database connection...")
    
    try:
        from dotenv import load_dotenv
        load_dotenv()
        
        import mysql.connector
        
        config = {
            "host": os.getenv("DB_HOST", "localhost"),
            "port": int(os.getenv("DB_PORT", "3306")),
            "user": os.getenv("DB_USER", "testlink"),
            "password": os.getenv("DB_PASSWORD", ""),
            "database": os.getenv("DB_NAME", "testlink")
        }
        
        connection = mysql.connector.connect(**config)
        cursor = connection.cursor()
        cursor.execute("SELECT 1")
        cursor.fetchone()
        connection.close()
        
        print("✅ Database connection successful")
        return True
        
    except Exception as e:
        print(f"❌ Database connection failed: {e}")
        print("💡 Please check your .env configuration")
        return False

def start_server():
    """Start the FastAPI server"""
    print("🚀 Starting TestLink Optimized Execution API...")
    
    try:
        import uvicorn
        from dotenv import load_dotenv
        
        load_dotenv()
        
        host = os.getenv("HOST", "0.0.0.0")
        port = int(os.getenv("PORT", "8000"))
        reload = os.getenv("RELOAD", "true").lower() == "true"
        log_level = os.getenv("LOG_LEVEL", "info")
        
        print(f"🌐 Server will be available at: http://{host}:{port}")
        print(f"📚 API Documentation: http://{host}:{port}/docs")
        print(f"🔍 Health Check: http://{host}:{port}/health")
        print("\n⏹️  Press Ctrl+C to stop the server\n")
        
        uvicorn.run(
            "main:app",
            host=host,
            port=port,
            reload=reload,
            log_level=log_level
        )
        
    except KeyboardInterrupt:
        print("\n👋 Server stopped by user")
    except Exception as e:
        print(f"❌ Failed to start server: {e}")

def main():
    """Main startup function"""
    print("=" * 60)
    print("🚀 TestLink Optimized Execution Module - Startup")
    print("=" * 60)
    
    # Run checks
    checks = [
        check_environment,
        check_dependencies,
        test_database_connection
    ]
    
    for check in checks:
        if not check():
            print("\n❌ Startup failed! Please fix the issues above.")
            sys.exit(1)
    
    print("\n✅ All checks passed!")
    
    # Start server
    start_server()

if __name__ == "__main__":
    main()
