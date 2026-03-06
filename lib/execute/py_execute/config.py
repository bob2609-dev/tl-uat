import os

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
