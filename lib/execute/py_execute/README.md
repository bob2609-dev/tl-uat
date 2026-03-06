# TestLink Optimized Execution Module - Python Backend

High-performance Python backend for the TestLink Optimized Execution Module using FastAPI and MySQL connection pooling.

## 🚀 Features

- **Async Operations**: FastAPI with async database operations
- **Connection Pooling**: Efficient MySQL connection management
- **Environment Configuration**: Flexible .env-based configuration
- **Error Handling**: Comprehensive error handling with retry logic
- **Performance Monitoring**: Built-in metrics and health checks
- **CORS Support**: Ready for frontend integration
- **API Documentation**: Auto-generated OpenAPI docs

## 📁 Project Structure

```
py_execute/
├── .env                    # Environment configuration (copy from .env.example)
├── main.py                 # FastAPI application entry point
├── config.py               # Configuration management
├── database.py             # Database operations and queries
├── requirements.txt        # Python dependencies
├── README.md              # This file
└── logs/                  # Log files (created automatically)
```

## 🛠️ Installation

### Prerequisites
- Python 3.8+
- MySQL 5.7+ or 8.0+
- TestLink database

### Setup Steps

1. **Install Dependencies**
   ```bash
   pip install -r requirements.txt
   ```

2. **Configure Environment**
   ```bash
   # Copy and edit environment configuration
   cp .env.example .env
   
   # Update with your database details
   nano .env
   ```

3. **Create Log Directory**
   
   **Windows:**
   ```cmd
   mkdir logs
   ```
   
   **Linux/Mac:**
   ```bash
   mkdir -p logs
   ```

4. **Run Server**
   
   **Windows:**
   ```cmd
   cd py_execute
   python main.py
   # OR
   python start.py
   ```
   
   **Linux/Mac:**
   ```bash
   python main.py
   # OR
   python start.py
   ```

## ⚙️ Configuration

### Environment Variables (.env)

#### Database Configuration
```env
DB_HOST=localhost
DB_PORT=3306
DB_USER=testlink
DB_PASSWORD=your_password
DB_NAME=testlink
```

#### Server Configuration
```env
HOST=0.0.0.0
PORT=8000
DEBUG=false
LOG_LEVEL=info
```

#### Performance Settings
```env
DB_POOL_SIZE=10
ENABLE_QUERY_CACHE=true
MAX_CONNECTION_RETRIES=3
```

#### Security
```env
SECRET_KEY=your-secret-key-here
JWT_ALGORITHM=HS256
```

## 📡 API Endpoints

### Core Endpoints

- `GET /` - Root endpoint
- `GET /docs` - API documentation (Swagger UI)
- `GET /redoc` - API documentation (ReDoc)
- `GET /health` - Health check

### Tree Navigation
- `GET /api/tree_nodes` - Get tree nodes for lazy loading

### Test Case Operations
- `GET /api/testcase/{tcversion_id}` - Get test case details
- `POST /api/execution` - Update execution status

### Statistics
- `GET /api/stats` - Get execution statistics

### Example Requests

#### Get Tree Nodes
```bash
curl "http://localhost:8000/api/tree_nodes?parent_id=0&tplan_id=1&build_id=1&platform_id=1"
```

#### Get Test Case Details
```bash
curl "http://localhost:8000/api/testcase/123?tplan_id=1&build_id=1&platform_id=1"
```

#### Update Execution
```bash
curl -X POST "http://localhost:8000/api/execution" \
  -H "Content-Type: application/json" \
  -d '{
    "tcversion_id": 123,
    "tplan_id": 1,
    "build_id": 1,
    "platform_id": 1,
    "status": "p",
    "notes": "Test passed successfully"
  }'
```

## 🔧 Development

### Running in Development Mode

**Windows:**
```cmd
# With auto-reload
python start.py

# Or with uvicorn directly
uvicorn main:app --host 0.0.0.0 --port 8000 --reload
```

