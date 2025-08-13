import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { CircleAlert, Eye, EyeOff, Lock, Mail, Phone, Save, Shield, User } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/admin/users',
    },
    {
        title: 'Edit User',
        href: '#',
    },
];

interface EditProps {
    user: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
        mobile: string;
        status: string;
        roles: Array<{ id: number; name: string }>;
    };
    roles: Array<{
        id: number;
        name: string;
        guard_name: string;
    }>;
    [key: string]: unknown;
}

export default function Edit() {
    const { user, roles } = usePage<EditProps>().props;
    const { data, setData, put, processing, errors } = useForm({
        first_name: user.first_name,
        last_name: user.last_name,
        email: user.email,
        mobile: user.mobile,
        password: '',
        password_confirmation: '',
        status: user.status,
        roles: user.roles.map((role) => role.id),
    });

    const [showPassword, setShowPassword] = useState(false);
    const [showPasswordConfirmation, setShowPasswordConfirmation] = useState(false);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('admin.users.update', user.id), {
            onSuccess: () => {
                setData((prev) => ({
                    ...prev,
                    password: '',
                    password_confirmation: '',
                }));
            },
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit User - ${user.first_name} ${user.last_name}`} />

            {/* Header Section */}
            <div className="m-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <div>
                            <h1 className="text-2xl font-bold tracking-tight">Edit User</h1>
                            <p className="text-muted-foreground">
                                Update {user.first_name} {user.last_name}'s details and permissions.
                            </p>
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
                    <div className="grid gap-6 md:grid-cols-2">
                        {/* Personal Information Card */}
                        <Card className="md:col-span-2">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <User className="h-5 w-5" />
                                    Personal Information
                                </CardTitle>
                                <CardDescription>Update the user's basic personal details and contact information.</CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-6 md:grid-cols-2">
                                {/* First Name */}
                                <div className="space-y-2">
                                    <Label htmlFor="first_name" className="flex items-center gap-2 font-medium">
                                        First Name
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="first_name"
                                        type="text"
                                        value={data.first_name}
                                        onChange={(e) => setData('first_name', e.target.value)}
                                        placeholder="Enter first name"
                                        className="transition-all"
                                        required
                                    />
                                    {errors.first_name && <p className="text-sm text-red-600 dark:text-red-400">{errors.first_name}</p>}
                                </div>

                                {/* Last Name */}
                                <div className="space-y-2">
                                    <Label htmlFor="last_name" className="flex items-center gap-2 font-medium">
                                        Last Name
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="last_name"
                                        type="text"
                                        value={data.last_name}
                                        onChange={(e) => setData('last_name', e.target.value)}
                                        placeholder="Enter last name"
                                        className="transition-all"
                                        required
                                    />
                                    {errors.last_name && <p className="text-sm text-red-600 dark:text-red-400">{errors.last_name}</p>}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Contact Information Card */}
                        <Card className="md:col-span-2">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Mail className="h-5 w-5" />
                                    Contact Information
                                </CardTitle>
                                <CardDescription>Update the user's email address and mobile number for communication.</CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-6 md:grid-cols-2">
                                {/* Email */}
                                <div className="space-y-2">
                                    <Label htmlFor="email" className="flex items-center gap-2 font-medium">
                                        <Mail className="h-4 w-4" />
                                        Email Address
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="user@example.com"
                                        className="transition-all"
                                        required
                                    />
                                    {errors.email && <p className="text-sm text-red-600 dark:text-red-400">{errors.email}</p>}
                                </div>

                                {/* Mobile */}
                                <div className="space-y-2">
                                    <Label htmlFor="mobile" className="flex items-center gap-2 font-medium">
                                        <Phone className="h-4 w-4" />
                                        Mobile Number
                                        <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="mobile"
                                        type="tel"
                                        value={data.mobile}
                                        onChange={(e) => setData('mobile', e.target.value)}
                                        placeholder="1234567890"
                                        className="transition-all"
                                        required
                                    />
                                    {errors.mobile && <p className="text-sm text-red-600 dark:text-red-400">{errors.mobile}</p>}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Security Information Card */}
                        <Card className="md:col-span-2">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Lock className="h-5 w-5" />
                                    Security Information
                                </CardTitle>
                                <CardDescription>
                                    Update the user's password and account status. Leave password fields empty to keep current password.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="grid gap-6 md:grid-cols-2">
                                {/* Password */}
                                <div className="space-y-2">
                                    <Label htmlFor="password" className="flex items-center gap-2 font-medium">
                                        <Lock className="h-4 w-4" />
                                        New Password
                                        <span className="text-sm text-gray-500">(optional)</span>
                                    </Label>
                                    <div className="relative">
                                        <Input
                                            id="password"
                                            type={showPassword ? 'text' : 'password'}
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            placeholder="Enter new password (leave empty to keep current)"
                                            className="pr-10 transition-all"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPassword(!showPassword)}
                                            className="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                                        >
                                            {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                        </button>
                                    </div>
                                    {errors.password && <p className="text-sm text-red-600 dark:text-red-400">{errors.password}</p>}
                                </div>

                                {/* Password Confirmation */}
                                <div className="space-y-2">
                                    <Label htmlFor="password_confirmation" className="flex items-center gap-2 font-medium">
                                        <Lock className="h-4 w-4" />
                                        Confirm New Password
                                        <span className="text-sm text-gray-500">(optional)</span>
                                    </Label>
                                    <div className="relative">
                                        <Input
                                            id="password_confirmation"
                                            type={showPasswordConfirmation ? 'text' : 'password'}
                                            value={data.password_confirmation}
                                            onChange={(e) => setData('password_confirmation', e.target.value)}
                                            placeholder="Confirm new password"
                                            className="pr-10 transition-all"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPasswordConfirmation(!showPasswordConfirmation)}
                                            className="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                                        >
                                            {showPasswordConfirmation ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                        </button>
                                    </div>
                                    {errors.password_confirmation && (
                                        <p className="text-sm text-red-600 dark:text-red-400">{errors.password_confirmation}</p>
                                    )}
                                </div>

                                {/* Roles */}
                                <div className="space-y-2 md:col-span-2">
                                    <Label htmlFor="roles" className="flex items-center gap-2 font-medium">
                                        <Shield className="h-4 w-4" />
                                        Roles
                                    </Label>
                                    <div className="space-y-3">
                                        <p className="text-sm text-gray-600 dark:text-gray-400">Select one or more roles to assign to this user</p>
                                        <div className="grid gap-3 md:grid-cols-2">
                                            {roles.map((role) => (
                                                <div
                                                    key={role.id}
                                                    className="flex items-center space-x-3 rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                                >
                                                    <Checkbox
                                                        id={`role-${role.id}`}
                                                        checked={data.roles.includes(role.id)}
                                                        onCheckedChange={(checked) => {
                                                            if (checked) {
                                                                setData('roles', [...data.roles, role.id]);
                                                            } else {
                                                                setData(
                                                                    'roles',
                                                                    data.roles.filter((id) => id !== role.id),
                                                                );
                                                            }
                                                        }}
                                                    />
                                                    <div className="grid gap-1.5 leading-none">
                                                        <Label
                                                            htmlFor={`role-${role.id}`}
                                                            className="cursor-pointer text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                        >
                                                            {role.name}
                                                        </Label>
                                                        <p className="text-xs text-gray-600 dark:text-gray-400">Guard: {role.guard_name}</p>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                        {roles.length === 0 && (
                                            <div className="py-4 text-center text-gray-500 dark:text-gray-400">No roles available</div>
                                        )}
                                    </div>
                                    {errors.roles && <p className="text-sm text-red-600 dark:text-red-400">{errors.roles}</p>}
                                </div>

                                {/* Status */}
                                <div className="space-y-2 md:col-span-2">
                                    <Label htmlFor="status" className="font-medium">
                                        Account Status
                                    </Label>
                                    <Select value={data.status} onValueChange={(value) => setData('status', value)}>
                                        <SelectTrigger className="transition-all">
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="ACTIVE">
                                                <div className="flex items-center gap-2">
                                                    <div className="h-2 w-2 rounded-full bg-gray-600"></div>
                                                    Active
                                                </div>
                                            </SelectItem>
                                            <SelectItem value="INACTIVE">
                                                <div className="flex items-center gap-2">
                                                    <div className="h-2 w-2 rounded-full bg-gray-400"></div>
                                                    Inactive
                                                </div>
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.status && <p className="text-sm text-red-600 dark:text-red-400">{errors.status}</p>}
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Form Actions */}
                    <div className="mt-8 flex justify-end gap-4">
                        <Link href={route('admin.users.index')}>
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
                                    Update User
                                </>
                            )}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
