# TestLink Test Execution Export Tool

This tool exports test execution data from the TestLink database to Excel format with multiple sheets. It replicates the logic found in `test_execution_summary.php` but provides the data in Excel format for easier analysis and sharing.

## Features

- Query test execution data from TestLink database
- Filter by project, test plan, build, status, and date range
- Export to Excel with multiple dashboard-style sheets:
  - Execution Overview - Overall metrics and statistics
  - Top Testers - Tester performance statistics
  - Test Suite Progress - Progress tracking by suite
  - Test Suite Execution Summary - Detailed suite metrics
  - Execution Details - Detailed test case execution data
- Standalone executables for both real database and mock data
- Runtime configuration via external JSON file (no hardcoded credentials)

## Installation

1. Make sure Python 3.6+ is installed on your system
2. Install required packages:

```
pip install -r requirements.txt
```

## Configuration

### Option 1: JSON Configuration File
Create a JSON configuration file (e.g., `db_config.json`) with your database connection settings:

```json
{
  "host": "localhost",
  "user": "testlink",
  "password": "yourpassword",
  "database": "testlink",
  "port": 3306
}
```

### Option 2: Script Configuration
For development, you can also edit `script_DB_config.py` to configure your database connection:

```python
# Database connection parameters
DB_CONFIG = {
    'host': 'your_db_host',        # Database host
    'port': 3306,                  # Database port (default MySQL port)
    'user': 'your_db_user',        # Database username
    'password': 'your_password',   # Database password
    'database': 'your_db_name',    # Database name
    'charset': 'utf8mb4',          # Character set
    'use_unicode': True            # Use Unicode
}
```

## Usage

### Standalone Executables

Two standalone executables are provided in the `dist` folder:

1. **Real Database Version:**
   ```
   testlink_export.exe --config path\to\db_config.json [options]
   ```

2. **Mock Data Version (for testing/demo):**
   ```
   testlink_export_mock.exe [options]
   ```

### Python Script

You can also run the Python scripts directly:

```
python test_execution_export.py --config path\to\db_config.json [options]
python test_execution_export_mock.py [options]
```

### Options:
- `--config <file>`: Path to database config JSON file (required for real DB version)
- `--project <id>`: Filter by project ID
- `--testplan <id>`: Filter by test plan ID
- `--build <id>`: Filter by build ID
- `--status <status>`: Filter by execution status (p=passed, f=failed, b=blocked, n=not run)
- `--startdate <YYYY-MM-DD>`: Filter by execution start date
- `--enddate <YYYY-MM-DD>`: Filter by execution end date (YYYY-MM-DD)
- `-o, --output`: Output file path
- `-d, --output-dir`: Output directory
- `-l, --list`: List available projects, test plans, and builds

### Examples

#### Real Database Examples:
List available projects, test plans, and builds:
```
python test_execution_export.py -l
```

Export data for a specific project:
```
python test_execution_export.py -p 1
```

Export data for a specific test plan and build:
```
python test_execution_export.py -t 5 -b 10
```

Export only passed test cases:
```
python test_execution_export.py -s p
```

Export with date range:
```
python test_execution_export.py --start-date 2025-01-01 --end-date 2025-07-11
```

Specify output file:
```
python test_execution_export.py -o my_report.xlsx
```

Specify output directory:
```
python test_execution_export.py -d /path/to/reports
```

## Excel Output Structure

The generated Excel file contains the following sheets:

1. **Summary**: Overall metrics and charts showing test execution status distribution
2. **Projects**: Project-level statistics including test plan count and execution status
3. **TestPlans**: Test plan details with test case counts and execution status
4. **TestSuites**: Suite-level statistics with hierarchical path information
5. **Executions**: Detailed list of all test case executions with status, tester info, etc.

## Modular Structure

The tool consists of three main modules:

1. **db_connector.py**: Handles database connections and basic queries
2. **data_processor.py**: Processes data and implements core business logic
3. **excel_exporter.py**: Handles Excel file creation and formatting
4. **test_execution_export.py**: Main script that integrates all modules
