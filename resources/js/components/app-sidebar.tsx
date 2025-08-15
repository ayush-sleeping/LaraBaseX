import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChartNoAxesCombined, LayoutGrid, Notebook, ReceiptText, SquareUser, Users } from 'lucide-react';
import AppLogo from './app-logo';

// Navigation items with their required permissions ::
const allNavItems: (NavItem & { permission?: string })[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
        permission: 'dashboard-view',
    },
    {
        title: 'Roles',
        href: '/admin/roles',
        icon: Notebook,
        permission: 'role-view',
    },
    {
        title: 'Users',
        href: '/admin/users',
        icon: Users,
        permission: 'user-view',
    },
    {
        title: 'Employees',
        href: '/admin/employees',
        icon: SquareUser,
        permission: 'employee-view',
    },
    {
        title: 'Enquiries',
        href: '/admin/enquiries',
        icon: ReceiptText,
        permission: 'enquiry-view',
    },
    {
        title: 'Analytics',
        href: '/admin/analytics',
        icon: ChartNoAxesCombined,
        permission: 'analytics-view',
    },
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Repository',
    //     href: 'https://github.com/laravel/react-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#react',
    //     icon: BookOpen,
    // },
];

interface AuthUser {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    permissions: Array<{ id: number; name: string; guard_name: string }>;
    roles: Array<{ id: number; name: string; guard_name: string }>;
}

interface PageProps {
    auth: {
        user: AuthUser | null;
    };
    [key: string]: unknown;
}

// Helper function to check if user has permission
const hasPermission = (user: AuthUser | null, permission: string): boolean => {
    if (!user) return false;

    // Check if user has RootUser role (has all permissions)
    if (user.roles?.some((role) => role.name === 'RootUser')) {
        return true;
    }

    // Check if user has the specific permission
    return user.permissions?.some((p) => p.name === permission) || false;
};

export function AppSidebar() {
    const { auth } = usePage<PageProps>().props;

    // Filter navigation items based on user permissions
    const mainNavItems = allNavItems.filter((item) => {
        // If no permission required, show the item
        if (!item.permission) return true;
        // Check if user has the required permission
        return hasPermission(auth.user, item.permission);
    });

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
