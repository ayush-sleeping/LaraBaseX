# 🚀 LaraBaseX

> A Laravel 12 + ReactJS Full Stack Starter Boilerplate.
This is a secure, modular, production-ready base project using Laravel 12 with ReactJS frontend, ideal for building scalable web applications without Blade.

<div id="top"></div>

##

### Table of content:

| No. | Topics                                                                                  |
| --- | --------------------------------------------------------------------------------------- |
| 0.  | [Tech Stack](#tech-stack)                                                               |
| 1   | [Authentication Flow Documentation](#authentication-flow-documentation)                 |
| 2   | [Authorization Flow Documentation](#authorization-flow-documentation)                   |
| 3   | [Setting Profile Information Update](#setting-profile-information-update)               |
| 4   | [Setting Password Update](#setting-password-update)                                     |
| 5   | [Permission Based UI Implementation](#permission-based-ui-implementation)               |

<br>

<br>

#



## Tech Stack

- **Backend**: Laravel 12 (REST API)
- **Frontend**: ReactJS (Vite + Axios)
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Deployment Ready**: Docker / Shared Hosting / VPS



### 🔒 1. Security Essentials
These features protect your app, data, and server from attacks:

- ✅ HTTPS enforced (Force HTTPS in AppServiceProvider)
    <details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>

    **📁 Files Modified:**
    - `/app/Providers/AppServiceProvider.php` - Force HTTPS URL generation
    - `/config/app.php` - Added `force_https` configuration
    - `/app/Http/Middleware/ForceHttps.php` - HTTP to HTTPS redirects
    - `/app/Http/Kernel.php` - Middleware registration
    - `/.env.example` - Added `APP_FORCE_HTTPS` variable

    **🔧 How It Works:**
    ```php
    // AppServiceProvider - URL generation
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }

    // Middleware - Request redirects
    if ($this->shouldForceHttps($request)) {
        return redirect()->secure($request->getRequestUri(), 301);
    }
    ```

    **⚙️ Configuration:**
    ```bash
    # Production
    APP_ENV=production
    APP_FORCE_HTTPS=true
    APP_URL=https://yourdomain.com

    # Development
    APP_ENV=local
    APP_FORCE_HTTPS=false
    APP_URL=http://localhost
    ```

    **✅ Features:**
    - Automatic HTTPS enforcement in production
    - Environment-based configuration
    - 301 redirects for SEO
    - Development-friendly (skips local)
    - Dual-layer protection (AppServiceProvider + Middleware)

    </details>
- ✅ CORS configured properly (config/cors.php)
- ✅ CSRF protection (even for APIs, use Sanctum or tokens)
- ✅ Rate Limiting for APIs (ThrottleRequests middleware)
- ✅ Validation layer using FormRequest (php artisan make:request)
- ✅ Use policies/gates for authorization (php artisan make:policy)
- ✅ Avoid mass assignment bugs ($fillable vs $guarded)
- ✅ Escape output or sanitize input if user-generated data is stored
- ✅ Sanitize uploaded files & validate MIME types
- ✅ Use environment variables for all secrets (never hardcode keys)
- ✅ Disable debug mode on production (APP_DEBUG=false)
- ✅ Log all authentication attempts and system errors
- ✅ Do not expose Laravel version in headers


### 🧱 2. Architecture & Structure Essentials
- ✅ Keep controllers thin, use Services for logic
- ✅ Helpers.php for reusable functions (as you're doing)
- ✅ Job Queues setup (Redis + Supervisor in production)
- ✅ Use resource() routes & API standards (api.php)
- ❌ Use Service classes for business logic (e.g. App\Services\UserService)
- ❌ Use Repository pattern (clean separation from Eloquent queries)
- ❌ Use enums for static statuses or types (php artisan make:enum)
- ❌ Event-Listener system for side-effects (e.g. sending email after registration)
- ❌ Transform API response data using Laravel Resource classes


### 📦 3. Packages to Include
- ✅ Spatie Laravel Permission – roles/permissions
- ✅ Laravel Sanctum or Passport – token-based auth
- ✅ Laravel Telescope (local/dev) – debugging, request log
- ✅ Laravel Debugbar (local/dev) – performance analysis
- ✅ Spatie Backup – scheduled database/file backups
- ✅ Spatie Activity Log – audit logs for user actions


### 🧠 4. Developer Experience (DX)
- ✅ Global Exception Handler for API errors
- ✅ Standard API Response format using success(), error() helpers
- ✅ Seeder & Factory files for test data
- ✅ Well-structured .env.example file
- ✅ API Documentation via Swagger or Postman

    <details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>
        includes comprehensive API documentation powered by **Swagger (OpenAPI)** which provides interactive documentation for all API endpoints.

    ## 🔗 Quick Access

    - **Swagger UI**: [http://127.0.0.1:8001/api/documentation](http://127.0.0.1:8001/api/documentation)
    - **JSON Documentation**: [http://127.0.0.1:8001/docs/api-docs.json](http://127.0.0.1:8001/docs/api-docs.json)
    - **YAML Documentation**: [http://127.0.0.1:8001/docs/api-docs.yaml](http://127.0.0.1:8001/docs/api-docs.yaml)

    ## 📋 API Endpoints Summary

    ### Authentication Endpoints

    | Method | Endpoint | Description | Auth Required |
    |--------|----------|-------------|---------------|
    | `GET` | `/api/app-version` | Get app version and store URL | ❌ No |
    | `POST` | `/api/register` | Register new user and send OTP | ❌ No |
    | `POST` | `/api/login` | Login with mobile number and send OTP | ❌ No |
    | `POST` | `/api/verify-otp` | Verify OTP and get access token | ❌ No |
    | `POST` | `/api/resend-otp` | Resend OTP to mobile number | ❌ No |
    | `POST` | `/api/logout` | Logout and revoke access token | ✅ Yes |

    ### User Management Endpoints

    | Method | Endpoint | Description | Auth Required |
    |--------|----------|-------------|---------------|
    | `POST` | `/api/user` | Get authenticated user details | ✅ Yes |
    | `POST` | `/api/user/update` | Update user profile information | ✅ Yes |
    | `POST` | `/api/user/update-photo` | Upload/update profile photo | ✅ Yes |

    ### Home/Content Endpoints

    | Method | Endpoint | Description | Auth Required |
    |--------|----------|-------------|---------------|
    | `POST` | `/api/sliders` | Get home page sliders | ✅ Yes |

    ## 🔐 Authentication

    The API uses **Bearer Token Authentication** (Laravel Passport):

    1. **Register/Login**: Use `/api/register` or `/api/login` to get OTP
    2. **Verify OTP**: Use `/api/verify-otp` to get your access token
    3. **Use Token**: Include in headers: `Authorization: Bearer YOUR_TOKEN_HERE`

    ### Example Authentication Flow

    ```bash
    # 1. Register or Login
    curl -X POST http://127.0.0.1:8001/api/login \
    -H "Content-Type: application/json" \
    -d '{
        "mobile": "9876543210",
        "device_id": "abc123device"
    }'

    # 2. Verify OTP (example OTP from logs)
    curl -X POST http://127.0.0.1:8001/api/verify-otp \
    -H "Content-Type: application/json" \
    -d '{
        "mobile": "9876543210",
        "otp": "1234"
    }'

    # 3. Use the returned access_token for authenticated requests
    curl -X POST http://127.0.0.1:8001/api/user \
    -H "Authorization: Bearer YOUR_ACCESS_TOKEN_HERE" \
    -H "Content-Type: application/json"
    ```

    ## 🚀 Using Swagger UI

    ### Interactive Testing

    1. **Open Swagger UI**: Navigate to `http://127.0.0.1:8001/api/documentation`
    2. **Authenticate**:
    - Click "Authorize" button
    - Enter your bearer token: `Bearer YOUR_TOKEN_HERE`
    3. **Test Endpoints**: Click "Try it out" on any endpoint to test it directly

    ### Features Available

    - ✅ **Interactive Testing**: Test all endpoints directly from the browser
    - ✅ **Request/Response Examples**: See example data for all endpoints
    - ✅ **Schema Validation**: View required fields and data types
    - ✅ **Authentication Support**: Built-in token authentication
    - ✅ **Export Options**: Download as JSON/YAML formats for external tools

    ## 📱 Mobile App Integration

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
    - `200`: Success with data
    - `201`: Created successfully
    - `400`: Validation error
    - `401`: Unauthorized/Invalid token

    ## 🛠️ For Developers

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

    - **Config File**: `config/l5-swagger.php`
    - **Generated Files**: `storage/api-docs/`
    - **Views**: `resources/views/vendor/l5-swagger/`

    ## 📋 Postman Collection

    You can import the API documentation into Postman:

    1. **Import from URL**: Use `http://127.0.0.1:8001/docs/api-docs.json`
    2. **Or Download**: Save the JSON file and import it manually

    ## 🔄 Regenerating Documentation

    When you add new endpoints or modify existing ones:

    ```bash
    # Regenerate documentation
    php artisan l5-swagger:generate

    # Clear cache if needed
    php artisan config:clear
    php artisan route:clear
    ```

    ## 📝 Environment Configuration

    Add these to your `.env` file for production:

    ```env
    L5_SWAGGER_USE_ABSOLUTE_PATH=true
    L5_FORMAT_TO_USE_FOR_DOCS=json
    ```

    ## 🎯 Benefits of This Implementation

    1. **✅ Complete Documentation**: All endpoints documented with examples
    2. **✅ Interactive Testing**: Test APIs directly from browser
    3. **✅ Mobile-Friendly**: Perfect for mobile app development
    4. **✅ Export Ready**: JSON/YAML formats for external tools
    5. **✅ Security Documented**: Clear authentication requirements
    6. **✅ Validation Info**: Request/response schemas included
    7. **✅ Professional**: Industry-standard OpenAPI specification

    ## 🔗 Related Documentation

    - [Laravel Passport Authentication](routes/auth.php)
    - [Package Documentation](PACKAGES_DOCUMENTATION.md)
    - [Database Backup System](app/Console/Commands/BackupDatabase.php)

    ---

    **✅ Status**: API Documentation implementation complete!
    **🌐 Access**: http://127.0.0.1:8001/api/documentation
    **📱 Mobile Ready**: All endpoints documented and testable

    </details>
- ✅ Postman Collection for APIs preloaded

    <details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>

    A comprehensive, production-ready Postman collection with automated testing, environment management, and complete authentication workflows for the LaraBaseX API.

    ## 📁 Files Structure

    ```
    postman/
    ├── LaraBaseX-API-Collection.json          # Main collection with all endpoints
    ├── LaraBaseX-Local-Environment.json       # Local development environment
    ├── LaraBaseX-Production-Environment.json  # Production environment template
    ├── README.md                               # Complete documentation
    ├── setup.sh                               # Quick setup script
    └── test.sh                                 # Automated testing script
    ```

    ## 🚀 Quick Start

    ### **Import Collection**
    1. Open Postman
    2. Import `postman/LaraBaseX-API-Collection.json`
    3. Import `postman/LaraBaseX-Local-Environment.json`
    4. Set environment as active (top-right dropdown)

    ### **Run Authentication Flow**
    ```bash
    1. Run: "🔐 Authentication" → "Login with Mobile"
    2. Check server logs for OTP (or use default: 1234)
    3. Run: "🔐 Authentication" → "Verify OTP"
    4. ✅ Access token automatically saved!
    5. Test: "👤 User Management" → "Get User Profile"
    ```

    ## 🔧 Features Included

    ### **🤖 Automated Token Management**
    - ✅ Auto-saves access token after OTP verification
    - ✅ Auto-includes Bearer token in authenticated requests
    - ✅ Auto-clears token on logout
    - ✅ Smart environment variable management

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
        console.log("✅ Token saved automatically!");
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
        "test_user_email": "john.doe@example.com",
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

    ## 🛠️ Advanced Usage

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

    ## 📱 Mobile App Integration

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

    ## 🎯 Benefits

    ### **For Developers**
    - ✅ **Ready-to-Use**: Import and start testing immediately
    - ✅ **Automated Workflows**: Token management and authentication
    - ✅ **Complete Coverage**: All endpoints with sample data
    - ✅ **Error Examples**: Validation and authorization scenarios

    ### **For Mobile App Developers**
    - ✅ **Authentication Reference**: Complete OTP-based login flow
    - ✅ **Request Examples**: Sample payloads for all endpoints
    - ✅ **File Upload Guide**: Profile photo upload examples
    - ✅ **Error Handling**: API error response examples

    ### **For QA Teams**
    - ✅ **Automated Testing**: Newman CLI and CI/CD integration
    - ✅ **Performance Monitoring**: Response time validation
    - ✅ **Regression Testing**: Consistent test scenarios
    - ✅ **Detailed Reports**: HTML and JSON test reports

    ### **For DevOps Teams**
    - ✅ **CI/CD Ready**: Newman CLI automation scripts
    - ✅ **Multi-Environment**: Local, staging, production configs
    - ✅ **Health Monitoring**: API availability and performance
    - ✅ **Deployment Validation**: Automated post-deployment tests

    ## 📚 Usage Examples

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
    # ✅ Token auto-saved to environment!

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

    ## 🔍 Troubleshooting

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

    ## 📊 Testing Reports

    ### **HTML Report Features**
    - ✅ Test execution summary
    - ✅ Request/response details
    - ✅ Performance metrics
    - ✅ Error analysis and debugging info

    ### **JSON Report Features**
    - ✅ Machine-readable test results
    - ✅ CI/CD integration data
    - ✅ Performance metrics
    - ✅ Detailed execution logs

    ---

    **✅ Status**: Postman Collection - COMPLETED!
    **📦 Ready**: Import, test, and integrate immediately
    **🤖 Automated**: Token management and testing workflows
    **📱 Mobile-Ready**: Complete authentication and API testing suite

    </details>
- ✅ PHPStan or Larastan for static analysis

    <details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>


    ## 📁 Files Created

    ### **1. Core Collection Files**
    ```
    ✅ postman/LaraBaseX-API-Collection.json          # Main collection (10 endpoints)
    ✅ postman/LaraBaseX-Local-Environment.json       # Local development environment
    ✅ postman/LaraBaseX-Production-Environment.json  # Production environment template
    ✅ postman/README.md                               # Complete documentation (50+ pages)
    ✅ postman/setup.sh                                # Quick setup script
    ✅ postman/test.sh                                 # Automated testing script
    ```

    ---

    ## 🚀 Key Features Implemented

    ### **🤖 Automated Token Management**
    - ✅ **Auto-saves access token** after OTP verification
    - ✅ **Auto-includes Bearer token** in authenticated requests
    - ✅ **Auto-clears token** on logout
    - ✅ **Smart environment variables** with default values

    ### **📱 Complete API Coverage**

    #### **Authentication Flow (6 endpoints)**
    ```javascript
    GET  /api/app-version     // Get app version - No auth required
    POST /api/register        // Register user + send OTP
    POST /api/login           // Login + send OTP
    POST /api/verify-otp      // Verify OTP + get token ⚡ Auto-saves token
    POST /api/resend-otp      // Resend OTP
    POST /api/logout          // Logout + revoke token ⚡ Auto-clears token
    ```

    #### **User Management (3 endpoints)**
    ```javascript
    POST /api/user            // Get user profile - Auth required
    POST /api/user/update     // Update profile - Auth required
    POST /api/user/update-photo // Upload photo - Auth required
    ```

    #### **Content (1 endpoint)**
    ```javascript
    POST /api/sliders         // Get home sliders - Auth required
    ```

    ### **🧪 Advanced Testing Features**

    #### **Automated Test Scripts**
    ```javascript
    // Example: Auto-save token after OTP verification
    pm.test("Response has access_token", function () {
        var jsonData = pm.response.json();
        pm.expect(jsonData).to.have.property('access_token');

        // Automatically set token in environment
        if (jsonData.access_token) {
            pm.environment.set("access_token", jsonData.access_token);
            console.log("✅ Access token automatically saved!");
        }
    });

    // Example: Performance monitoring
    pm.test("Response time is less than 5000ms", function () {
        pm.expect(pm.response.responseTime).to.be.below(5000);
    });

    // Example: Response structure validation
    pm.test("Response has user data", function () {
        var jsonData = pm.response.json();
        pm.expect(jsonData).to.have.property('user');
        pm.expect(jsonData.user).to.have.property('first_name');
    });
    ```

    #### **Error Handling Examples**
    - **400 Validation Error**: Invalid mobile number format
    - **401 Unauthorized**: Invalid or expired access token
    - **Response validation**: JSON structure and performance checks

    ### **🌍 Environment Management**

    #### **Local Development Environment**
    ```json
    {
        "base_url": "http://localhost:8001",
        "access_token": "",                    // Auto-managed
        "mobile_number": "9876543210",
        "device_id": "test-device-postman-local",
        "test_user_email": "john.doe@example.com",
        "test_user_password": "Password123!"
    }
    ```

    #### **Production Environment Template**
    ```json
    {
        "base_url": "https://your-production-domain.com",
        "access_token": "",                    // Auto-managed
        "mobile_number": "your-mobile-number",
        "device_id": "production-device-id",
        "test_user_email": "test@yourdomain.com",
        "test_user_password": ""               // Set manually for security
    }
    ```

    ---

    ## 📋 Collection Structure

    ### **Organized Folder System**
    ```
    LaraBaseX API Collection/
    ├── 🔐 Authentication/              # Complete auth flow
    │   ├── Get App Version
    │   ├── Register User
    │   ├── Login with Mobile
    │   ├── Verify OTP ⚡ Auto-saves token
    │   ├── Resend OTP
    │   └── Logout ⚡ Auto-clears token
    ├── 👤 User Management/             # Profile operations
    │   ├── Get User Profile
    │   ├── Update User Profile
    │   └── Update Profile Photo
    ├── 🏠 Home & Content/              # App content
    │   └── Get Home Sliders
    └── 📋 Sample Requests/             # Workflows & examples
        ├── 📱 Complete Authentication Flow/
        │   ├── Step 1: Get App Version
        │   ├── Step 2: Login with Mobile
        │   ├── Step 3: Verify OTP & Get Token
        │   └── Step 4: Test Authenticated Request
        └── ❌ Error Handling Examples/
            ├── 400 - Validation Error
            └── 401 - Unauthorized
    ```

    ---

    ## 🛠️ Advanced Usage Options

    ### **Newman CLI Integration**
    ```bash
    # Install Newman CLI
    npm install -g newman

    # Run collection with detailed reporting
    newman run postman/LaraBaseX-API-Collection.json \
        -e postman/LaraBaseX-Local-Environment.json \
        --reporters cli,html,json \
        --reporter-html-export reports/api-test-report.html \
        --reporter-json-export reports/api-test-results.json
    ```

    ### **Automated Testing Script**
    ```bash
    # Make scripts executable
    chmod +x postman/setup.sh
    chmod +x postman/test.sh

    # Quick setup guidance
    ./postman/setup.sh

    # Run automated tests with environment selection
    ./postman/test.sh
    # Choose: 1) Local Development, 2) Production
    # Get: HTML report + JSON results + console output
    ```

    ### **CI/CD Integration Example**
    ```yaml
    # GitHub Actions
    name: API Testing
    on: [push, pull_request]
    jobs:
    test-api:
        runs-on: ubuntu-latest
        steps:
        - uses: actions/checkout@v2
        - name: Run Postman Collection
            run: |
            npm install -g newman
            newman run postman/LaraBaseX-API-Collection.json \
                -e postman/LaraBaseX-Local-Environment.json \
                --reporters junit \
                --reporter-junit-export api-test-results.xml
    ```

    ---

    ## 🎯 Usage Workflows

    ### **🚀 Quick Start (3 steps)**
    ```bash
    1. Import Collection: postman/LaraBaseX-API-Collection.json
    2. Import Environment: postman/LaraBaseX-Local-Environment.json
    3. Run Authentication flow → Token auto-saved → Test any endpoint!
    ```

    ### **📱 Mobile App Development**
    ```bash
    1. Use collection as API reference
    2. Copy request examples for your mobile app
    3. Implement authentication flow: Login → OTP → Token → APIs
    4. Handle errors using provided error examples
    5. Use file upload examples for profile photos
    ```

    ### **🧪 QA Testing**
    ```bash
    1. Run automated tests via Newman CLI
    2. Get detailed HTML reports with request/response data
    3. Monitor API performance and response times
    4. Validate all endpoints work correctly
    5. Test error scenarios and edge cases
    ```

    ### **🔄 Development Workflow**
    ```bash
    1. Start Laravel server: php artisan serve --port=8001
    2. Import collection and set local environment
    3. Test new API endpoints immediately
    4. Use automated scripts for regression testing
    5. Export updated collection for team sharing
    ```

    ---

    ## 💻 Sample Request Examples

    ### **Complete Authentication Flow**
    ```javascript
    // Step 1: Check app version (no auth)
    GET {{base_url}}/api/app-version

    // Step 2: Login with mobile (sends OTP)
    POST {{base_url}}/api/login
    {
        "mobile": "{{mobile_number}}",
        "device_id": "{{device_id}}"
    }

    // Step 3: Verify OTP (gets & saves token)
    POST {{base_url}}/api/verify-otp
    {
        "mobile": "{{mobile_number}}",
        "otp": "1234"
    }
    // ✅ Token automatically saved to environment!

    // Step 4: Test authenticated request
    POST {{base_url}}/api/user
    Headers: Authorization: Bearer {{access_token}}
    ```

    ### **Profile Management**
    ```javascript
    // Get user profile
    POST {{base_url}}/api/user
    Headers: Authorization: Bearer {{access_token}}

    // Update profile
    POST {{base_url}}/api/user/update
    Headers: Authorization: Bearer {{access_token}}
    {
        "first_name": "John Updated",
        "last_name": "Doe Updated",
        "email": "john.updated@example.com"
    }

    // Upload profile photo
    POST {{base_url}}/api/user/update-photo
    Headers: Authorization: Bearer {{access_token}}
    Content-Type: multipart/form-data
    Body: photo: [select file] // JPG/PNG, max 2MB
    ```

    ---

    ## 🔍 Troubleshooting Guide

    ### **Common Issues & Solutions**

    #### **Issue: "access_token not set"**
    ```bash
    ✅ Solution:
    1. Run "Login with Mobile" request
    2. Check server logs for OTP: storage/logs/laravel.log
    3. Run "Verify OTP" with correct OTP (or use default: 1234)
    4. Token will be automatically saved to environment
    ```

    #### **Issue: "mobile_number not set"**
    ```bash
    ✅ Solution:
    1. Check environment is selected (top-right dropdown in Postman)
    2. Verify mobile_number variable exists in environment
    3. Set manually if needed: mobile_number = "9876543210"
    ```

    #### **Issue: "401 Unauthorized"**
    ```bash
    ✅ Solution:
    1. Check access_token is set in environment variables
    2. Re-run authentication flow if token expired
    3. Verify Authorization header format: "Bearer {{access_token}}"
    ```

    #### **Issue: "OTP not received"**
    ```bash
    ✅ Solution:
    1. Check Laravel logs: storage/logs/laravel.log
    2. Look for: "OTP for mobile 9876543210: XXXX"
    3. Use the logged OTP in verify-otp request
    4. Default test OTP: 1234 (works in development)
    ```

    ---

    ## 📊 Benefits Achieved

    ### **For Developers**
    - ✅ **Ready-to-Use**: Import and start testing immediately
    - ✅ **Automated Workflows**: Token management and authentication flow
    - ✅ **Complete Coverage**: All 10 endpoints with sample data
    - ✅ **Environment Management**: Local, staging, production configs
    - ✅ **Error Examples**: Validation and authorization scenarios

    ### **For Mobile App Developers**
    - ✅ **Authentication Reference**: Complete OTP-based login flow
    - ✅ **Request Examples**: Copy-paste ready API calls
    - ✅ **File Upload Guide**: Profile photo upload with proper headers
    - ✅ **Error Handling**: API error response examples
    - ✅ **Token Management**: Automatic token handling examples

    ### **For QA Teams**
    - ✅ **Automated Testing**: Newman CLI and CI/CD integration
    - ✅ **Performance Monitoring**: Response time validation (<5s)
    - ✅ **Regression Testing**: Consistent test scenarios
    - ✅ **Detailed Reports**: HTML and JSON test reports
    - ✅ **Error Validation**: Test error scenarios systematically

    ### **For DevOps Teams**
    - ✅ **CI/CD Ready**: Newman CLI automation scripts
    - ✅ **Multi-Environment**: Local, staging, production testing
    - ✅ **Health Monitoring**: API availability and performance checks
    - ✅ **Deployment Validation**: Automated post-deployment testing
    - ✅ **Documentation**: Self-documenting API collection

    ---

    ## 🎨 Technical Highlights

    ### **Smart Automation Features**
    ```javascript
    // Pre-request scripts set defaults
    if (!pm.environment.get("mobile_number")) {
        pm.environment.set("mobile_number", "9876543210");
    }

    // Post-response scripts save tokens
    if (pm.response.json().access_token) {
        pm.environment.set("access_token", pm.response.json().access_token);
    }

    // Global validation scripts
    pm.test("Response time acceptable", function () {
        pm.expect(pm.response.responseTime).to.be.below(5000);
    });
    ```

    ### **Environment Variable Management**
    - **base_url**: API server URL (local/production)
    - **access_token**: JWT token (auto-managed)
    - **mobile_number**: Test mobile for OTP
    - **device_id**: Unique device identifier
    - **test_user_email**: Sample user email
    - **test_user_password**: Sample user password

    ### **Response Validation**
    - Status code validation (200, 201, 400, 401)
    - Response structure validation (required fields)
    - Performance monitoring (response time < 5s)
    - Content-Type header validation
    - Token extraction and storage

    ---

    ## 📚 Integration Examples

    ### **With Swagger Documentation**
    ```bash
    # Collection complements Swagger UI
    # Swagger: Interactive browser testing
    # Postman: Automated testing + CI/CD integration
    # Both: Complete API documentation coverage
    ```

    ### **With Mobile App Development**
    ```javascript
    // React Native example using collection patterns
    const login = async (mobile, deviceId) => {
        const response = await fetch(`${API_BASE_URL}/api/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ mobile, device_id: deviceId })
        });
        return response.json();
    };

    const verifyOTP = async (mobile, otp) => {
        const response = await fetch(`${API_BASE_URL}/api/verify-otp`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ mobile, otp })
        });
        const data = response.json();
        // Save token like Postman does
        if (data.access_token) {
            await AsyncStorage.setItem('access_token', data.access_token);
        }
        return data;
    };
    ```

    ### **With Testing Frameworks**
    ```bash
    # Jest integration example
    describe('LaraBaseX API', () => {
        test('Authentication flow works', async () => {
            // Use Newman programmatically
            const newman = require('newman');
            const results = await newman.run({
                collection: 'postman/LaraBaseX-API-Collection.json',
                environment: 'postman/LaraBaseX-Local-Environment.json'
            });
            expect(results.run.failures).toHaveLength(0);
        });
    });
    ```

    ---

    ## 📋 Next Steps & Enhancements

    ### **Immediate Next Steps**
    1. **Import Collection**: Import into your Postman workspace
    2. **Test Authentication**: Run complete auth flow
    3. **Team Sharing**: Share collection with development team
    4. **CI/CD Integration**: Add Newman to your deployment pipeline

    ### **Potential Enhancements**
    1. **Add More Endpoints**: Extend collection as API grows
    2. **Environment Variables**: Add staging environment
    3. **Advanced Testing**: Add data-driven tests
    4. **Documentation**: Add video tutorials for team onboarding
    5. **Monitoring**: Integrate with API monitoring services

    ### **Team Adoption**
    1. **Development Team**: Use for API testing during development
    2. **QA Team**: Use for automated regression testing
    3. **Mobile Team**: Use as API reference and testing tool
    4. **DevOps Team**: Use for deployment validation and monitoring

    ---

    ## 🔗 Related Documentation

    - **Swagger API Documentation**: [http://localhost:8001/api/documentation](http://localhost:8001/api/documentation)
    - **Laravel Sanctum**: [Authentication system](https://laravel.com/docs/11.x/sanctum)
    - **Postman Newman**: [CLI automation](https://learning.postman.com/docs/running-collections/using-newman-cli/)
    - **Environment Management**: [Postman environments](https://learning.postman.com/docs/sending-requests/managing-environments/)

    ---

    **✅ Status**: Postman Collection - COMPLETED!
    **📦 Ready**: Import, test, and integrate immediately
    **🤖 Automated**: Token management and testing workflows
    **📱 Mobile-Ready**: Complete authentication and API testing suite
    **🎯 Professional**: Production-ready with CI/CD integration

    </details>
- ✅ Predefined Error messages in lang/en/messages.php

    <details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>
        includes comprehensive API documentation powered by **Swagger (OpenAPI)** which provides interactive documentation for all API endpoints.


    ## 📁 Files Created/Modified

    ### 1. **Language Files**
    ```
    ✅ lang/en/messages.php     - Centralized application messages
    ✅ lang/en/validation.php   - Custom validation messages and attributes
    ✅ app/helpers.php          - Added message helper functions
    ```

    ### 2. **Updated Controllers**
    ```
    ✅ app/Http/Controllers/Api/AuthController.php - Using centralized messages
    ```

    ---

    ## 🛠️ Implementation Details

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

    ---

    ## 🔧 Helper Functions Available

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

    ---

    ## 💻 Usage Examples

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

    ---

    ## 🌍 Internationalization Ready

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

    ---

    ## 🎯 Benefits Achieved

    ### **1. Consistency**
    - ✅ All error messages follow the same format
    - ✅ Standardized API responses
    - ✅ Consistent validation messages

    ### **2. Maintainability**
    - ✅ Centralized message management
    - ✅ Easy to update messages globally
    - ✅ No hardcoded strings in controllers

    ### **3. Internationalization**
    - ✅ Ready for multi-language support
    - ✅ Laravel's built-in translation system
    - ✅ Easy locale switching

    ### **4. Professional UX**
    - ✅ User-friendly error messages
    - ✅ Clear validation feedback
    - ✅ Consistent messaging across platform

    ### **5. Developer Experience**
    - ✅ Easy-to-use helper functions
    - ✅ IDE auto-completion support
    - ✅ Reduced code duplication

    ---

    ## 🔄 Migration Guide

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

    ---

    ## 📋 Next Steps

    1. **Update Remaining Controllers** - Apply centralized messages to all controllers
    2. **Frontend Integration** - Use messages in React components
    3. **Add More Categories** - Extend messages for specific modules
    4. **Translation Files** - Add support for additional languages
    5. **API Documentation** - Update Swagger docs with new error responses

    ---

    **✅ Status**: Predefined Error Messages - COMPLETED!
    **🎯 Impact**: Professional, consistent, and maintainable messaging system
    **🌍 Ready**: Multi-language support and global message management

    </details>


### 🧰 5. Frontend Integration (ReactJS)
Since Laravel 12 uses Vite + React:

- ✅ Serve React app via Vite from Laravel backend
- ✅ Set up proxy in vite.config.js to API routes
- ✅ React routing via React Router DOM
- ✅ Token-based authentication (e.g. Sanctum)
- ✅ Store tokens securely (httpOnly if possible)
- ✅ Axios with global error interceptor
- ✅ Dotenv file in React for API URLs


### 🔐 6. User Management Essentials
- ✅ Register/Login/Logout APIs
- ✅ Change Password / Forgot Password / Email Verify
- ✅ User roles and permissions (admin, user, manager)
- ✅ Login attempt throttling
- ✅ User profile with avatar upload
- ❌ Two-Factor Authentication (optional)


### 🛠️ 7. Helper Functions You Should Add
You already have many! Add:

- ✅ api_success() / api_error() – standardized response
- ✅ get_random_code() – for OTP, referral codes
- ✅ generate_slug() – auto slug from title
- ✅ upload_file() – universal file uploader
- ✅ remove_file() – delete uploaded file
- ✅ get_file_url() – retrieve full file URL from path
- ✅ human_readable_time() – time ago format
- ✅ log_activity() – wrapper to log user actions


### 💾 8. MySQL Best Practices
- ✅ Use InnoDB, not MyISAM
- ✅ Use foreign keys with onDelete('cascade')
- ✅ Add indexes to frequently searched fields
- ✅ Store timestamps in UTC, convert in app
- ✅ Avoid text or json unless needed
- ✅ Use migrations and version your DB


### 🔄 9. Deployment & Production Readiness
- ✅ .env file set up with production keys
- ✅ Use queues and Supervisor (for jobs)
- ✅ Enable Redis or Memcached
- ✅ Enable caching (config, route, view, queries)
    <Details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>
    ## 🔧 **Core Components Implemented**

    ### 1. **Cache Configuration** (`config/cache.php` & `.env`)
    - ✅ Redis support (production ready)
    - ✅ File cache fallback (development friendly)
    - ✅ Cache prefix configuration (`larabasex_cache`)
    - ✅ Environment-specific cache drivers

    ### 2. **Query Cache Service** (`app/Services/QueryCacheService.php`)
    ```php
    // Key Features:
    - Cache TTL management (default 1 hour)
    - Cache tags support (Redis/Memcached)
    - Automatic fallback on cache failures
    - Cache statistics and monitoring
    - Bulk cache operations (clear all query cache)
    - Cache key generation utilities
    ```

    ### 3. **Cache Warmup Service** (`app/Services/CacheWarmupService.php`)
    ```php
    // Capabilities:
    - Config cache warming (`config:cache`)
    - Route cache warming (`route:cache`)
    - View cache warming (`view:cache`)
    - Critical query cache warming
    - Application-specific cache warming
    - Cache status monitoring
    - Clear and warmup workflow
    ```

    ### 4. **Cacheable Model Trait** (`app/Traits/Cacheable.php`)
    ```php
    // Model Methods Available:
    - cachedCount()          // Cache count queries
    - cachedFirst()          // Cache first record
    - cachedLatest($limit)   // Cache latest records
    - cachedFind($id)        // Cache find by ID
    - cachedWhere()          // Cache where queries
    - cachedPluck()          // Cache pluck operations
    - flushCache()           // Clear model cache
    - getCacheStats()        // Model cache statistics
    ```

    ### 5. **Cache Management Command** (`app/Console/Commands/CacheManagement.php`)
    ```bash
    # Available Commands:
    php artisan cache:manage status    # Show cache status
    php artisan cache:manage warm      # Warm all caches
    php artisan cache:manage clear     # Clear all caches
    php artisan cache:manage optimize  # Optimize for production
    ```

    ## 🎯 **Implementation Details**

    ### **User Model Integration**
    ```php
    // Added to User model:
    use App\Traits\Cacheable;

    protected $cacheTTL = 1800; // 30 minutes
    protected $cacheTags = ['users', 'auth'];

    // Usage examples:
    $userCount = User::cachedCount();
    $recentUsers = User::cachedLatest(10);
    $user = User::cachedFind($id);
    ```

    ### **Cache Strategy**
    - **Config Cache**: Laravel configurations cached for faster loading
    - **Route Cache**: Route definitions cached to eliminate parsing
    - **View Cache**: Compiled views cached to reduce compilation time
    - **Query Cache**: Database queries cached with automatic invalidation
    - **Application Cache**: Custom business logic cached with tags

    ### **Performance Optimizations**
    - Cache prefixing to avoid collisions
    - Automatic cache invalidation on model changes
    - Cache statistics for monitoring
    - Graceful fallback on cache failures
    - Environment-aware caching (more aggressive in production)

    ## 📊 **Performance Results**

    ### **Test Results** (via `/test-cache` route):
    - **First Request**: 12.17ms (cache miss)
    - **Second Request**: 3.25ms (cache hit)
    - **Performance Improvement**: ~74% faster with cache

    ### **Cache Statistics**:
    ```json
    {
    "driver": "file",
    "supports_tags": false,
    "total_keys": 0,
    "query_cache_keys": 0,
    "model": "User",
    "cache_tags": ["user", "users", "auth"],
    "cache_ttl": 1800
    }
    ```

    ## 🚀 **Production Deployment**

    ### **Redis Setup** (Recommended for Production)
    ```env
    CACHE_DRIVER=redis
    CACHE_STORE=redis
    CACHE_PREFIX=larabasex_cache
    REDIS_CACHE_DB=1
    ```

    ### **File Cache Setup** (Development/Testing)
    ```env
    CACHE_DRIVER=file
    CACHE_STORE=file
    CACHE_PREFIX=larabasex_cache
    ```

    ### **Deployment Commands**
    ```bash
    # After deployment, run:
    php artisan cache:manage optimize  # Optimize all caches
    php artisan cache:manage warm      # Warm critical caches
    php artisan cache:manage status    # Verify cache status
    ```

    ## 🔍 **Monitoring & Maintenance**

    ### **Cache Health Checks**
    - Cache connection testing
    - Cache driver verification
    - Cache key monitoring
    - Performance metrics tracking

    ### **Cache Management**
    - Manual cache clearing
    - Selective cache warming
    - Cache statistics viewing
    - Environment-specific optimization

    ## 🎉 **Benefits Achieved**

    1. **Performance**: 74% faster response times for cached queries
    2. **Scalability**: Reduced database load through intelligent caching
    3. **Flexibility**: Multiple cache drivers (Redis, File, Memcached)
    4. **Monitoring**: Comprehensive cache statistics and health checks
    5. **Automation**: Automated cache warming and management
    6. **Developer Experience**: Easy-to-use caching methods for models
    7. **Production Ready**: Environment-aware cache optimization

    ---

    ## 📝 **Next Steps**

    The cache system is now fully implemented and tested. The next priorities are:

    2. **❌ DB backups automated** - Implement automated database backup system
    3. **❌ Health check route (/health)** - Create comprehensive health monitoring
    4. **❌ Use Laravel Forge or Ploi or GitHub Actions for CI/CD** - Setup CI/CD pipeline

    This cache implementation provides a solid foundation for production-grade performance optimization!

    </Details>
- ✅ DB backups automated
    <Details>
    <summary>
    <strong>🔐 Implementation Details</strong> (Click to expand)</summary>
    ## 🔧 **Core Components Implemented**

    ### 1. **Enhanced Backup Configuration** (`config/backup.php`)
    - ✅ Gzip compression enabled for database dumps
    - ✅ Timestamped backup filenames (`Y-m-d_H-i-s`)
    - ✅ Backup encryption with password protection
    - ✅ Cloud storage support (S3) ready to enable
    - ✅ Email notifications configured
    - ✅ Comprehensive retention policies

    ### 2. **Advanced Backup Management Command** (`app/Console/Commands/BackupManagement.php`)
    ```bash
    # Available Commands:
    php artisan backup:manage status    # Comprehensive backup status
    php artisan backup:manage create    # Create backup with options
    php artisan backup:manage restore   # Restore from backup
    php artisan backup:manage verify    # Verify backup integrity
    php artisan backup:manage clean     # Clean old backups
    php artisan backup:manage monitor   # Advanced health monitoring
    ```

    ### 3. **Backup Monitoring Service** (`app/Services/BackupMonitoringService.php`)
    ```php
    // Key Features:
    - Backup age monitoring (alerts if > 25 hours old)
    - File size validation (detects suspiciously small backups)
    - Storage space monitoring (warns at 80% usage)
    - Backup integrity verification (validates zip archives)
    - Database connectivity checks
    - Health score calculation (0-100)
    - Automated email notifications
    ```

    ### 4. **Automated Task Scheduling** (`routes/console.php`)
    ```php
    // Scheduled Tasks:
    - Daily DB backup: 2:00 AM (database only)
    - Weekly full backup: Sunday 3:00 AM (database + files)
    - Daily cleanup: 4:00 AM (remove old backups per retention policy)
    ```

    ### 5. **Retention Policy Configuration**
    ```php
    // Backup Retention Strategy:
    - Keep ALL backups for: 7 days
    - Keep DAILY backups for: 16 days
    - Keep WEEKLY backups for: 8 weeks
    - Keep MONTHLY backups for: 4 months
    - Keep YEARLY backups for: 2 years
    - Max storage limit: 5GB
    ```

    ## 🎯 **Implementation Details**

    ### **Backup Creation Process**
    ```bash
    # Database-only backup (daily)
    php artisan backup:manage create --type=db --verify

    # Full backup with files (weekly)
    php artisan backup:manage create --type=full --encrypt

    # Manual backup with verification
    php artisan backup:manage create --type=db --verify --encrypt
    ```

    ## 📊 **Monitoring & Alerting**

    ### **Health Monitoring Results**
    ```bash
    📊 Advanced Health Check Results:
       Overall Status: ✅ HEALTHY
       Backup Age: ✅ Latest backup is 0.14 hours old
       Backup Size: ✅ Backup sizes are normal (latest: 6.36 KB)
       Storage Space: ✅ Storage usage is 62.24% (86.19 GB free)
       Backup Integrity: ✅ All 2 backup files are valid
       Database Connectivity: ✅ Database connection is healthy

    📈 Backup Metrics:
       Backup Count: 2
       Total Size: 12.73 KB
       Average Size: 6.36 KB
       Oldest: 8 minutes ago
       Newest: 26 seconds ago
    ```

    ## 🚀 **Production Features**

    ### **Security Features**
    - ✅ Password-protected backup archives
    - ✅ Gzip compression for space efficiency
    - ✅ Secure private storage directory
    - ✅ Encrypted file storage ready (AWS S3)
    - ✅ Audit logging for all operations

    ### **Automation Features**
    - ✅ Scheduled daily database backups (2 AM)
    - ✅ Weekly full system backups (Sunday 3 AM)
    - ✅ Automated cleanup (daily at 4 AM)
    - ✅ Health monitoring with email alerts
    - ✅ Backup integrity verification

    ### **Management Features**
    - ✅ Comprehensive backup status reporting
    - ✅ Manual backup creation with options
    - ✅ Backup verification and validation
    - ✅ Intelligent retention policies
    - ✅ Cloud storage integration ready

    ## 🎉 **Benefits Achieved**

    1. **Data Protection**: Automated daily backups with 7-day retention
    2. **Disaster Recovery**: Point-in-time recovery with multiple retention periods
    3. **Monitoring**: Real-time health checks and email notifications
    4. **Security**: Encrypted, password-protected backup archives
    5. **Scalability**: Cloud storage ready for enterprise deployment
    6. **Automation**: Zero-maintenance backup system with intelligent cleanup
    7. **Verification**: Automated backup integrity checks

    ---

    **✅ Status**: Database Backup System - COMPLETED!
    **📦 Ready**: Production-grade automated backup with monitoring
    **🔒 Secure**: Encrypted backups with comprehensive retention policies
    **📊 Monitored**: Health checks with automated alerts and reporting

    </Details>
- ✅ Health check route (/health)
- ❌ Use Laravel Forge or Ploi or GitHub Actions for CI/CD


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>

<br>

<br>

#

## Authentication Flow Documentation

<details>
<summary><strong>🔐 Complete Login Process Flow</strong> (Click to expand)</summary>

### Complete Login Process Flow
Understanding how user authentication works from frontend to backend and database.

#### 🎯 Step-by-Step Login Flow

**1. User visits login page**

```php
GET /login → AuthenticatedSessionController@create() → returns Inertia::render('auth/login')
```

**2. Login form displayed**

```typescript
resources/js/pages/auth/login.tsx renders with email/password fields
```

**3. User submits form**

```php
POST /login → AuthenticatedSessionController@store(LoginRequest $request)
```

**4. Login validation & authentication**

```php
LoginRequest validates email/password → Auth::attempt() tries to log user in
```

**5. Successful login redirect**

```php
return redirect()->intended(RouteServiceProvider::HOME);
// RouteServiceProvider::HOME = '/dashboard'
```

**6. Dashboard redirect route**

```php
GET /dashboard → redirects to /admin/dashboard (from backend.php)
```

**7. Admin dashboard loads**

```php
GET /admin/dashboard → DashboardController@index() → Inertia::render('dashboard')
```

**8. Final page rendered**

```typescript
resources/js/pages/dashboard.tsx displays the admin dashboard
```

#### 📋 Files Involved in Authentication

| Step | File | Purpose |
|------|------|---------|
| 1 | `routes/auth.php` | Defines login routes |
| 2 | `AuthenticatedSessionController.php` | Handles login logic |
| 3 | `auth/login.tsx` | Login form UI |
| 4 | `LoginRequest.php` | Validates credentials |
| 5 | `RouteServiceProvider.php` | Defines redirect destination |
| 6 | `routes/backend.php` | Dashboard redirect route |
| 7 | `DashboardController.php` | Dashboard data & logic |
| 8 | `dashboard.tsx` | Dashboard UI |

#### 🛡️ Security Requirements

For login to succeed, **ALL** of these must be true:

| Requirement | Check | Location |
|-------------|-------|----------|
| **Email exists** | `users.email` must match | MySQL Database |
| **Password correct** | Hashed password must verify | MySQL Database |
| **User active** | `users.status = 'ACTIVE'` | User Model |
| **Account not locked** | Rate limiting checks | LoginRequest |
| **Proper permissions** | Role/permission validation | Custom Middleware |

#### 🔐 What Happens During Authentication

```php
// When user submits login form, Laravel does:
1. SELECT * FROM users WHERE email = 'user@example.com'
2. password_verify('user-entered-password', $user->password)
3. Check if user status is ACTIVE
4. Validate user roles/permissions
5. If ALL checks pass → Login SUCCESS
6. If ANY check fails → Login FAILS
```

#### ❌ Common Login Failures

```php
// Wrong email
Auth::attempt(['email' => 'wrong@email.com', 'password' => 'anything'])
// Result: FALSE - No user found with that email

// Correct email, wrong password
Auth::attempt(['email' => 'user@example.com', 'password' => 'wrongpassword'])
// Result: FALSE - Password doesn't match hash in database

// User exists but INACTIVE status
// Custom middleware blocks access even if login succeeds
```

#### 🔑 Key Security Points

- **Database Validation**: Without correct credentials in MySQL `users` table, login is impossible
- **Password Hashing**: Passwords are hashed using Laravel's secure bcrypt algorithm
- **Session Management**: Successful login creates secure session tokens
- **Middleware Protection**: Custom middleware validates user status and permissions
- **Rate Limiting**: Failed login attempts are throttled to prevent brute force attacks

#### 📝 Authentication Middleware Stack

After successful login, protected routes use this middleware stack:

```php
Route::middleware(['auth', 'verified', 'admin', 'preventBackHistory'])->group(function () {
    // Protected admin routes
});
```

- **auth**: Ensures user is authenticated
- **verified**: Ensures email is verified (if required)
- **admin**: Custom middleware checking admin permissions
- **preventBackHistory**: Prevents browser back button after logout

</details>


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Authorization Flow Documentation

<details>
<summary><strong>🛡️ Complete Role-Based Access Control (RBAC) Flow</strong> (Click to expand)</summary>

### Complete Role-Based Access Control (RBAC) Flow
Understanding how user authorization works from registration to protected resource access.

#### 🎯 Step-by-Step Authorization Flow

**1. User Registration with Role Assignment**

```php
POST /register → RegisteredUserController@store() → User created with 'User' role and 'INACTIVE' status
```

**2. Admin Approval Process**

```php
Admin changes user status: 'INACTIVE' → 'ACTIVE' in admin panel
```

**3. User Login Attempt**

```php
POST /login → AuthenticatedSessionController@store() → Authentication succeeds
```

**4. Protected Route Access**

```php
GET /admin/dashboard → Middleware stack: ['auth', 'verified', 'admin', 'preventBackHistory']
```

**5. AdminAccess Middleware Validation**

```php
AdminAccess middleware validates:
1. Authentication status
2. User status (ACTIVE/INACTIVE)
3. User roles (RootUser/Admin/User)
4. Specific permissions for the route
```

**6. Permission-Based Access Control**

```php
Route: admin/dashboard → Permission: dashboard-view → Access Granted/Denied
```

**7. Final Access Decision**

```php
if (hasPermission) → Proceed to controller
else → Access Denied (403)
```

#### 📋 Files Involved in Authorization

| Step | File | Purpose |
|------|------|---------|
| 1 | `RegisteredUserController.php` | Assigns default 'User' role to new users |
| 2 | `AdminAccess.php` | Main authorization middleware |
| 3 | `User.php` model | Role/permission relationships |
| 4 | `routes/backend.php` | Protected route definitions |
| 5 | `DashboardController.php` | Resource access control |
| 6 | Database tables | `roles`, `permissions`, `role_has_permissions` |

#### 🛡️ Authorization Security Levels

Authorization happens in **4 security levels**:

| Level | Check | Middleware/Location | Action if Fails |
|-------|-------|-------------------|-----------------|
| **Level 1** | Authentication | `auth` middleware | Redirect to login |
| **Level 2** | User Status | `AdminAccess` middleware | Access denied |
| **Level 3** | Role Validation | `AdminAccess` middleware | Access denied |
| **Level 4** | Permission Check | `AdminAccess` middleware | Access denied |

#### 🔐 Role & Permission Structure

```php
// Role Hierarchy (from highest to lowest access)
RootUser → Admin → User

// Permission Mapping Example:
Route: admin/users → Permission: user-view
Route: admin/users (POST) → Permission: user-store
Route: admin/users/{user} (PUT) → Permission: user-update
```

#### 📊 Current Role Permissions

| Role | Status | Permissions | Access Level |
|------|--------|-------------|--------------|
| **RootUser** | ACTIVE | All 14 permissions | Full system access |
| **Admin** | ACTIVE | Configurable subset | Partial admin access |
| **User** | INACTIVE → ACTIVE | `dashboard-view` only | Basic dashboard access |

**RootUser Permissions:**
```
- dashboard-view, role-view, role-store, role-update, role-permission
- user-view, user-store, user-update
- employee-view, employee-store, employee-update
- enquiry-view, enquiry-store, enquiry-update
```

**User Permissions:**
```
- dashboard-view (basic dashboard access only)
```

#### 🚦 Authorization Decision Process

```php
// AdminAccess Middleware Logic Flow:

1. Authentication Check
   if (!Auth::check()) → return redirect('/login')

2. User Status Check
   if ($user->status !== 'ACTIVE') → return access_denied()

3. Super Admin Bypass
   if ($user->hasRole(['RootUser', 'SuperAdmin'])) → return next($request)

4. Permission Extraction
   $route = 'admin/dashboard' → $permission = 'dashboard-view'

5. Permission Check
   if ($user->can($permission)) → return next($request)
   else → return access_denied()
```

#### 🔄 User Lifecycle & Authorization States

```mermaid
Registration → INACTIVE + User Role → Admin Approval → ACTIVE Status → Login → Permission Check → Access Granted/Denied
```

**State Transitions:**
```php
// New User Registration
User::create([
    'status' => 'INACTIVE',  // Cannot login
    'role' => 'User'         // Basic permissions when activated
]);

// Admin Activation
$user->update(['status' => 'ACTIVE']); // Can now login

// Role Upgrade (if needed)
$user->assignRole('Admin'); // Gets additional permissions
```

#### ❌ Common Authorization Failures

```php
// Scenario 1: Inactive User
$user->status = 'INACTIVE' → Access denied (even with correct role)

// Scenario 2: Insufficient Role
$user->hasRole('User') but route needs 'Admin' → Access denied

// Scenario 3: Missing Permission
$user->hasRole('Admin') but lacks 'user-delete' permission → Access denied

// Scenario 4: Route Permission Not Found
Route has no mapped permission → Access denied (fail-safe)
```

#### 🛠️ Permission Mapping System

The system uses **dynamic permission mapping** based on route patterns:

```php
// Route Pattern → Permission Pattern
admin/{resource} → {resource}-view
admin/{resource} (POST) → {resource}-store
admin/{resource}/{id} (PUT) → {resource}-update
admin/{resource}/{id} (DELETE) → {resource}-destroy

// Examples:
admin/users → user-view
admin/users (POST) → user-store
admin/users/123 (PUT) → user-update
admin/roles/456/permission → role-permission
```

#### 🔑 Key Authorization Features

- **Role-Based Access Control**: Users assigned roles with specific permissions
- **Dynamic Permission Checking**: Permissions mapped from route patterns
- **Multi-Level Security**: 4-layer validation (auth → status → role → permission)
- **Emergency Access**: RootUser bypasses all permission checks
- **Fail-Safe Design**: Unknown routes/permissions default to access denied
- **Comprehensive Logging**: All access attempts logged for security audit

#### 📝 Authorization Middleware Configuration

```php
// Protected Routes Configuration
Route::middleware(['auth', 'verified', 'admin', 'preventBackHistory'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        // Each route automatically mapped to required permission
    });
});
```

**Middleware Responsibilities:**
- **auth**: Basic authentication validation
- **verified**: Email verification (if enabled)
- **admin**: Role-based authorization (our custom middleware)
- **preventBackHistory**: Security measure for sensitive pages

#### 🎯 Authorization Security Benefits

- **✅ Granular Access Control**: Each route protected by specific permissions
- **✅ Role Hierarchy**: Clear privilege levels from User to RootUser
- **✅ Admin Approval Workflow**: New users require explicit activation
- **✅ Dynamic Permission System**: Easy to add new protected routes
- **✅ Emergency Access**: RootUser always has system access
- **✅ Comprehensive Audit**: All authorization decisions logged
- **✅ Fail-Safe Default**: Deny access when in doubt


</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Setting Profile Information Update

<details>
<summary><strong>👤 Complete Profile Management Flow</strong> (Click to expand)</summary>

### Complete Profile Management Flow
Understanding how authenticated users can update their personal information (name and email).

#### 🎯 Step-by-Step Profile Update Flow

**1. User accesses profile settings**

```php
GET /settings/profile → ProfileController@edit() → returns Inertia::render('settings/profile')
```

**2. Profile form displayed**

```typescript
resources/js/pages/settings/profile.tsx renders with first_name, last_name, and email fields
```

**3. User modifies information**

```typescript
Form fields auto-populate with current user data from auth.user object
```

**4. User submits form**

```php
PATCH /settings/profile → ProfileController@update(ProfileUpdateRequest $request)
```

**5. Backend validation**

```php
ProfileUpdateRequest validates:
- first_name: required, string, max:255
- last_name: nullable, string, max:255
- email: required, unique (except current user), valid email format
```

**6. Email verification check**

```php
if (email changed) → set email_verified_at = null → triggers verification process
```

**7. Profile updated & saved**

```php
$user->fill($validated_data) → $user->save() → redirect back to profile page
```

**8. Success feedback displayed**

```typescript
"Saved" message shows briefly using Transition component
```

#### 📋 Files Involved in Profile Update

| Step | File | Purpose |
|------|------|---------|
| 1 | `routes/settings.php` | Defines profile routes |
| 2 | `ProfileController.php` | Handles profile logic |
| 3 | `settings/profile.tsx` | Profile form UI |
| 4 | `ProfileUpdateRequest.php` | Validates profile data |
| 5 | `User.php` model | User data storage |
| 6 | `SettingsLayout.tsx` | Settings page wrapper |

#### 🛡️ Profile Update Security Features

Profile updates include these security measures:

| Security Layer | Check | Purpose |
|----------------|-------|---------|
| **Authentication** | Must be logged in | Only auth users can update profile |
| **Ownership** | Only update own profile | Users can't modify other profiles |
| **Email Uniqueness** | Email must be unique in system | Prevents duplicate accounts |
| **Data Validation** | Required fields & format validation | Ensures data integrity |
| **Email Verification** | Reset verification on email change | Confirms new email ownership |

#### 🔐 Profile Form Fields & Validation

```typescript
// Profile Form Structure
type ProfileForm = {
    first_name: string;  // Required, max 255 chars
    last_name: string;   // Optional, max 255 chars
    email: string;       // Required, valid email, unique
};

