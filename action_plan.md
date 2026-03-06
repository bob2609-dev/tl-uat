# Security Action Plan for TestLink Application

## Overview
This action plan addresses the security vulnerabilities identified in the VAPT report for the CBS Test Management TestLink application. The plan prioritizes issues based on risk rating (High followed by Medium) and provides specific remediation steps for each issue.

## High Risk Issues

### 1. Stored Cross-Site Scripting (XSS) in Inventory Module
**Status**: Pending  
**Description**: The inventory module accepts unsanitized user input in several fields, allowing malicious scripts in POST requests to be stored and executed when other users view the inventory page.  
**Impact**: Session hijacking, credential theft, or redirection to malicious sites; affects all users who view the compromised inventory entry.

**Remediation Plan**:
- Implement output encoding for all user-supplied data rendered in HTML pages
- Apply context-aware encoding (e.g., htmlspecialchars) in PHP
- Validate input strictly to only accept safe, expected characters (alphanumerics, dashes)
- Modify files:
  - Files handling inventory creation/editing (need to identify specific files)
  - Implementation will involve adding proper input validation and output encoding functions

### 2. Time-Based SQL Injection in Search Function
**Status**: Pending  
**Description**: The search functionality using the edited_by parameter is vulnerable to time-based blind SQL injection. The search query is constructed unsafely with user input concatenated directly into the SQL query.  
**Impact**: Data exposure, sensitive data access, system resource abuse, denial of service.

**Remediation Plan**:
- Implement prepared statements for all database queries in the search functionality
- Replace direct string concatenation with parameterized queries
- Apply server-side validation for input parameters
- Files to modify:
  - Search-related files containing the vulnerable query
  - Focus on any file handling the 'edited_by' parameter

### 3. Role Modification via Developer Tools
**Status**: Pending  
**Description**: When an admin role is assigned to a user, it can be changed to another role by using browser developer tools. The disabled attribute on role fields can be bypassed.  
**Impact**: Unauthorized modification of user roles, potentially escalating privileges.

**Remediation Plan**:
- Move all access control logic to the server-side
- Validate that the machine owner value corresponds to the authenticated session
- Add double-checking of role assignments on the server before committing changes
- Never rely on HTML attributes like 'disabled' for security
- Files to modify:
  - User management and role assignment files

### 4. Exposed Backup Installation Path
**Status**: Pending  
**Description**: Install/setup/backup php files publicly accessible, posing a risk during database setup phases.  
**Impact**: May allow unauthorized reinstallation or exposure of DB configuration.

**Remediation Plan**:
- Delete or restrict access to old installer files
- Move necessary installation files outside web root
- Implement .htaccess rules to block access to sensitive paths
- Files/directories to secure:
  - Install/setup/backup directories

### 5. Exposed Diagnostic Endpoint
**Status**: Pending  
**Description**: CGI script /cgi-bin/printenv.pl was publicly accessible, printing all server environment variables.  
**Impact**: Script exposes sensitive server environment details, helping attackers gather information for further attacks.

**Remediation Plan**:
- Disable unused CGI scripts
- Remove default or test scripts from production servers
- Configure web server to block access to diagnostic endpoints
- Files to modify:
  - Remove or secure /cgi-bin/printenv.pl
  - Update web server configuration

### 6. Predictable machineOwner
**Status**: Pending  
**Description**: When submitting forms, the machineOwner field uses a numeric user ID in the POST body that can be manipulated.  
**Impact**: An attacker can impersonate or assign assets under other users through predictable IDs.

**Remediation Plan**:
- Delete or restrict access to old installer files or backup folders
- Implement server-side validation of user ownership
- Use session-based authentication instead of relying on machineOwner parameters
- Files to modify:
  - Form processing files that use machineOwner field

### 7. Cross-Site Request Forgery (CSRF)
**Status**: Pending  
**Description**: Install/setup/backup php files publicly accessible during installation process.  
**Impact**: An attacker can cause state-changing actions on behalf of authenticated users.

**Remediation Plan**:
- Apply CSRF tokens on all state-changing forms and APIs
- Enforce Same-Site=Strict for cookies
- Validate origin/referrer headers where applicable
- Files to modify:
  - All form submission handlers
  - Authentication system

