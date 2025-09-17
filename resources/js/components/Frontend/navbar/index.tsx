'use client';
import { Link, usePage } from '@inertiajs/react';
import { ChevronDownIcon, CircleAlert, HomeIcon, Key, LayersIcon, Mail, NotebookText } from 'lucide-react';
import * as React from 'react';
import { useEffect, useRef, useState } from 'react';
import { cn } from '../../../lib/utils';
import { Avatar, AvatarFallback, AvatarImage } from '../../ui/avatar';
import { Button } from '../../ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '../../ui/dropdown-menu';
import { NavigationMenu, NavigationMenuItem, NavigationMenuLink, NavigationMenuList } from '../../ui/navigation-menu';
import { Popover, PopoverContent, PopoverTrigger } from '../../ui/popover';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '../../ui/tooltip';

// Simple logo component for the navbar
const Logo = (props: React.ImgHTMLAttributes<HTMLImageElement>) => {
    return (
        <img
            src="/logo_larabasex.png"
            alt="LaraBaseX Logo"
            width={32}
            height={32}
            style={{ display: 'inline-block', verticalAlign: 'middle' }}
            {...props}
        />
    );
};

// Hamburger icon component
const HamburgerIcon = ({ className, ...props }: React.SVGAttributes<SVGElement>) => (
    <svg
        className={cn('pointer-events-none', className)}
        width={16}
        height={16}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
    >
        <path
            d="M4 12L20 12"
            className="origin-center -translate-y-[7px] transition-all duration-300 ease-[cubic-bezier(.5,.85,.25,1.1)] group-aria-expanded:translate-x-0 group-aria-expanded:translate-y-0 group-aria-expanded:rotate-[315deg]"
        />
        <path d="M4 12H20" className="origin-center transition-all duration-300 ease-[cubic-bezier(.5,.85,.25,1.8)] group-aria-expanded:rotate-45" />
        <path
            d="M4 12H20"
            className="origin-center translate-y-[7px] transition-all duration-300 ease-[cubic-bezier(.5,.85,.25,1.1)] group-aria-expanded:translate-y-0 group-aria-expanded:rotate-[135deg]"
        />
    </svg>
);

// Theme Toggle Component
// const ThemeToggle = ({ onThemeChange }: { onThemeChange?: (theme: 'light' | 'dark') => void }) => {
//     const [theme, setTheme] = useState<'light' | 'dark'>('light');

//     const toggleTheme = () => {
//         const newTheme = theme === 'light' ? 'dark' : 'light';
//         setTheme(newTheme);
//         onThemeChange?.(newTheme);
//     };

//     return (
//         <Button variant="ghost" size="icon" className="h-8 w-8" onClick={toggleTheme}>
//             {theme === 'light' ? <SunIcon className="h-4 w-4" /> : <MoonIcon className="h-4 w-4" />}
//             <span className="sr-only">Toggle theme</span>
//         </Button>
//     );
// };

// User Menu Component
const UserMenu = ({
    userName = 'Root User',
    userEmail = 'root@example.com',
    userAvatar,
    onItemClick,
}: {
    userName?: string;
    userEmail?: string;
    userAvatar?: string;
    onItemClick?: (item: string) => void;
}) => (
    <DropdownMenu>
        <DropdownMenuTrigger asChild>
            <Button variant="ghost" className="h-8 px-2 py-0 hover:bg-accent hover:text-accent-foreground">
                <Avatar className="h-6 w-6">
                    <AvatarImage src={userAvatar} alt={userName} />
                    <AvatarFallback className="text-xs">
                        {userName
                            .split(' ')
                            .map((n) => n[0])
                            .join('')}
                    </AvatarFallback>
                </Avatar>
                <ChevronDownIcon className="ml-1 h-3 w-3" />
                <span className="sr-only">User menu</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" className="w-56">
            <DropdownMenuLabel>
                <div className="flex flex-col space-y-1">
                    <p className="text-sm leading-none font-medium">{userName}</p>
                    <p className="text-xs leading-none text-muted-foreground">{userEmail}</p>
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem onClick={() => onItemClick?.('profile')}>Profile</DropdownMenuItem>
            <DropdownMenuItem onClick={() => onItemClick?.('settings')}>Settings</DropdownMenuItem>
            <DropdownMenuItem onClick={() => onItemClick?.('billing')}>Billing</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem onClick={() => onItemClick?.('logout')}>Log out</DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
);

