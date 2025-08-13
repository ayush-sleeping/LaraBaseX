# 🧩 Frontend Integration Documentation (LaraBaseX)

This document describes how LaraBaseX integrates ReactJS with Laravel, focusing on real implementation details and features provided by the codebase.

#

## 5.1 Serve React with Vite

- LaraBaseX uses Vite as the build tool for the React frontend, configured in `vite.config.ts`.
- The Vite config includes `@vitejs/plugin-react` for React support and `laravel-vite-plugin` for seamless Laravel integration.
- Entry points are defined for both SPA (`resources/js/app.tsx`) and SSR (`resources/js/ssr.tsx`).
- Hot module replacement and fast refresh are enabled for rapid development.
- TailwindCSS is integrated via plugin for utility-first styling.
- The frontend is served via Vite in development and built for production using `npm run build`.

**Files:**
- `vite.config.ts`
- `resources/js/app.tsx`
- `resources/js/ssr.tsx`

**How to test:**
- Run `npm run dev` to start the Vite development server and access the React frontend at the configured URL.
- Run `npm run build` to build the production assets and verify output in `public/build`.
- Edit `app.tsx` or `ssr.tsx` and confirm hot reload works.

#

## 5.2 Proxy Setup

- API requests from React are routed to Laravel backend using Inertia.js, so no manual proxy setup is required for most cases.
- The Vite config does not use a custom proxy; instead, Inertia handles routing and API calls via Laravel routes.
- All route definitions are managed server-side and exposed to the frontend via Ziggy for type-safe route generation.

**Files:**
- `vite.config.ts`
- `resources/js/ziggy.js`
- `routes/web.php` (for backend route definitions)

**How to test:**
- Make an API request from a React page (e.g., login form) and verify it reaches the Laravel backend.
- Use Ziggy in a React component to generate a route and confirm navigation works.

#

## 5.3 React Router

- LaraBaseX uses Inertia.js for SPA navigation, replacing the need for `react-router-dom`.
- Page components are resolved dynamically using `resolvePageComponent` in `app.tsx` and `ssr.tsx`.
- Navigation, redirects, and route changes are handled by Inertia, providing seamless SPA experience with Laravel backend.
- Route names and parameters are type-safe, thanks to Ziggy integration.

**Files:**
- `resources/js/app.tsx`
- `resources/js/ssr.tsx`
- `resources/js/ziggy.js`
- `routes/web.php`

**How to test:**
- Click links or submit forms in the React app and observe SPA navigation (no full page reloads).
- Add a new page in `resources/js/pages/` and register its route in `web.php`, then navigate to it via Inertia.

#

## 5.4 Token Auth

- Authentication is handled via Laravel Sanctum, with token-based auth for API requests.
- Login, registration, and password reset flows are implemented in `resources/js/pages/auth/`.
- Tokens are managed automatically by Laravel and sent via cookies for SPA authentication.
- The frontend does not manually store tokens; it relies on secure HTTP-only cookies set by Sanctum.

**Files:**
- `resources/js/pages/auth/login.tsx`
- `resources/js/pages/auth/register.tsx`
- `resources/js/pages/auth/forgot-password.tsx`
- `routes/web.php` (auth routes)
- `config/sanctum.php`

**How to test:**
- Use the login/register forms and inspect cookies in the browser; confirm `XSRF-TOKEN` and `laravel_session` are set.
- Access a protected route and verify authentication is enforced.

#

## 5.5 Secure Token Storage

- Tokens are never stored in localStorage or sessionStorage, reducing XSS risk.
- Sanctum uses HTTP-only cookies for secure token storage, inaccessible to JavaScript.
- Appearance/theme preferences are stored in localStorage and cookies, but never sensitive auth data.
- All sensitive operations (login, logout, password reset) are performed via secure API endpoints.

**Files:**
- `resources/js/hooks/use-appearance.tsx` (for theme storage)
- `resources/js/pages/auth/` (for auth flows)
- `config/sanctum.php`

**How to test:**
- Log in and check browser storage; confirm no tokens are present in localStorage/sessionStorage.
- Change theme and verify preference is stored in localStorage/cookie.

#

## 5.6 Axios Interceptor

- API requests are made using Inertia.js, which wraps Axios internally.
- Error handling, redirects, and progress indicators are managed by Inertia, not custom Axios interceptors.
- If custom Axios usage is needed, it can be added in `resources/js/lib/` (currently not present).
- CSRF tokens are automatically included in requests via Laravel's middleware and meta tags.

**Files:**
- `resources/js/app.tsx` (Inertia setup)
- `resources/js/pages/auth/login.tsx` (API usage)
- `resources/js/lib/` (for custom Axios, if added)

**How to test:**
- Submit a form (e.g., login) and observe error handling and redirects.
- Add a custom API call in `resources/js/lib/` and test with Axios if needed.

#

## 5.7 Dotenv in React

- Environment variables are accessed using `import.meta.env` in Vite-powered React code.
- Example: `const appName = import.meta.env.VITE_APP_NAME || 'Laravel';` in `app.tsx` and `ssr.tsx`.
- All frontend environment variables are prefixed with `VITE_` and defined in `.env` or `.env.example`.
- Backend and frontend share environment configuration for consistent deployment.

**Files:**
- `.env`
- `.env.example`
- `resources/js/app.tsx`
- `resources/js/ssr.tsx`

**How to test:**
- Edit `.env` and set a value for `VITE_APP_NAME`, then restart the dev server and confirm the value is reflected in the app UI.
- Access `import.meta.env` in a React component and log the value to verify.

#

**LaraBaseX provides a tightly integrated, secure, and scalable React + Laravel experience, with all frontend features managed via modern best practices and real codebase implementations.**
