import ProtectedSection from '@/components/protected-section';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import {
    ColumnDef,
    ColumnFiltersState,
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    SortingState,
    useReactTable,
    VisibilityState,
} from '@tanstack/react-table';
import { AlertTriangle, ArrowUpDown, ChevronDown, Circle, Filter, Mail, MessageSquare, MoreHorizontal, Phone, Trash2, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { toast, Toaster } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Enquiries',
        href: '/admin/enquiries',
    },
];

interface EnquiryPageProps {
    enquiries: Enquiry[];
    filters: {
        remark_status?: string;
        date_from?: string;
        date_to?: string;
    };
    [key: string]: unknown;
}

interface Enquiry {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    mobile: string;
    message: string;
    remark?: string;
    created_at?: string;
}

// Define columns for the DataTable
// --------------------------------------------------------------------------------------------------------------- ::
const createColumns = (handleDelete: (id: number, name: string) => void, processing: boolean): ColumnDef<Enquiry>[] => [
    {
        id: 'select',
        header: ({ table }) => (
            <div className="flex justify-center">
                {/* ----------------------------------------------------------------------- :: */}
                <Checkbox
                    checked={table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate')}
                    onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
                    aria-label="Select all"
                />
            </div>
        ),
        cell: ({ row }) => (
            <div className="flex justify-center">
                {/* ----------------------------------------------------------------------- :: */}
                <Checkbox checked={row.getIsSelected()} onCheckedChange={(value) => row.toggleSelected(!!value)} aria-label="Select row" />
            </div>
        ),
        enableSorting: false,
        enableHiding: false,
    },
    {
        id: 'index',
        header: () => <div className="text-center">#</div>,
        cell: ({ row, table }) => {
            const pageIndex = table.getState().pagination.pageIndex;
            const pageSize = table.getState().pagination.pageSize;
            return <div className="text-center font-medium">{pageIndex * pageSize + row.index + 1}</div>;
        },
        enableSorting: false,
        enableHiding: false,
    },
    {
        id: 'actions',
        header: () => <div className="text-center">Actions</div>,
        enableHiding: false,
        cell: ({ row }) => {
            const enquiry = row.original;

            return (
                <div className="flex justify-center">
                    {/* ----------------------------------------------------------------------- :: */}
                    <DropdownMenu>
                        {/* ---------------------------------------- :: */}
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="h-8 w-8 p-0">
                                <span className="sr-only">Open menu</span>
                                <MoreHorizontal className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        {/* ---------------------------------------- :: */}
                        <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem asChild>
                                <Link href={route('admin.enquiries.show', enquiry.id)}>View Details</Link>
                            </DropdownMenuItem>
                            <ProtectedSection permission="enquiry-update" showDeniedMessage={false}>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    onClick={() => handleDelete(enquiry.id, `${enquiry.first_name} ${enquiry.last_name}`)}
                                    disabled={processing}
                                    className="text-red-600 focus:text-red-600"
                                >
                                    Delete Enquiry
                                </DropdownMenuItem>
                            </ProtectedSection>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            );
        },
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return (
                <div className="flex justify-center">
                    {/* ----------------------------------------------------------------------- :: */}
                    <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                        Name
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                </div>
            );
        },
        cell: ({ row }) => {
            const enquiry = row.original;
            return <div className="text-center font-medium">{`${enquiry.first_name} ${enquiry.last_name}`.trim()}</div>;
        },
        sortingFn: (rowA, rowB) => {
            const nameA = `${rowA.original.first_name} ${rowA.original.last_name}`.trim();
            const nameB = `${rowB.original.first_name} ${rowB.original.last_name}`.trim();
            return nameA.localeCompare(nameB);
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return (
                <div className="flex justify-center">
                    {/* ----------------------------------------------------------------------- :: */}
                    <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                        Email
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                </div>
            );
        },
        cell: ({ row }) => <div className="text-center">{row.getValue('email')}</div>,
    },
    {
        accessorKey: 'mobile',
        header: ({ column }) => {
            return (
                <div className="flex justify-center">
                    {/* ----------------------------------------------------------------------- :: */}
                    <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                        Mobile
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                </div>
            );
        },
        cell: ({ row }) => <div className="text-center">{row.getValue('mobile')}</div>,
    },
    {
        accessorKey: 'message',
        header: ({ column }) => {
            return (
                <div className="flex justify-center">
                    {/* ----------------------------------------------------------------------- :: */}
                    <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                        Message
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                </div>
            );
        },
        cell: ({ row }) => {
            const message = row.getValue('message') as string;
            const truncatedMessage = message.length > 20 ? message.substring(0, 20) + '...' : message;
            return (
                <div className="max-w-xs text-center" title={message}>
                    {truncatedMessage}
                </div>
            );
        },
    },
    {
        accessorKey: 'remark',
        header: ({ column }) => {
            return (
                <div className="flex justify-center">
                    {/* ----------------------------------------------------------------------- :: */}
                    <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                        Remark
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                </div>
            );
        },
        cell: ({ row }) => {
            const remark = row.getValue('remark') as string;

            if (!remark || remark.trim() === '') {
                return (
                    <div className="flex justify-center">
                        <Circle className="h-3 w-3 fill-gray-400 text-gray-400" />
                    </div>
                );
            }

            const truncatedRemark = remark.length > 20 ? remark.substring(0, 20) + '...' : remark;
            return (
                <div className="max-w-xs text-center" title={remark}>
                    {truncatedRemark}
                </div>
            );
        },
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => {
            return (
                <div className="flex justify-center">
                    {/* ----------------------------------------------------------------------- :: */}
                    <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')}>
                        Date
                        <ArrowUpDown className="ml-2 h-4 w-4" />
                    </Button>
                </div>
            );
        },
        cell: ({ row }) => {
            const date = row.getValue('created_at') as string;
            const formattedDate = date ? new Date(date).toLocaleDateString() : 'N/A';
            return <div className="text-center">{formattedDate}</div>;
        },
    },
];

