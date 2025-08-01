<?php
/**
 * LaraBaseX Project Helpers
 *
 * This file contains the following helper functions:
 *
 * 1. str_ends_with            - Check if a string ends with a given substring.
 * 2. str_singular             - Get the singular form of a string.
 * 3. str_snake                - Convert a string to snake_case.
 * 4. str_plural               - Get the plural form of a string.
 * 5. to_indian_datetime       - Format a datetime string to Indian date/time format.
 * 6. get_system_roles         - Get system roles from PermissionSeeder.
 * 7. get_counting_number      - Generate a unique counting number for a model.
 * 8. is_desktop               - Detect if the device is desktop (requires Mobile_Detect).
 * 9. is_mobile                - Detect if the device is mobile (requires Mobile_Detect).
 * 10. send_sms                - Send an SMS using Fast2SMS API (requires env key).
 * 11. get_discounted_percentage - Calculate the discount percentage.
 * 12. in_rupee                - Format a number as Indian Rupees.
 * 13. get_domain_url          - Get the application domain URL from env.
 * 14. json_response           - Return a JSON response for APIs.
 * 15. get_env                 - Safe access to .env values.
 * 16. uuid                    - Generate a UUID string.
 * 17. is_api_request          - Detect if the current request is for an API route.
 * 18. carbon                  - Easy Carbon date object wrapper.
 * 19. asset_timestamped       - Append filemtime to asset for cache busting.
 * 20. dd_log                  - Dump data to log for debugging.
 * 21. format_mobile           - Standardize mobile numbers (digits only).
 * 22. random_otp              - Generate a random OTP of given digits.
 * 23. api_success             - Standard API success response.
 * 24. api_error               - Standard API error response.
 * 25. client_ip               - Get the real client IP address.
 * 26. is_local_env            - Detect if running in local environment.
 * 27. current_user            - Get the authenticated user (API guard).
 * 28. file_size_readable      - Convert bytes to human-readable file size.
 * 29. sanitize_string         - Remove HTML tags and unwanted chars from a string.
 *
 * Add or update helpers as needed for your project.
 */

use Illuminate\Support\Str;
use Carbon\Carbon;

// 🔠 String Helpers
if (!function_exists('str_ends_with')) {
    /**
     * Check if a string ends with a given substring.
     */
    function str_ends_with(string $haystack, string|array $needles): bool
    {
        return Str::endsWith($haystack, $needles);
    }
}


if (!function_exists('str_singular')) {
    /**
     * Get the singular form of a string.
     */
    function str_singular(string $value): string
    {
        return Str::singular($value);
    }
}


if (!function_exists('str_snake')) {
    /**
     * Convert a string to snake_case.
     */
    function str_snake(string $value): string
    {
        return Str::snake($value);
    }
}


if (!function_exists('str_plural')) {
    /**
     * Get the plural form of a string.
     */
    function str_plural(string $value): string
    {
        return Str::plural($value);
    }
}


// 📅 Date/Time Helpers
if (!function_exists('to_indian_datetime')) {
    /**
     * Format a datetime string to Indian date/time format.
     */
    function to_indian_datetime(string $datetime): string
    {
        return Carbon::parse($datetime)->format('d-m-Y h:i A');
    }
}


// ⚙️ App/Utility Helpers
if (!function_exists('get_system_roles')) {
    /**
     * Get system roles from PermissionSeeder.
     *
     * @return array<int, string>
     */
    function get_system_roles(): array
    {
        $permissionSeeder = new \Database\Seeders\PermissionSeeder;
        $roles = $permissionSeeder->roles ?? [];
        return array_keys($roles);
    }
}


// ⚙️ App/Utility Helpers (continued)
if (!function_exists('get_counting_number')) {
    /**
     * Generate a unique counting number for a model.
     */
    function get_counting_number(string $model, string $prefix, string $fieldName, bool $year = true): string
    {
        $modelClass = "\\App\\Models\\{$model}";
        $latestNumber = $modelClass::max($fieldName);
        $lastNumberPart = 1;
        if ($latestNumber) {
            $parts = explode('-', $latestNumber);
            $lastNumberPart = (int) end($parts) + 1;
        }
        $currentYear = date('Y');
        $currentMonth = date('n');
        $fyStart = ($currentMonth >= 4) ? substr($currentYear, -2) : substr(($currentYear - 1), -2);
        $fyEnd = ($currentMonth >= 4) ? substr(($currentYear + 1), -2) : substr($currentYear, -2);
        $number = $prefix . '-' . $fyStart . '-' . $fyEnd . '-' . str_pad($lastNumberPart, 4, '0', STR_PAD_LEFT);
        if (!$year) {
            $number = $prefix . '-' . str_pad($lastNumberPart, 4, '0', STR_PAD_LEFT);
        }
        return $number;
    }
}

// 📱 Device Helpers (requires Mobile_Detect package)
if (!function_exists('is_desktop')) {
    /**
     * Detect if the device is desktop.
     */
    function is_desktop(): bool
    {
        if (!class_exists('Mobile_Detect')) return true;
        $detect = new \Mobile_Detect;
        return !$detect->isMobile();
    }
}


if (!function_exists('is_mobile')) {
    /**
     * Detect if the device is mobile.
     */
    function is_mobile(): bool
    {
        if (!class_exists('Mobile_Detect')) return false;
        $detect = new \Mobile_Detect;
        return $detect->isMobile();
    }
}


