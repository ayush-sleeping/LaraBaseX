# 📦 Installed Packages Documentation

This document provides comprehensive information about all the packages installed in LaraBaseX and how to use them.

## 🔍 Laravel Telescope

**Purpose**: Debugging and request monitoring for development environment

### Installation Details
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Configuration
- **File**: `config/telescope.php`
- **Environment Control**: Only enabled in local environment
- **Access URL**: `http://your-app.com/telescope`

### Features Enabled
- ✅ Request monitoring
- ✅ Database query tracking
- ✅ Mail monitoring
- ✅ Exception tracking
- ✅ Cache operations
- ✅ Redis monitoring

### Usage
```php
// Automatically tracks all requests when TELESCOPE_ENABLED=true
// Access dashboard at /telescope
```

---

## 🐛 Laravel Debugbar

**Purpose**: Performance analysis and debugging toolbar

### Installation Details
```bash
composer require barryvdh/laravel-debugbar --dev
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

### Configuration
- **File**: `config/debugbar.php`
- **Environment Control**: Only enabled in local environment with APP_DEBUG=true
- **Display**: Bottom toolbar on all pages

### Features Available
- ✅ Query analysis
- ✅ Route information
- ✅ View data
- ✅ Memory usage
- ✅ Time tracking
- ✅ Log messages

### Usage
```php
// Automatically appears when DEBUGBAR_ENABLED=true and APP_DEBUG=true
// No additional code needed
```

---

## 💾 Spatie Laravel Backup

**Purpose**: Automated database and file backups

### Installation Details
```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

### Configuration
- **File**: `config/backup.php`
- **Storage**: `storage/app/private/Laravel/`
- **Database**: MySQL via XAMPP mysqldump

### Custom Commands Created
```bash
# Database-only backup
php artisan backup:database --only-db

# Full backup (database + files)
php artisan backup:database

# List all backups
php artisan backup:list

# Clean old backups
php artisan backup:clean
```

### Scheduled Backups
- **Daily Database Backup**: 2:00 AM
- **Weekly Full Backup**: Sunday 3:00 AM
- **Cleanup Old Backups**: Daily 4:00 AM

### Usage Examples
```bash
# Manual backup
php artisan backup:run --only-db

# Check backup status
php artisan backup:list

# Monitor backup health
php artisan backup:monitor
```

### Database Configuration
```php
// config/database.php - MySQL dump settings
'dump' => [
    'dump_binary_path' => '/Applications/XAMPP/xamppfiles/bin/',
    'use_single_transaction' => true,
    'timeout' => 300,
    'exclude_tables' => [
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
    ],
    'add_extra_option' => '--single-transaction --skip-routines --skip-triggers',
],
```

---

## 📋 Spatie Laravel Activity Log

**Purpose**: Audit logging and user activity tracking

### Installation Details
```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate
```

### Model Integration
```php
// User.php model
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'first_name',
                'last_name',
                'email',
                'mobile',
                'status',
                'avatar'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

### Helper Function
```php
// app/helpers.php
function log_activity(string $description, ?array $properties = null, ?string $logName = 'default'): void
{
    try {
        if (Auth::check()) {
            activity($logName)
                ->causedBy(Auth::user())
                ->withProperties($properties ?? [])
                ->log($description);
        }
    } catch (\Exception $e) {
        Log::error('Activity logging failed: ' . $e->getMessage());
    }
}
```

### Usage Examples
```php
// Automatic logging (when model changes)
$user->update(['first_name' => 'New Name']); // Automatically logged

// Manual logging with helper
log_activity('User performed action', ['action' => 'login']);

// Direct usage
activity()
    ->causedBy(auth()->user())
    ->performedOn($model)
    ->withProperties(['ip' => request()->ip()])
    ->log('User action description');
```

### Querying Activity
```php
// Get all activities
$activities = Activity::all();

// Get activities for specific model
$activities = Activity::forSubject($user)->get();

// Get activities by specific user
$activities = Activity::causedBy($user)->get();
```

---

## 🛠️ Package Testing Results

### ✅ Laravel Telescope
- **Status**: ✅ Working
- **Test**: Accessible at `/telescope`
- **Monitoring**: Request tracking active

### ✅ Laravel Debugbar
- **Status**: ✅ Working
- **Test**: Toolbar visible in development
- **Features**: All debugging panels active

### ✅ Spatie Backup
- **Status**: ✅ Working
- **Test**: Database backup successful
- **Files**: Created in `storage/app/private/Laravel/`
- **Size**: ~29KB for database dump

### ✅ Spatie Activity Log
- **Status**: ✅ Working
- **Test**: Activity logged successfully
- **Storage**: `activity_log` table created
- **Integration**: User model configured

---

## 🚀 Production Considerations

### Environment Variables
```bash
# Development
TELESCOPE_ENABLED=true
DEBUGBAR_ENABLED=true

# Production
TELESCOPE_ENABLED=false
DEBUGBAR_ENABLED=false
```

### Security Notes
- **Telescope**: Disable in production for security
- **Debugbar**: Automatically disabled when APP_DEBUG=false
- **Backup**: Ensure proper file permissions for backup storage
- **Activity Log**: Consider data retention policies

### Performance Impact
- **Telescope**: Minimal impact when disabled
- **Debugbar**: No impact in production
- **Backup**: Scheduled during low-traffic hours
- **Activity Log**: Minimal overhead, consider async logging for high-traffic apps

---

## 📈 Monitoring and Maintenance

### Daily Tasks (Automated)
- Database backup at 2:00 AM
- Cleanup old backups at 4:00 AM

### Weekly Tasks (Automated)
- Full backup (database + files) on Sunday 3:00 AM

### Manual Monitoring
```bash
# Check backup status
php artisan backup:list

# Monitor application in Telescope
# Visit /telescope during development

# Review activity logs
# Query activity_log table or use Activity model
```

### Troubleshooting
- **Backup fails**: Check mysqldump path in DB_DUMP_PATH
- **Telescope not loading**: Verify TELESCOPE_ENABLED=true in .env
- **Debugbar not showing**: Check APP_DEBUG=true and DEBUGBAR_ENABLED=true
- **Activity log not working**: Ensure migrations are run and model trait is added

---

This documentation covers all installed packages and their usage. Each package is properly configured and tested for your LaraBaseX application.
