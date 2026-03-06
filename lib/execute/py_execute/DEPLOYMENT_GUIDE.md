# TestLink Optimized Execution Module - Production Deployment Guide

## 🎯 Target Server Configuration

**Application Server**: localhost (XAMPP + TestLink + Python API)  
**Database Server**: 10.200.224.21:3306 (MySQL - Database ONLY)  
**Database**: tl_uat  
**User**: tl_uat  
**Password**: tl_uat269

**IMPORTANT**: `10.200.224.21:3306` is **DATABASE SERVER ONLY** - do NOT use for application access!

**Application Path**: `C:\xampp\htdocs\tl-uat\lib\execute\py_execute`  
**Python Path**: `C:\Python\Python314\python.exe`  
**Frontend Path**: `C:\xampp\htdocs\tl-uat\lib\execute\`

**Architecture**: Python API runs on application server, connects to remote database server

## 📁 Deployment Structure

```
C:\xampp\htdocs\tl-uat\lib\execute\
├── py_execute\                    # Python backend (service)
├── optimized_execution_standalone.html  # Frontend
└── optimized_execution_module.php     # PHP backend

C:\TestLinkServices\py_execute\          # Service installation
```

## 🚀 Quick Deployment Steps

### **Step 1: Deploy Files**

```cmd
# Run from local development machine
cd lib\execute\py_execute
deploy_to_server.bat
```

### **Step 2: Install Service** (Remote on target server)

1. **RDP** to `localhost`
2. **Navigate** to `C:\TestLinkServices\py_execute`
3. **Run**: `install_service.bat` as Administrator

### **Step 3: Alternative: Simple Launcher** (Recommended)

Instead of Windows service, use the simple launcher:

```cmd
# On target server, navigate to Python backend directory
cd C:\xampp\htdocs\tl-uat\lib\execute\py_execute

# Run the launcher (reads .env automatically)
python run_optimized_execution.py
```

**Benefits of Simple Launcher:**

- ✅ No Windows service installation required
- ✅ Reads .env file automatically
- ✅ Easy to start/stop/restart
- ✅ Clear error messages and logging
- ✅ Works with any Python installation
- ✅ No administrator privileges needed after initial setup

### **Step 3: Verify Deployment**

```cmd
# Check API health
curl http://localhost:8000/health

# Check API documentation
# Open in browser: http://localhost:8000/docs

# Expected response
{"status": "healthy", "database": "connected"}
```

## 🔧 Service Management

### **Start/Stop/Restart**

```cmd
# Start service
nssm start TestLinkPythonAPI

# Stop service
nssm stop TestLinkPythonAPI

# Restart service
nssm restart TestLinkPythonAPI

# Check status
nssm status TestLinkPythonAPI
```

### **Service Configuration**

The service will be configured with:

- **Name**: TestLinkPythonAPI
- **Startup**: Automatic
- **Python Path**: C:\Python39\python.exe
- **Working Directory**: C:\TestLinkServices\py_execute
- **Environment**: PYTHONPATH set correctly

## 🌐 Access URLs

| Service             | URL                                                                                     | Description             |
| ------------------- | --------------------------------------------------------------------------------------- | ----------------------- |
| **API Health**      | `http://localhost:8000/health`                                                          | Service status (remote) |
| **API Docs**        | `http://localhost:8000/docs`                                                            | Swagger UI (remote)     |
| **Standalone HTML** | `http://test-management.nmbtz.com:9443/lib/execute/optimized_execution_standalone.html` | Frontend (web)          |
| **PHP Backend**     | `http://test-management.nmbtz.com:9443/lib/execute/optimized_execution_module.php`      | Integrated (web)        |

## 🔒 Security Configuration

### **Database Security**

- **Host**: localhost (restricted access)
- **User**: tl_uat (dedicated user)
- **Database**: tl_uat (isolated database)
- **Connection Pooling**: 20 connections max
- **Timeouts**: 30s connection, 60s read

### **API Security**

