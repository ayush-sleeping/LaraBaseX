import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardAction, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ChartConfig, ChartContainer, ChartTooltip, ChartTooltipContent } from '@/components/ui/chart';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { useIsMobile } from '@/hooks/use-mobile';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import {
    ColumnDef,
    ColumnFiltersState,
    SortingState,
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { ArrowUpDown, ChevronLeft, ChevronRight, TrendingDown, TrendingUp } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    LabelList,
    Pie,
    PieChart,
    PolarAngleAxis,
    PolarGrid,
    Radar,
    RadarChart,
    XAxis,
    YAxis,
} from 'recharts';

// Breadcrumbs
// ---------------------------------------------------------------------- ::
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Chart data
// ---------------------------------------------------------------------- ::
const chartData = [
    { date: '2024-04-01', desktop: 222, mobile: 150 },
    { date: '2024-04-02', desktop: 97, mobile: 180 },
    { date: '2024-04-03', desktop: 167, mobile: 120 },
    { date: '2024-04-04', desktop: 242, mobile: 260 },
    { date: '2024-04-05', desktop: 373, mobile: 290 },
    { date: '2024-04-06', desktop: 301, mobile: 340 },
    { date: '2024-04-07', desktop: 245, mobile: 180 },
    { date: '2024-04-08', desktop: 409, mobile: 320 },
    { date: '2024-04-09', desktop: 59, mobile: 110 },
    { date: '2024-04-10', desktop: 261, mobile: 190 },
    { date: '2024-04-11', desktop: 327, mobile: 350 },
    { date: '2024-04-12', desktop: 292, mobile: 210 },
    { date: '2024-04-13', desktop: 342, mobile: 380 },
    { date: '2024-04-14', desktop: 137, mobile: 220 },
    { date: '2024-04-15', desktop: 120, mobile: 170 },
    { date: '2024-04-16', desktop: 138, mobile: 190 },
    { date: '2024-04-17', desktop: 446, mobile: 360 },
    { date: '2024-04-18', desktop: 364, mobile: 410 },
    { date: '2024-04-19', desktop: 243, mobile: 180 },
    { date: '2024-04-20', desktop: 89, mobile: 150 },
    { date: '2024-04-21', desktop: 137, mobile: 200 },
    { date: '2024-04-22', desktop: 224, mobile: 170 },
    { date: '2024-04-23', desktop: 138, mobile: 230 },
    { date: '2024-04-24', desktop: 387, mobile: 290 },
    { date: '2024-04-25', desktop: 215, mobile: 250 },
    { date: '2024-04-26', desktop: 75, mobile: 130 },
    { date: '2024-04-27', desktop: 383, mobile: 420 },
    { date: '2024-04-28', desktop: 122, mobile: 180 },
    { date: '2024-04-29', desktop: 315, mobile: 240 },
    { date: '2024-04-30', desktop: 454, mobile: 380 },
    { date: '2024-05-01', desktop: 165, mobile: 220 },
    { date: '2024-05-02', desktop: 293, mobile: 310 },
    { date: '2024-05-03', desktop: 247, mobile: 190 },
    { date: '2024-05-04', desktop: 385, mobile: 420 },
    { date: '2024-05-05', desktop: 481, mobile: 390 },
    { date: '2024-05-06', desktop: 498, mobile: 520 },
    { date: '2024-05-07', desktop: 388, mobile: 300 },
    { date: '2024-05-08', desktop: 149, mobile: 210 },
    { date: '2024-05-09', desktop: 227, mobile: 180 },
    { date: '2024-05-10', desktop: 293, mobile: 330 },
    { date: '2024-05-11', desktop: 335, mobile: 270 },
    { date: '2024-05-12', desktop: 197, mobile: 240 },
    { date: '2024-05-13', desktop: 197, mobile: 160 },
    { date: '2024-05-14', desktop: 448, mobile: 490 },
    { date: '2024-05-15', desktop: 473, mobile: 380 },
    { date: '2024-05-16', desktop: 338, mobile: 400 },
    { date: '2024-05-17', desktop: 499, mobile: 420 },
    { date: '2024-05-18', desktop: 315, mobile: 350 },
    { date: '2024-05-19', desktop: 235, mobile: 180 },
    { date: '2024-05-20', desktop: 177, mobile: 230 },
    { date: '2024-05-21', desktop: 82, mobile: 140 },
    { date: '2024-05-22', desktop: 81, mobile: 120 },
    { date: '2024-05-23', desktop: 252, mobile: 290 },
    { date: '2024-05-24', desktop: 294, mobile: 220 },
    { date: '2024-05-25', desktop: 201, mobile: 250 },
    { date: '2024-05-26', desktop: 213, mobile: 170 },
    { date: '2024-05-27', desktop: 420, mobile: 460 },
    { date: '2024-05-28', desktop: 233, mobile: 190 },
    { date: '2024-05-29', desktop: 78, mobile: 130 },
    { date: '2024-05-30', desktop: 340, mobile: 280 },
    { date: '2024-05-31', desktop: 178, mobile: 230 },
    { date: '2024-06-01', desktop: 178, mobile: 200 },
    { date: '2024-06-02', desktop: 470, mobile: 410 },
    { date: '2024-06-03', desktop: 103, mobile: 160 },
    { date: '2024-06-04', desktop: 439, mobile: 380 },
    { date: '2024-06-05', desktop: 88, mobile: 140 },
    { date: '2024-06-06', desktop: 294, mobile: 250 },
    { date: '2024-06-07', desktop: 323, mobile: 370 },
    { date: '2024-06-08', desktop: 385, mobile: 320 },
    { date: '2024-06-09', desktop: 438, mobile: 480 },
    { date: '2024-06-10', desktop: 155, mobile: 200 },
    { date: '2024-06-11', desktop: 92, mobile: 150 },
    { date: '2024-06-12', desktop: 492, mobile: 420 },
    { date: '2024-06-13', desktop: 81, mobile: 130 },
    { date: '2024-06-14', desktop: 426, mobile: 380 },
    { date: '2024-06-15', desktop: 307, mobile: 350 },
    { date: '2024-06-16', desktop: 371, mobile: 310 },
    { date: '2024-06-17', desktop: 475, mobile: 520 },
    { date: '2024-06-18', desktop: 107, mobile: 170 },
    { date: '2024-06-19', desktop: 341, mobile: 290 },
    { date: '2024-06-20', desktop: 408, mobile: 450 },
    { date: '2024-06-21', desktop: 169, mobile: 210 },
    { date: '2024-06-22', desktop: 317, mobile: 270 },
    { date: '2024-06-23', desktop: 480, mobile: 530 },
    { date: '2024-06-24', desktop: 132, mobile: 180 },
    { date: '2024-06-25', desktop: 141, mobile: 190 },
    { date: '2024-06-26', desktop: 434, mobile: 380 },
    { date: '2024-06-27', desktop: 448, mobile: 490 },
    { date: '2024-06-28', desktop: 149, mobile: 200 },
    { date: '2024-06-29', desktop: 103, mobile: 160 },
    { date: '2024-06-30', desktop: 446, mobile: 400 },
];

