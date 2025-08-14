<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used throughout the application for
    | various messages that we need to display to the user. You are free to
    | modify these language lines according to your application's requirements.
    |
    */

    // Authentication Messages
    'auth' => [
        'login_successful' => 'Login successful! Welcome back.',
        'logout_successful' => 'You have been logged out successfully.',
        'registration_successful' => 'Registration successful! Please check your email for verification.',
        'verification_successful' => 'Email verified successfully.',
        'password_reset_sent' => 'Password reset link sent to your email.',
        'password_reset_successful' => 'Password reset successfully.',
        'invalid_credentials' => 'Invalid email or password.',
        'account_inactive' => 'Your account is inactive. Please contact administrator.',
        'email_not_verified' => 'Please verify your email address before continuing.',
        'too_many_attempts' => 'Too many login attempts. Please try again later.',
        'session_expired' => 'Your session has expired. Please login again.',
        'unauthorized' => 'You are not authorized to access this resource.',
    ],

    // API Messages
    'api' => [
        'success' => 'Operation completed successfully.',
        'created' => 'Resource created successfully.',
        'updated' => 'Resource updated successfully.',
        'deleted' => 'Resource deleted successfully.',
        'not_found' => 'Resource not found.',
        'validation_failed' => 'Validation failed. Please check your input.',
        'server_error' => 'Internal server error. Please try again later.',
        'unauthorized' => 'Unauthorized access.',
        'forbidden' => 'Access forbidden.',
        'method_not_allowed' => 'Method not allowed.',
        'rate_limit_exceeded' => 'Rate limit exceeded. Please try again later.',
        'otp_sent' => 'OTP sent successfully.',
        'otp_verified' => 'OTP verified successfully.',
        'otp_expired' => 'OTP has expired. Please request a new one.',
        'otp_invalid' => 'Invalid OTP. Please try again.',
        'token_expired' => 'Token has expired. Please login again.',
        'token_invalid' => 'Invalid token provided.',
    ],

    // User Management Messages
    'user' => [
        'created' => 'User created successfully.',
        'updated' => 'User updated successfully.',
        'deleted' => 'User deleted successfully.',
        'status_changed' => 'User status changed successfully.',
        'profile_updated' => 'Profile updated successfully.',
        'password_changed' => 'Password changed successfully.',
        'photo_uploaded' => 'Profile photo uploaded successfully.',
        'photo_removed' => 'Profile photo removed successfully.',
        'not_found' => 'User not found.',
        'email_exists' => 'Email address already exists.',
        'mobile_exists' => 'Mobile number already exists.',
        'cannot_delete_self' => 'You cannot delete your own account.',
        'cannot_change_own_status' => 'You cannot change your own status.',
        'insufficient_permissions' => 'You do not have sufficient permissions.',
    ],

    // Role & Permission Messages
    'role' => [
        'created' => 'Role created successfully.',
        'updated' => 'Role updated successfully.',
        'deleted' => 'Role deleted successfully.',
        'permissions_updated' => 'Role permissions updated successfully.',
        'not_found' => 'Role not found.',
        'name_exists' => 'Role name already exists.',
        'cannot_delete_assigned' => 'Cannot delete role that is assigned to users.',
        'cannot_modify_root' => 'Cannot modify root user role.',
    ],

    'permission' => [
        'granted' => 'Permission granted successfully.',
        'revoked' => 'Permission revoked successfully.',
        'not_found' => 'Permission not found.',
        'access_denied' => 'You do not have permission to access this resource.',
        'contact_admin' => 'Please contact your administrator to request access.',
    ],

    // Employee Management Messages
    'employee' => [
        'created' => 'Employee created successfully.',
        'updated' => 'Employee updated successfully.',
        'deleted' => 'Employee deleted successfully.',
        'status_changed' => 'Employee status changed successfully.',
        'not_found' => 'Employee not found.',
        'email_exists' => 'Employee with this email already exists.',
        'phone_exists' => 'Employee with this phone number already exists.',
        'employee_id_exists' => 'Employee ID already exists.',
    ],

    // Enquiry Management Messages
    'enquiry' => [
        'created' => 'Enquiry created successfully.',
        'updated' => 'Enquiry updated successfully.',
        'deleted' => 'Enquiry deleted successfully.',
        'remark_added' => 'Remark added successfully.',
        'status_changed' => 'Enquiry status changed successfully.',
        'not_found' => 'Enquiry not found.',
        'already_processed' => 'Enquiry has already been processed.',
    ],

    // File Upload Messages
    'file' => [
        'uploaded' => 'File uploaded successfully.',
        'deleted' => 'File deleted successfully.',
        'not_found' => 'File not found.',
        'invalid_type' => 'Invalid file type.',
        'too_large' => 'File size is too large.',
        'upload_failed' => 'File upload failed.',
        'max_files_exceeded' => 'Maximum number of files exceeded.',
    ],

    // Validation Messages
    'validation' => [
        'required' => 'This field is required.',
        'email' => 'Please enter a valid email address.',
        'min_length' => 'Must be at least :min characters.',
        'max_length' => 'Cannot exceed :max characters.',
        'numeric' => 'Must be a number.',
        'alpha' => 'Must contain only letters.',
        'alpha_numeric' => 'Must contain only letters and numbers.',
        'phone' => 'Please enter a valid phone number.',
        'date' => 'Please enter a valid date.',
        'url' => 'Please enter a valid URL.',
        'unique' => 'This value already exists.',
        'confirmed' => 'Confirmation does not match.',
        'password_strength' => 'Password must contain at least one uppercase letter, lowercase letter, number, and special character.',
    ],

    // System Messages
    'system' => [
        'maintenance' => 'System is under maintenance. Please try again later.',
        'backup_created' => 'System backup created successfully.',
        'backup_failed' => 'System backup failed.',
        'cache_cleared' => 'System cache cleared successfully.',
        'settings_updated' => 'System settings updated successfully.',
        'database_error' => 'Database connection error.',
        'service_unavailable' => 'Service temporarily unavailable.',
    ],

    // Dashboard Messages
    'dashboard' => [
        'welcome' => 'Welcome to your dashboard!',
        'no_data' => 'No data available.',
        'loading' => 'Loading...',
        'refresh' => 'Data refreshed successfully.',
        'export_success' => 'Data exported successfully.',
        'import_success' => 'Data imported successfully.',
        'import_failed' => 'Data import failed.',
    ],

    // Backup Messages
    'backup' => [
        'started' => 'Backup process started.',
        'completed' => 'Backup completed successfully.',
        'failed' => 'Backup failed. Please check system logs.',
        'file_created' => 'Backup file created: :filename',
        'no_backups' => 'No backups found.',
        'restored' => 'System restored from backup successfully.',
        'cleanup_completed' => 'backup files cleaned up.',
    ],

    // Activity Log Messages
    'activity' => [
        'logged' => 'Activity logged successfully.',
        'user_created' => ':user created a new user: :target',
        'user_updated' => ':user updated user: :target',
        'user_deleted' => ':user deleted user: :target',
        'login' => ':user logged in',
        'logout' => ':user logged out',
        'password_changed' => ':user changed their password',
        'profile_updated' => ':user updated their profile',
        'role_assigned' => ':user assigned role :role to :target',
        'permission_granted' => ':user granted permission :permission to :target',
    ],

    // General Messages
    'general' => [
        'success' => 'Operation completed successfully.',
        'error' => 'An error occurred. Please try again.',
        'warning' => 'Warning: Please review your action.',
        'info' => 'Information updated.',
        'confirm' => 'Are you sure you want to continue?',
        'cancel' => 'Operation cancelled.',
        'save' => 'Changes saved successfully.',
        'discard' => 'Changes discarded.',
        'retry' => 'Please try again.',
        'contact_support' => 'Please contact support if the problem persists.',
        'coming_soon' => 'This feature is coming soon.',
        'under_development' => 'This feature is under development.',
    ],

];
