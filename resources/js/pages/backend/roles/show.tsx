import ProtectedSection from '@/components/protected-section';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Calendar, Edit, Shield, UserCheck } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/admin/roles',
    },
    {
        title: 'Role Details',
        href: '#',
    },
];

interface Role {
    id: number;
    name: string;
    guard_name?: string;
    created_at: string;
    updated_at: string;
    permissions?: Array<{
        id: number;
        name: string;
    }>;
    users_count?: number;
}

interface Props {
    role: Role;
    [key: string]: unknown;
}

export default function Show() {
    const { role } = usePage<Props>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Role: ${role.name}`} />

            {/* Header with Back Button */}
            <div className="m-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Role Details</h1>
                        <p className="text-muted-foreground">View detailed information about the {role.name} role.</p>
                    </div>
                    <div className="flex gap-2">
                        <Link href={route('admin.roles.index')}>
                            <Button variant="outline">Back to Roles</Button>
                        </Link>
                        <ProtectedSection permission="role-update" showDeniedMessage={false}>
                            <Link href={route('admin.roles.edit', role.id)}>
                                <Button>
                                    <Edit className="mr-2 h-4 w-4" />
                                    Edit Role
                                </Button>
                            </Link>
                        </ProtectedSection>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="mx-4 mb-8">
                <div className="mx-auto grid max-w-6xl gap-6 md:grid-cols-2">
                    {/* Role Information Card */}
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Shield className="h-5 w-5" />
                                Role Information
                            </CardTitle>
                            <CardDescription>Basic role details and configuration.</CardDescription>
                        </CardHeader>
                        <CardContent className="grid gap-6 md:grid-cols-3">
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Role Name</span>
                                <span className="text-lg font-semibold text-gray-900 dark:text-gray-100">{role.name}</span>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Permissions Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <UserCheck className="h-5 w-5" />
                                Permissions
                            </CardTitle>
                            <CardDescription>Permissions assigned to this role.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <span className="mb-2 block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <UserCheck className="h-4 w-4" />
                                    Assigned Permissions ({role.permissions?.length || 0})
                                </span>
                                <div className="flex max-h-48 flex-wrap gap-2 overflow-y-auto">
                                    {role.permissions && role.permissions.length > 0 ? (
                                        role.permissions.map((permission) => (
                                            <Badge key={permission.id} variant="outline" className="text-xs">
                                                {permission.name}
                                            </Badge>
                                        ))
                                    ) : (
                                        <div className="flex flex-col items-center justify-center py-8 text-center">
                                            <UserCheck className="mb-2 h-8 w-8 text-gray-400" />
                                            <span className="text-sm text-gray-500 dark:text-gray-400">No permissions assigned</span>
                                            <p className="mt-1 text-xs text-gray-400">
                                                Assign permissions to this role to grant specific access rights.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* System Information Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Calendar className="h-5 w-5" />
                                System Information
                            </CardTitle>
                            <CardDescription>Role creation and modification details.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Role ID</span>
                                <span className="font-mono text-sm text-gray-900 dark:text-gray-100">#{role.id}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Created At</span>
                                <span className="text-sm text-gray-900 dark:text-gray-100">
                                    {new Date(role.created_at).toLocaleDateString()} at {new Date(role.created_at).toLocaleTimeString()}
                                </span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</span>
                                <span className="text-sm text-gray-900 dark:text-gray-100">
                                    {new Date(role.updated_at).toLocaleDateString()} at {new Date(role.updated_at).toLocaleTimeString()}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
