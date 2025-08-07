import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/react';
import { FileText, Save, X } from 'lucide-react';

interface RemarkModalProps {
    enquiry: {
        id: number;
        first_name: string;
        last_name: string;
        message: string;
        remark: string | null;
    };
    isOpen: boolean;
    onClose: () => void;
    onSuccess: (remark: string) => void;
}

export default function RemarkModal({ enquiry, isOpen, onClose, onSuccess }: RemarkModalProps) {
    const { data, setData, post, processing, errors, reset } = useForm({
        remark: enquiry.remark || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        post(route('admin.enquiries.update.remark', enquiry.id), {
            onSuccess: () => {
                // Use the submitted data since backend doesn't return JSON
                const updatedRemark = data.remark;
                onSuccess(updatedRemark);
                onClose();
                reset();
            },
            onError: (errors) => {
                console.error('Failed to update remark:', errors);
            },
        });
    };

    const handleClose = () => {
        reset();
        onClose();
    };

    return (
        <Dialog open={isOpen} onOpenChange={handleClose}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <FileText className="h-5 w-5" />
                        Add Internal Remark
                    </DialogTitle>
                    <DialogDescription>
                        Add or update internal remarks for this enquiry from {enquiry.first_name} {enquiry.last_name}.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Original Message Display */}
                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="text-sm">Original Enquiry Message</CardTitle>
                            <CardDescription className="text-xs">For reference while adding your remarks</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="max-h-32 overflow-y-auto rounded-md bg-gray-50 p-3 dark:bg-gray-800/50">
                                <p className="text-sm whitespace-pre-wrap text-gray-700 dark:text-gray-300">
                                    {enquiry.message || 'No message provided'}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Remark Input */}
                    <div className="space-y-2">
                        <Label htmlFor="remark" className="flex items-center gap-2 text-sm font-medium">
                            <FileText className="h-4 w-4" />
                            Internal Remark
                            <span className="text-red-500">*</span>
                        </Label>
                        <Textarea
                            id="remark"
                            value={data.remark}
                            onChange={(e) => setData('remark', e.target.value)}
                            placeholder="Add your internal notes, follow-up actions, or observations about this enquiry..."
                            className="min-h-[120px] resize-none"
                            required
                        />
                        {errors.remark && <p className="text-sm text-red-600 dark:text-red-400">{errors.remark}</p>}
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                            This remark is for internal use only and will not be visible to the enquirer.
                        </p>
                    </div>

                    {/* Form Actions */}
                    <div className="flex justify-end gap-3 pt-4">
                        <Button type="button" variant="outline" onClick={handleClose} disabled={processing}>
                            <X className="mr-2 h-4 w-4" />
                            Cancel
                        </Button>
                        <Button type="submit" disabled={processing || !data.remark.trim()} className="min-w-[120px]">
                            {processing ? (
                                <>
                                    <div className="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                    Saving...
                                </>
                            ) : (
                                <>
                                    <Save className="mr-2 h-4 w-4" />
                                    Save Remark
                                </>
                            )}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
