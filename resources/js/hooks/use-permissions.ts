import { usePage } from '@inertiajs/react';

interface AuthUser {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    permissions: Array<{ id: number; name: string; guard_name: string }>;
    roles: Array<{ id: number; name: string; guard_name: string }>;
}

interface PageProps {
    auth: {
        user: AuthUser | null;
    };
    [key: string]: unknown;
}

export function usePermissions() {
    const { auth } = usePage<PageProps>().props;

    const hasPermission = (permission: string): boolean => {
        const user = auth.user;
        if (!user) return false;

        // Check if user has RootUser role (has all permissions)
        if (user.roles?.some(role => role.name === 'RootUser')) {
            return true;
        }

        // Check if user has the specific permission
        return user.permissions?.some(p => p.name === permission) || false;
    };

    const hasRole = (role: string): boolean => {
        const user = auth.user;
        if (!user) return false;

        return user.roles?.some(r => r.name === role) || false;
    };

    const hasAnyPermission = (permissions: string[]): boolean => {
        return permissions.some(permission => hasPermission(permission));
    };

    const hasAllPermissions = (permissions: string[]): boolean => {
        return permissions.every(permission => hasPermission(permission));
    };

    const hasAnyRole = (roles: string[]): boolean => {
        return roles.some(role => hasRole(role));
    };

    const isRootUser = (): boolean => {
        return hasRole('RootUser');
    };

    return {
        user: auth.user,
        hasPermission,
        hasRole,
        hasAnyPermission,
        hasAllPermissions,
        hasAnyRole,
        isRootUser,
    };
}
