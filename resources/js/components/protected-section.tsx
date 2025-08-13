import PermissionDenied from '@/components/permission-denied';
import { usePermissions } from '@/hooks/use-permissions';
import { type ReactNode } from 'react';

interface ProtectedSectionProps {
    permission?: string;
    permissions?: string[];
    role?: string;
    roles?: string[];
    requireAll?: boolean; // If true, requires ALL permissions/roles, otherwise ANY
    fallback?: ReactNode;
    children: ReactNode;
    showDeniedMessage?: boolean;
    deniedTitle?: string;
    deniedMessage?: string;
    deniedDescription?: string;
    className?: string;
}

export default function ProtectedSection({
    permission,
    permissions = [],
    role,
    roles = [],
    requireAll = false,
    fallback,
    children,
    showDeniedMessage = true,
    deniedTitle,
    deniedMessage,
    deniedDescription,
    className = '',
}: ProtectedSectionProps) {
    const { hasAnyPermission, hasAllPermissions, hasAnyRole } = usePermissions();

    // Build permissions array
    const allPermissions = [...(permission ? [permission] : []), ...permissions];

    // Build roles array
    const allRoles = [...(role ? [role] : []), ...roles];

    // Check permissions
    let hasPermissionAccess = true;
    if (allPermissions.length > 0) {
        hasPermissionAccess = requireAll ? hasAllPermissions(allPermissions) : hasAnyPermission(allPermissions);
    }

    // Check roles
    let hasRoleAccess = true;
    if (allRoles.length > 0) {
        hasRoleAccess = hasAnyRole(allRoles);
    }

    // User must have both permission and role access (if specified)
    const hasAccess = hasPermissionAccess && hasRoleAccess;

    if (!hasAccess) {
        if (fallback) {
            return <>{fallback}</>;
        }

        if (showDeniedMessage) {
            return (
                <div className={className}>
                    <PermissionDenied
                        title={deniedTitle || 'Section Access Denied'}
                        message={deniedMessage || "You don't have permission to view this section"}
                        description={deniedDescription || 'Please contact your administrator to request access to this feature.'}
                        showBackButton={false}
                        className="min-h-[40vh]"
                    />
                </div>
            );
        }

        return null;
    }

    return <>{children}</>;
}