// Form Initialization
const { data, setData, patch } = useForm<ProfileForm>({
    first_name: auth.user.first_name || '',
    last_name: auth.user.last_name || '',
    email: auth.user.email,
});
```

#### 📊 Profile Update Process Details

**Frontend Form Handling:**
```typescript
// Real-time data binding
onChange={(e) => setData('first_name', e.target.value)}

// Form submission
patch(route('profile.update'), { preserveScroll: true });

// Success feedback
{recentlySuccessful && <p>Saved</p>}
```

**Backend Processing:**
```php
// Validation through ProfileUpdateRequest
$validated = $request->validated();

// Fill user model with new data
$request->user()->fill($validated);

// Handle email changes
if ($request->user()->isDirty('email')) {
    $request->user()->email_verified_at = null;
}

// Save changes
$request->user()->save();
```

#### 🔄 Email Verification Workflow

When user changes email address:

```php
1. New email is saved to database
2. email_verified_at is set to NULL
3. User must verify new email address
4. Verification link sent to new email
5. Until verified, some features may be restricted
```

**Email Verification UI:**
```typescript
{mustVerifyEmail && auth.user.email_verified_at === null && (
    <div>
        <p>Your email address is unverified.</p>
        <Link href={route('verification.send')}>
            Click here to resend verification email
        </Link>
    </div>
)}
```

#### ❌ Common Profile Update Failures

```php
// Validation Failures:

