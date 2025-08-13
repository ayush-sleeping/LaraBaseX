import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Check, Save, Shield, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { toast, Toaster } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/admin/roles',
    },
    {
        title: 'Manage Permissions',
        href: '#',
    },
];

interface Permission {
    id: number;
    name: string;
    guard_name: string;
    permissiongroup_id: number;
}

interface PermissionGroup {
    id: number;
    name: string;
    permissions: Permission[];
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

interface Props {
    role: Role;
    permissionGroups: PermissionGroup[];
    permissions: Permission[];
    rolePermissionIds: number[];
    [key: string]: unknown;
}

export default function Permissions() {
    const { role, permissionGroups, rolePermissionIds } = usePage<Props>().props;
    const flashFromGlobal = (usePage().props.flash as { success?: string; error?: string }) || {};

    const [selectedPermissions, setSelectedPermissions] = useState<number[]>(rolePermissionIds);
    const [processing, setProcessing] = useState(false);

    // Toast notification effect for flash messages
    useEffect(() => {
        if (flashFromGlobal.success) {
            toast.success(flashFromGlobal.success);
        }
        if (flashFromGlobal.error) {
            toast.error(flashFromGlobal.error);
        }
    }, [flashFromGlobal.success, flashFromGlobal.error]);

    // Function to get permission type from permission name
    const getPermissionType = (permissionName: string, groupName: string): string => {
        const groupPrefix = groupName.toLowerCase().replace(/\s/g, '') + '-';
        const permissionType = permissionName.replace(groupPrefix, '');

        if (permissionType === 'view') return 'view';
        if (permissionType === 'store') return 'create';
        if (permissionType === 'update') return 'update';
        return 'other';
    };

    // Function to organize permissions by type for each group
    const organizePermissions = (group: PermissionGroup) => {
        const organized = {
            view: null as Permission | null,
            create: null as Permission | null,
            update: null as Permission | null,
            other: [] as Permission[],
        };

        group.permissions.forEach((permission) => {
            const type = getPermissionType(permission.name, group.name);
            if (type === 'view') organized.view = permission;
            else if (type === 'create') organized.create = permission;
            else if (type === 'update') organized.update = permission;
            else organized.other.push(permission);
        });

        return organized;
    };

    // Toggle permission selection
    const togglePermission = (permissionId: number) => {
        setSelectedPermissions((prev) => {
            if (prev.includes(permissionId)) {
                return prev.filter((id) => id !== permissionId);
            } else {
                return [...prev, permissionId];
            }
        });
    };

    // Handle form submission
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);

