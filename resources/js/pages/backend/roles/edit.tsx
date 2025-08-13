import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { CircleAlert, Save, Shield } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/admin/roles',
    },
    {
        title: 'Edit Role',
        href: '/admin/roles/edit',
    },
];

interface Role {
    id: number;
    name: string;
    guard_name?: string;
}

interface EditPageProps {
    role: Role;
    [key: string]: unknown;
}

export default function Edit() {
    const { role } = usePage<EditPageProps>().props;
    const { data, setData, put, processing, errors } = useForm({
        name: role?.name || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (role?.id) {
            put(route('admin.roles.update', role.id));
        }
    };

    if (!role) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title="Edit Role" />
                <div className="flex min-h-[60vh] items-center justify-center">
                    <div className="text-center">
                        <h2 className="text-2xl font-bold text-gray-800 dark:text-gray-100">Role not found</h2>
                        <p className="mt-2 text-gray-600 dark:text-gray-400">The requested role could not be found.</p>
                        <Link href={route('admin.roles.index')} className="mt-4 inline-block">
                            <Button>Back to Roles</Button>
                        </Link>
                    </div>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Role" />

            {/* Header Section */}
            <div className="m-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">Edit Role</h1>
                            <p className="text-muted-foreground">Update the role information and settings.</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Error Messages */}
            {Object.keys(errors).length > 0 && (
                <div className="mx-4 mb-6">
                    <Alert variant="destructive" className="border-red-200 bg-red-50 dark:border-red-800/50 dark:bg-red-900/10">
                        <CircleAlert className="h-5 w-5" />
                        <div>
                            <AlertTitle>Validation Errors</AlertTitle>
                            <AlertDescription>
                                <ul className="mt-2 list-inside list-disc space-y-1">
                                    {Object.entries(errors).map(([field, message]) => (
                                        <li key={field}>
                                            <span className="font-medium capitalize">{field.replace('_', ' ')}</span>: {message}
                                        </li>
                                    ))}
                                </ul>
                            </AlertDescription>
                        </div>
                    </Alert>
                </div>
            )}

            {/* Main Form */}
            <div className="mx-4 mb-8">
                <form onSubmit={handleSubmit} className="mx-auto max-w-6xl">
                    <div className="grid gap-6">
                        {/* Role Information Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Shield className="h-5 w-5" />
                                    Role Information
                                </CardTitle>
                                <CardDescription>Update the role details. The role name should be unique and descriptive.</CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-6">
                                {/* Role Name */}
                                <div className="space-y-2">
                                    <Label htmlFor="name" className="flex items-center gap-2 font-medium">
                                        Role Name
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="name"
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="Enter role name (e.g., Admin, Editor, Manager)"
                                        className="transition-all"
                                        required
                                    />
                                    <p className="text-sm text-gray-600 dark:text-gray-400">
                                        Choose a clear, descriptive name that represents the role's purpose and responsibilities.
                                    </p>
                                    {errors.name && <p className="text-sm text-red-600 dark:text-red-400">{errors.name}</p>}
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Form Actions */}
                    <div className="mt-8 flex justify-end gap-4">
                        <Link href={route('admin.roles.index')}>
                            <Button variant="outline" type="button">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing} className="min-w-[120px]">
                            {processing ? (
                                <>
                                    <div className="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                    Updating...
                                </>
                            ) : (
                                <>
                                    <Save className="mr-2 h-4 w-4" />
                                    Update Role
                                </>
                            )}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
