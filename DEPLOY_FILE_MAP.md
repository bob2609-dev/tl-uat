# Deployment File Map

This file lists all modified files and their locations to guide UAT → PROD deployments.

## High-Risk Items

- Stored XSS (Inventory module)
  - gui/templates/tl-classic/inventory/inventoryView.tpl
  - lib/inventory/setInventory.php

- Reflected XSS (link entry points)
  - linkto.php
  - ltx.php

- Time-based SQLi (Search) and ORDER BY hardening
  - lib/search/searchCommands.class.php
  - lib/functions/database.class.php
  - lib/functions/testplan.class.php
  - lib/functions/tlCodeTracker.class.php
  - lib/functions/tlIssueTracker.class.php
  - lib/functions/tlReqMgrSystem.class.php

- Role modification via DevTools
  - lib/usermanagement/usersEdit.php

- Predictable machineOwner validation
  - lib/inventory/setInventory.php
  - lib/functions/tlInventory.class.php
  - locale/en_US/custom_strings.txt
  - locale/en_GB/custom_strings.txt

## Medium-Risk Items

- File download/header hardening
  - direct_image.php

- API input/header hardening
  - redmine_status_api.php

- Exposed config/dev/info files review (ongoing)
  - .htaccess
  - diagnostic.php

- Login flow validation (cookies + CSRF)
  - lib/functions/common.php
  - login.php
  - lib/functions/csrf.php

## Notes

- No database schema changes are required for the above items.
- After copying PHP files, clear opcode cache (restart PHP-FPM or gracefully reload Apache) to load new code.
- Keep timestamped backups of replaced files on PROD for rollback.