**Linux/Mac:**
```bash
# With auto-reload
python start.py

# Or with uvicorn directly
uvicorn main:app --host 0.0.0.0 --port 8000 --reload
```

### Testing
```bash
# Install test dependencies
pip install pytest pytest-asyncio httpx

# Run tests
pytest tests/
```

### Code Structure

#### Main Components
- **main.py**: FastAPI application and API endpoints
- **config.py**: Configuration management and validation
- **database.py**: Database operations and connection pooling

#### Key Classes
- `DatabaseManager`: Connection pooling and query execution
- `TestLinkQueries`: TestLink-specific database queries
- `AppConfig`: Configuration management

## 📊 Performance

### Connection Pooling
- Default pool size: 10 connections
- Automatic connection recycling
- Retry logic for failed connections

### Caching
- Query result caching (configurable)
- Connection pooling reduces overhead
- Efficient cursor management

### Monitoring
- Request/response logging
- Performance metrics
- Health check endpoints

## 🔒 Security

### Features
- CORS configuration
- Environment-based secrets
- SQL injection prevention
- Request validation

### Recommendations
1. Change default secret key in production
2. Use HTTPS in production
3. Configure appropriate CORS origins
4. Enable rate limiting

## 🚨 Troubleshooting

### Common Issues

#### Database Connection Failed
```bash
# Check database configuration
echo $DB_HOST $DB_USER $DB_NAME

# Test connection manually
mysql -h $DB_HOST -u $DB_USER -p $DB_NAME
```

#### Port Already in Use

**Windows:**
```cmd
# Find process using port 8000
netstat -ano | findstr :8000

# Kill process (replace <PID> with actual process ID)
taskkill /PID <PID> /F
```

**Linux/Mac:**
```bash
# Find process using port 8000
lsof -i :8000

# Kill process
kill -9 <PID>
```

#### Permission Denied
```bash
# Check file permissions
ls -la logs/

# Fix permissions
chmod 755 logs/
```

### Logs
Check application logs for detailed error information:

**Windows:**
```cmd
# View last 50 lines
type logs\py_execute.log | more

# Monitor live logs (PowerShell)
Get-Content logs\py_execute.log -Wait -Tail 10
```

**Linux/Mac:**
```bash
tail -f logs/py_execute.log
```

## 📈 Production Deployment

### Using Gunicorn

**Windows:**
```cmd
# Install gunicorn
pip install gunicorn

# Run with gunicorn
gunicorn main:app -w 4 -k uvicorn.workers.UvicornWorker --bind 0.0.0.0:8000
```

**Linux/Mac:**
```bash
# Install gunicorn
pip install gunicorn

# Run with gunicorn
gunicorn main:app -w 4 -k uvicorn.workers.UvicornWorker --bind 0.0.0.0:8000
```

### Using Docker
```dockerfile
FROM python:3.9-slim

WORKDIR /app
COPY requirements.txt .
RUN pip install -r requirements.txt

COPY . .
EXPOSE 8000

CMD ["python", "main.py"]
```

### Environment Setup
1. Set production environment variables
2. Configure reverse proxy (nginx/Apache)
3. Set up SSL certificates
4. Configure monitoring and logging

## 🤝 Integration

### Frontend Integration
The backend is designed to work with the standalone HTML frontend:

```javascript
// Toggle between PHP and Python backends
function toggleBackend() {
    OEM.usePythonBackend = !OEM.usePythonBackend;
    // Backend URL automatically switches to Python API
}
```

### TestLink Integration
- Uses native TestLink database schema
- Compatible with existing TestLink data
- No database modifications required

## 📝 License

This module is part of the TestLink project and follows the same license terms.

## 🆘 Support

For issues and support:
1. Check the logs: `logs/py_execute.log`
2. Review configuration: `.env`
3. Test database connectivity
4. Check API documentation: `http://localhost:8000/docs`