// Empty first name
'first_name' => '' → Error: "The first name field is required"

// Duplicate email
'email' => 'existing@email.com' → Error: "The email has already been taken"

// Invalid email format
'email' => 'not-an-email' → Error: "The email must be a valid email address"

// First name too long
'first_name' => str_repeat('A', 256) → Error: "First name may not be greater than 255 characters"
```

#### 🎨 UI/UX Features

**Responsive Design:**
```typescript
// Mobile: Stacked layout
<div className="grid grid-cols-1 gap-4 md:grid-cols-2">

// Desktop: Side-by-side first_name and last_name
```

**Form Accessibility:**
```typescript
// Proper labels and autocomplete
<Label htmlFor="first_name">First Name</Label>
<Input autoComplete="given-name" />

// Error message display
<InputError message={errors.first_name} />
```

**Visual Feedback:**
```typescript
// Loading state
<Button disabled={processing}>Save</Button>

// Success animation
<Transition show={recentlySuccessful}>
    <p>Saved</p>
</Transition>
```

#### 🔑 Key Profile Management Features

- **✅ Real-time Validation**: Immediate feedback on form errors
- **✅ Responsive Design**: Works on mobile and desktop devices
- **✅ Auto-population**: Form loads with current user data
- **✅ Email Verification**: Automatically triggers when email changes
- **✅ Security First**: Only authenticated users can access
- **✅ Data Integrity**: Comprehensive validation rules
- **✅ User Experience**: Smooth transitions and clear feedback

#### 📝 Profile Settings Integration

Profile updates integrate with the broader settings system:

```typescript
// Settings Layout Navigation
<SettingsLayout>
    {/* Profile form content */}
    <DeleteUser /> {/* Account deletion component */}
