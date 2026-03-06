# TestLink Optimized Execution Module - Standalone Deployment Guide

## 🎯 Simple Deployment - Single File Solution

This approach creates **one standalone Python file** that contains everything needed to run the Optimized Execution Module.

## 🚀 Quick Deployment Steps

### **Step 1: Create Standalone File**

```cmd
# On your development machine
cd lib\execute\py_execute
python create_standalone.py
```

**This creates:**

- `TestLinkOptimizedExecution.py` - Complete standalone application
- All configuration embedded
- No external dependencies needed

### **Step 2: Deploy to Application Server**

```cmd
# Copy the single file to your XAMPP server
copy TestLinkOptimizedExecution.py \\localhost\c$\xampp\htdocs\tl-uat\lib\execute\py_execute\
```

### **Step 3: Run the Application**

```cmd
# On the application server (RDP to localhost)
cd C:\xampp\htdocs\tl-uat\lib\execute\py_execute
python TestLinkOptimizedExecution.py
```

## 🌐 Access URLs After Deployment

| Service        | URL                                                                                     | Description       |
| -------------- | --------------------------------------------------------------------------------------- | ----------------- |
| **Frontend**   | `http://test-management.nmbtz.com:9443/lib/execute/optimized_execution_standalone.html` | Web interface     |
| **API Health** | `http://localhost:8000/health`                                                          | Service status    |
| **API Docs**   | `http://localhost:8000/docs`                                                            | API documentation |

## ✅ Benefits of Standalone Approach

### **🎯 Ultra-Simple Deployment**

- **Single file** - Just copy `TestLinkOptimizedExecution.py`
- **Zero configuration** - Production settings embedded
- **No dependencies** - Everything is self-contained
- **No service installation** - Just run Python script

### **🔧 Easy Management**

- **Start/Stop** - Use Ctrl+C to stop and restart
- **Update** - Replace one file to update everything
- **Debug** - Clear console output shows all activity
- **Logs** - Application logs to console

### **🏗️ Architecture**

```
Database Server: localhost (MySQL - Database Only)
Application Server: localhost (XAMPP + TestLink)
Standalone API: localhost:8000 (Python script with embedded config)
```

## 📋 What the Standalone File Contains

The `TestLinkOptimizedExecution.py` file includes:

1. **FastAPI Application** - Complete web API
2. **Database Connection** - MySQL connector with production settings
3. **Configuration** - All environment variables embedded
4. **CORS Settings** - Properly configured for your domains
5. **Health Endpoints** - `/health` and `/` endpoints

## 🔄 Management Commands

### **Start Application**

```cmd
cd C:\xampp\htdocs\tl-uat\lib\execute\py_execute
python TestLinkOptimizedExecution.py
```

### **Stop Application**

```cmd
# Press Ctrl+C in the console where it's running
```

### **Restart Application**

```cmd
# Stop with Ctrl+C, then run start command again
python TestLinkOptimizedExecution.py
```

### **Check Status**

```cmd
# Open in browser
http://localhost:8000/health
```

## 🎉 Deployment Complete!

When you see this output, your deployment is successful:

```
🚀 Starting TestLink Optimized Execution API...
📍 API Documentation: http://localhost:8000/docs
🏥 Health Check: http://localhost:8000/health
```

**The Optimized Execution Module is now running!** 🎯

## 🔧 Troubleshooting

### **Port Already in Use**

```cmd
# Check what's using port 8000
netstat -ano | findstr :8000

# Kill the process
taskkill /PID <PID> /F
```

### **Database Connection Issues**

The standalone file has embedded database settings, but if you need to modify:

1. Open `TestLinkOptimizedExecution.py`
2. Find the database configuration section
3. Update the connection details
4. Restart the application

### **Permission Issues**

Since this runs as a regular Python script, no special permissions are needed beyond:

- Read access to the directory
- Network access to connect to database server

## 📞 Support

If you encounter issues:

1. Check the console output for error messages
2. Verify database connectivity: `telnet localhost 3306`
3. Check if port 8000 is available
4. Ensure Python 3.8+ is installed

---

**This standalone approach eliminates all Windows service complexity while providing the same functionality!** 🚀
