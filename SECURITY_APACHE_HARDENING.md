# Apache Hardening for TestLink (VAPT Remediation)

This guide documents Apache/web-server level mitigations applied to this TestLink instance to address VAPT findings (directory listing, exposed endpoints, exposed install path, and general sensitive file access).

## Changes Applied

- Blocked sensitive files and common dev/info files via `FilesMatch` rules in `.htaccess`.
- Disabled directory listing globally (`Options -Indexes`).
- Blocked SCM and vendor folders (`/.git`, `/.svn`, `/vendor`).
- Blocked `install/` directory in production.
- Restricted `diagnostic.php` to localhost only.
- Provided optional security headers (commented) you can safely enable.

## Final `.htaccess` Excerpts

Sensitive files block:
```apache
<FilesMatch "(?i)(^\.|dockerfile|config\.inc\.php|info\.php|README\.md|CHANGELOG)$">
  Require all denied
</FilesMatch>

<FilesMatch "(?i)\.(env|git|htaccess|htpasswd|bak|conf|config|ini|inc|log|sh|sql|swp|~)$">
  Require all denied
</FilesMatch>
```

Disable listings and block VCS/vendor:
```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteRule ^(\.git|\.svn|vendor)/ - [F,L]
</IfModule>
Options -Indexes
```

Block install directory:
```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteRule ^install/ - [F,L]
</IfModule>
```

Restrict diagnostics to localhost:
```apache
<Files "diagnostic.php">
  Require local
  <IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
    Allow from 127.0.0.1 ::1
  </IfModule>
</Files>
```

Optional headers (enable when validated):
```apache
<IfModule mod_headers.c>
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "DENY"
  Header set X-XSS-Protection "0"  # modern browsers ignore; kept for legacy
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
  Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
</IfModule>
```

## How to Enable on Apache

1) Ensure `.htaccess` is honored:
- In site conf, set `AllowOverride All` (or at least `FileInfo, AuthConfig, Limit, Options`) for the TestLink DocumentRoot.

2) Reload Apache:
- Linux: `sudo systemctl reload apache2` or `sudo systemctl reload httpd`
- Windows: restart Apache service from Services.msc

3) Verify:
- Browse to `.../install/` → should return 403/blocked.
- Browse to `.../diagnostic.php` from remote host → should return 403.
- Directory listing for folders (e.g., `.../lib/`) → should be disabled.
- Access to files like `.env`, `.git/config`, `*.sql`, `*.bak` → should be denied.

## Rollback / Exceptions

- To temporarily allow `diagnostic.php` remotely, comment the `<Files "diagnostic.php"> ... </Files>` block. Re-enable immediately after use.
- If you need the `install/` directory for upgrades, temporarily comment the `RewriteRule ^install/ - [F,L]` and restore thereafter.

## Notes

- These server-level controls complement application-layer fixes (CSRF enforcement, cookie flags, input validation). Keep both layers.
- If you have a fronting reverse proxy (NGINX), mirror these rules there as well (e.g., location blocks for `/install/` and `/diagnostic.php`).