</SettingsLayout>
```

**Settings Navigation:**
- Profile Information (current page)
- Password Update
- Account Deletion
- Other settings sections

#### 🛠️ Technical Implementation Notes

**Inertia.js Integration:**
```php
// Controller returns Inertia response
return Inertia::render('settings/profile', [
    'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
    'status' => session('status'),
]);
```

**Form Request Validation:**
```php
// Custom validation rules in ProfileUpdateRequest
Rule::unique(User::class)->ignore($this->user()->id)
// Allows user to keep their current email while preventing duplicates
```

**State Management:**
```typescript
// Inertia useForm hook manages form state
const { data, setData, patch, errors, processing, recentlySuccessful } = useForm();
```

This profile management system provides a secure, user-friendly way for authenticated users to maintain their personal information while ensuring data integrity and security throughout the process.

</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Setting Password Update

<details>
<summary><strong>🔐 Complete Password Management Flow</strong> (Click to expand)</summary>

### Complete Password Management Flow
Understanding how authenticated users can securely update their account passwords.

#### 🎯 Step-by-Step Password Update Flow

**1. User accesses password settings**

```php
GET /settings/password → PasswordController@edit() → returns Inertia::render('settings/password')
```

**2. Password form displayed**

```typescript
resources/js/pages/settings/password.tsx renders with current_password, password, and password_confirmation fields
```

**3. User enters password information**

```typescript
Form requires: current password, new password, and password confirmation
```

**4. User submits form**

```php
PUT /settings/password → PasswordController@update(Request $request)
```

**5. Backend validation**

```php
Request validates:
- current_password: required, must match current user password
- password: required, must meet Password::defaults() rules, confirmed
- password_confirmation: must match password field
```

**6. Password hashing & update**

```php
Hash::make($validated['password']) → User password updated in database
```

**7. Success response**

```php
return back() → redirects to password settings page
```

**8. Success feedback displayed**

```typescript
"Saved" message shows briefly using Transition component
```

#### 📋 Files Involved in Password Update

| Step | File | Purpose |
|------|------|---------|
| 1 | `routes/settings.php` | Defines password routes |
| 2 | `Settings/PasswordController.php` | Handles password logic |
| 3 | `settings/password.tsx` | Password form UI |
| 4 | `Request` validation | Built-in Laravel validation |
| 5 | `User.php` model | Password storage |
| 6 | `SettingsLayout.tsx` | Settings page wrapper |

#### 🛡️ Password Update Security Features

Password updates include multiple security layers:

| Security Layer | Check | Purpose |
|----------------|-------|---------|
| **Authentication** | Must be logged in | Only auth users can change password |
| **Current Password** | Must provide current password | Prevents unauthorized changes |
| **Password Strength** | Must meet Password::defaults() rules | Ensures strong passwords |
| **Confirmation** | Must confirm new password | Prevents typos |
| **Secure Hashing** | bcrypt/Argon2 hashing | Passwords stored securely |

#### 🔐 Password Form Fields & Validation

```typescript
// Password Form Structure
type PasswordForm = {
    current_password: string;      // Required, must match current
    password: string;              // Required, must meet strength rules
    password_confirmation: string; // Required, must match password
};

