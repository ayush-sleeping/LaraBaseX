# PHPStan Static Analysis

This document describes the PHPStan static analysis setup for LaraBaseX.

## Overview

PHPStan is a static analysis tool for PHP that helps find bugs in your code without running it. It's particularly useful for Laravel applications with its Larastan extension.

## Installation

PHPStan with Larastan is already installed as a development dependency:

```bash
composer require --dev larastan/larastan
```

## Configuration

### Main Configuration File: `phpstan.neon`

The configuration file includes:

- **Analysis Level**: Set to level 6 (good balance of strictness and practicality)
- **Paths Analyzed**: `app/`, `database/factories/`, `database/seeders/`, `routes/`, `tests/`
- **Laravel Integration**: Full Larastan extension for Laravel-specific features
- **Parallel Processing**: Enabled for faster analysis
- **Custom Stubs**: Additional type definitions in `stubs/common.stub`

### Key Configuration Options

```yaml
level: 6                           # Analysis strictness (0-10)
paths: [app/, tests/, routes/]     # Directories to analyze
parallel: true                     # Enable parallel processing
checkModelProperties: true        # Check Eloquent model properties
```

## Usage

### Composer Scripts

Several convenient scripts are available:

```bash
# Run PHPStan analysis
composer phpstan

# Generate baseline (capture current state)
composer phpstan:baseline

# Clear result cache
composer phpstan:clear

# Complete code quality check (Pint + PHPStan + Tests)
composer code:check

# Fix code style and run analysis
composer code:fix
```

### Direct Commands

```bash
# Basic analysis
vendor/bin/phpstan analyse

# With custom memory limit
vendor/bin/phpstan analyse --memory-limit=2G

# Generate baseline
vendor/bin/phpstan analyse --generate-baseline

# Different output formats
vendor/bin/phpstan analyse --error-format=json
vendor/bin/phpstan analyse --error-format=github
```

### Shell Script

A comprehensive shell script is available for advanced usage:

```bash
# Basic analysis
./scripts/phpstan.sh

# Generate baseline
./scripts/phpstan.sh --baseline

# Custom level and paths
./scripts/phpstan.sh --level 8 --paths app/Models/

# Clear cache and run
./scripts/phpstan.sh --clear-cache --verbose

# Custom output format
./scripts/phpstan.sh --format json
```

## Error Types and Solutions

### Common Issues and Fixes

#### 1. Missing Return Types

**Issue**: `Method has no return type specified`

**Fix**: Add return type declarations
```php
// Before
public function getUser() {
    return auth()->user();
}

// After
public function getUser(): ?User {
    return auth()->user();
}
```

#### 2. Missing Parameter Types

**Issue**: `Parameter has no type specified`

**Fix**: Add parameter type hints
```php
// Before
public function updateUser($data) {
    // code
}

// After
public function updateUser(array $data): User {
    // code
}
```

#### 3. Array Type Specification

**Issue**: `No value type specified in iterable type array`

**Fix**: Use specific array types
```php
// Before
private $rules = [];

// After
/** @var array<string, string> */
private $rules = [];
```

#### 4. Laravel Dynamic Methods

**Issue**: `Call to an undefined method`

**Fix**: Use proper type hints or add to ignored patterns
```php
// Use specific model types
/** @var User $user */
$user = User::find(1);
$user->someMethod(); // PHPStan now knows this is a User
```

#### 5. Environment Calls Outside Config

**Issue**: `Called 'env' outside of the config directory`

**Fix**: Use `config()` helper instead
```php
// Before
$value = env('APP_NAME');

// After
$value = config('app.name');
```

## Baseline Management

### Understanding Baselines

A baseline captures the current state of errors, allowing you to:
- Focus on new issues in new code
- Gradually improve existing code
- Prevent regression

### Working with Baselines

```bash
# Generate initial baseline
composer phpstan:baseline

# Run analysis (only shows new errors)
composer phpstan

# Update baseline after fixing issues
composer phpstan:baseline
```

### Best Practices

1. **Start with Baseline**: Generate baseline for existing projects
2. **Regular Updates**: Update baseline as you fix issues
3. **Team Coordination**: Share baseline file in version control
4. **Progressive Improvement**: Gradually increase analysis level

## Integration

### IDE Integration

#### VS Code

Install the PHPStan extension:
```json
{
    "phpstan.enabled": true,
    "phpstan.configFile": "./phpstan.neon",
    "phpstan.memoryLimit": "2G"
}
```

#### PhpStorm

1. Go to Settings → PHP → Quality Tools → PHPStan
2. Set PHPStan path: `vendor/bin/phpstan`
3. Set configuration file: `phpstan.neon`

### CI/CD Integration

#### GitHub Actions

The project includes a GitHub Actions workflow (`.github/workflows/static-analysis.yml`) that:

- Runs PHPStan on multiple PHP versions (8.2, 8.3)
- Caches results for faster subsequent runs
- Reports errors in GitHub-friendly format
- Integrates with Laravel Pint and tests

#### Pre-commit Hooks

Add to `.git/hooks/pre-commit`:
```bash
#!/bin/sh
composer code:check
```

## Performance Optimization

### Memory Usage

```bash
# Increase memory limit for large projects
vendor/bin/phpstan analyse --memory-limit=4G
```

### Parallel Processing

```yaml
# In phpstan.neon
parameters:
    parallel:
        jobSize: 20
        maximumNumberOfProcesses: 32
```

### Result Caching

PHPStan automatically caches results. To manage cache:

```bash
# Clear cache
vendor/bin/phpstan clear-result-cache

# View cache info
ls -la .phpstan-result-cache/
```

## Custom Rules and Extensions

### Stub Files

Custom type definitions are in `stubs/common.stub`:
- Enhanced Laravel collection types
- Request parameter types
- Builder method types

### Ignoring Specific Errors

```yaml
# In phpstan.neon
parameters:
    ignoreErrors:
        - '#Call to an undefined method App\\Models\\User::[a-zA-Z0-9_]+\(\)#'
        -
            message: '#Cannot access offset#'
            path: vendor/*
```

## Troubleshooting

### Common Issues

1. **High Memory Usage**: Increase memory limit or reduce parallel processes
2. **Slow Analysis**: Enable parallel processing and use result cache
3. **False Positives**: Use baseline or add specific ignores
4. **Laravel Methods Not Found**: Ensure Larastan is properly loaded

### Debug Mode

```bash
# Run with verbose output
vendor/bin/phpstan analyse -v

# Debug autoloading issues
vendor/bin/phpstan analyse --debug
```

## Resources

- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Larastan Documentation](https://github.com/larastan/larastan)
- [PHPStan Rules Reference](https://phpstan.org/user-guide/rules)
- [Laravel Static Analysis Best Practices](https://laravel.com/docs/11.x/packages#static-analysis)

## Current Status

- **Analysis Level**: 6/10
- **Total Issues Found**: 203 (baselined)
- **Files Analyzed**: 80
- **Integration**: GitHub Actions, Composer Scripts
- **Next Steps**: Gradually fix issues and increase analysis level
