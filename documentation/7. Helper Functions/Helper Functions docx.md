# 🛠️ Helper Functions Documentation (LaraBaseX)

This section documents the key helper functions available in LaraBaseX, based strictly on the actual codebase.

#

## PHP Helper Functions (`app/helpers.php`)

LaraBaseX provides a rich set of global PHP helper functions for string manipulation, date formatting, device detection, API utilities, file management, and more.

**Examples of available helpers:**

- `str_ends_with($haystack, $needles)`: Checks if a string ends with a given substring.
- `str_singular($value)`, `str_plural($value)`, `str_snake($value)`: String transformations.
- `to_indian_datetime($datetime)`: Formats a datetime string to Indian date/time format.
- `get_system_roles()`: Retrieves system roles from the PermissionSeeder.
- `get_counting_number($model, $prefix, $fieldName, $year)`: Generates a unique counting number for a model.
- `is_desktop()`, `is_mobile()`: Device detection (requires Mobile_Detect package).
- `send_sms($numbers, $message)`: Sends SMS using Fast2SMS API (requires API key in `.env`).
- `get_discounted_percentage($original, $discounted)`: Calculates discount percentage.
- `in_rupee($number)`: Formats a number as Indian Rupees.
- `get_domain_url()`: Gets the application domain URL from `.env`.
- `json_response($data, $status)`: Returns a JSON response for APIs.
- `get_env($key)`: Safe access to `.env` values.
- `uuid()`: Generates a UUID string.
- `is_api_request()`: Detects if the current request is for an API route.
- `carbon($date)`: Easy Carbon date object wrapper.
- `asset_timestamped($path)`: Appends filemtime to asset for cache busting.
- `dd_log($data)`: Dumps data to log for debugging.
- `format_mobile($number)`: Standardizes mobile numbers.
- `get_random_code()`: Generates random codes for OTP/referral.
- `generate_slug($title)`: Creates a slug from a title.
- `upload_file($file, $path)`, `remove_file($path)`, `get_file_url($path)`: Universal file upload/delete/retrieve.
- `human_readable_time($datetime)`: Formats time as "time ago".
- `log_activity($action, $userId)`: Logs user actions.

**Where implemented:**
- All functions are in `app/helpers.php` and autoloaded by Laravel.

**How to use/test:**
- Call any helper in your PHP code (controllers, models, views).
- Example: `str_snake('UserName') // returns 'user_name'`
- Test by writing unit tests or using Tinker/Artisan commands.

#

## React/TypeScript Helper Functions

### Utility Functions (`resources/js/lib/utils.ts`)

- `cn(...inputs)`: Combines and merges Tailwind CSS class names using `clsx` and `tailwind-merge`.
  - **Usage:** `cn('btn', isActive && 'btn-active')`
  - **Where:** `resources/js/lib/utils.ts`
  - **Test:** Use in any React component to conditionally merge classes.

### Custom Hooks (`resources/js/hooks/`)

- `useInitials()`: Returns initials from a full name string.
  - **Usage:** `const getInitials = useInitials(); getInitials('John Doe') // 'JD'`
  - **Where:** `resources/js/hooks/use-initials.tsx`
  - **Test:** Use in avatar/profile components.

- `useAppearance()`: Manages theme (light/dark/system) and persists preference in localStorage/cookie.
  - **Usage:** `const { appearance, setAppearance } = useAppearance();`
  - **Where:** `resources/js/hooks/use-appearance.tsx`
  - **Test:** Change theme in UI and verify persistence.

- `usePermissions()`: Checks user permissions and roles for UI access control.
  - **Usage:** `const { hasPermission, hasRole } = usePermissions();`
  - **Where:** `resources/js/hooks/use-permissions.ts`
  - **Test:** Use in protected components/pages to show/hide features based on user roles/permissions.

#

> All helper functions above are strictly based on the LaraBaseX codebase. For more details, see the referenced files and try them in your project code.
