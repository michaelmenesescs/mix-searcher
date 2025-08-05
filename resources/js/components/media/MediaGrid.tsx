import MediaCard from './MediaCard';

interface MediaItem {
    id: number;
    title: string;
    subtitle?: string;
    year?: number;
    cover_image_url?: string;
    track_count?: number;
    badge?: string;
}

interface MediaGridProps {
    items: MediaItem[];
    type: 'album' | 'artist' | 'playlist' | 'song';
    title?: string;
    subtitle?: string;
    showViewAll?: boolean;
    className?: string;
}

export default function MediaGrid({ 
    items, 
    type, 
    title, 
    subtitle, 
    showViewAll = false,
    className = "" 
}: MediaGridProps) {
    return (
        <div className={className}>
            {/* Section Header */}
            {(title || subtitle) && (
                <div className="flex items-center justify-between mb-6">
                    <div>
                        {title && (
                            <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {title}
                            </h2>
                        )}
                        {subtitle && (
                            <p className="text-gray-600 dark:text-gray-400">
                                {subtitle}
                            </p>
                        )}
                    </div>
                    
                    {showViewAll && (
                        <button className="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            VIEW ALL
                        </button>
                    )}
                </div>
            )}
            
            {/* Grid */}
            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                {items.map((item) => (
                    <MediaCard 
                        key={item.id} 
                        item={item} 
                        type={type}
                    />
                ))}
            </div>
        </div>
    );
} 