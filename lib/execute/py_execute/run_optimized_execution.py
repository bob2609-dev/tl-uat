#!/usr/bin/env python3
"""
TestLink Optimized Execution Module - Simple Launcher
Reads configuration from .env and runs the FastAPI application
"""

import os
import sys
from pathlib import Path

def load_env():
    """Load environment variables from .env file"""
    env_vars = {}
    env_file = Path('.env')
    
    if env_file.exists():
        with open(env_file, 'r') as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, value = line.split('=', 1)
                    env_vars[key.strip()] = value.strip().strip('"')
    
    return env_vars

def main():
    """Main launcher function"""
    print("🚀 TestLink Optimized Execution Module - Launcher")
    print("=" * 50)
    
    # Load environment variables
    env = load_env()
    
    if not env:
        print("❌ ERROR: .env file not found!")
        print("📝 Please create .env file with your database configuration")
        print("\nExample .env file:")
        print("DB_HOST=localhost")
        print("DB_USER=tl_uat")
        print("DB_PASSWORD=tl_uat269")
        print("DB_NAME=tl_uat")
        print("HOST=0.0.0.0")
        print("PORT=8000")
        return 1
    
    # Display configuration
    print("📋 Configuration loaded:")
    print(f"  Database Host: {env.get('DB_HOST', 'Not set')}")
    print(f"  Database Name: {env.get('DB_NAME', 'Not set')}")
    print(f"  API Host: {env.get('HOST', 'Not set')}")
    print(f"  API Port: {env.get('PORT', 'Not set')}")
    
    # Check if main.py exists
    main_script = Path('main.py')
    if not main_script.exists():
        print("❌ ERROR: main.py not found!")
        print("📁 Current directory contents:")
        for item in Path('.').iterdir():
            print(f"  {item.name}")
        return 1
    
    try:
        # Change to the script directory
        script_dir = Path(__file__).parent
        os.chdir(script_dir)
        
        # Import and run the FastAPI app
        print("🔄 Starting FastAPI application...")
        print(f"📁 Working directory: {script_dir.absolute()}")
        
        # Import main module (this will run the FastAPI app)
        import main
        
        print("✅ Application started successfully!")
        print(f"🌐 API Documentation: http://{env.get('HOST', 'localhost')}:{env.get('PORT', '8000')}/docs")
        print(f"🏥 Health Check: http://{env.get('HOST', 'localhost')}:{env.get('PORT', '8000')}/health")
        print("\n" + "=" * 50)
        print("🔧 Management Commands:")
        print("  Press Ctrl+C to stop the application")
        print("  Check logs: type logs/py_execute_production.log")
        print("  Restart: python run_optimized_execution.py")
        
        return 0
        
    except KeyboardInterrupt:
        print("\n👋 Application stopped by user")
        return 0
    except Exception as e:
        print(f"\n❌ ERROR: Failed to start application: {e}")
        return 1

if __name__ == "__main__":
    sys.exit(main())
