# Health Check Endpoints

LaraBaseX includes comprehensive health check endpoints for monitoring application status and integrating with load balancers, monitoring systems, and DevOps tools.

## Endpoints

### Basic Health Check
- **URL**: `/api/health`
- **Method**: `GET`
- **Purpose**: Simple health check for load balancers
- **Authentication**: None required

**Response Example**:
```json
{
    "status": "OK",
    "timestamp": "2025-08-08T16:25:59.830873Z",
    "service": "LaraBaseX",
    "version": "1.0.0"
}
```

### Detailed Health Check
- **URL**: `/api/health/detailed`
- **Method**: `GET`
- **Purpose**: Comprehensive health check with service details
- **Authentication**: None required

**Response Example**:
```json
{
    "status": "healthy",
    "timestamp": "2025-08-08T16:25:59.830873Z",
    "service": "LaraBaseX",
    "version": "1.0.0",
    "environment": "local",
    "checks": {
        "database": {
            "healthy": true,
            "status": "connected",
            "driver": "mysql",
            "query_time_ms": 1.4,
            "response_time_ms": 5.37,
            "tables_accessible": true,
            "user_count": 5
        },
        "cache": {
            "healthy": true,
            "status": "operational",
            "driver": "file",
            "write_time_ms": 0.57,
            "read_time_ms": 0.11,
            "response_time_ms": 0.95
        },
        "storage": {
            "healthy": true,
            "status": "operational",
            "details": {
                "local": {
                    "healthy": true,
                    "status": "operational",
                    "disk": "local"
                },
                "public": {
                    "healthy": true,
                    "status": "operational",
                    "disk": "public"
                },
                "disk_space": {
                    "healthy": true,
                    "status": "ok",
                    "usage_percent": 62.29,
                    "free_gb": 86.09,
                    "total_gb": 228.27
                }
            },
            "response_time_ms": 0.87
        },
        "queue": {
            "healthy": true,
            "status": "operational",
            "driver": "database",
            "response_time_ms": 0.1
        },
        "application": {
            "healthy": true,
            "status": "operational",
            "php_version": "8.3.12",
            "laravel_version": "12.21.0",
            "environment": "local",
            "debug_mode": true,
            "timezone": "UTC",
            "maintenance_mode": false,
            "issues": [],
            "response_time_ms": 0.09
        },
        "backup": {
            "healthy": true,
            "status": "operational",
            "latest_backup": "2025-08-08-14-50-23.zip",
            "backup_age_hours": 0.97,
            "backup_count": 2,
            "backup_path": "/Applications/XAMPP/xamppfiles/htdocs/LaraBaseX/storage/app/private/LaraBaseX",
            "response_time_ms": 0.34
        }
    },
    "response_time_ms": 7.73,
    "uptime": {
        "seconds": 432,
        "human": "7 minutes",
        "started_at": "2025-08-08T15:41:05.000000Z"
    }
}
```

## Service Checks

The detailed health check monitors:

1. **Database**: Connection, query performance, table access, user count
2. **Cache**: Read/write operations, driver status, performance
3. **Storage**: Disk accessibility, space usage, multiple disk support
4. **Queue**: Driver status, connection health
5. **Application**: PHP/Laravel versions, environment, maintenance mode
6. **Backup**: Latest backup status, age, count, path verification

## Integration

### Load Balancers
Use the basic endpoint `/api/health` for load balancer health checks:
- Returns HTTP 200 with "OK" status when healthy
- Simple JSON response for fast parsing

### Monitoring Systems
Use the detailed endpoint `/api/health/detailed` for comprehensive monitoring:
- Includes performance metrics (response times)
- Service-specific health indicators
- Uptime tracking
- Issue detection and reporting

### CI/CD Integration
Both endpoints can be used in deployment pipelines:
- Verify application health after deployment
- Monitor service dependencies
- Automated testing of critical components

## Response Times
All health checks include response time measurements:
- Individual service check times
- Overall health check response time
- Performance monitoring for optimization

## Security
- No authentication required (for monitoring systems)
- No sensitive data exposed in responses
- Rate limiting applied through standard API middleware
