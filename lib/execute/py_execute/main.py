from fastapi import FastAPI
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