// Types
export interface NavbarNavItem {
    href?: string;
    label: string;
    icon: React.ComponentType<{ size?: number; className?: string; 'aria-hidden'?: boolean }>;
    active?: boolean;
}

// Props for the Navbar component
export interface NavbarProps extends React.HTMLAttributes<HTMLElement> {
    logo?: React.ReactNode;
    navigationLinks?: NavbarNavItem[];
    userName?: string;
    userEmail?: string;
    userAvatar?: string;
    loginText?: string;
    // onThemeChange?: (theme: 'light' | 'dark') => void;
    onUserItemClick?: (item: string) => void;
}

// Navigation links with icons
const defaultNavigationLinks: NavbarNavItem[] = [
    { href: '/', label: 'Home', icon: HomeIcon },
    { href: '/services', label: 'Services', icon: LayersIcon },
    { href: '/blogs', label: 'Blogs', icon: NotebookText },
    { href: '/about', label: 'About', icon: CircleAlert },
    { href: '/contact', label: 'Contact', icon: Mail },
];

// Navbar component
export const Navbar = React.forwardRef<HTMLElement, NavbarProps>(
    (
        {
            className,
            logo = <Logo />,
            navigationLinks = defaultNavigationLinks,
            loginText = 'Login',
            userName = 'Root User',
            userEmail = 'root@example.com',
            userAvatar,
            // onThemeChange,
            onUserItemClick,
            ...props
        },
        ref,
    ) => {
        const [isMobile, setIsMobile] = useState(false);
        const containerRef = useRef<HTMLElement>(null);
        const { url } = usePage();

        useEffect(() => {
            const checkWidth = () => {
                if (containerRef.current) {
                    const width = containerRef.current.offsetWidth;
                    setIsMobile(width < 768); // 768px is md breakpoint
                }
            };

            checkWidth();

            const resizeObserver = new ResizeObserver(checkWidth);
            if (containerRef.current) {
                resizeObserver.observe(containerRef.current);
            }

            return () => {
                resizeObserver.disconnect();
            };
        }, []);

        // Dynamically set active link
        const navLinks = navigationLinks.map((link) => ({
            ...link,
            active: link.href === url,
        }));

        // Combine refs
        const combinedRef = React.useCallback(
            (node: HTMLElement | null) => {
                containerRef.current = node;
                if (typeof ref === 'function') {
                    ref(node);
                } else if (ref) {
                    ref.current = node;
                }
            },
            [ref],
        );

        return (
            <header
                ref={combinedRef}
                className={cn(
                    'sticky top-0 z-50 w-full border-b bg-background/95 px-4 backdrop-blur supports-[backdrop-filter]:bg-background/60 md:px-6 [&_*]:no-underline',
                    className,
                )}
                {...props}
            >
                <div className="container mx-auto flex h-16 max-w-screen-2xl items-center justify-between gap-4">
                    {/* Left side */}
                    {/* ---------------------------------- */}
                    <div className="flex flex-1 items-center gap-2">
                        {/* Mobile menu trigger */}
                        {/* ---------------------------------------------------------------------------------- */}
                        {isMobile && (
                            <Popover>
                                <PopoverTrigger asChild>
                                    <Button className="group h-8 w-8 hover:bg-accent hover:text-accent-foreground" variant="ghost" size="icon">
                                        <HamburgerIcon />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent align="start" className="w-64 p-1">
                                    <NavigationMenu className="max-w-none">
                                        <NavigationMenuList className="flex-col items-start gap-0">
                                            {navLinks.map((link, index) => {
                                                const Icon = link.icon;
                                                return (
                                                    <NavigationMenuItem key={index} className="w-full">
                                                        <NavigationMenuLink asChild>
                                                            <Link
                                                                href={link.href ?? '#'}
                                                                className={cn(
                                                                    'flex w-full cursor-pointer items-center gap-2 rounded-md px-3 py-2 text-sm font-medium no-underline transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground',
                                                                    link.active && 'bg-accent text-accent-foreground',
                                                                )}
                                                            >
                                                                <Icon size={16} className="text-muted-foreground" aria-hidden={true} />
                                                                <span>{link.label}</span>
                                                            </Link>
                                                        </NavigationMenuLink>
                                                    </NavigationMenuItem>
                                                );
                                            })}
                                        </NavigationMenuList>
                                    </NavigationMenu>
                                </PopoverContent>
                            </Popover>
                        )}
                        <div className="flex items-center gap-6">
                            {/* Logo */}
                            {/* ---------------------------------------------------------------------------------- */}
                            <button
                                onClick={(e) => e.preventDefault()}
                                className="flex cursor-pointer items-center space-x-2 text-primary transition-colors hover:text-primary/90"
                            >
                                <div className="text-2xl">{logo}</div>
                                <span className="hidden text-xl font-bold sm:inline-block">LaraBaseX</span>
                            </button>
                            {/* Desktop navigation - icon only */}
                            {/* ---------------------------------------------------------------------------------- */}
                            {!isMobile && (
                                <NavigationMenu className="flex">
                                    <NavigationMenuList className="gap-2">
                                        <TooltipProvider>
                                            {navLinks.map((link) => {
                                                const Icon = link.icon;
                                                return (
                                                    <NavigationMenuItem key={link.label}>
                                                        <Tooltip>
                                                            <TooltipTrigger asChild>
                                                                <NavigationMenuLink asChild>
                                                                    <Link
                                                                        href={link.href ?? '#'}
                                                                        className={cn(
                                                                            'flex size-8 cursor-pointer items-center justify-center rounded-md p-1.5 transition-colors hover:bg-accent hover:text-accent-foreground',
                                                                            link.active && 'bg-accent text-accent-foreground',
                                                                        )}
                                                                    >
                                                                        <Icon size={20} aria-hidden={true} />
                                                                        <span className="sr-only">{link.label}</span>
                                                                    </Link>
                                                                </NavigationMenuLink>
                                                            </TooltipTrigger>
                                                            <TooltipContent side="bottom" className="px-2 py-1 text-xs">
                                                                <p>{link.label}</p>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    </NavigationMenuItem>
                                                );
                                            })}
                                        </TooltipProvider>
                                    </NavigationMenuList>
                                </NavigationMenu>
                            )}
                        </div>
                    </div>
                    {/* Right side */}
                    {/* ---------------------------------- */}
                    <div className="flex items-center gap-2">
                        {/* Theme toggle */}
                        {/* ---------------------------------------------------------------------------------- */}
                        {/* <ThemeToggle onThemeChange={onThemeChange} /> */}
                        {/* User menu */}
                        {/* ---------------------------------------------------------------------------------- */}
                        <UserMenu userName={userName} userEmail={userEmail} userAvatar={userAvatar} onItemClick={onUserItemClick} />
                        {/* Login link (Inertia SPA) */}
                        {/* ---------------------------------------------------------------------------------- */}
                        <Link
                            href="/login"
                            className={cn(
                                'inline-flex items-center gap-1 rounded-md bg-primary px-3 py-1.5 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none',
                                'sm:text-sm',
                            )}
                        >
                            <Key className="mr-1 opacity-60" size={16} aria-hidden={true} />
                            <span className="hidden sm:inline">{loginText}</span>
                            <span className="sr-only sm:hidden">{loginText}</span>
                        </Link>
                    </div>
                </div>
            </header>
        );
    },
);

Navbar.displayName = 'Navbar';
export { HamburgerIcon, Logo, UserMenu };