// Chart configuration
// ---------------------------------------------------------------------- ::
const chartConfig = {
    visitors: {
        label: 'Visitors',
    },
    desktop: {
        label: 'Desktop',
        color: 'hsl(217, 91%, 60%)', // Blue color
    },
    mobile: {
        label: 'Mobile',
        color: 'hsl(212, 95%, 68%)', // Lighter blue color
    },
} satisfies ChartConfig;

// Bar chart data
// ---------------------------------------------------------------------- ::
const barChartData = [
    { browser: 'chrome', visitors: 275, fill: 'hsl(217, 91%, 60%)' },
    { browser: 'safari', visitors: 200, fill: 'hsl(212, 95%, 68%)' },
    { browser: 'firefox', visitors: 187, fill: 'hsl(207, 89%, 75%)' },
    { browser: 'edge', visitors: 173, fill: 'hsl(202, 83%, 80%)' },
    { browser: 'other', visitors: 90, fill: 'hsl(197, 77%, 85%)' },
];

// Bar chart configuration
// ---------------------------------------------------------------------- ::
const barChartConfig = {
    visitors: {
        label: 'Visitors',
    },
    chrome: {
        label: 'Chrome',
        color: 'hsl(217, 91%, 60%)',
    },
    safari: {
        label: 'Safari',
        color: 'hsl(212, 95%, 68%)',
    },
    firefox: {
        label: 'Firefox',
        color: 'hsl(207, 89%, 75%)',
    },
    edge: {
        label: 'Edge',
        color: 'hsl(202, 83%, 80%)',
    },
    other: {
        label: 'Other',
        color: 'hsl(197, 77%, 85%)',
    },
} satisfies ChartConfig;

