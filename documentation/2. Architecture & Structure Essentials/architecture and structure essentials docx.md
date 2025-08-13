## Architecture and Structure essentials


<div id="top"></div>

<br>

### Table of Contents
1. [Helpers for reusable functions](#helpers-for-reusable-functions)
2. [Job Queues setup](#job-queues-setup)
3. [Use resource routes and API standards](#use-resource-routes-and-api-standards)

<br>

<br>

#

## Helpers for reusable functions
> Helpers.php for reusable functions



### What this topic is:
Centralized helper functions for common tasks and utilities.

### Why we are using it:
- Avoids code duplication across controllers, models, and views.
- Makes codebase easier to maintain and extend.
- Provides a single place for reusable logic (e.g., API responses, formatting).

### What it does in our project:
- Defines functions like `apiResponse()` for consistent API output.
- Used in controllers and exception handler for standardized responses.
- Includes other utility functions as needed.

### Because of this, files of code:
- `app/helpers.php`: Main helper functions file.
- Referenced in: `app/Exceptions/Handler.php`, various controllers.

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>

#

## Job Queues setup
> Job Queues setup (Redis + Supervisor in production)



### What this topic is:
Background job processing using Laravel Queues, Redis, and Supervisor.

### Why we are using it:
- Offloads time-consuming tasks (emails, notifications, backups) from HTTP requests.
- Improves performance and user experience.
- Ensures reliable job execution in production.

### What it does in our project:
- Handles queued jobs for emails, notifications, backups, etc.
- Uses Redis as the queue driver for fast, persistent job storage.
- Supervisor manages queue workers in production for fault tolerance.

### Because of this, files of code:
- `config/queue.php`: Queue configuration.
- `app/Jobs/`: Custom job classes.
- `.env.example`: Sets queue driver and Redis connection.
- Production setup: Supervisor config (see deployment docs).

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>

#

## Use resource routes and API standards
> Use resource() routes & API standards (api.php)



### What this topic is:
RESTful routing and standardized API responses using Laravel resource routes.

### Why we are using it:
- Promotes consistency and best practices in API design.
- Simplifies route definitions and controller logic.
- Ensures predictable endpoints for frontend and third-party clients.

### What it does in our project:
- Uses `Route::resource()` for CRUD endpoints.
- Applies API standards for response format, error handling, and status codes.
- Integrates with frontend via Inertia.js and React.

### Because of this, files of code:
- `routes/api.php`: Defines resource routes.
- `app/Http/Controllers/`: Resource controllers for models.
- `app/helpers.php`: Standardizes API responses.
- `phpunit.xml`, `tests/Feature/`: API tests for endpoints and standards.

<p align="right"><a href="#top"><img src="https://img.shields.io/badge/-Back%20to%20Top-blueviolet?style=for-the-badge" /></a></p>


<br>

<br>
