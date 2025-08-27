import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, Search } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        url: '/dashboard'
    },
    {
        title: 'Search',
        url: '/search',
        icon: Search
    },
    {
        title: 'Explore',
        url: '/artists'
    },
    {
        title: 'DJ Sets',
        url: '/dj-sets'
    },
    {
        title: 'Albums',
        url: '/albums'
    },
];
const footerNavItems: NavItem[] = [
];

const playlistNavItems: NavItem[] = [
    {
        title: 'Hardgroove Techno',
        url: '/playlists',
        icon: Folder,
    },
    {
        title: '90s Deep House',
        url: '/playlists',
        icon: Folder,
    },
    {
        title: '90s Jungle',
        url: '/albums',
        icon: Folder,
    },
    {
        title: '80s Synthwave',
        url: '/tracks',
        icon: Folder,
    },
    {
        title: 'Citypop',
        url: '/artists',
        icon: Folder,
    },
];

export function AppSidebar() {
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

            <SidebarContent>
                <NavMain items={mainNavItems} NavGroupLabel="My Collection" />
            </SidebarContent>

            <SidebarContent>
                <NavMain items={playlistNavItems} NavGroupLabel="Playlists" />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