// 🌐 API Helpers (SMS, API responses, etc.)
if (!function_exists('send_sms')) {
    /**
     * Send an SMS using Fast2SMS API.
     */
    function send_sms($numbers, string $message): bool
    {
        $apiKey = env('FAST2SMS_API_KEY');
        if (!$apiKey) return false;
        $fields = [
            "variables_values" => $message,
            "route" => "otp",
            "numbers" => $numbers,
        ];
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => [
                    "authorization: {$apiKey}",
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ],
            ]);
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            return !$err;
        } catch (\Exception $e) {
            return false;
        }
    }
}


// 💸 Finance Helpers
if (!function_exists('get_discounted_percentage')) {
    /**
     * Calculate the discount percentage.
     */
    function get_discounted_percentage(float $originalPrice, float $discountedPrice): string
    {
        if ($originalPrice == 0) {
            return '0%';
        }
        $percentage = (($originalPrice - $discountedPrice) / $originalPrice) * 100;
        return round($percentage, 0) . '%';
    }
}


// 💸 Finance Helpers (continued)
if (!function_exists('in_rupee')) {
    /**
     * Format a number as Indian Rupees.
     */
    function in_rupee($num, bool $symbol = true, bool $pdf = false): string
    {
        $nums = explode('.', $num);
        $num = $nums[0];
        $minus = false;
        if (substr($num, 0, 1) === '-') {
            $minus = true;
            $num = substr($num, 1);
        }
        $explrestunits = "";
        if (strlen($num) > 3) {
            $lastthree = substr($num, -3);
            $restunits = substr($num, 0, -3);
            $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
            $expunit = str_split($restunits, 2);
            foreach ($expunit as $i => $unit) {
                $explrestunits .= ($i == 0 ? (int)$unit : $unit) . ",";
            }
            $thecash = $explrestunits . $lastthree;
        } else {
            $thecash = $num;
        }
        if ($minus) {
            $thecash = "-" . $thecash;
        }
        if (isset($nums[1]) && $nums[1] > 0) {
            $thecash .= "." . $nums[1];
        }
        if ($symbol) {
            return '₹ ' . $thecash . '/-';
        } elseif ($pdf) {
            return $thecash . '/-';
        } else {
            return html_entity_decode('₹ ' . $thecash . '/-');
        }
    }
}


// ⚙️ App/Utility Helpers (continued)
// if (!function_exists('get_domain_url')) {
//     /**
//      * Get the application domain URL from env.
//      */
//     function get_domain_url(): string
//     {
//         return env(key: 'APP_URL', 'https://example.com');
//     }
// }

// 🌐 API Helpers (continued) & Miscellaneous

if (!function_exists('json_response')) {
    /**
     * Return a JSON response for APIs.
     */
    function json_response($data = [], int $status = 200) {
        return response()->json($data, $status);
    }
}

if (!function_exists('get_env')) {
    /**
     * Safe access to .env values.
     */
    function get_env($key, $default = null) {
        return env($key, $default);
    }
}

if (!function_exists('uuid')) {
    /**
     * Generate a UUID string.
     */
    function uuid(): string {
        return \Illuminate\Support\Str::uuid()->toString();
    }
}

if (!function_exists('is_api_request')) {
    /**
     * Detect if the current request is for an API route.
     */
    function is_api_request(): bool {
        return request()->is('api/*');
    }
}

if (!function_exists('carbon')) {
    /**
     * Easy Carbon date object wrapper.
     */
    function carbon($date = null): \Carbon\Carbon {
        return \Carbon\Carbon::parse($date);
    }
}

if (!function_exists('asset_timestamped')) {
    /**
     * Append filemtime to asset for cache busting.
     */
    function asset_timestamped($path): string {
        $fullPath = public_path($path);
        return asset($path) . '?v=' . (file_exists($fullPath) ? filemtime($fullPath) : time());
    }
}

if (!function_exists('dd_log')) {
    /**
     * Dump data to log for debugging.
     */
    function dd_log($data): void {
        \Log::debug(print_r($data, true));
    }
}

if (!function_exists('format_mobile')) {
    /**
     * Standardize mobile numbers (digits only).
     */
    function format_mobile($mobile): string {
        return preg_replace('/[^0-9]/', '', $mobile);
    }
}

if (!function_exists('random_otp')) {
    /**
     * Generate a random OTP of given digits.
     */
    function random_otp(int $digits = 6): int {
        return rand(pow(10, $digits-1), pow(10, $digits)-1);
    }
}

if (!function_exists('api_success')) {
    /**
     * Standard API success response.
     */
    function api_success($data = [], string $message = 'Success') {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
}

if (!function_exists('api_error')) {
    /**
     * Standard API error response.
     */
    function api_error(string $message = 'Something went wrong', int $status = 400) {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $status);
    }
}

if (!function_exists('client_ip')) {
    /**
     * Get the real client IP address.
     */
    function client_ip(): ?string {
        return request()->getClientIp();
    }
}

if (!function_exists('is_local_env')) {
    /**
     * Detect if running in local environment.
     */
    function is_local_env(): bool {
        return app()->environment('local');
    }
}

if (!function_exists('current_user')) {
    /**
     * Get the authenticated user (API guard).
     */
    function current_user() {
        return auth()->user();
    }
}

if (!function_exists('file_size_readable')) {
    /**
     * Convert bytes to human-readable file size.
     */
    function file_size_readable($bytes, int $decimals = 2): string {
        $size = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen((string)$bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $size[$factor];
    }
}

if (!function_exists('sanitize_string')) {
    /**
     * Remove HTML tags and unwanted chars from a string.
     */
    function sanitize_string($string): string {
        return htmlspecialchars(strip_tags(trim($string)));
    }
}
