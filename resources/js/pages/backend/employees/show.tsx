import ProtectedSection from '@/components/protected-section';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Briefcase, Calendar, Edit, Mail, Phone, Shield, User, UserCheck, Users } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employees',
        href: '/admin/employees',
    },
    {
        title: 'Employee Details',
        href: '#',
    },
];

interface Employee {
    id: number;
    hashid: string;
    emp_id: string;
    personal_email: string;
    designation: string;
    created_at: string;
    updated_at: string;
    user: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
        mobile: string;
        status: string;
        roles: Array<{
            id: number;
            name: string;
        }>;
        permissions?: Array<{
            id: number;
            name: string;
        }>;
    };
    creator?: {
        id: number;
        first_name: string;
        last_name: string;
    } | null;
    updator?: {
        id: number;
        first_name: string;
        last_name: string;
    } | null;
}

interface Props {
    employee: Employee;
    [key: string]: unknown;
}

export default function Show() {
    const { employee } = usePage<Props>().props;

    const fullName = `${employee.user?.first_name} ${employee.user?.last_name}`;
    const statusColor =
        employee.user?.status === 'ACTIVE'
            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
            : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Employee: ${fullName}`} />

            {/* Header with Back Button */}
            <div className="m-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Employee Details</h1>
                        <p className="text-muted-foreground">View detailed information about {fullName}.</p>
                    </div>
                    <div className="flex gap-2">
                        <Link href={route('admin.employees.index')}>
                            <Button variant="outline">Back to Employees</Button>
                        </Link>
                        <ProtectedSection permission="employee-update" showDeniedMessage={false}>
                            <Link href={route('admin.employees.edit', employee.id)}>
                                <Button>
                                    <Edit className="mr-2 h-4 w-4" />
                                    Edit Employee
                                </Button>
                            </Link>
                        </ProtectedSection>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="mx-4 mb-8">
                <div className="mx-auto grid max-w-6xl gap-6 md:grid-cols-2">
                    {/* Personal Information Card */}
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <User className="h-5 w-5" />
                                Personal Information
                            </CardTitle>
                            <CardDescription>Basic personal details and contact information.</CardDescription>
                        </CardHeader>
                        <CardContent className="grid gap-6 md:grid-cols-2">
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</span>
                                <span className="text-lg font-semibold text-gray-900 dark:text-gray-100">{fullName}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</span>
                                <Badge className={statusColor}>{employee.user?.status}</Badge>
                            </div>

                            <div className="space-y-2">
                                <span className="block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <Mail className="h-4 w-4" />
                                    Official Email
                                </span>
                                <span className="text-lg font-medium text-gray-900 dark:text-gray-100">{employee.user?.email}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <Phone className="h-4 w-4" />
                                    Mobile Number
                                </span>
                                <span className="text-lg font-medium text-gray-900 dark:text-gray-100">{employee.user?.mobile}</span>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Employee Information Card */}
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Briefcase className="h-5 w-5" />
                                Employee Information
                            </CardTitle>
                            <CardDescription>Employee-specific details and professional information.</CardDescription>
                        </CardHeader>
                        <CardContent className="grid gap-6 md:grid-cols-2">
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Employee ID</span>
                                <span className="font-mono text-lg font-semibold text-gray-900 dark:text-gray-100">{employee.emp_id}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Designation</span>
                                <span className="text-lg font-medium text-gray-900 dark:text-gray-100">{employee.designation}</span>
                            </div>

                            {employee.personal_email && (
                                <div className="space-y-2">
                                    <span className="block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                        <Mail className="h-4 w-4" />
                                        Personal Email
                                    </span>
                                    <span className="text-lg font-medium text-gray-900 dark:text-gray-100">{employee.personal_email}</span>
                                </div>
                            )}

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Database ID</span>
                                <span className="font-mono text-sm text-gray-900 dark:text-gray-100">#{employee.id}</span>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Roles & Permissions Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Shield className="h-5 w-5" />
                                Roles & Permissions
                            </CardTitle>
                            <CardDescription>Assigned roles and permissions for this employee.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <span className="mb-2 block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <Users className="h-4 w-4" />
                                    Roles ({employee.user?.roles?.length || 0})
                                </span>
                                <div className="flex flex-wrap gap-2">
                                    {employee.user?.roles && employee.user.roles.length > 0 ? (
                                        employee.user.roles.map((role) => (
                                            <Badge key={role.id} variant="secondary" className="text-xs">
                                                {role.name}
                                            </Badge>
                                        ))
                                    ) : (
                                        <span className="text-sm text-gray-500 dark:text-gray-400">No roles assigned</span>
                                    )}
                                </div>
                            </div>

                            <div>
                                <span className="mb-2 block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <UserCheck className="h-4 w-4" />
                                    Permissions ({employee.user?.permissions?.length || 0})
                                </span>
                                <div className="flex max-h-32 flex-wrap gap-2 overflow-y-auto">
                                    {employee.user?.permissions && employee.user.permissions.length > 0 ? (
                                        employee.user.permissions.map((permission) => (
                                            <Badge key={permission.id} variant="outline" className="text-xs">
                                                {permission.name}
                                            </Badge>
                                        ))
                                    ) : (
                                        <span className="text-sm text-gray-500 dark:text-gray-400">No permissions assigned</span>
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
                            <CardDescription>Account creation and modification details.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Unique Hash ID</span>
                                <span className="font-mono text-sm text-gray-900 dark:text-gray-100">{employee.hashid}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Created At</span>
                                <span className="text-sm text-gray-900 dark:text-gray-100">
                                    {new Date(employee.created_at).toLocaleDateString()} at {new Date(employee.created_at).toLocaleTimeString()}
                                </span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</span>
                                <span className="text-sm text-gray-900 dark:text-gray-100">
                                    {new Date(employee.updated_at).toLocaleDateString()} at {new Date(employee.updated_at).toLocaleTimeString()}
                                </span>
                            </div>

                            {employee.creator && (
                                <div className="space-y-2">
                                    <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</span>
                                    <span className="text-sm text-gray-900 dark:text-gray-100">
                                        {employee.creator.first_name} {employee.creator.last_name}
                                    </span>
                                </div>
                            )}

                            {employee.updator && (
                                <div className="space-y-2">
                                    <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated By</span>
                                    <span className="text-sm text-gray-900 dark:text-gray-100">
                                        {employee.updator.first_name} {employee.updator.last_name}
                                    </span>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