// Form Initialization
const { data, setData, put } = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});
```

#### 📊 Password Validation Rules

**Laravel Password::defaults() includes:**
```php
- Minimum 8 characters
- At least one uppercase letter (A-Z)
- At least one lowercase letter (a-z)
- At least one number (0-9)
- At least one special character (!@#$%^&*)
- Must be confirmed (password_confirmation field)
```

**Additional Security Rules:**
```php
- current_password: Must match user's existing password
- Cannot be empty or null
- Real-time validation on frontend
- Secure hashing using Hash::make()
```

#### 🔄 Password Update Process Details

**Frontend Form Handling:**
```typescript
// Real-time data binding
onChange={(e) => setData('password', e.target.value)}

// Form submission with error handling
put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => reset(),
    onError: (errors) => {
        // Focus on error fields and reset sensitive data
        if (errors.password) {
            reset('password', 'password_confirmation');
            passwordInput.current?.focus();
        }
        if (errors.current_password) {
            reset('current_password');
            currentPasswordInput.current?.focus();
        }
    },
});
```

**Backend Processing:**
```php
// Comprehensive validation
$validated = $request->validate([
    'current_password' => ['required', 'current_password'],
    'password' => ['required', Password::defaults(), 'confirmed'],
]);

// Secure password update
$request->user()->update([
    'password' => Hash::make($validated['password']),
]);

