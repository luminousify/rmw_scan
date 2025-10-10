# CKU Scan - Agent Development Guide

## Build Commands
- `npm run dev` - Start Tailwind CSS watch mode for development
- `npm run build` - Build CSS with Tailwind (watch mode)
- `npm run build-prod` - Build minified CSS for production

## Testing
- No formal test framework configured
- Use `test.php` for manual testing
- Database tests: `test_rmw.php`
- Connection tests: `check_connection.php`

## Code Style Guidelines

### PHP
- Use PSR-4 autoloading style for class files
- Database operations: Use PDO with prepared statements
- Error handling: Try-catch blocks with meaningful exceptions
- Session management: Start session with `session_start()` at top of controllers
- Authentication: Check `$_SESSION['loggedin']` before protected content

### File Structure
- Controllers: `app/controllers/` - Handle request routing
- Views: `app/` - Main application pages
- Includes: `includes/` - Shared components and utilities
- Database: `database/` - Schema and migration files
- Assets: `includes/css/`, `includes/js/`, `includes/img/`

### Database
- Use `DatabaseManager` class for connections
- Support both SQLite and MySQL via DB_TYPE config
- Use prepared statements for all queries
- Connection config in `config.php`
- **Important**: When using DatabaseManager in controllers, include: `require_once '../../includes/DatabaseManager.php';`

### Frontend
- Tailwind CSS for styling with shadcn/ui components
- jQuery for DOM manipulation
- Bootstrap Icons for UI icons
- Responsive design with mobile-first approach

### Security
- Input validation via `validate.php`
- SQL injection prevention with prepared statements
- Session-based authentication
- Path traversal protection via `url()` and `path()` helpers

## User Notification System

### BurntToast Integration
The system uses Windows BurntToast module to notify users when the AI assistant requires attention or action. This provides immediate visual feedback for completed tasks, errors, or user input requirements.

#### Prerequisites
```bash
# Install BurntToast module (run once)
Install-Module -Name BurntToast -Scope CurrentUser

# Verify installation
Get-Module -ListAvailable -Name "BurntToast"
```

#### Notification Templates

##### Task Completion Notifications
```powershell
# Success notification for completed tasks
Import-Module BurntToast
New-BurntToastNotification -Text "✅ Task Completed!", "Products page fixes completed successfully" -AppLogo "C:\Windows\System32\imageres.dll,78"

# With detailed breakdown
New-BurntToastNotification -Text "🎉 Development Complete", "Fixed 3 critical issues in products module" -Header (New-BTHeader -Id "Dev-Complete" -Title "DPR System Update")
```

##### Error/Warning Notifications
```powershell
# Error notification
Import-Module BurntToast
New-BurntToastNotification -Text "⚠️ Action Required", "Critical error found in DER module validation" -AppLogo "C:\Windows\System32\imageres.dll,79"

# Warning notification
New-BurntToastNotification -Text "⚡ Attention Needed", "Database connection timeout detected" -AppLogo "C:\Windows\System32\imageres.dll,80"
```

##### User Input Required
```powershell
# Request for user decision
Import-Module BurntToast
New-BurntToastNotification -Text "🤔 Decision Required", "Multiple solutions available - please choose approach" -Header (New-BTHeader -Id "User-Input" -Title "AI Assistant Request")

# Confirmation needed
New-BurntToastNotification -Text "❓ Please Confirm", "About to apply database schema changes" -AppLogo "C:\Windows\System32\imageres.dll,81"
```

##### Progress Updates
```powershell
# Long-running task notification
Import-Module BurntToast
New-BurntToastNotification -Text "🔄 Processing", "Running PHPStan analysis on DER module..." -Header (New-BTHeader -Id "Progress" -Title "Background Task")

# Milestone reached
New-BurntToastNotification -Text "📊 Analysis Complete", "Found 3 type hints to be added" -AppLogo "C:\Windows\System32\imageres.dll,82"
```

#### AI Assistant Usage Guidelines

When to trigger notifications:

1. **✅ Task Completion**: After completing complex multi-step tasks
2. **⚠️ Critical Issues**: When errors prevent continuation
3. **❓ User Decisions**: Multiple valid approaches need user choice
4. **🔄 Long Operations**: Tasks taking >30 seconds (progress updates)
5. **🚨 Blocking Issues**: Problems requiring immediate user intervention

Notification content standards:
- **Title**: Clear, concise status (max 50 chars)
- **Message**: Specific details about what was done or needed
- **Icons**: Use appropriate visual indicators (✅, ⚠️, ❓, 🔄, 🚨)
- **Headers**: Use descriptive headers for related notifications

#### PowerShell Functions for Development
```powershell
# Add to PowerShell profile for easy access
function Send-CompletionNotification {
    param([string]$Title, [string]$Message)
    Import-Module BurntToast
    New-BurntToastNotification -Text "✅ $Title" -Message $Message -AppLogo "C:\Windows\System32\imageres.dll,78"
}

function Send-ErrorNotification {
    param([string]$Title, [string]$Message)
    Import-Module BurntToast
    New-BurntToastNotification -Text "⚠️ $Title" -Message $Message -AppLogo "C:\Windows\System32\imageres.dll,79"
}

function Send-InputRequiredNotification {
    param([string]$Title, [string]$Message)
    Import-Module BurntToast
    New-BurntToastNotification -Text "❓ $Title" -Message $Message -Header (New-BTHeader -Id "User-Input" -Title "AI Assistant Request")
}
```

#### Quick Reference Examples
```powershell
# After fixing bugs
Send-CompletionNotification -Title "Products Module Fixed" -Message "Created date sort, dynamic cards, delete button fixed"

# When encountering database issues
Send-ErrorNotification -Title "Database Error" -Message "Connection timeout - check MySQL service status"

# When user needs to choose approach
Send-InputRequiredNotification -Title "Architecture Decision" -Message "Service vs Repository pattern for new feature"
```