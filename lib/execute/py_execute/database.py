import mysql.connector
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