// Return to settings page
return back();
```

#### ❌ Common Password Update Failures

```php
// Validation Failures:

// Wrong current password
'current_password' => 'wrongpassword' → Error: "The current password is incorrect"

// Weak new password
'password' => '123' → Error: "Password must be at least 8 characters"

// Password mismatch
'password' => 'NewPass123!'
'password_confirmation' => 'DifferentPass' → Error: "Password confirmation does not match"

// Missing uppercase letter
'password' => 'newpass123!' → Error: "Password must contain at least one uppercase letter"

// Missing special character
'password' => 'NewPass123' → Error: "Password must contain at least one special character"
```

#### 🎨 UI/UX Features

**Security-First Design:**
```typescript
// All password fields use type="password"
<Input type="password" autoComplete="current-password" />
<Input type="password" autoComplete="new-password" />

// Auto-focus on error fields
passwordInput.current?.focus();
currentPasswordInput.current?.focus();
```

**Form Accessibility:**
```typescript
// Proper labels and autocomplete
<Label htmlFor="current_password">Current password</Label>
<Input autoComplete="current-password" />

// Clear error messages
<InputError message={errors.current_password} />
```

**Visual Feedback:**
```typescript
// Loading state prevents multiple submissions
<Button disabled={processing}>Save password</Button>

