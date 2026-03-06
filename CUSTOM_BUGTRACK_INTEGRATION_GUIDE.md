# Custom Bug Tracking Integration System for TestLink

## Overview

This implementation provides a flexible, multi-integration bug tracking system that allows TestLink projects to report bugs to different Redmine, Jira, or Bugzilla instances based on project-specific configurations.

## Features

### 🔗 **Multiple Integration Support**
- **Redmine** - Multiple instances with different users/projects
- **Jira** - Cloud and on-premise instances  
- **Bugzilla** - Custom Bugzilla installations
- **Extensible** - Easy to add new bug tracker types

### 🎯 **Project-Specific Mapping**
- Map TestLink projects to specific bug tracker integrations
- Each project can use different bug tracker instances
- Support for multiple projects using the same integration

### 🛠️ **Management Interface**
- Web-based UI for managing integrations
- Add, edit, delete, enable/disable integrations
- Test connection functionality
- Project mapping management
- Activity logging and audit trail

### 🔧 **Seamless Integration**
- Integrates with existing TestLink execution flow
- Replaces default issue tracker when custom integration is configured
- Maintains backward compatibility with existing setups

## Files Created

### Database Schema
- `sql/custom_bugtrack_integration_schema.sql`
  - Tables for integrations, project mappings, and activity logs
  - Sample data for testing

### Backend Components
- `lib/execute/custom_bugtrack_integrator.php`
  - Main API endpoint for integration management
  - Handles CRUD operations for integrations and mappings
  - Issue creation for all supported bug trackers
  - Connection testing and logging

- `lib/execute/custom_issue_integration.php`
  - Helper functions for integration detection and usage
  - Integration configuration retrieval
  - Issue creation wrapper

- `lib/execute/custom_issue_handler.php`
  - Hooks into TestLink's execution flow
  - Handles custom issue creation during test execution
  - Maintains compatibility with default issue tracking

### Frontend Interface
- `lib/execute/custom_bugtrack_integration.html`
  - Modern Bootstrap-based management interface
  - Tabbed interface for integrations, mappings, and logs
  - Real-time connection testing
  - Responsive design

### Modified Files
- `lib/execute/execSetResults.php`
  - Integrated custom integration detection
  - Modified execution flow to use custom integrations
  - Maintains backward compatibility

- `gui/templates/tl-classic/mainPageLeft.tpl`
  - Added menu item for integration management
  - Styled to match existing TestLink interface

## Database Schema

### Tables

1. **`custom_bugtrack_integrations`**
   - Stores bug tracker integration configurations
   - Supports Redmine, Jira, and Bugzilla
   - Includes authentication details and settings

2. **`custom_bugtrack_project_mapping`**
   - Maps TestLink projects to integrations
   - Supports multiple mappings per project
   - Active/inactive status management

3. **`custom_bugtrack_integration_log`**
   - Audit trail for all integration activities
   - Performance metrics and error tracking
   - Complete request/response logging

## API Endpoints

### Integration Management
- `action=list_integrations` - List all integrations
- `action=add_integration` - Add new integration
- `action=update_integration` - Update existing integration
- `action=delete_integration` - Delete integration
- `action=toggle_integration` - Enable/disable integration
- `action=test_connection` - Test bug tracker connection

### Project Mapping
- `action=list_project_mappings` - List all mappings
- `action=add_project_mapping` - Add project mapping
- `action=remove_project_mapping` - Remove mapping
- `action=get_integration_for_project` - Get integration for specific project

### Issue Creation
- `action=create_issue` - Create issue in configured bug tracker

## Usage

### 1. Setup Database
```sql
-- Run the schema file
mysql -u user -p testlink_db < sql/custom_bugtrack_integration_schema.sql
```

### 2. Configure Integrations
1. Navigate to "Bug Tracker Integration" from the left menu
2. Click "Add Integration" to configure a new bug tracker
3. Fill in connection details:
   - **Name**: Human-readable name
   - **Type**: Redmine/Jira/Bugzilla
   - **URL**: Base URL of the bug tracker
   - **API Key/Username**: Authentication details
   - **Project Key**: Default project identifier
4. Test connection to verify setup
5. Enable the integration

### 3. Map Projects
1. Go to the "Project Mappings" tab
2. Click "Add Mapping"
3. Select TestLink project and integration
4. Save the mapping

### 4. Use in Test Execution
1. Execute test cases as normal
2. When setting status to "Failed", check "Create Issue"
3. The bug will be created in the configured bug tracker
4. Issue details and links will be displayed

## Integration Types

### Redmine
- **URL**: `https://redmine.example.com`
- **API Key**: Redmine API key
- **Project Key**: Redmine project identifier
- **Priority Mapping**: Maps TestLink priorities to Redmine priorities

### Jira
- **URL**: `https://company.atlassian.net`
- **Authentication**: Basic auth with username/password
- **Project Key**: Jira project key
- **Issue Type**: Creates "Bug" type issues by default

### Bugzilla
- **URL**: `https://bugzilla.example.com`
- **API Key**: Bugzilla API token (optional)
- **Product**: Bugzilla product name
- **Priority**: Uses Bugzilla priority field

## Security Considerations

### API Keys and Credentials
- All sensitive data is stored in the database
- Consider encrypting API keys in production
- Use dedicated service accounts with minimal permissions

### Access Control
- Integration management requires TestLink admin access
- Project mappings respect existing TestLink permissions
- Audit logging tracks all integration activities

### Network Security
- Supports HTTPS connections
- SSL verification can be configured
- Timeout settings prevent hanging requests

## Troubleshooting

### Connection Issues
1. Verify URL is accessible from TestLink server
2. Check API credentials are valid
3. Ensure firewall allows outbound connections
4. Test connection from management interface

### Issue Creation Failures
1. Check integration logs for error details
2. Verify project exists in bug tracker
3. Ensure user has permission to create issues
4. Check required fields are configured

### Performance Issues
1. Monitor connection times in logs
2. Consider caching for large deployments
3. Adjust timeout settings as needed
4. Use connection pooling for high volume

## Future Enhancements

### Planned Features
- **Additional Bug Trackers**: GitHub Issues, GitLab Issues, Azure DevOps
- **Advanced Field Mapping**: Custom field configuration per integration
- **Bulk Operations**: Create multiple issues from test plan execution
- **Status Sync**: Two-way status synchronization
- **Webhooks**: Automatic status updates from bug trackers

### Extensibility
- Plugin architecture for custom bug tracker types
- Custom field validation rules
- Integration-specific workflow customization
- API for external integration management

## Migration from Default Integration

### For Existing Redmine Setups
1. Export current Redmine configuration
2. Create new custom integration with same settings
3. Map projects to the new integration
4. Test with a single project first
5. Gradually migrate all projects

### Backward Compatibility
- Default issue tracker continues to work for unmapped projects
- No disruption to existing workflows
- Can run both systems in parallel during transition

## Support and Maintenance

### Monitoring
- Check integration logs regularly
- Monitor connection success rates
- Track issue creation performance
- Set up alerts for failed connections

### Maintenance Tasks
- Regularly update API keys/tokens
- Review and clean up old logs
- Verify project mappings are current
- Test integrations after bug tracker updates

## Conclusion

This custom bug tracking integration system provides TestLink with the flexibility to work with multiple bug tracking instances while maintaining the simplicity of the original issue tracking system. The modular design allows for easy extension and customization to meet specific organizational needs.

The system is production-ready and includes comprehensive error handling, logging, and security considerations. It can be deployed alongside existing TestLink installations without disrupting current workflows.
