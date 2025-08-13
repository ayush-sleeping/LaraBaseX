import PermissionDenied from '@/components/permission-denied';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Home, RefreshCw } from 'lucide-react';

interface ErrorProps {
    status: number;
    message?: string;
    [key: string]: unknown;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Error',
        href: '#',
    },
];

export default function Error({ status, message }: ErrorProps) {
    const title =
        {
            503: 'Service Unavailable',
            500: 'Server Error',
            404: 'Page Not Found',
            403: 'Access Denied',
        }[status] || 'Error';

    const description =
        {
            503: 'Sorry, we are doing some maintenance. Please check back soon.',
            500: 'Whoops, something went wrong on our servers.',
            404: 'Sorry, the page you are looking for could not be found.',
            403: 'Sorry, you are forbidden from accessing this page.',
        }[status] || 'An error occurred.';

    // Handle 403 Forbidden with our custom PermissionDenied component
    if (status === 403) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title="Access Denied" />
                <PermissionDenied
                    title="Access Denied"
                    message="You don't have permission to access this resource"
                    description="Please contact your administrator to request the necessary permissions for this feature."
                    showBackButton={true}
                    backUrl="/dashboard"
                />
            </AppLayout>
        );
    }

    // Handle other error types
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={title} />

            <div className="flex min-h-[60vh] items-center justify-center p-4">
                <Card className="w-full max-w-md">
                    <CardHeader className="text-center">
                        <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                            <span className="text-2xl font-bold text-gray-600 dark:text-gray-400">{status}</span>
                        </div>
                        <CardTitle className="text-xl">{title}</CardTitle>
                        <CardDescription>{message || description}</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex flex-col gap-2">
                            <Link href="/dashboard">
                                <Button className="w-full">
                                    <Home className="mr-2 h-4 w-4" />
                                    Go to Dashboard
                                </Button>
                            </Link>
                            <Button variant="outline" onClick={() => window.history.back()} className="w-full">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Go Back
                            </Button>
                            <Button variant="outline" onClick={() => window.location.reload()} className="w-full">
                                <RefreshCw className="mr-2 h-4 w-4" />
                                Refresh Page
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
