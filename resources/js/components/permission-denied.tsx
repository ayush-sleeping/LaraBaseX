import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/react';
import { ArrowLeft, Lock, Shield, UserX } from 'lucide-react';

interface PermissionDeniedProps {
    title?: string;
    message?: string;
    description?: string;
    showBackButton?: boolean;
    backUrl?: string;
    className?: string;
}

export default function PermissionDenied({
    title = 'Access Denied',
    message = "You don't have permission to access this resource",
    description = 'Please contact your administrator to request the necessary permissions for this feature.',
    showBackButton = true,
    backUrl = '/dashboard',
    className = '',
}: PermissionDeniedProps) {
    return (
        <div className={`flex min-h-[60vh] items-center justify-center p-4 ${className}`}>
            <Card className="w-full max-w-md border-red-200 bg-red-50/50 dark:border-red-800/50 dark:bg-red-900/10">
                <CardHeader className="text-center">
                    <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                        <Lock className="h-8 w-8 text-red-600 dark:text-red-400" />
                    </div>
                    <CardTitle className="flex items-center justify-center gap-2 text-xl text-red-800 dark:text-red-200">
                        <Shield className="h-5 w-5" />
                        {title}
                    </CardTitle>
                    <CardDescription className="text-red-700 dark:text-red-300">{message}</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4 text-center">
                    <div className="rounded-md bg-yellow-50 p-4 dark:bg-yellow-900/20">
                        <div className="flex items-start gap-3">
                            <UserX className="mt-0.5 h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                            <div className="text-sm text-yellow-800 dark:text-yellow-200">
                                <p className="font-medium">Permission Required</p>
                                <p className="mt-1">{description}</p>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-2 pt-2">
                        <p className="text-sm text-gray-600 dark:text-gray-400">Contact Information:</p>
                        <div className="rounded-md bg-gray-100 p-3 dark:bg-gray-800">
                            <p className="text-sm font-medium text-gray-900 dark:text-gray-100">System Administrator</p>
                            <p className="text-xs text-gray-600 dark:text-gray-400">Request access to this feature</p>
                        </div>
                    </div>

                    {showBackButton && (
                        <div className="pt-4">
                            <Link href={backUrl}>
                                <Button variant="outline" className="w-full">
                                    <ArrowLeft className="mr-2 h-4 w-4" />
                                    Go Back
                                </Button>
                            </Link>
                        </div>
                    )}
                </CardContent>
            </Card>
        </div>
    );
}