// Custom labeled bar chart data
// ---------------------------------------------------------------------- ::
const customBarChartData = [
    { month: 'January', desktop: 186, mobile: 80 },
    { month: 'February', desktop: 305, mobile: 200 },
    { month: 'March', desktop: 237, mobile: 120 },
    { month: 'April', desktop: 73, mobile: 190 },
    { month: 'May', desktop: 209, mobile: 130 },
    { month: 'June', desktop: 214, mobile: 140 },
];

// Custom bar chart configuration
// ---------------------------------------------------------------------- ::
const customBarChartConfig = {
    desktop: {
        label: 'Desktop',
        color: 'hsl(217, 91%, 60%)',
    },
    mobile: {
        label: 'Mobile',
        color: 'hsl(212, 95%, 68%)',
    },
    label: {
        color: 'var(--background)',
    },
} satisfies ChartConfig;

// Pie chart data
// ---------------------------------------------------------------------- ::
const pieChartData = [
    { browser: 'chrome', visitors: 275, fill: 'hsl(217, 91%, 60%)' },
    { browser: 'safari', visitors: 200, fill: 'hsl(212, 95%, 68%)' },
    { browser: 'firefox', visitors: 187, fill: 'hsl(207, 89%, 75%)' },
    { browser: 'edge', visitors: 173, fill: 'hsl(202, 83%, 80%)' },
    { browser: 'other', visitors: 90, fill: 'hsl(197, 77%, 85%)' },
];

// Pie chart configuration
// ---------------------------------------------------------------------- ::
const pieChartConfig = {
    visitors: {
        label: 'Visitors',
    },
    chrome: {
        label: 'Chrome',
        color: 'hsl(217, 91%, 60%)',
    },
    safari: {
        label: 'Safari',
        color: 'hsl(212, 95%, 68%)',
    },
    firefox: {
        label: 'Firefox',
        color: 'hsl(207, 89%, 75%)',
    },
    edge: {
        label: 'Edge',
        color: 'hsl(202, 83%, 80%)',
    },
    other: {
        label: 'Other',
        color: 'hsl(197, 77%, 85%)',
    },
} satisfies ChartConfig;

// Radar chart data
// ---------------------------------------------------------------------- ::
const radarChartData = [
    { month: 'January', desktop: 186 },
    { month: 'February', desktop: 305 },
    { month: 'March', desktop: 237 },
    { month: 'April', desktop: 273 },
    { month: 'May', desktop: 209 },
    { month: 'June', desktop: 214 },
];

// Radar chart configuration
// ---------------------------------------------------------------------- ::
const radarChartConfig = {
    desktop: {
        label: 'Desktop',
        color: 'hsl(217, 91%, 60%)',
    },
} satisfies ChartConfig;

// Radar chart component
// ---------------------------------------------------------------------- ::
function RadarChartComponent() {
    return (
        <Card>
            <CardHeader className="items-center">
                <CardTitle>Radar Chart - Dots</CardTitle>
                <CardDescription>Showing total visitors for the last 6 months</CardDescription>
            </CardHeader>
            <CardContent className="pb-0">
                <ChartContainer config={radarChartConfig} className="mx-auto aspect-square max-h-[250px]">
                    <RadarChart data={radarChartData}>
                        <ChartTooltip cursor={false} content={<ChartTooltipContent />} />
                        <PolarAngleAxis dataKey="month" />
                        <PolarGrid />
                        <Radar
                            dataKey="desktop"
                            fill="var(--color-desktop)"
                            fillOpacity={0.6}
                            dot={{
                                r: 4,
                                fillOpacity: 1,
                            }}
                        />
                    </RadarChart>
                </ChartContainer>
            </CardContent>
            <CardFooter className="flex-col gap-2 text-sm">
                <div className="flex items-center gap-2 leading-none font-medium">
                    Trending up by 5.2% this month <TrendingUp className="h-4 w-4" />
                </div>
                <div className="flex items-center gap-2 leading-none text-muted-foreground">January - June 2024</div>
            </CardFooter>
        </Card>
    );
}