## Medium Risk Issues

### 8. Exposed Configuration Files
**Status**: Pending  
**Description**: Sensitive files such as config files, .htaccess, and .htpasswd were directly accessible from the web server.  
**Impact**: May expose database credentials, internal paths, and environment variables.

**Remediation Plan**:
- Apply proper access controls (e.g., .htaccess, Nginx rules)
- Do not expose config files to the public
- Move configuration files outside web root where possible
- Files to protect:
  - Any .php.config files
  - .htaccess files
  - Configuration files containing credentials

### 9. Directory Listing Enabled
**Status**: Pending  
**Description**: Directories such as /lib/, /docs/, and /logs/ allowed full directory indexing and browsing.  
**Impact**: Attackers can discover sensitive files, unused components, or outdated libraries.

**Remediation Plan**:
- Disable directory listing via web server configuration
- Add "Options -Indexes" in Apache configuration
- Create index files in directories that should be accessible
- Files to modify:
  - Web server configuration files (.htaccess, apache2.conf, etc.)

### 10. Exposed Development/Info Files
**Status**: Pending  
**Description**: Internal documentation and development artifacts such as README and documentation files were publicly accessible.  
**Impact**: Files can reveal application versions, components, plugin info, or other technical logic helpful to attackers.

**Remediation Plan**:
- Remove unnecessary documentation and internal files from the public directory
- Restrict access to development files
- Files to secure:
  - README files
  - Documentation files
  - Development artifacts

### 11. Unhelpful Debug Error & Path Disclosure
**Status**: Pending  
**Description**: Application displays raw debug output when database errors occur, showing stack traces including file paths and SQL exceptions.  
**Impact**: Revealing full stack traces and filesystem paths can aid attackers in crafting targeted exploits.

**Remediation Plan**:
- Disable debug mode in production environments
- Configure error handling to log details server-side only
- Ensure generic error messages are displayed to users
- Files to modify:
  - Error handling configuration
  - Database connection files

### 12. Missing Secure, HttpOnly, and SameSite Flags on Cookies
**Status**: Pending  
**Description**: Application sets cookies without Secure, HttpOnly, or SameSite attributes.  
**Impact**: Lack of these attributes may lead to cookie theft via XSS, interception over HTTP, or CSRF.

**Remediation Plan**:
- Set HttpOnly to prevent JavaScript access
- Configure Secure to allow cookies only over HTTPS
- Set SameSite=Strict or Lax to limit cross-site requests
- Files to modify:
  - Session handling files
  - Cookie setting functionality

## Progress Tracking

| No. | Issue | Status | Modified Files | Modifications | Date |
|-----|-------|--------|----------------|--------------|------|
| 1 | Stored XSS in Inventory Module | In Progress | lib/inventory/setInventory.php | Added input sanitization with htmlspecialchars() for all user inputs in the inventory module. Fixed type mismatch by converting stdClass to array. | 2025-06-20 |
| 2 | Time-Based SQL Injection | In Progress | lib/search/searchCommands.class.php | Applied proper input sanitization using $db->prepare_string() for both edited_by and created_by parameters in the searchReq method. Fixed incomplete SQL query formation. Also fixed an undefined variable reference by using this->cfieldMgr instead of $cfieldMgr. | 2025-06-20 |
| 3 | Role Modification via Developer Tools | Pending | | | |
| 4 | Exposed Backup Installation Path | Pending | | | |
| 5 | Exposed Diagnostic Endpoint | Pending | | | |
| 6 | Predictable machineOwner | Pending | | | |
| 7 | Cross-Site Request Forgery | Pending | | | |
| 8 | Exposed Configuration Files | Pending | | | |
| 9 | Directory Listing Enabled | Pending | | | |
| 10 | Exposed Development/Info Files | Pending | | | |
| 11 | Unhelpful Debug Error & Path Disclosure | Pending | | | |
| 12 | Missing Cookie Security Flags | Pending | | | |

## Next Steps
1. Identify specific files that need to be modified for each vulnerability
2. Create test cases to verify vulnerabilities are fixed
3. Implement fixes in priority order (High risk first)
4. Test each fix to ensure functionality isn't broken
5. Document all changes and update the progress tracking table