export default function Index() {
    const pageProps = usePage<EnquiryPageProps>().props;
    const flashFromGlobal = (usePage().props.flash as { success?: string; error?: string }) || {};
    const { enquiries = [], filters = {} } = pageProps;
    const { processing, delete: destroy } = useForm();

    // Filter states
    // ----------------------------------------------------------------------- ::
    const [remarkFilter, setRemarkFilter] = useState<string>(filters.remark_status || 'all');
    const [dateFromFilter, setDateFromFilter] = useState<string>(filters.date_from || '');
    const [dateToFilter, setDateToFilter] = useState<string>(filters.date_to || '');

    // Toast notification effect for flash messages
    useEffect(() => {
        if (flashFromGlobal.success) {
            toast.success(flashFromGlobal.success);
        }
        if (flashFromGlobal.error) {
            toast.error(flashFromGlobal.error);
        }
    }, [flashFromGlobal.success, flashFromGlobal.error]);

    // TanStack Table states
    // ----------------------------------------------------------------------- ::
    const [sorting, setSorting] = useState<SortingState>([]);
    const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
    const [columnVisibility, setColumnVisibility] = useState<VisibilityState>({});
    const [rowSelection, setRowSelection] = useState({});

    // Delete dialog state
    // ----------------------------------------------------------------------- ::
    const [deleteDialog, setDeleteDialog] = useState({
        open: false,
        enquiry: null as Enquiry | null,
    });

    // ----------------------------------------------------------------------- ::
    const handleDelete = (id: number) => {
        const enquiry = enquiries.find((enq) => enq.id === id);
        setDeleteDialog({
            open: true,
            enquiry: enquiry || null,
        });
    };

    // ----------------------------------------------------------------------- ::
    const handleRemarkFilter = (value: string) => {
        setRemarkFilter(value);

        const params = new URLSearchParams(window.location.search);

        if (value === 'all') {
            params.delete('remark_status');
        } else {
            params.set('remark_status', value);
        }

        const queryString = params.toString();
        const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;

        router.get(url, {}, { preserveState: true, preserveScroll: true });
    };

    // ----------------------------------------------------------------------- ::
    const handleDateFromFilter = (value: string) => {
        setDateFromFilter(value);
        applyDateFilters(value, dateToFilter);
    };

    // ----------------------------------------------------------------------- ::
    const handleDateToFilter = (value: string) => {
        setDateToFilter(value);
        applyDateFilters(dateFromFilter, value);
    };

    // ----------------------------------------------------------------------- ::
    const applyDateFilters = (dateFrom: string, dateTo: string) => {
        const params = new URLSearchParams(window.location.search);

        if (dateFrom) {
            params.set('date_from', dateFrom);
        } else {
            params.delete('date_from');
        }

        if (dateTo) {
            params.set('date_to', dateTo);
        } else {
            params.delete('date_to');
        }

        const queryString = params.toString();
        const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;

        router.get(url, {}, { preserveState: true, preserveScroll: true });
    };

    // ----------------------------------------------------------------------- ::
    const clearFilters = () => {
        setRemarkFilter('all');
        setDateFromFilter('');
        setDateToFilter('');
        router.get(window.location.pathname, {}, { preserveState: true, preserveScroll: true });
    };

    // ----------------------------------------------------------------------- ::
    const hasActiveFilters = remarkFilter !== 'all' || dateFromFilter !== '' || dateToFilter !== '';

    // ----------------------------------------------------------------------- ::
    const confirmDelete = () => {
        if (deleteDialog.enquiry) {
            destroy(route('admin.enquiries.destroy', deleteDialog.enquiry.id), {
                onSuccess: () => {
                    setDeleteDialog({ open: false, enquiry: null });
                },
                onError: () => {
                    setDeleteDialog({ open: false, enquiry: null });
                },
            });
        }
    };

    // ----------------------------------------------------------------------- ::
    const cancelDelete = () => {
        setDeleteDialog({ open: false, enquiry: null });
    };

    const columns = createColumns(handleDelete, processing);

    // ----------------------------------------------------------------------- ::
    const table = useReactTable({
        data: enquiries,
        columns,
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onRowSelectionChange: setRowSelection,
        state: {
            sorting,
            columnFilters,
            columnVisibility,
            rowSelection,
        },
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Enquiries" />

            {/* Toast Notifications */}
            <Toaster position="top-right" richColors closeButton duration={4000} />

            {/* Main Content */}
            {/* ----------------------------------------------------------------------- :: */}
            <div className="m-4">
                {/* Header with Create Button */}
                <div className="flex items-center justify-between py-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Enquiries Management</h1>
                        <p className="text-muted-foreground">Manage customer enquiries and responses.</p>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" className="ml-auto">
                                Columns <ChevronDown className="ml-2 h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            {table
                                .getAllColumns()
                                .filter((column) => column.getCanHide())
                                .map((column) => {
                                    return (
                                        <DropdownMenuCheckboxItem
                                            key={column.id}
                                            className="capitalize"
                                            checked={column.getIsVisible()}
                                            onCheckedChange={(value) => column.toggleVisibility(!!value)}
                                        >
                                            {column.id}
                                        </DropdownMenuCheckboxItem>
                                    );
                                })}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>

                {/* Filters and Column Visibility */}
                {/* ----------------------------------------------------------------------- :: */}
                <div className="flex flex-wrap items-center gap-4 py-4">
                    <Input
                        placeholder="Filter enquiries..."
                        value={(table.getColumn('name')?.getFilterValue() as string) ?? ''}
                        onChange={(event) => table.getColumn('name')?.setFilterValue(event.target.value)}
                        className="max-w-sm"
                    />

                    <div className="flex items-center gap-2">
                        <Input
                            type="date"
                            placeholder="From Date"
                            value={dateFromFilter}
                            onChange={(e) => handleDateFromFilter(e.target.value)}
                            className="w-40"
                        />
                        <span className="text-gray-500">to</span>
                        <Input
                            type="date"
                            placeholder="To Date"
                            value={dateToFilter}
                            onChange={(e) => handleDateToFilter(e.target.value)}
                            className="w-40"
                        />
                    </div>

                    {hasActiveFilters && (
                        <Button variant="outline" onClick={clearFilters} className="flex items-center gap-2">
                            <X className="h-4 w-4" />
                            Clear Filters
                        </Button>
                    )}

                    <Select value={remarkFilter} onValueChange={handleRemarkFilter}>
                        <SelectTrigger className="w-44">
                            <div className="flex items-center gap-2">
                                <Filter className="h-4 w-4" />
                                <SelectValue placeholder="Remark Status" />
                            </div>
                        </SelectTrigger>
                        <SelectContent align="end">
                            <SelectItem value="all">All Enquiries</SelectItem>
                            <SelectItem value="with_remark">With Remark</SelectItem>
                            <SelectItem value="without_remark">Without Remark</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                {/* Data Table */}
                {/* ----------------------------------------------------------------------- :: */}
                <div className="overflow-hidden rounded-md border">
                    <Table>
                        <TableHeader>
                            {table.getHeaderGroups().map((headerGroup) => (
                                <TableRow key={headerGroup.id}>
                                    {headerGroup.headers.map((header, index) => {
                                        return (
                                            <TableHead key={header.id} className={index < headerGroup.headers.length - 1 ? 'border-r' : ''}>
                                                {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                                            </TableHead>
                                        );
                                    })}
                                </TableRow>
                            ))}
                        </TableHeader>
                        <TableBody>
                            {table.getRowModel().rows?.length ? (
                                table.getRowModel().rows.map((row) => (
                                    <TableRow key={row.id} data-state={row.getIsSelected() && 'selected'}>
                                        {row.getVisibleCells().map((cell, index) => (
                                            <TableCell key={cell.id} className={index < row.getVisibleCells().length - 1 ? 'border-r' : ''}>
                                                {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                            </TableCell>
                                        ))}
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell colSpan={columns.length} className="h-24 text-center">
                                        No enquiries found.
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </div>

                {/* Pagination */}
                {/* ----------------------------------------------------------------------- :: */}
                <div className="flex items-center justify-end space-x-2 py-4">
                    <div className="flex-1 text-sm text-muted-foreground">
                        {table.getFilteredSelectedRowModel().rows.length} of {table.getFilteredRowModel().rows.length} row(s) selected.
                    </div>
                    <div className="space-x-2">
                        <Button variant="outline" size="sm" onClick={() => table.previousPage()} disabled={!table.getCanPreviousPage()}>
                            Previous
                        </Button>
                        <Button variant="outline" size="sm" onClick={() => table.nextPage()} disabled={!table.getCanNextPage()}>
                            Next
                        </Button>
                    </div>
                </div>
            </div>

            {/* Delete Confirmation Dialog */}
            {/* ----------------------------------------------------------------------- :: */}
            <Dialog open={deleteDialog.open} onOpenChange={(open) => !open && cancelDelete()}>
                <DialogContent className="sm:max-w-[525px]">
                    <DialogHeader className="pb-4">
                        <DialogTitle className="flex items-center gap-3 text-xl">
                            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                                <AlertTriangle className="h-6 w-6 text-red-600 dark:text-red-400" />
                            </div>
                            Delete Enquiry Confirmation
                        </DialogTitle>
                        <DialogDescription className="text-base leading-relaxed">
                            This action cannot be undone. Please review the details carefully before proceeding.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="py-4">
                        <Card className="border-red-200 bg-red-50/50 dark:border-red-800/50 dark:bg-red-900/10">
                            <CardHeader className="pb-3">
                                <CardTitle className="flex items-center gap-2 text-lg text-red-800 dark:text-red-200">
                                    <MessageSquare className="h-5 w-5" />
                                    Enquiry Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="flex items-center justify-between rounded-md bg-white/70 p-3 dark:bg-gray-800/50">
                                    <span className="font-medium text-gray-700 dark:text-gray-300">Customer Name:</span>
                                    <span className="font-semibold text-gray-900 dark:text-gray-100">
                                        {deleteDialog.enquiry ? `${deleteDialog.enquiry.first_name} ${deleteDialog.enquiry.last_name}` : 'N/A'}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between rounded-md bg-white/70 p-3 dark:bg-gray-800/50">
                                    <span className="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                        <Mail className="h-4 w-4" />
                                        Email:
                                    </span>
                                    <span className="font-semibold text-gray-900 dark:text-gray-100">{deleteDialog.enquiry?.email}</span>
                                </div>
                                <div className="flex items-center justify-between rounded-md bg-white/70 p-3 dark:bg-gray-800/50">
                                    <span className="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                        <Phone className="h-4 w-4" />
                                        Mobile:
                                    </span>
                                    <span className="font-semibold text-gray-900 dark:text-gray-100">{deleteDialog.enquiry?.mobile}</span>
                                </div>
                                <div className="flex items-start gap-3 rounded-md bg-yellow-50 p-3 dark:bg-yellow-900/20">
                                    <AlertTriangle className="mt-0.5 h-4 w-4 text-yellow-600 dark:text-yellow-400" />
                                    <div className="text-sm text-yellow-800 dark:text-yellow-200">
                                        <p className="font-medium">Warning:</p>
                                        <p>
                                            Deleting this enquiry will permanently remove it from the system. All associated messages and responses
                                            will also be removed.
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <DialogFooter className="flex gap-3 pt-4">
                        <Button variant="outline" onClick={cancelDelete} disabled={processing} className="flex-1">
                            Cancel
                        </Button>
                        <Button variant="destructive" onClick={confirmDelete} disabled={processing} className="flex-1">
                            {processing ? (
                                <>
                                    <div className="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" />
                                    Deleting...
                                </>
                            ) : (
                                <>
                                    <Trash2 className="h-4 w-4" />
                                    Delete Enquiry
                                </>
                            )}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
