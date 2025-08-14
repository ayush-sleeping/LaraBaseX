# Developer Experience Documentation


<div id="top"></div>

<br>

### Table of Contents
1. [Global Exception Handler](#global-exception-handler)
2. [Standard API Response](#standard-api-response)
3. [Seeder and Factory](#seeder-and-factory)
4. [Env Example File](#env-example-file)
5. [API Documentation](#api-documentation)
6. [Postman Collection](#postman-collection)
7. [Static Analysis](#static-analysis)
8. [Predefined Error Messages](#predefined-error-messages)

<br>

<br>

#

## Global Exception Handler

This guide explains the centralized exception handling system in LaraBaseX, with clear sections for code location, capabilities, process, and best practices.

<br>

### 1. Where is the Code?
- Main handler: [`app/Exceptions/Handler.php`](../../app/Exceptions/Handler.php)
- Language messages: [`lang/en/messages.php`](../../lang/en/messages.php)
- API controllers: `app/Http/Controllers/Api/` and other controllers that throw exceptions

<br>

### 2. What Can It Do?
- Processes all uncaught exceptions and API errors
- Returns standardized error responses for API endpoints
- Customizes messages for common errors (403, 404, 500, validation)
- Integrates with the frontend to display user-friendly error messages

<br>

### 3. What Does It Do?
- Formats error responses using language keys
- Ensures frontend receives clear, consistent error messages
- Allows customization for new error types and messages

<br>

### 4. How Does It Work?
- API controllers throw exceptions or validation errors
- `Handler.php` catches and processes these, formats the response
- Uses language keys from `lang/en/messages.php` for message consistency
- Frontend displays the error messages received from the API

<br>

### 5. Best Practices
- Update `Handler.php` to add custom error handling logic or support new exception types
- Keep error message keys in sync with frontend and language files for consistency

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Standard API Response

<br>

### 1. Where is the Code?
- Helper functions: `app/helpers.php` (e.g., `api_success_message`, `api_error_message`)
- Message definitions: `lang/en/messages.php`
- Used in API controllers: `app/Http/Controllers/Api/` and other controllers returning API responses

<br>

### 2. What Can It Do?
- Standardizes all API responses for success and error cases
- Ensures consistent structure and messaging across endpoints
- Allows easy localization and customization of messages

<br>

### 3. What Does It Do?
- Returns success responses using `api_success_message('key')`
- Returns error responses using `api_error_message('key', statusCode)`
- Uses message keys from `messages.php` for consistency

<br>

### 4. How Does It Work?
- API controllers call the helper functions to format responses
- Helper functions fetch message text from `messages.php`
- Response structure is consistent for frontend consumption

<br>

### 5. Usage Example
```php
// Success response
return api_success_message('user_created');

// Error response
return api_error_message('invalid_credentials', 401);
```

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Seeder and Factory


<br>



### 1. Where is the Code?
- Main seeder: `database/seeders/PermissionSeeder.php`
- Factories: `database/factories/` (e.g., `UserFactory.php`)
- Used in tests: `tests/Feature/`, `tests/Unit/`


<br>



### 2. What Can It Do?
- Seeds permissions, roles, and default users for consistent access control
- Factories generate test data for models (users, etc.)
- Ensures all roles/permissions are in sync with controllers and UI


<br>



### 3. What Does It Do?
- Creates permission groups, permissions, roles, and assigns them to users
- Factories allow easy creation of test users and other entities for testing


<br>



### 4. How Does It Work?
- Run `php artisan db:seed` to execute `PermissionSeeder.php`
- Factories are used in tests or via `artisan tinker` to generate model instances
- Seeder logic keeps permission names and mappings consistent across backend and frontend


<br>



### 5. Best Practice
- Add new permissions/roles to the seeder and keep names in sync with controllers and UI

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Env Example File


<br>



### 1. Where is the Code?
- Example template: `.env.example`
- Actual environment file: `.env` (created from the example)
- Referenced in config files: `config/app.php`, `config/database.php`, etc.

<br>


### 2. What Can It Do?
- Contains all required keys for local and production environments
- Ensures all environment variables are documented and easy to configure
- Supports both backend and frontend settings

<br>



### 3. What Does It Do?
- Provides a template for environment setup
- Lists all necessary keys for application to run
- Prevents missing or misconfigured environment variables

<br>



### 4. How Does It Work?
- Copy `.env.example` to `.env` and update values for your environment
- Application loads variables from `.env` at runtime
- Config files reference these variables for settings

<br>



### 5. Usage Tip
- Always keep `.env.example` up to date with all required keys
- Never commit your actual `.env` file to version control

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>

#

## API Documentation

Welcome to the LaraBaseX API! This guide is tailored for onboarding developers and covers everything you need to understand, test, and extend our API.

### 📂 Key Files & Structure

- **API Routes:** `routes/api.php` (all endpoints defined here)
- **Controllers:** `app/Http/Controllers/` (business logic, OpenAPI annotations)
- **Swagger Config:** `config/l5-swagger.php` (Swagger settings)
- **Generated Docs:** `storage/api-docs/` (Swagger output)
- **Postman Collection:** `postman/LaraBaseX-API-Collection.json` (ready to import)

### 🛠️ Developer Workflow

1. **Test Endpoints:** Use Swagger UI or Postman for interactive testing.
2. **Add/Update Endpoints:**
     - Create controller methods with OpenAPI annotations.
     - Add routes in `routes/api.php`.
     - Regenerate docs: `php artisan l5-swagger:generate`.
3. **Authentication:** All protected endpoints use Laravel Passport (Bearer Token).
4. **Error/Success Format:** Responses are standardized (see examples below).

### 🧩 Troubleshooting & Tips

- If docs don't update, run:
    - `php artisan config:clear`
    - `php artisan route:clear`
- Always keep controller annotations and routes in sync.
- For mobile integration, use the provided Postman collection and header examples.
- Regenerate docs after any endpoint changes.

#


### 🔗 Quick Access


### 📋 API Endpoints Summary

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/api/app-version` | Get app version and store URL | ❌ No |
| `POST` | `/api/register` | Register new user and send OTP | ❌ No |
| `POST` | `/api/login` | Login with mobile number and send OTP | ❌ No |
| `POST` | `/api/verify-otp` | Verify OTP and get access token | ❌ No |
| `POST` | `/api/resend-otp` | Resend OTP to mobile number | ❌ No |
| `POST` | `/api/logout` | Logout and revoke access token |  Yes |

### User Management Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/api/user` | Get authenticated user details |  Yes |
| `POST` | `/api/user/update` | Update user profile information |  Yes |
| `POST` | `/api/user/update-photo` | Upload/update profile photo |  Yes |

### Home/Content Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/api/sliders` | Get home page sliders |  Yes |

### 🔐 Authentication

The API uses **Bearer Token Authentication** (Laravel Passport):

1. **Register/Login**: Use `/api/register` or `/api/login` to get OTP
2. **Verify OTP**: Use `/api/verify-otp` to get your access token
3. **Use Token**: Include in headers: `Authorization: Bearer YOUR_TOKEN_HERE`

### Example Authentication Flow

```bash
# 1. Register or Login
curl -X POST http://127.0.0.1:8001/api/login \
    "mobile": "9876543210",
    "device_id": "abc123device"
}'

# 2. Verify OTP (example OTP from logs)
curl -X POST http://127.0.0.1:8001/api/verify-otp \
    "mobile": "9876543210",
    "otp": "1234"
}'

# 3. Use the returned access_token for authenticated requests
curl -X POST http://127.0.0.1:8001/api/user \
```

### 🚀 Using Swagger UI

### Interactive Testing

1. **Open Swagger UI**: Navigate to `http://127.0.0.1:8001/api/documentation`
2. **Authenticate**:
3. **Test Endpoints**: Click "Try it out" on any endpoint to test it directly

### Features Available


### 📱 Mobile App Integration

### Headers Required

```javascript
{
"Content-Type": "application/json",
"Accept": "application/json",
"Authorization": "Bearer YOUR_ACCESS_TOKEN" // For protected endpoints
}
```

### Error Responses

All endpoints return consistent error responses:

```json
{
"errors": {
    "field_name": ["Error message here"]
}
}
```

### Success Responses

Most endpoints return:

### 🛠️ For Developers

### Adding New API Endpoints

1. **Create Controller Method** with OpenAPI annotations:

```php
/**
 * @OA\Post(
 *     path="/api/your-endpoint",
 *     operationId="yourMethod",
 *     tags={"Your Tag"},
 *     summary="Your endpoint description",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="field", type="string", example="value")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Success")
 * )
 */
public function yourMethod(Request $request) {
    // Your code here
}
```

2. **Add Route** in `routes/api.php`
3. **Regenerate Documentation**:
```bash
php artisan l5-swagger:generate
```

### Configuration


### 📋 Postman Collection

You can import the API documentation into Postman:

1. **Import from URL**: Use `http://127.0.0.1:8001/docs/api-docs.json`
2. **Or Download**: Save the JSON file and import it manually

### 🔄 Regenerating Documentation

When you add new endpoints or modify existing ones:

```bash
# Regenerate documentation
php artisan l5-swagger:generate

# Clear cache if needed
php artisan config:clear
php artisan route:clear
```

### 📝 Environment Configuration

Add these to your `.env` file for production:

```env
L5_SWAGGER_USE_ABSOLUTE_PATH=true
L5_FORMAT_TO_USE_FOR_DOCS=json
```

### 🎯 Benefits of This Implementation

1. ** Complete Documentation**: All endpoints documented with examples
2. ** Interactive Testing**: Test APIs directly from browser
3. ** Mobile-Friendly**: Perfect for mobile app development
4. ** Export Ready**: JSON/YAML formats for external tools
5. ** Security Documented**: Clear authentication requirements
6. ** Validation Info**: Request/response schemas included
7. ** Professional**: Industry-standard OpenAPI specification

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>

#

## Postman Collection
Welcome to the LaraBaseX Postman Collection! This guide is designed for onboarding developers and covers everything you need to import, test, and automate API workflows.

### 📂 Key Files & Structure

- **Main Collection:** `postman/LaraBaseX-API-Collection.json` (all endpoints)
- **Environments:**
    - `postman/LaraBaseX-Local-Environment.json` (local dev)
    - `postman/LaraBaseX-Production-Environment.json` (production template)
- **Docs & Scripts:**
    - `postman/README.md` (detailed usage)
    - `postman/setup.sh` (quick setup)
    - `postman/test.sh` (automated testing)

### 🛠️ Developer Workflow

1. **Import Collection & Environment:** Use Postman to import both files and set the environment active.
2. **Authentication Flow:** Run login and OTP verification requests to auto-save your token.
3. **Test Endpoints:** Use saved token for all authenticated requests; scripts handle token management automatically.
4. **Automated Testing:** Use `test.sh` or Newman CLI for batch testing and reporting.
5. **Environment Management:** Switch between local and production configs as needed.

### 🧩 Troubleshooting & Tips

- If token is not set, re-run login and OTP requests.
- Always check the active environment in Postman before testing.
- Use server logs (`storage/logs/laravel.log`) to find OTPs during development.
- For automated tests, ensure scripts are executable (`chmod +x postman/test.sh`).
- Review HTML/JSON reports for detailed test results.

#

### 📁 Files Structure

```
postman/
├── LaraBaseX-API-Collection.json          # Main collection with all endpoints
├── LaraBaseX-Local-Environment.json       # Local development environment
├── LaraBaseX-Production-Environment.json  # Production environment template
├── README.md                               # Complete documentation
├── setup.sh                               # Quick setup script
└── test.sh                                 # Automated testing script
```

### 🚀 Quick Start

#### **Import Collection**
1. Open Postman
2. Import `postman/LaraBaseX-API-Collection.json`
3. Import `postman/LaraBaseX-Local-Environment.json`
4. Set environment as active (top-right dropdown)

#### **Run Authentication Flow**
```bash
1. Run: "🔐 Authentication" → "Login with Mobile"
2. Check server logs for OTP (or use default: 1234)
3. Run: "🔐 Authentication" → "Verify OTP"
4.  Access token automatically saved!
5. Test: "👤 User Management" → "Get User Profile"
```

### 🔧 Features Included

#### **🤖 Automated Token Management**
-  Auto-saves access token after OTP verification
-  Auto-includes Bearer token in authenticated requests
-  Auto-clears token on logout
-  Smart environment variable management

### **📱 Complete API Coverage**

#### **Authentication Endpoints**
- `GET /api/app-version` - Get app version and store URL
- `POST /api/register` - Register new user and send OTP
- `POST /api/login` - Login with mobile number and send OTP
- `POST /api/verify-otp` - Verify OTP and get access token
- `POST /api/resend-otp` - Resend OTP to mobile number
- `POST /api/logout` - Logout and revoke access token

#### **User Management Endpoints**
- `POST /api/user` - Get authenticated user details
- `POST /api/user/update` - Update user profile information
- `POST /api/user/update-photo` - Upload/update profile photo

#### **Content Endpoints**
- `POST /api/sliders` - Get home page sliders

### **🧪 Testing & Validation**

#### **Automated Test Scripts**
```javascript
// Auto-save access token
if (pm.response.json().access_token) {
    pm.environment.set("access_token", pm.response.json().access_token);
    console.log(" Token saved automatically!");
}

// Status code validation
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

// Response structure validation
pm.test("Response has access_token", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('access_token');
});
```

#### **Error Handling Examples**
- **400 Validation Error**: Invalid mobile number format
- **401 Unauthorized**: Invalid or expired access token
- **Response Time Monitoring**: < 5 seconds validation

### **🌍 Environment Management**

#### **Local Development Environment**
```json
{
    "base_url": "http://localhost:8001",
    "access_token": "",
    "mobile_number": "9876543210",
    "device_id": "test-device-postman-local",
    "test_user_email": "dev@example.com",
    "test_user_password": "Password123!"
}
```

#### **Production Environment Template**
```json
{
    "base_url": "https://your-production-domain.com",
    "access_token": "",
    "mobile_number": "your-mobile-number",
    "device_id": "production-device-id",
    "test_user_email": "test@yourdomain.com",
    "test_user_password": ""
}
```

### 🛠️ Advanced Usage

### **Newman CLI Testing**
```bash
# Install Newman CLI
npm install -g newman

# Run collection via CLI
newman run postman/LaraBaseX-API-Collection.json \
    -e postman/LaraBaseX-Local-Environment.json \
    --reporters cli,html \
    --reporter-html-export newman-report.html
```

### **Automated Testing Script**
```bash
# Make script executable
chmod +x postman/test.sh

# Run automated tests
./postman/test.sh

# Choose environment (Local/Production)
# Get detailed HTML and JSON reports
```

### **CI/CD Integration**
```yaml
# GitHub Actions example
- name: Run API Tests
    run: |
    npm install -g newman
    newman run postman/LaraBaseX-API-Collection.json \
        -e postman/LaraBaseX-Local-Environment.json \
        --reporters junit \
        --reporter-junit-export api-test-results.xml
```

### 📱 Mobile App Integration

### **Complete Authentication Flow**
```bash
1. GET /api/app-version                    # Check compatibility
2. POST /api/login + mobile + device_id    # Send OTP
3. POST /api/verify-otp + mobile + otp     # Get access token
4. Use Bearer token for authenticated APIs
```

### **Sample Request Headers**
```javascript
{
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Authorization": "Bearer {{access_token}}"
}
```

### **File Upload Example**
```bash
POST /api/user/update-photo
Content-Type: multipart/form-data
Authorization: Bearer {{access_token}}
Body: photo: [select file] # JPG/PNG, max 2MB
```

### 🎯 Benefits

### **For Developers**
-  **Ready-to-Use**: Import and start testing immediately
-  **Automated Workflows**: Token management and authentication
-  **Complete Coverage**: All endpoints with sample data
-  **Error Examples**: Validation and authorization scenarios

### **For Mobile App Developers**
-  **Authentication Reference**: Complete OTP-based login flow
-  **Request Examples**: Sample payloads for all endpoints
-  **File Upload Guide**: Profile photo upload examples
-  **Error Handling**: API error response examples

### **For QA Teams**
-  **Automated Testing**: Newman CLI and CI/CD integration
-  **Performance Monitoring**: Response time validation
-  **Regression Testing**: Consistent test scenarios
-  **Detailed Reports**: HTML and JSON test reports

### **For DevOps Teams**
-  **CI/CD Ready**: Newman CLI automation scripts
-  **Multi-Environment**: Local, staging, production configs
-  **Health Monitoring**: API availability and performance
-  **Deployment Validation**: Automated post-deployment tests

### 📚 Usage Examples

### **Quick Setup**
```bash
# Run setup script for guidance
./postman/setup.sh

# Or manual setup:
# 1. Import Collection: postman/LaraBaseX-API-Collection.json
# 2. Import Environment: postman/LaraBaseX-Local-Environment.json
# 3. Set environment as active
# 4. Run authentication flow
```

### **Testing Authentication**
```bash
# Step 1: Login (sends OTP)
POST {{base_url}}/api/login
{
    "mobile": "{{mobile_number}}",
    "device_id": "{{device_id}}"
}

# Step 2: Verify OTP (gets token)
POST {{base_url}}/api/verify-otp
{
    "mobile": "{{mobile_number}}",
    "otp": "1234"
}
#  Token auto-saved to environment!

# Step 3: Test authenticated request
POST {{base_url}}/api/user
Headers: Authorization: Bearer {{access_token}}
```

### **Automated Testing**
```bash
# Run all tests with detailed reporting
newman run postman/LaraBaseX-API-Collection.json \
    -e postman/LaraBaseX-Local-Environment.json \
    --reporters cli,html,json \
    --reporter-html-export reports/api-test-report.html \
    --reporter-json-export reports/api-test-results.json
```

### 🔍 Troubleshooting

### **Common Issues**

#### **"access_token not set"**
```bash
Solution:
1. Run "Login with Mobile" request
2. Check server logs for OTP: storage/logs/laravel.log
3. Run "Verify OTP" with correct OTP
4. Token will be auto-saved to environment
```

#### **"mobile_number not set"**
```bash
Solution:
1. Check environment is selected (top-right dropdown)
2. Verify mobile_number exists in environment variables
3. Set manually if needed: mobile_number = "9876543210"
```

#### **"401 Unauthorized"**
```bash
Solution:
1. Check access_token is set in environment
2. Re-run authentication flow if token expired
3. Verify Authorization header format: "Bearer {{access_token}}"
```

### 📊 Testing Reports

### **HTML Report Features**
-  Test execution summary
-  Request/response details
-  Performance metrics
-  Error analysis and debugging info

### **JSON Report Features**
-  Machine-readable test results
-  CI/CD integration data
-  Performance metrics
-  Detailed execution logs

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>

#

## Static Analysis

Static analysis for LaraBaseX! This guide is designed for onboarding developers and covers everything you need to run, configure, and troubleshoot PHPStan (with Larastan) in this project.

### 📂 Key Files & Structure

- **Config:** `phpstan.neon` (main config)
- **Baseline:** `phpstan-baseline.neon` (error baseline)
- **Custom Stubs:** `stubs/common.stub` (type definitions)
- **Scripts:** `scripts/phpstan.sh` (advanced usage)
- **Composer Scripts:** See `composer.json` for shortcuts

### 🛠️ Developer Workflow

1. **Run Analysis:** Use `composer phpstan` or `./scripts/phpstan.sh` for static checks.
2. **Fix Issues:** Follow error messages and use code examples below for common fixes.
3. **Update Baseline:** After fixing issues, regenerate baseline with `composer phpstan:baseline`.
4. **IDE Integration:** Enable PHPStan in VS Code or PhpStorm for inline feedback.
5. **CI/CD:** Static analysis runs automatically in GitHub Actions on every push.

## 🧩 Troubleshooting & Tips

- Increase memory limit for large codebases (`--memory-limit=2G`).
- Use parallel processing for faster analysis.
- If you see Laravel method errors, check Larastan is installed and loaded.
- Use baseline to focus on new issues and gradually improve code quality.
- For custom types, update `stubs/common.stub`.

---

This document describes the PHPStan static analysis setup for LaraBaseX.

### Overview

PHPStan is a static analysis tool for PHP that helps find bugs in your code without running it. It's particularly useful for Laravel applications with its Larastan extension.

### Installation

PHPStan with Larastan is already installed as a development dependency:

```bash
composer require --dev larastan/larastan
```

### Configuration

#### Main Configuration File: `phpstan.neon`

The configuration file includes:

- **Analysis Level**: Set to level 6 (good balance of strictness and practicality)
- **Paths Analyzed**: `app/`, `database/factories/`, `database/seeders/`, `routes/`, `tests/`
- **Laravel Integration**: Full Larastan extension for Laravel-specific features
- **Parallel Processing**: Enabled for faster analysis
- **Custom Stubs**: Additional type definitions in `stubs/common.stub`

#### Key Configuration Options

```yaml
level: 6                           # Analysis strictness (0-10)
paths: [app/, tests/, routes/]     # Directories to analyze
parallel: true                     # Enable parallel processing
checkModelProperties: true        # Check Eloquent model properties
```

### Usage

#### Composer Scripts

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

#### Direct Commands

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

#### Shell Script

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

### Baseline Management

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

### Integration

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

### Performance Optimization

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

### Custom Rules and Extensions

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

### Troubleshooting

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

### Resources

- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Larastan Documentation](https://github.com/larastan/larastan)
- [PHPStan Rules Reference](https://phpstan.org/user-guide/rules)
- [Laravel Static Analysis Best Practices](https://laravel.com/docs/11.x/packages#static-analysis)


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>

#

## Predefined Error Messages
Need to use, extend, and troubleshoot predefined error and success messages.

### 📂 Key Files & Structure

- **Language Files:**
    - `lang/en/messages.php` (main messages)
    - `lang/en/validation.php` (validation messages)
- **Helpers:** `app/helpers.php` (message helper functions)
- **Controllers:** Use helpers for API and web responses

### 🛠️ Developer Workflow

1. **Use Helpers:** Call message helpers in controllers and views for consistent responses.
2. **Customize Messages:** Edit language files to update or add new messages.
3. **Internationalization:** Add new language files (e.g., `lang/es/messages.php`) for translations.
4. **Frontend Integration:** Pass messages to React via Inertia or session flash data.
5. **Validation:** Custom validation messages are loaded automatically from `validation.php`.

### 🧩 Troubleshooting & Tips

- If a message is missing, check the language file for the correct key.
- Use replacement parameters for dynamic messages.
- For new modules, add a new category in `messages.php` and corresponding helper.
- Keep messages short, clear, and user-friendly for best UX.

#

### 1. **Language Files**
```
 lang/en/messages.php     - Centralized application messages
 lang/en/validation.php   - Custom validation messages and attributes
 app/helpers.php          - Added message helper functions
```

### 2. **Updated Controllers**
```
 app/Http/Controllers/Api/AuthController.php - Using centralized messages
```

#

### 🛠️ Implementation Details

### **Message Categories**

#### 1. **Authentication Messages** (`auth.*`)
```php
auth_message('login_successful')          // "Login successful! Welcome back."
auth_message('invalid_credentials')       // "Invalid email or password."
auth_message('account_inactive')          // "Your account is inactive. Please contact administrator."
```

#### 2. **API Messages** (`api.*`)
```php
api_message('success')                    // "Operation completed successfully."
api_message('otp_sent')                   // "OTP sent successfully."
api_message('otp_verified')               // "OTP verified successfully."
api_message('unauthorized')               // "Unauthorized access."
```

#### 3. **User Management** (`user.*`)
```php
user_message('created')                   // "User created successfully."
user_message('profile_updated')           // "Profile updated successfully."
user_message('email_exists')              // "Email address already exists."
```

#### 4. **Validation Messages** (`validation.*`)
```php
validation_message('required')            // "This field is required."
validation_message('email')               // "Please enter a valid email address."
validation_message('phone')               // "Please enter a valid phone number."
```

#### 5. **File Upload Messages** (`file.*`)
```php
message('file.uploaded')                  // "File uploaded successfully."
message('file.invalid_type')              // "Invalid file type."
message('file.too_large')                 // "File size is too large."
```

#### 6. **System Messages** (`system.*`)
```php
message('system.maintenance')             // "System is under maintenance. Please try again later."
message('system.backup_created')          // "System backup created successfully."
```

#

### 🔧 Helper Functions Available

### **Basic Message Helpers**
```php
message('category.key')                   // Get any message
message('user.created')                   // "User created successfully."
message('api.success')                    // "Operation completed successfully."
```

### **Category-Specific Helpers**
```php
auth_message('login_successful')          // Authentication messages
api_message('otp_sent')                   // API messages
user_message('profile_updated')           // User management messages
validation_message('required')            // Validation messages
```

### **API Response Helpers**
```php
api_success_message('otp_sent')           // Standard API success with message
api_error_message('unauthorized', 401)    // Standard API error with message
```

### **Web Response Helpers**
```php
success_message('user', 'created')        // "User created successfully."
error_message('user', 'not_found')        // "User not found."
```

#

### 💻 Usage Examples

### **In Controllers**
```php
// API Controllers
return api_success_message('otp_sent');
return api_error_message('unauthorized', 401);

// Web Controllers
return redirect()->back()->with('success', user_message('created'));
return redirect()->back()->with('error', user_message('not_found'));
```

### **In Validation**
```php
// Custom validation messages automatically loaded from lang/en/validation.php
$validator = Validator::make($request->all(), [
    'mobile' => 'required|digits:10|unique:users',
    'email' => 'required|email|unique:users',
]);

// Will show: "Please enter a valid 10-digit mobile number."
// Will show: "This email address is already registered."
```

### **In Blade Views**
```php
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
@endif
```

### **In React Components**
```typescript
// Messages passed from backend via Inertia
const { flash } = usePage().props;

{flash.success && (
    <Alert variant="success">{flash.success}</Alert>
)}

{flash.error && (
    <Alert variant="destructive">{flash.error}</Alert>
)}
```

#

### 🌍 Internationalization Ready

### **Easy Translation Support**
```php
// Create lang/es/messages.php for Spanish
'auth' => [
    'login_successful' => '¡Inicio de sesión exitoso! Bienvenido de vuelta.',
    'invalid_credentials' => 'Correo electrónico o contraseña inválidos.',
],

// Usage remains the same
auth_message('login_successful') // Returns Spanish version if locale is 'es'
```

### **With Replacements**
```php
// In messages.php
'user_created' => 'User :name was created successfully.',

// Usage with replacements
user_message('user_created', ['name' => $user->first_name])
// Returns: "User John was created successfully."
```

#

### 🎯 Benefits Achieved

### **1. Consistency**
-  All error messages follow the same format
-  Standardized API responses
-  Consistent validation messages

### **2. Maintainability**
-  Centralized message management
-  Easy to update messages globally
-  No hardcoded strings in controllers

### **3. Internationalization**
-  Ready for multi-language support
-  Laravel's built-in translation system
-  Easy locale switching

### **4. Professional UX**
-  User-friendly error messages
-  Clear validation feedback
-  Consistent messaging across platform

### **5. Developer Experience**
-  Easy-to-use helper functions
-  IDE auto-completion support
-  Reduced code duplication

#

### 🔄 Migration Guide

### **Before (Hardcoded)**
```php
return response()->json(['message' => 'OTP sent successfully'], 200);
return response()->json(['message' => 'User not found'], 404);
```

### **After (Centralized)**
```php
return api_success_message('otp_sent');
return api_error_message('not_found', 404);
```

#

### 📋 Next Steps

1. **Update Remaining Controllers** - Apply centralized messages to all controllers
2. **Frontend Integration** - Use messages in React components
3. **Add More Categories** - Extend messages for specific modules
4. **Translation Files** - Add support for additional languages
5. **API Documentation** - Update Swagger docs with new error responses

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


#
