import RemarkModal from '@/components/remark-modal';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Calendar, CheckCircle, Edit, FileText, Mail, MessageCircle, Phone, User, UserCheck, XCircle } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Enquiries',
        href: '/admin/enquiries',
    },
    {
        title: 'Enquiry Details',
        href: '#',
    },
];

interface Enquiry {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    mobile: string;
    message: string;
    remark: string | null;
    created_at: string;
    updated_at: string;
    createdBy?: {
        id: number;
        first_name: string;
        last_name: string;
    } | null;
    updatedBy?: {
        id: number;
        first_name: string;
        last_name: string;
    } | null;
}

interface Props {
    enquiry: Enquiry;
    flash?: {
        success?: string;
        error?: string;
    };
    [key: string]: unknown;
}

export default function Show() {
    const { enquiry, flash } = usePage<Props>().props;
    const [isRemarkModalOpen, setIsRemarkModalOpen] = useState(false);
    const [currentRemark, setCurrentRemark] = useState(enquiry.remark);

    const fullName = `${enquiry.first_name} ${enquiry.last_name}`;
    const hasRemark = currentRemark && currentRemark.trim().length > 0;

    const handleRemarkSuccess = (newRemark: string) => {
        setCurrentRemark(newRemark);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Enquiry: ${fullName}`} />

            {/* Header with Back Button */}
            <div className="m-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Enquiry Details</h1>
                        <p className="text-muted-foreground">View detailed information about the enquiry from {fullName}.</p>
                    </div>
                    <div className="flex gap-2">
                        <Link href={route('admin.enquiries.index')}>
                            <Button variant="outline">Back to Enquiries</Button>
                        </Link>
                    </div>
                </div>
            </div>

            {/* Flash Messages */}
            {flash?.success && (
                <div className="mx-4 mb-6">
                    <Alert className="border-green-200 bg-green-50 dark:border-green-800/50 dark:bg-green-900/10">
                        <CheckCircle className="h-4 w-4 text-green-600" />
                        <AlertDescription className="text-green-800 dark:text-green-400">{flash.success}</AlertDescription>
                    </Alert>
                </div>
            )}

            {flash?.error && (
                <div className="mx-4 mb-6">
                    <Alert variant="destructive" className="border-red-200 bg-red-50 dark:border-red-800/50 dark:bg-red-900/10">
                        <XCircle className="h-4 w-4" />
                        <AlertDescription>{flash.error}</AlertDescription>
                    </Alert>
                </div>
            )}

            {/* Main Content */}
            <div className="mx-4 mb-8">
                <div className="mx-auto grid max-w-6xl gap-6 md:grid-cols-2">
                    {/* Contact Information Card */}
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <User className="h-5 w-5" />
                                Contact Information
                            </CardTitle>
                            <CardDescription>Basic contact details and information.</CardDescription>
                        </CardHeader>
                        <CardContent className="grid gap-6 md:grid-cols-2">
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</span>
                                <span className="text-lg font-semibold text-gray-900 dark:text-gray-100">{fullName}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Enquiry ID</span>
                                <span className="font-mono text-sm text-gray-900 dark:text-gray-100">#{enquiry.id}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <Mail className="h-4 w-4" />
                                    Email Address
                                </span>
                                <span className="text-lg font-medium text-gray-900 dark:text-gray-100">{enquiry.email}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block flex items-center gap-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    <Phone className="h-4 w-4" />
                                    Mobile Number
                                </span>
                                <span className="text-lg font-medium text-gray-900 dark:text-gray-100">{enquiry.mobile}</span>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Enquiry Message Card */}
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MessageCircle className="h-5 w-5" />
                                Enquiry Message
                            </CardTitle>
                            <CardDescription>The original message submitted by the contact.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Message Content</span>
                                <div className="rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50">
                                    <p className="whitespace-pre-wrap text-gray-900 dark:text-gray-100">{enquiry.message || 'No message provided'}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Internal Remarks Card */}
                    <Card className="md:col-span-2">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <FileText className="h-5 w-5" />
                                Internal Remarks
                            </CardTitle>
                            <CardDescription>Internal notes and remarks from staff members.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Staff Remarks</span>
                                {hasRemark ? (
                                    <div className="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                        <p className="whitespace-pre-wrap text-gray-900 dark:text-gray-100">{currentRemark}</p>
                                    </div>
                                ) : (
                                    <div className="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 py-8 text-center dark:border-gray-600">
                                        <FileText className="mb-2 h-8 w-8 text-gray-400" />
                                        <span className="text-sm text-gray-500 dark:text-gray-400">No internal remarks added yet</span>
                                        <p className="mt-1 text-xs text-gray-400">Add remarks to keep track of follow-ups and internal notes.</p>
                                    </div>
                                )}
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
                            <CardDescription>Record creation and modification details.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Enquiry ID</span>
                                <span className="font-mono text-sm text-gray-900 dark:text-gray-100">#{enquiry.id}</span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Submitted At</span>
                                <span className="text-sm text-gray-900 dark:text-gray-100">
                                    {new Date(enquiry.created_at).toLocaleDateString()} at {new Date(enquiry.created_at).toLocaleTimeString()}
                                </span>
                            </div>

                            <div className="space-y-2">
                                <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</span>
                                <span className="text-sm text-gray-900 dark:text-gray-100">
                                    {new Date(enquiry.updated_at).toLocaleDateString()} at {new Date(enquiry.updated_at).toLocaleTimeString()}
                                </span>
                            </div>

                            {enquiry.createdBy && (
                                <div className="space-y-2">
                                    <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</span>
                                    <span className="text-sm text-gray-900 dark:text-gray-100">
                                        {enquiry.createdBy.first_name} {enquiry.createdBy.last_name}
                                    </span>
                                </div>
                            )}

                            {enquiry.updatedBy && (
                                <div className="space-y-2">
                                    <span className="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated By</span>
                                    <span className="text-sm text-gray-900 dark:text-gray-100">
                                        {enquiry.updatedBy.first_name} {enquiry.updatedBy.last_name}
                                    </span>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Follow-up Actions Card */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <UserCheck className="h-5 w-5" />
                                Follow-up Actions
                            </CardTitle>
                            <CardDescription>Quick actions for this enquiry.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-3">
                                <a
                                    href={`mailto:${enquiry.email}`}
                                    className="flex w-full items-center gap-2 rounded-lg border p-3 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                >
                                    <Mail className="h-4 w-4 text-blue-600" />
                                    <div>
                                        <p className="text-sm font-medium">Send Email</p>
                                        <p className="text-xs text-gray-500">Reply to {enquiry.email}</p>
                                    </div>
                                </a>

                                <a
                                    href={`tel:${enquiry.mobile}`}
                                    className="flex w-full items-center gap-2 rounded-lg border p-3 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                >
                                    <Phone className="h-4 w-4 text-green-600" />
                                    <div>
                                        <p className="text-sm font-medium">Call Contact</p>
                                        <p className="text-xs text-gray-500">Call {enquiry.mobile}</p>
                                    </div>
                                </a>

                                <button
                                    onClick={() => setIsRemarkModalOpen(true)}
                                    className="flex w-full items-center gap-2 rounded-lg border p-3 text-left hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                >
                                    <Edit className="h-4 w-4 text-purple-600" />
                                    <div>
                                        <p className="text-sm font-medium">Add Remarks</p>
                                        <p className="text-xs text-gray-500">Update internal notes</p>
                                    </div>
                                </button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Remark Modal */}
            <RemarkModal
                enquiry={{
                    id: enquiry.id,
                    first_name: enquiry.first_name,
                    last_name: enquiry.last_name,
                    message: enquiry.message,
                    remark: currentRemark,
                }}
                isOpen={isRemarkModalOpen}
                onClose={() => setIsRemarkModalOpen(false)}
                onSuccess={handleRemarkSuccess}
            />
        </AppLayout>
    );
}
