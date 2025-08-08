# 🚀 LaraBaseX

> A Laravel 12 + ReactJS Full Stack Starter Boilerplate.
This is a secure, modular, production-ready base project using Laravel 12 with ReactJS frontend, ideal for building scalable web applications without Blade.

<div id="top"></div>

##

### Table of content:

| No.     | Topics                                                                                  |
| ------- | --------------------------------------------------------------------------------------- |
| 0.      | [Tech Stack](#tech-stack)                                                               |
| 1       | [Codebase Important Documentations](#codebase-important-documentations)                 |




<br>

<br>

#



## Tech Stack

- **Backend**: Laravel 12 (REST API)
- **Frontend**: ReactJS (Vite + Axios)
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Deployment Ready**: Docker / Shared Hosting / VPS


<br>

<br>

#

## Codebase Important Documentations

| No.     | Topics                                                                                  | Status |
| ------- | --------------------------------------------------------------------------------------- | ------ |
| 1       | [Security Essentials Documentation](documentation/1.%20Security%20Essentials/)         | ✅ Complete |
| &emsp;1.1 | [HTTPS Enforced](documentation/1.%20Security%20Essentials/1.%20HTTPS%20enforced%20(Force%20HTTPS%20in%20AppServiceProvider)%20docx.md) | ✅ |
| &emsp;1.2 | [CORS Configured Properly](documentation/1.%20Security%20Essentials/2.%20CORS%20configured%20properly%20docx.md) | ✅ |
| &emsp;1.3 | [CSRF Protection](documentation/1.%20Security%20Essentials/3.%20CSRF%20protection%20docx.md) | ✅ |
| &emsp;1.4 | [Rate Limiting](documentation/1.%20Security%20Essentials/4.%20Rate%20Limiting%20for%20APIs%20docx.md) | ✅ |
| &emsp;1.5 | [Validation Layer](documentation/1.%20Security%20Essentials/5.%20Validation%20layer%20using%20FormRequest%20docx.md) | ✅ |
| &emsp;1.6 | [Policies & Gates](documentation/1.%20Security%20Essentials/6.%20Use%20policies%20or%20gates%20for%20authorization%20docx.md) | ✅ |
| &emsp;1.7 | [Mass Assignment Protection](documentation/1.%20Security%20Essentials/7.%20Avoid%20mass%20assignment%20bugs%20docx.md) | ✅ |
| &emsp;1.8 | [Escape & Sanitize Output](documentation/1.%20Security%20Essentials/8.%20Escape%20output%20or%20sanitize%20input%20if%20user-generated%20data%20is%20stored%20docx.md) | ✅ |
| &emsp;1.9 | [File Upload Security](documentation/1.%20Security%20Essentials/9.%20Sanitize%20uploaded%20files%20&%20validate%20MIME%20types%20docx.md) | ✅ |
| &emsp;1.10| [Environment Variables](documentation/1.%20Security%20Essentials/10.%20Use%20environment%20variables%20for%20all%20secrets%20docx.md) | ✅ |
| &emsp;1.11| [Disable Debug Mode](documentation/1.%20Security%20Essentials/11.%20Disable%20debug%20mode%20on%20production%20docx.md) | ✅ |
| &emsp;1.12| [Log Authentication Attempts](documentation/1.%20Security%20Essentials/12.%20Log%20all%20authentication%20attempts%20and%20system%20errors%20docx.md) | ✅ |
| &emsp;1.13| [Hide Laravel Version](documentation/1.%20Security%20Essentials/13.%20Do%20not%20expose%20Laravel%20version%20in%20headers%20docx.md) | ✅ |
| 2       | [Architecture & Structure Essentials](documentation/2.%20Architecture%20&%20Structure%20Essentials/) | 🚧 Partial |
| &emsp;2.1 | [Keep Controllers Thin](documentation/2.%20Architecture%20&%20Structure%20Essentials/1.%20Keep%20controllers%20thin,%20use%20Services%20for%20logic%20docx.md) | ✅ |
| &emsp;2.2 | [Helpers.php](documentation/2.%20Architecture%20&%20Structure%20Essentials/2.%20Helpers%20php%20for%20reusable%20functions%20docx.md) | ✅ |
| &emsp;2.3 | [Job Queues Setup](documentation/2.%20Architecture%20&%20Structure%20Essentials/3.%20Job%20Queues%20setup%20(Redis%20+%20Supervisor%20in%20production)%20docx.md) | ✅ |
| &emsp;2.4 | [Use Resource Routes & API Standards](documentation/2.%20Architecture%20&%20Structure%20Essentials/4.%20Use%20resource()%20routes%20&%20API%20standards%20docx.md) | ✅ |
| &emsp;2.5 | [Service Classes](#use-service-classes-for-business-logic-eg-appservicesuserservice) | ❌ Pending |
| &emsp;2.6 | [Repository Pattern](#use-repository-pattern-clean-separation-from-eloquent-queries) | ❌ Pending |
| &emsp;2.7 | [Enums](#use-enums-for-static-statuses-or-types-php-artisan-makeenum) | ❌ Pending |
| &emsp;2.8 | [Event-Listener System](#event-listener-system-for-side-effects-eg-sending-email-after-registration) | ❌ Pending |
| &emsp;2.9 | [Transform API Responses](#transform-api-response-data-using-laravel-resource-classes) | ❌ Pending |
| 3       | [Packages to Include Documentation](documentation/3.%20Packages%20to%20Include/) | ✅ Complete |
| &emsp;3.1 | [Spatie Laravel Permission](documentation/3.%20Packages%20to%20Include/1.%20Spatie%20Laravel%20Permission%20–%20roles%20permissions%20docx.md) | ✅ |
| &emsp;3.2 | [Laravel Sanctum or Passport](documentation/3.%20Packages%20to%20Include/2.%20Laravel%20Sanctum%20or%20Passport%20–%20token-based%20auth%20docx.md) | ✅ |
| &emsp;3.3 | [Laravel Telescope](documentation/3.%20Packages%20to%20Include/3.%20Laravel%20Telescope%20(local%20-%20dev)%20–%20debugging,%20request%20log%20docx.md) | ✅ |
| &emsp;3.4 | [Laravel Debugbar](documentation/3.%20Packages%20to%20Include/4.%20Laravel%20Debugbar%20docx.md) | ✅ |
| &emsp;3.5 | [Spatie Backup](documentation/3.%20Packages%20to%20Include/5.%20Spatie%20Backup%20–%20scheduled%20database%20docx.md) | ✅ |
| &emsp;3.6 | [Spatie Activity Log](documentation/3.%20Packages%20to%20Include/6.%20Spatie%20Activity%20Log%20–%20audit%20logs%20for%20user%20actions%20docx.md) | ✅ |
| 4       | [Developer Experience Documentation](documentation/4.%20Developer%20Experience%20(DX)/) | ✅ Complete |
| &emsp;4.1 | [Global Exception Handler](documentation/4.%20Developer%20Experience%20(DX)/1.%20Global%20Exception%20Handler%20for%20API%20errors%20docx.md) | ✅ |
| &emsp;4.2 | [Standard API Response](documentation/4.%20Developer%20Experience%20(DX)/2.%20Standard%20API%20Response%20format%20docx.md) | ✅ |
| &emsp;4.3 | [Seeder & Factory](documentation/4.%20Developer%20Experience%20(DX)/3.%20Seeder%20&%20Factory%20files%20for%20test%20data%20docx.md) | ✅ |
| &emsp;4.4 | [Env Example File](documentation/4.%20Developer%20Experience%20(DX)/4.%20Well-structured%20env%20example%20file%20docx.md) | ✅ |
| &emsp;4.5 | [API Documentation](documentation/4.%20Developer%20Experience%20(DX)/5.%20API%20Documentation%20via%20Swagger%20or%20Postman%20docx.md) | ✅ |
| &emsp;4.6 | [Postman Collection](documentation/4.%20Developer%20Experience%20(DX)/6.%20Postman%20Collection%20for%20APIs%20preloaded%20docx.md) | ✅ |
| &emsp;4.7 | [Static Analysis](documentation/4.%20Developer%20Experience%20(DX)/7.%20PHPStan%20or%20Larastan%20for%20static%20analysis%20docx.md) | ✅ |
| &emsp;4.8 | [Predefined Error Messages](documentation/4.%20Developer%20Experience%20(DX)/8.%20Predefined%20Error%20messages%20in%20lang%20-%20en%20-%20messages%20docx.md) | ✅ |
| 5       | [Frontend Integration Documentation](documentation/5.%20Frontend%20Integration%20(ReactJS)/) | ❌ Empty Folder |
| &emsp;5.1 | [Serve React with Vite](#serve-react-app-via-vite-from-laravel-backend) | ❌ Pending |
| &emsp;5.2 | [Proxy Setup](#set-up-proxy-in-viteconfigjs-to-api-routes) | ❌ Pending |
| &emsp;5.3 | [React Router](#react-routing-via-react-router-dom) | ❌ Pending |
| &emsp;5.4 | [Token Auth](#token-based-authentication-eg-sanctum) | ❌ Pending |
| &emsp;5.5 | [Secure Token Storage](#store-tokens-securely-httponly-if-possible) | ❌ Pending |
| &emsp;5.6 | [Axios Interceptor](#axios-with-global-error-interceptor) | ❌ Pending |
| &emsp;5.7 | [Dotenv in React](#dotenv-file-in-react-for-api-urls) | ❌ Pending |
| 6       | [User Management Essentials](documentation/6.%20User%20Management%20Essentials/) | ❌ Empty Folder |
| &emsp;6.1 | [Auth APIs](#registerloginlogout-apis) | ❌ Pending |
| &emsp;6.2 | [Password Management](#change-password--forgot-password--email-verify) | ❌ Pending |
| &emsp;6.3 | [Roles & Permissions](#user-roles-and-permissions-admin-user-manager) | ❌ Pending |
| &emsp;6.4 | [Login Throttling](#login-attempt-throttling) | ❌ Pending |
| &emsp;6.5 | [User Profile](#user-profile-with-avatar-upload) | ❌ Pending |
| &emsp;6.6 | [Two-Factor Auth](#two-factor-authentication-optional) | ❌ Pending |
| 7       | [Helper Functions Documentation](documentation/7.%20Helper%20Functions/) | ❌ Empty Folder |
| 8       | [MySQL Best Practices Documentation](documentation/8.%20MySQL%20Best%20Practices/) | ❌ Empty Folder |
| 9       | [Deployment & Production Readiness](documentation/9.%20Deployment%20&%20Production%20Readiness/) | ❌ Empty Folder |
| 10      | [Authentication Flow Documentation](documentation/10.%20Authentication%20Flow%20Documentation/Authentication%20Flow%20Documentation%20docx.md) | ✅ Complete |
| 11      | [Authorization Flow Documentation](documentation/11.%20Authorization%20Flow%20Documentation/Authorization%20Flow%20Documentation%20docx.md) | ✅ Complete |
| 12      | [Setting Profile Information Update](documentation/12.%20Setting%20Profile%20Information%20Update/) | ❌ Empty Folder |
| 13      | [Setting Password Update](documentation/13.%20Setting%20Password%20Update/) | ❌ Empty Folder |
| 14      | [Permission Based UI Implementation](documentation/14.%20Permission%20Based%20UI%20Implementation/) | ❌ Empty Folder |


<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>

#