- **CORS**: Restricted to test-management.nmbtz.com domains
- **Rate Limiting**: 200 requests per minute
- **Secret Key**: Production-specific key
- **JWT Tokens**: 8-hour expiration

### **Network Security**

- **Port**: 8000 (internal only)
- **Firewall**: Restrict to internal networks
- **SSL**: Frontend uses HTTPS (port 9443)

## 📊 Performance Settings

### **Optimized for Production**

- **Debug Mode**: Disabled
- **Query Cache**: 10-minute TTL
- **Connection Pool**: 20 connections
- **Retry Logic**: 5 attempts with 2s delay
- **Log Level**: Warning (reduced verbosity)

### **Monitoring**

- **Health Endpoint**: `/health` with database connectivity check
- **Metrics**: Request/response timing
- **Logs**: Rotating logs (10 files, 50MB max)
- **Performance**: Query execution tracking

## 🔍 Troubleshooting

### **Service Won't Start**

```cmd
# Check service status
nssm status TestLinkPythonAPI

# Check event logs
eventvwr.msc

# Check application logs
type C:\TestLinkServices\py_execute\logs\py_execute_production.log
```

### **Database Connection Issues**

```cmd
# Test connection from application server
mysql -h localhost -u tl_uat -p tl_uat269 tl_uat

# Check network connectivity to database server
ping localhost

# Check firewall
telnet localhost 3306
```

### **Port Conflicts**

```cmd
# Check what's using port 8000
netstat -ano | findstr :8000

# Kill conflicting process
taskkill /PID <PID> /F
```

### **Permission Issues**

```cmd
# Check service permissions
icacls C:\TestLinkServices\py_execute

# Grant necessary permissions
icacls C:\TestLinkServices\py_execute /grant "NT AUTHORITY\SYSTEM":(OI)(CI)F
```

## 📋 Deployment Checklist

### **Pre-Deployment**

- [ ] Backup current TestLink installation
- [ ] Verify database credentials work
- [ ] Test network connectivity to target server
- [ ] Ensure Python 3.9+ installed on target
- [ ] Plan maintenance window

### **Deployment**

- [ ] Run `deploy_to_server.bat` successfully
- [ ] Verify all files copied correctly
- [ ] Check `.env` file contains production settings
- [ ] Create logs directory on target

### **Post-Deployment**

- [ ] Run `install_service.bat` as Administrator
- [ ] Verify service starts automatically
- [ ] Test API health endpoint
- [ ] Test frontend integration
- [ ] Verify database connectivity
- [ ] Check for errors in event logs

### **Testing**

- [ ] Access API documentation: `http://localhost:8000/docs`
- [ ] Test health check: `http://localhost:8000/health`
- [ ] Access frontend: TestLink menu → "🚀 Standalone Version"
- [ ] Test backend toggle functionality
- [ ] Verify database operations work
- [ ] Check performance under load

## 🔄 Maintenance

### **Update Service**

```cmd
# Stop service
nssm stop TestLinkPythonAPI

# Update files
xcopy ".\*" "C:\TestLinkServices\py_execute\" /E /Y

# Start service
nssm start TestLinkPythonAPI
```

### **Log Management**

```cmd
# View current logs
type C:\TestLinkServices\py_execute\logs\py_execute_production.log | more

# Archive old logs
move C:\TestLinkServices\py_execute\logs\py_execute_production.log.1 C:\TestLinkServices\py_execute\logs\archive\
```

## 🆘 Support

### **Emergency Contacts**

- **Database Admin**: For database connectivity issues
- **System Admin**: For server/service issues
- **Development Team**: For application bugs

### **Critical Commands**

```cmd
# Emergency stop
nssm stop TestLinkPythonAPI

# Emergency restart
nssm restart TestLinkPythonAPI

# Remove service (if needed)
nssm remove TestLinkPythonAPI
```

---

**Note**: This deployment is specifically configured for the UAT environment at localhost with the tl_uat database. Adjust settings for production deployment.