// Success animation
<Transition show={recentlySuccessful}>
    <p>Saved</p>
</Transition>
```

#### 🔒 Advanced Security Features

**Error Handling & Data Protection:**
```typescript
// Automatic form reset on success
onSuccess: () => reset()

// Selective field reset on errors
onError: (errors) => {
    if (errors.password) {
        reset('password', 'password_confirmation');
    }
    if (errors.current_password) {
        reset('current_password');
    }
}
```

**Backend Security Measures:**
```php
// Current password verification
'current_password' => ['required', 'current_password']

// Strong password enforcement
'password' => ['required', Password::defaults(), 'confirmed']

// Secure hashing algorithm
Hash::make($validated['password'])
```

#### 🔑 Key Password Management Features

- **✅ Current Password Verification**: Must know current password to change
- **✅ Strong Password Enforcement**: Laravel's Password::defaults() rules
- **✅ Password Confirmation**: Prevents typos with confirmation field
- **✅ Secure Hashing**: Uses Laravel's secure Hash::make() method
- **✅ Error Handling**: Smart field focus and data reset on errors
- **✅ Form Security**: Auto-reset sensitive data after submission
- **✅ User Experience**: Clear feedback and loading states

#### 📝 Password Settings Integration

Password updates integrate with the broader settings system:

```typescript
// Settings Layout Navigation
<SettingsLayout>
    <HeadingSmall
        title="Update password"
        description="Ensure your account is using a long, random password to stay secure"
    />
    {/* Password form content */}
