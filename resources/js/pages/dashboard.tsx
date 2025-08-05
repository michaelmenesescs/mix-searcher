import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import MediaGrid from '@/components/media/MediaGrid';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface Track {
    id: number;
    title: string;
    artist: string;
    album: string;
    picture?: string;
    duration?: number;
    platform: string;
    added_date?: number;
}

interface DashboardProps {
    tracks: Track[];
}

export default function Dashboard({ tracks }: DashboardProps) {
    // Transform tracks data to match MediaItem interface
    const mediaItems = tracks.map(track => ({
        id: track.id,
        title: track.title,
        subtitle: track.artist,
        year: track.added_date ? new Date(track.added_date * 1000).getFullYear() : undefined,
        cover_image_url: track.picture,
        badge: track.platform === 'spotify' ? 'SPOTIFY' : track.platform.toUpperCase(),
        duration: track.duration,
    }));

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-8 p-6">
                {/* Recently Added Section */}
                <MediaGrid 
                    items={mediaItems}
                    type="song"
                    title="Recently added"
                    subtitle="Your latest tracks"
                    showViewAll={true}
                />
                
                {/* Essentials Section */}
                <MediaGrid 
                    items={mediaItems.slice(0, 8).map(item => ({
                        ...item,
                        badge: 'ESSENTIALS'
                    }))}
                    type="song"
                    title="Essentials to explore"
                    subtitle="Curated for you"
                    showViewAll={true}
                />
            </div>
        </AppLayout>
    );
}
