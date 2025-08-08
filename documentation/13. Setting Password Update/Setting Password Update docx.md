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