// Sample data for metrics cards
// ---------------------------------------------------------------------- ::
const metricsData = [
    {
        title: 'Total Revenue',
        value: '$1,250.00',
        change: '+12.5%',
        trend: 'up',
        description: 'Trending up this month',
        footer: 'Visitors for the last 6 months',
    },
    {
        title: 'New Customers',
        value: '1,234',
        change: '-20%',
        trend: 'down',
        description: 'Down 20% this period',
        footer: 'Acquisition needs attention',
    },
    {
        title: 'Active Accounts',
        value: '45,678',
        change: '+12.5%',
        trend: 'up',
        description: 'Strong user retention',
        footer: 'Engagement exceed targets',
    },
    {
        title: 'Growth Rate',
        value: '4.5%',
        change: '+4.5%',
        trend: 'up',
        description: 'Steady performance increase',
        footer: 'Meets growth projections',
    },
];

// Sample data for the table
// ---------------------------------------------------------------------- ::
type User = {
    id: number;
    name: string;
    email: string;
    status: string;
    role: string;
    joinDate: string;
};

// Sample user data
// ---------------------------------------------------------------------- ::
const sampleUsers: User[] = [
    { id: 1, name: 'John Doe', email: 'john@example.com', status: 'Active', role: 'Admin', joinDate: '2024-01-15' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', status: 'Active', role: 'User', joinDate: '2024-02-20' },
    { id: 3, name: 'Bob Johnson', email: 'bob@example.com', status: 'Inactive', role: 'User', joinDate: '2024-03-10' },
    { id: 4, name: 'Alice Brown', email: 'alice@example.com', status: 'Active', role: 'Moderator', joinDate: '2024-04-05' },
    { id: 5, name: 'Charlie Wilson', email: 'charlie@example.com', status: 'Pending', role: 'User', joinDate: '2024-05-12' },
];

// Column definitions for the user table
// ---------------------------------------------------------------------- ::
const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return (
                <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')} className="p-0 hover:bg-transparent">
                    Name
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            );
        },
    },
    {
        accessorKey: 'email',
        header: 'Email',
    },
    {
        accessorKey: 'role',
        header: 'Role',
        cell: ({ row }) => (
            <Badge variant="outline" className="text-xs">
                {row.getValue('role')}
            </Badge>
        ),
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
            const status = row.getValue('status') as string;
            let badgeStyle = {};
            let variant: 'default' | 'secondary' | 'outline' = 'default';

            if (status === 'Active') {
                badgeStyle = { backgroundColor: 'hsl(217, 91%, 60%)', color: 'white' };
                variant = 'default';
            } else if (status === 'Inactive') {
                badgeStyle = { backgroundColor: 'hsl(207, 89%, 75%)', color: 'white' };
                variant = 'default';
            } else {
                badgeStyle = { borderColor: 'hsl(212, 95%, 68%)', color: 'hsl(212, 95%, 68%)' };
                variant = 'outline';
            }

            return (
                <Badge variant={variant} className="text-xs" style={badgeStyle}>
                    {status}
                </Badge>
            );
        },
    },
    {
        accessorKey: 'joinDate',
        header: ({ column }) => {
            return (
                <Button variant="ghost" onClick={() => column.toggleSorting(column.getIsSorted() === 'asc')} className="p-0 hover:bg-transparent">
                    Join Date
                    <ArrowUpDown className="ml-2 h-4 w-4" />
                </Button>
            );
        },
        cell: ({ row }) => {
            const date = new Date(row.getValue('joinDate'));
            return date.toLocaleDateString();
        },
    },
];

