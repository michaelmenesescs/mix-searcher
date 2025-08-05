import { cn } from '@/lib/utils';

interface MediaItem {
    id: number;
    title: string;
    subtitle?: string;
    year?: number;
    cover_image_url?: string;
    track_count?: number;
    badge?: string;
    duration?: number;
}

interface MediaCardProps {
    item: MediaItem;
    type: 'album' | 'artist' | 'playlist' | 'song';
    className?: string;
}

export default function MediaCard({ item, type, className }: MediaCardProps) {
    const getDefaultImage = () => {
        switch (type) {
            case 'album':
                return 'https://via.placeholder.com/300x300/374151/FFFFFF?text=Album';
            case 'artist':
                return 'https://via.placeholder.com/300x300/374151/FFFFFF?text=Artist';
            case 'playlist':
                return 'https://via.placeholder.com/300x300/374151/FFFFFF?text=Playlist';
            case 'song':
                return 'https://via.placeholder.com/300x300/374151/FFFFFF?text=Song';
            default:
                return 'https://via.placeholder.com/300x300/374151/FFFFFF?text=Media';
        }
    };

    return (
        <div className={cn(
            "group relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 cursor-pointer",
            className
        )}>
            {/* Cover Image */}
            <div className="relative aspect-square overflow-hidden">
                <img 
                    src={item.cover_image_url || getDefaultImage()} 
                    alt={item.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                />
                
                {/* Badge */}
                {item.badge && (
                    <div className="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                        {item.badge}
                    </div>
                )}
                
                {/* Track Count for Playlists */}
                {type === 'playlist' && item.track_count && (
                    <div className="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                        {item.track_count} TRACKS
                    </div>
                )}
            </div>
            
            {/* Content */}
            <div className="p-4">
                <h3 className="font-semibold text-lg mb-1 text-gray-900 dark:text-white truncate">
                    {item.title}
                </h3>
                
                {item.subtitle && (
                    <p className="text-gray-600 dark:text-gray-400 mb-1 truncate">
                        {item.subtitle}
                    </p>
                )}
                
                {item.year && (
                    <p className="text-sm text-gray-500 dark:text-gray-500">
                        {item.year}
                    </p>
                )}
                
                {item.duration && type === 'song' && (
                    <p className="text-sm text-gray-500 dark:text-gray-500">
                        {Math.floor(item.duration / 60)}:{(item.duration % 60).toString().padStart(2, '0')}
                    </p>
                )}
            </div>
        </div>
    );
} 