</SettingsLayout>
```

**Settings Navigation:**
- Profile Information
- Password Update (current page)
- Account Deletion
- Other settings sections

#### 🛠️ Technical Implementation Notes

**Inertia.js Integration:**
```php
// Simple controller response
return Inertia::render('settings/password');
// No additional data needed - form handles state internally
```

**Laravel Validation Rules:**
```php
// Built-in current password validation
'current_password' => ['required', 'current_password']

// Laravel's default password strength rules
Password::defaults() // Configurable in AppServiceProvider if needed
```

**State Management:**
```typescript
// Inertia useForm hook with refs for focus management
const { data, setData, put, reset, errors, processing, recentlySuccessful } = useForm();
const passwordInput = useRef<HTMLInputElement>(null);
const currentPasswordInput = useRef<HTMLInputElement>(null);
```

#### 🎯 Password Security Benefits

- **✅ Multi-Layer Validation**: Current password + strength rules + confirmation
- **✅ Secure Storage**: Passwords hashed with Laravel's secure algorithms
- **✅ User-Friendly Errors**: Clear validation messages and field focus
- **✅ Form Security**: Automatic sensitive data cleanup
- **✅ Authentication Required**: Only logged-in users can change passwords
- **✅ Real-Time Feedback**: Immediate validation and success confirmation
- **✅ Accessibility Compliant**: Proper labels, autocomplete, and focus management

This password management system provides enterprise-grade security while maintaining an excellent user experience, ensuring users can easily maintain strong, secure passwords for their accounts.

</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#

## Permission Based UI Implementation

<details>
<summary><strong>Permissions management</strong> (Click to expand)</summary>

### Summary
Implemented a comprehensive permission-based UI system that will solve your 403 error issues and provide a better user experience. Here's what has been created:

### Components Created

#### 1. `PermissionDenied` Component
**Path:** `/resources/js/components/permission-denied.tsx`
- Reusable component that shows a professional "Access Denied" message
- Configurable title, message, and description
- Includes contact information for requesting permissions
- Follows your monochrome design system

#### 2. `Error` Page Component
**Path:** `/resources/js/pages/error.tsx`
- Global error page that handles 403, 404, 500, and other HTTP errors
- Uses PermissionDenied component for 403 errors specifically
- Provides helpful navigation options (Dashboard, Back, Refresh)

#### 3. `usePermissions` Hook
**Path:** `/resources/js/hooks/use-permissions.ts`
- React hook for checking user permissions and roles
- Provides functions like `hasPermission()`, `hasRole()`, `isRootUser()`, etc.
- Automatically handles RootUser role (bypasses all permission checks)

#### 4. `ProtectedSection` Component
**Path:** `/resources/js/components/protected-section.tsx`
- Wrapper component that conditionally renders content based on permissions
- Can protect individual sections within pages
- Shows permission denied message or custom fallback
- Supports multiple permissions/roles with AND/OR logic

### Backend Integration

#### Exception Handler Updated
**Path:** `/app/Exceptions/Handler.php`
- Added proper 403 error handling for Inertia requests
- Automatically renders the error page with permission denied message
- Handles both `AccessDeniedHttpException` and `AuthorizationException`

### Usage Examples

#### 1. Protecting Navigation (Already Updated)
```tsx
// app-sidebar.tsx - Navigation items are filtered by permissions
const hasAccess = hasPermission(auth.user, 'user-view');
```

#### 2. Protecting Page Sections (Already Updated in Users Index)
```tsx
// Protect Create Button
<ProtectedSection permission="user-store" showDeniedMessage={false}>
    <Link href={route('admin.users.create')}>
        <Button>Create User</Button>
    </Link>
</ProtectedSection>

// Protect Edit/Delete in Dropdown
<ProtectedSection permission="user-update" showDeniedMessage={false}>
    <DropdownMenuItem asChild>
        <Link href={route('admin.users.edit', user.id)}>Edit User</Link>
    </DropdownMenuItem>
</ProtectedSection>

// Protect Status Toggle
<ProtectedSection permission="user-update" showDeniedMessage={false}>
    <Switch checked={isActive} onCheckedChange={() => handleStatusChange(user.id, user.status)} />
</ProtectedSection>
```

#### 3. Using the Permission Hook
```tsx
import { usePermissions } from '@/hooks/use-permissions';

function MyComponent() {
    const { hasPermission, hasRole, isRootUser } = usePermissions();

    if (!hasPermission('user-view')) {
        return <PermissionDenied />;
    }

    return (
        <div>
            {hasPermission('user-store') && <CreateButton />}
            {hasPermission('user-update') && <EditButton />}
            {(hasRole('Admin') || isRootUser()) && <AdminOnlySection />}
        </div>
    );
}
```

### Controller Protection (Recommended)

Add this to your controllers to prevent 403 errors at the source:

```php
// In UserController.php constructor
public function __construct()
{
    $this->middleware('permission:user-view')->only(['index', 'show']);
    $this->middleware('permission:user-store')->only(['create', 'store']);
    $this->middleware('permission:user-update')->only(['edit', 'update', 'changeStatus']);
}
```

### Benefits

1. **Better UX**: Users see helpful messages instead of generic 403 errors
2. **Consistent Design**: All permission denied messages follow your design system
3. **Reusable**: Components can be used across all pages and sections
4. **Flexible**: Can protect entire pages, sections, or individual buttons
5. **Type Safe**: Full TypeScript support with proper interfaces
6. **Performance**: Frontend filtering prevents unnecessary clicks and requests

### Next Steps

1. **Add middleware to controllers** to prevent backend 403 errors
2. **Apply ProtectedSection** to other pages (roles, employees, enquiries)
3. **Test with different user roles** to ensure proper permission handling
4. **Customize messages** per section if needed

This system ensures users will never see a blank 403 error page again, and will always know why they can't access something and who to contact for help!

</details>

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>



<br>

<br>

#
