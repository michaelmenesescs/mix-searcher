import { Head } from '@inertiajs/react';
import MediaGrid from '@/components/media/MediaGrid';

interface Album {
    id: number;
    title: string;
    release_year: number;
    cover_image_url?: string;
    artist: {
        id: number;
        name: string;
    };
}

interface AlbumsPageProps {
    albums: Album[];
}

export default function Albums({ albums }: AlbumsPageProps) {
    // Transform albums data to match MediaItem interface
    const mediaItems = albums.map(album => ({
        id: album.id,
        title: album.title,
        subtitle: album.artist.name,
        year: album.release_year,
        cover_image_url: album.cover_image_url,
    }));

    return (
        <>
            <Head title="Albums" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <MediaGrid 
                                items={mediaItems}
                                type="album"
                                title="Albums"
                                subtitle={`${albums.length} albums in your collection`}
                                showViewAll={false}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
} 