// DataTable component
// ---------------------------------------------------------------------- ::
function DataTable() {
    const [sorting, setSorting] = useState<SortingState>([]);
    const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
    const [globalFilter, setGlobalFilter] = useState('');

    const table = useReactTable({
        data: sampleUsers,
        columns,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        onGlobalFilterChange: setGlobalFilter,
        state: {
            sorting,
            columnFilters,
            globalFilter,
        },
    });

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <div className="flex items-center space-x-2">
                    <Input
                        placeholder="Search users..."
                        value={globalFilter ?? ''}
                        onChange={(event) => setGlobalFilter(String(event.target.value))}
                        className="max-w-sm"
                    />
                </div>
                <div className="flex items-center space-x-2">
                    <Label htmlFor="status-filter">Status:</Label>
                    <Select onValueChange={(value) => table.getColumn('status')?.setFilterValue(value === 'all' ? undefined : value)}>
                        <SelectTrigger className="w-32">
                            <SelectValue placeholder="All" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All</SelectItem>
                            <SelectItem value="Active">Active</SelectItem>
                            <SelectItem value="Inactive">Inactive</SelectItem>
                            <SelectItem value="Pending">Pending</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => {
                                    return (
                                        <TableHead key={header.id}>
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
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell colSpan={columns.length} className="h-24 text-center">
                                    No results.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>
            <div className="flex items-center justify-between space-x-2 py-4">
                <div className="text-sm text-muted-foreground">
                    Showing {table.getFilteredRowModel().rows.length} of {sampleUsers.length} entries
                </div>
                <div className="flex items-center space-x-2">
                    <Button variant="outline" size="sm" onClick={() => table.previousPage()} disabled={!table.getCanPreviousPage()}>
                        <ChevronLeft className="h-4 w-4" />
                        Previous
                    </Button>
                    <Button variant="outline" size="sm" onClick={() => table.nextPage()} disabled={!table.getCanNextPage()}>
                        Next
                        <ChevronRight className="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>
    );
}

// Visitors chart component
// ---------------------------------------------------------------------- ::
function VisitorsChart() {
    const isMobile = useIsMobile();
    const [timeRange, setTimeRange] = useState('90d');

    useEffect(() => {
        if (isMobile) {
            setTimeRange('7d');
        }
    }, [isMobile]);

    const filteredData = useMemo(() => {
        return chartData.filter((item) => {
            const date = new Date(item.date);
            const referenceDate = new Date('2024-06-30');
            let daysToSubtract = 90;
            if (timeRange === '30d') {
                daysToSubtract = 30;
            } else if (timeRange === '7d') {
                daysToSubtract = 7;
            }
            const startDate = new Date(referenceDate);
            startDate.setDate(startDate.getDate() - daysToSubtract);
            return date >= startDate;
        });
    }, [timeRange]);

    return (
        <Card className="@container/card">
            <CardHeader>
                <CardTitle>Total Visitors</CardTitle>
                <CardDescription>
                    <span className="hidden @[540px]/card:block">Total for the last 3 months</span>
                    <span className="@[540px]/card:hidden">Last 3 months</span>
                </CardDescription>
                <CardAction>
                    <ToggleGroup
                        type="single"
                        value={timeRange}
                        onValueChange={setTimeRange}
                        variant="outline"
                        className="hidden *:data-[slot=toggle-group-item]:!px-4 @[767px]/card:flex"
                    >
                        <ToggleGroupItem value="90d">Last 3 months</ToggleGroupItem>
                        <ToggleGroupItem value="30d">Last 30 days</ToggleGroupItem>
                        <ToggleGroupItem value="7d">Last 7 days</ToggleGroupItem>
                    </ToggleGroup>
                    <Select value={timeRange} onValueChange={setTimeRange}>
                        <SelectTrigger
                            className="flex w-40 **:data-[slot=select-value]:block **:data-[slot=select-value]:truncate @[767px]/card:hidden"
                            aria-label="Select a value"
                        >
                            <SelectValue placeholder="Last 3 months" />
                        </SelectTrigger>
                        <SelectContent className="rounded-xl">
                            <SelectItem value="90d" className="rounded-lg">
                                Last 3 months
                            </SelectItem>
                            <SelectItem value="30d" className="rounded-lg">
                                Last 30 days
                            </SelectItem>
                            <SelectItem value="7d" className="rounded-lg">
                                Last 7 days
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </CardAction>
            </CardHeader>
            <CardContent className="px-2 pt-4 sm:px-6 sm:pt-6">
                <ChartContainer config={chartConfig} className="aspect-auto h-[250px] w-full">
                    <AreaChart data={filteredData}>
                        <defs>
                            <linearGradient id="fillDesktop" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="5%" stopColor="var(--color-desktop)" stopOpacity={1.0} />
                                <stop offset="95%" stopColor="var(--color-desktop)" stopOpacity={0.1} />
                            </linearGradient>
                            <linearGradient id="fillMobile" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="5%" stopColor="var(--color-mobile)" stopOpacity={0.8} />
                                <stop offset="95%" stopColor="var(--color-mobile)" stopOpacity={0.1} />
                            </linearGradient>
                        </defs>
                        <CartesianGrid vertical={false} />
                        <XAxis
                            dataKey="date"
                            tickLine={false}
                            axisLine={false}
                            tickMargin={8}
                            minTickGap={32}
                            tickFormatter={(value) => {
                                const date = new Date(value);
                                return date.toLocaleDateString('en-US', {
                                    month: 'short',
                                    day: 'numeric',
                                });
                            }}
                        />
                        <ChartTooltip
                            cursor={false}
                            content={
                                <ChartTooltipContent
                                    labelFormatter={(value) => {
                                        return new Date(value).toLocaleDateString('en-US', {
                                            month: 'short',
                                            day: 'numeric',
                                        });
                                    }}
                                    indicator="dot"
                                />
                            }
                        />
                        <Area dataKey="mobile" type="natural" fill="url(#fillMobile)" stroke="var(--color-mobile)" stackId="a" />
                        <Area dataKey="desktop" type="natural" fill="url(#fillDesktop)" stroke="var(--color-desktop)" stackId="a" />
                    </AreaChart>
                </ChartContainer>
            </CardContent>
        </Card>
    );
}

// Dashboard component
// ---------------------------------------------------------------------- ::
export default function Dashboard() {
    return (
        // Using app-layout -> app-sidebar-layout -> app-sidebar
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
                {/* Metrics Cards Section */}
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {metricsData.map((metric, index) => (
                        <Card key={index} className="relative overflow-hidden bg-gradient-to-t from-primary/5 to-card shadow-sm">
                            <CardHeader className="pb-2">
                                <CardDescription className="text-sm">{metric.title}</CardDescription>
                                <CardTitle className="text-2xl font-semibold tabular-nums">{metric.value}</CardTitle>
                                <div className="absolute top-4 right-4">
                                    <Badge variant="outline" className="flex items-center gap-1">
                                        {metric.trend === 'up' ? <TrendingUp className="h-3 w-3" /> : <TrendingDown className="h-3 w-3" />}
                                        {metric.change}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardFooter className="flex-col items-start gap-1.5 pt-0 text-sm">
                                <div className="flex items-center gap-2 font-medium">
                                    {metric.description}
                                    {metric.trend === 'up' ? <TrendingUp className="h-4 w-4" /> : <TrendingDown className="h-4 w-4" />}
                                </div>
                                <div className="text-xs text-muted-foreground">{metric.footer}</div>
                            </CardFooter>
                        </Card>
                    ))}
                </div>

                {/* Chart Section */}
                <VisitorsChart />

                {/* Data Table Section */}
                <Card>
                    <CardHeader>
                        <CardTitle>Users Management</CardTitle>
                        <CardDescription>Manage and view all platform users</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <DataTable />
                    </CardContent>
                </Card>

                {/* Quick Actions Section */}
                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">Quick Actions</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <Button className="w-full justify-start" variant="outline">
                                Add New User
                            </Button>
                            <Button className="w-full justify-start" variant="outline">
                                Generate Report
                            </Button>
                            <Button className="w-full justify-start" variant="outline">
                                System Settings
                            </Button>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">Recent Activity</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3 text-sm">
                                <div className="flex items-center gap-2">
                                    <div className="h-2 w-2 rounded-full" style={{ backgroundColor: 'hsl(217, 91%, 60%)' }}></div>
                                    New user registered
                                </div>
                                <div className="flex items-center gap-2">
                                    <div className="h-2 w-2 rounded-full" style={{ backgroundColor: 'hsl(212, 95%, 68%)' }}></div>
                                    System backup completed
                                </div>
                                <div className="flex items-center gap-2">
                                    <div className="h-2 w-2 rounded-full" style={{ backgroundColor: 'hsl(207, 89%, 75%)' }}></div>
                                    Update available
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">System Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Server</span>
                                    <Badge variant="default" style={{ backgroundColor: 'hsl(217, 91%, 60%)', color: 'white' }}>
                                        Online
                                    </Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Database</span>
                                    <Badge variant="default" style={{ backgroundColor: 'hsl(212, 95%, 68%)', color: 'white' }}>
                                        Healthy
                                    </Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm">Storage</span>
                                    <Badge variant="outline" style={{ borderColor: 'hsl(207, 89%, 75%)', color: 'hsl(207, 89%, 75%)' }}>
                                        85% Used
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Browser Chart Section */}
                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle>Browser Usage</CardTitle>
                            <CardDescription>January - June 2024</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <ChartContainer config={barChartConfig}>
                                <BarChart
                                    accessibilityLayer
                                    data={barChartData}
                                    layout="vertical"
                                    margin={{
                                        left: 0,
                                    }}
                                >
                                    <YAxis
                                        dataKey="browser"
                                        type="category"
                                        tickLine={false}
                                        tickMargin={10}
                                        axisLine={false}
                                        tickFormatter={(value) => barChartConfig[value as keyof typeof barChartConfig]?.label}
                                    />
                                    <XAxis dataKey="visitors" type="number" hide />
                                    <ChartTooltip cursor={false} content={<ChartTooltipContent hideLabel />} />
                                    <Bar dataKey="visitors" layout="vertical" radius={5} />
                                </BarChart>
                            </ChartContainer>
                        </CardContent>
                        <CardFooter className="flex-col items-start gap-2 text-sm">
                            <div className="flex gap-2 leading-none font-medium">
                                Trending up by 5.2% this month <TrendingUp className="h-4 w-4" />
                            </div>
                            <div className="leading-none text-muted-foreground">Showing total visitors for the last 6 months</div>
                        </CardFooter>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Bar Chart - Custom Label</CardTitle>
                            <CardDescription>January - June 2024</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <ChartContainer config={customBarChartConfig}>
                                <BarChart
                                    accessibilityLayer
                                    data={customBarChartData}
                                    layout="vertical"
                                    margin={{
                                        right: 16,
                                    }}
                                >
                                    <CartesianGrid horizontal={false} />
                                    <YAxis
                                        dataKey="month"
                                        type="category"
                                        tickLine={false}
                                        tickMargin={10}
                                        axisLine={false}
                                        tickFormatter={(value) => value.slice(0, 3)}
                                        hide
                                    />
                                    <XAxis dataKey="desktop" type="number" hide />
                                    <ChartTooltip cursor={false} content={<ChartTooltipContent indicator="line" />} />
                                    <Bar dataKey="desktop" layout="vertical" fill="var(--color-desktop)" radius={4}>
                                        <LabelList dataKey="month" position="insideLeft" offset={8} className="fill-[--color-label]" fontSize={12} />
                                        <LabelList dataKey="desktop" position="right" offset={8} className="fill-foreground" fontSize={12} />
                                    </Bar>
                                </BarChart>
                            </ChartContainer>
                        </CardContent>
                        <CardFooter className="flex-col items-start gap-2 text-sm">
                            <div className="flex gap-2 leading-none font-medium">
                                Trending up by 5.2% this month <TrendingUp className="h-4 w-4" />
                            </div>
                            <div className="leading-none text-muted-foreground">Showing total visitors for the last 6 months</div>
                        </CardFooter>
                    </Card>

                    <Card className="flex flex-col">
                        <CardHeader className="items-center pb-0">
                            <CardTitle>Pie Chart - Label</CardTitle>
                            <CardDescription>January - June 2024</CardDescription>
                        </CardHeader>
                        <CardContent className="flex-1 pb-0">
                            <ChartContainer
                                config={pieChartConfig}
                                className="mx-auto aspect-square max-h-[250px] pb-0 [&_.recharts-pie-label-text]:fill-foreground"
                            >
                                <PieChart>
                                    <ChartTooltip content={<ChartTooltipContent hideLabel />} />
                                    <Pie data={pieChartData} dataKey="visitors" label nameKey="browser" />
                                </PieChart>
                            </ChartContainer>
                        </CardContent>
                        <CardFooter className="flex-col gap-2 text-sm">
                            <div className="flex items-center gap-2 leading-none font-medium">
                                Trending up by 5.2% this month <TrendingUp className="h-4 w-4" />
                            </div>
                            <div className="leading-none text-muted-foreground">Showing total visitors for the last 6 months</div>
                        </CardFooter>
                    </Card>
                </div>

                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <RadarChartComponent />
                </div>
            </div>
        </AppLayout>
    );
}