        router.post(
            route('admin.roles.permissions.update', role.id),
            {
                permissions: selectedPermissions,
            },
            {
                onSuccess: () => {
                    setProcessing(false);
                    toast.success('Role permissions updated successfully');
                    // Redirect after a short delay
                    setTimeout(() => {
                        router.visit(route('admin.roles.index'));
                    }, 1000);
                },
                onError: (errors) => {
                    setProcessing(false);
                    if (errors.message) {
                        toast.error(errors.message);
                    } else {
                        toast.error('An error occurred while updating permissions');
                    }
                },
            },
        );
    };

    // Get stats for current selection
    const totalPermissions = permissionGroups.reduce((acc, group) => acc + group.permissions.length, 0);
    const selectedCount = selectedPermissions.length;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${role.name} - Permissions`} />

            {/* Toast Notifications */}
            <Toaster position="top-right" richColors closeButton duration={4000} />

            {/* Header Section */}
            <div className="m-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">{role.name} - Permissions</h1>
                        <p className="text-muted-foreground">
                            Manage permissions for the {role.name} role. Selected: {selectedCount} of {totalPermissions} permissions.
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Link href={route('admin.roles.index')}>
                            <Button variant="outline">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Roles
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="mx-4 mb-8">
                <form onSubmit={handleSubmit} className="mx-auto max-w-7xl">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Shield className="h-5 w-5" />
                                Permission Management
                            </CardTitle>
                            <CardDescription>
                                Select the permissions you want to assign to the {role.name} role. Permissions are organized by module and action
                                type.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="overflow-hidden rounded-md border">
                                <Table>
                                    <TableHeader>
                                        <TableRow className="bg-gray-50 dark:bg-gray-800/50">
                                            <TableHead className="w-[200px] font-semibold">Module</TableHead>
                                            <TableHead className="w-[100px] text-center font-semibold">View</TableHead>
                                            <TableHead className="w-[100px] text-center font-semibold">Create</TableHead>
                                            <TableHead className="w-[100px] text-center font-semibold">Update</TableHead>
                                            <TableHead className="text-center font-semibold">Other Permissions</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {permissionGroups.map((group) => {
                                            const organized = organizePermissions(group);
                                            return (
                                                <TableRow key={group.id} className="border-b">
                                                    <TableCell className="font-medium">
                                                        <div className="flex items-center gap-2">
                                                            <Shield className="h-4 w-4 text-gray-500" />
                                                            {group.name}
                                                        </div>
                                                    </TableCell>

                                                    {/* View Permission */}
                                                    <TableCell className="text-center">
                                                        {organized.view ? (
                                                            <div className="flex flex-col items-center gap-1">
                                                                <Checkbox
                                                                    checked={selectedPermissions.includes(organized.view.id)}
                                                                    onCheckedChange={() => togglePermission(organized.view!.id)}
                                                                    id={`permission-${organized.view.id}`}
                                                                />
                                                            </div>
                                                        ) : (
                                                            <div className="flex justify-center">
                                                                <X className="h-4 w-4 text-gray-400" />
                                                            </div>
                                                        )}
                                                    </TableCell>

                                                    {/* Create Permission */}
                                                    <TableCell className="text-center">
                                                        {organized.create ? (
                                                            <div className="flex flex-col items-center gap-1">
                                                                <Checkbox
                                                                    checked={selectedPermissions.includes(organized.create.id)}
                                                                    onCheckedChange={() => togglePermission(organized.create!.id)}
                                                                    id={`permission-${organized.create.id}`}
                                                                />
                                                            </div>
                                                        ) : (
                                                            <div className="flex justify-center">
                                                                <X className="h-4 w-4 text-gray-400" />
                                                            </div>
                                                        )}
                                                    </TableCell>

                                                    {/* Update Permission */}
                                                    <TableCell className="text-center">
                                                        {organized.update ? (
                                                            <div className="flex flex-col items-center gap-1">
                                                                <Checkbox
                                                                    checked={selectedPermissions.includes(organized.update.id)}
                                                                    onCheckedChange={() => togglePermission(organized.update!.id)}
                                                                    id={`permission-${organized.update.id}`}
                                                                />
                                                            </div>
                                                        ) : (
                                                            <div className="flex justify-center">
                                                                <X className="h-4 w-4 text-gray-400" />
                                                            </div>
                                                        )}
                                                    </TableCell>

                                                    {/* Other Permissions */}
                                                    <TableCell>
                                                        <div className="flex flex-wrap justify-center gap-3">
                                                            {organized.other.length > 0 ? (
                                                                organized.other.map((permission) => (
                                                                    <div key={permission.id} className="flex flex-col items-center gap-1">
                                                                        <Checkbox
                                                                            checked={selectedPermissions.includes(permission.id)}
                                                                            onCheckedChange={() => togglePermission(permission.id)}
                                                                            id={`permission-${permission.id}`}
                                                                        />
                                                                        <label
                                                                            htmlFor={`permission-${permission.id}`}
                                                                            className="cursor-pointer text-center text-xs text-gray-600 dark:text-gray-400"
                                                                        >
                                                                            {permission.name
                                                                                .replace(group.name.toLowerCase().replace(/\s/g, '') + '-', '')
                                                                                .replace(/[-_]/g, ' ')
                                                                                .split(' ')
                                                                                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                                                                                .join(' ')}
                                                                        </label>
                                                                    </div>
                                                                ))
                                                            ) : (
                                                                <div className="flex justify-center">
                                                                    <X className="h-4 w-4 text-gray-400" />
                                                                </div>
                                                            )}
                                                        </div>
                                                    </TableCell>
                                                </TableRow>
                                            );
                                        })}
                                    </TableBody>
                                </Table>
                            </div>

                            {/* Permission Summary */}
                            <div className="mt-6 flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50">
                                <div className="flex items-center gap-4">
                                    <div className="flex items-center gap-2">
                                        <Check className="h-5 w-5 text-green-600" />
                                        <span className="font-medium">Selected: {selectedCount}</span>
                                    </div>
                                    <div className="text-sm text-gray-600 dark:text-gray-400">Total Available: {totalPermissions}</div>
                                </div>
                                <div className="text-sm text-gray-600 dark:text-gray-400">
                                    Role: <span className="font-medium">{role.name}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Form Actions */}
                    <div className="mt-8 flex justify-end gap-4">
                        <Link href={route('admin.roles.index')}>
                            <Button variant="outline" type="button">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing} className="min-w-[120px]">
                            {processing ? (
                                <>
                                    <div className="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                    Saving...
                                </>
                            ) : (
                                <>
                                    <Save className="mr-2 h-4 w-4" />
                                    Save Permissions
                                </>
                            )}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
