import React, { useState, useEffect, useCallback } from 'react';
import { Head } from '@inertiajs/react';
import { Search, Music, Disc, User, Loader2, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Separator } from '@/components/ui/separator';

interface SearchResult {
  id: number;
  score: number;
  highlight: Record<string, string[]>;
  source: Record<string, any>;
}

interface SearchResults {
  total: number;
  results: SearchResult[];
}

interface SearchData {
  songs: SearchResults;
  albums: SearchResults;
  artists: SearchResults;
}

interface Props {
  initialResults: SearchData;
}

export default function SearchIndex({ initialResults }: Props) {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState<SearchData>(initialResults);
  const [loading, setLoading] = useState(false);
  const [activeTab, setActiveTab] = useState('all');
  const [searchTimeout, setSearchTimeout] = useState<NodeJS.Timeout | null>(null);

  const performSearch = useCallback(async (searchQuery: string) => {
    if (!searchQuery.trim()) {
      setResults(initialResults);
      return;
    }

    setLoading(true);
    try {
      const response = await fetch('/search', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ query: searchQuery, limit: 20 }),
      });

      if (response.ok) {
        const data = await response.json();
        setResults(data.data);
      }
    } catch (error) {
      console.error('Search failed:', error);
    } finally {
      setLoading(false);
    }
  }, [initialResults]);

  useEffect(() => {
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    if (query.trim()) {
      const timeout = setTimeout(() => {
        performSearch(query);
      }, 300);

      setSearchTimeout(timeout);
    } else {
      setResults(initialResults);
    }

    return () => {
      if (searchTimeout) {
        clearTimeout(searchTimeout);
      }
    };
  }, [query, performSearch, searchTimeout, initialResults]);

  const clearSearch = () => {
    setQuery('');
    setResults(initialResults);
  };

  const formatDuration = (seconds: number): string => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  };

  const renderHighlightedText = (text: string, highlights: string[] = []) => {
    if (!highlights.length) return text;
    
    const highlight = highlights[0];
    return (
      <span dangerouslySetInnerHTML={{ __html: highlight }} />
    );
  };

  const SearchResultCard = ({ result, type }: { result: SearchResult; type: string }) => {
    const source = result.source;
    
    return (
      <Card className="hover:shadow-md transition-shadow cursor-pointer">
        <CardContent className="p-4">
          <div className="flex items-start space-x-3">
            <div className="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
              {type === 'songs' && <Music className="w-6 h-6 text-white" />}
              {type === 'albums' && <Disc className="w-6 h-6 text-white" />}
              {type === 'artists' && <User className="w-6 h-6 text-white" />}
            </div>
            
            <div className="flex-1 min-w-0">
              <div className="flex items-center justify-between">
                <h3 className="text-sm font-medium text-gray-900 truncate">
                  {renderHighlightedText(
                    type === 'songs' ? source.title : 
                    type === 'albums' ? source.title : 
                    source.name,
                    result.highlight[type === 'songs' ? 'title' : type === 'albums' ? 'title' : 'name']
                  )}
                </h3>
                <Badge variant="secondary" className="text-xs">
                  {Math.round(result.score * 100)}%
                </Badge>
              </div>
              
              {type === 'songs' && (
                <p className="text-sm text-gray-600 mt-1">
                  {renderHighlightedText(
                    source.artist_name || 'Unknown Artist',
                    result.highlight.artist_name
                  )}
                  {source.album_title && (
                    <>
                      <span className="mx-1">•</span>
                      {renderHighlightedText(source.album_title, result.highlight.album_title)}
                    </>
                  )}
                </p>
              )}
              
              {type === 'albums' && (
                <p className="text-sm text-gray-600 mt-1">
                  {renderHighlightedText(
                    source.artist_name || 'Unknown Artist',
                    result.highlight.artist_name
                  )}
                </p>
              )}
              
              <div className="flex items-center space-x-2 mt-2">
                {source.genre && (
                  <Badge variant="outline" className="text-xs">
                    {source.genre}
                  </Badge>
                )}
                
                {type === 'songs' && source.duration && (
                  <span className="text-xs text-gray-500">
                    {formatDuration(source.duration)}
                  </span>
                )}
                
                {type === 'albums' && source.release_year && (
                  <span className="text-xs text-gray-500">
                    {source.release_year}
                  </span>
                )}
                
                {type === 'artists' && source.song_count && (
                  <span className="text-xs text-gray-500">
                    {source.song_count} songs
                  </span>
                )}
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    );
  };

  const totalResults = results.songs.total + results.albums.total + results.artists.total;

  return (
    <>
      <Head title="Search" />
      
      <div className="container mx-auto px-4 py-8">
        <div className="max-w-4xl mx-auto">
          {/* Search Header */}
          <div className="text-center mb-8">
            <h1 className="text-3xl font-bold text-gray-900 mb-2">Search Music</h1>
            <p className="text-gray-600">Find songs, albums, and artists in your library</p>
          </div>

          {/* Search Input */}
          <div className="relative mb-8">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <Input
                type="text"
                placeholder="Search for songs, albums, or artists..."
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                className="pl-10 pr-10 h-12 text-lg"
              />
              {query && (
                <Button
                  variant="ghost"
                  size="sm"
                  onClick={clearSearch}
                  className="absolute right-2 top-1/2 transform -translate-y-1/2 h-8 w-8 p-0"
                >
                  <X className="w-4 h-4" />
                </Button>
              )}
            </div>
            
            {loading && (
              <div className="flex items-center justify-center mt-4">
                <Loader2 className="w-5 h-5 animate-spin text-gray-400 mr-2" />
                <span className="text-gray-600">Searching...</span>
              </div>
            )}
          </div>

          {/* Results */}
          {query && (
            <div className="mb-6">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-semibold text-gray-900">
                  Search Results
                </h2>
                <span className="text-sm text-gray-600">
                  {totalResults} result{totalResults !== 1 ? 's' : ''} found
                </span>
              </div>

              <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                <TabsList className="grid w-full grid-cols-4">
                  <TabsTrigger value="all">
                    All ({totalResults})
                  </TabsTrigger>
                  <TabsTrigger value="songs">
                    Songs ({results.songs.total})
                  </TabsTrigger>
                  <TabsTrigger value="albums">
                    Albums ({results.albums.total})
                  </TabsTrigger>
                  <TabsTrigger value="artists">
                    Artists ({results.artists.total})
                  </TabsTrigger>
                </TabsList>

                <TabsContent value="all" className="mt-6">
                  <div className="space-y-6">
                    {results.songs.total > 0 && (
                      <div>
                        <h3 className="text-lg font-medium text-gray-900 mb-3 flex items-center">
                          <Music className="w-5 h-5 mr-2" />
                          Songs ({results.songs.total})
                        </h3>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                          {results.songs.results.slice(0, 6).map((result) => (
                            <SearchResultCard key={`song-${result.id}`} result={result} type="songs" />
                          ))}
                        </div>
                      </div>
                    )}

                    {results.albums.total > 0 && (
                      <div>
                        <h3 className="text-lg font-medium text-gray-900 mb-3 flex items-center">
                          <Disc className="w-5 h-5 mr-2" />
                          Albums ({results.albums.total})
                        </h3>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                          {results.albums.results.slice(0, 6).map((result) => (
                            <SearchResultCard key={`album-${result.id}`} result={result} type="albums" />
                          ))}
                        </div>
                      </div>
                    )}

                    {results.artists.total > 0 && (
                      <div>
                        <h3 className="text-lg font-medium text-gray-900 mb-3 flex items-center">
                          <User className="w-5 h-5 mr-2" />
                          Artists ({results.artists.total})
                        </h3>
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                          {results.artists.results.slice(0, 6).map((result) => (
                            <SearchResultCard key={`artist-${result.id}`} result={result} type="artists" />
                          ))}
                        </div>
                      </div>
                    )}
                  </div>
                </TabsContent>

                <TabsContent value="songs" className="mt-6">
                  <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {results.songs.results.map((result) => (
                      <SearchResultCard key={result.id} result={result} type="songs" />
                    ))}
                  </div>
                </TabsContent>

                <TabsContent value="albums" className="mt-6">
                  <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {results.albums.results.map((result) => (
                      <SearchResultCard key={result.id} result={result} type="albums" />
                    ))}
                  </div>
                </TabsContent>

                <TabsContent value="artists" className="mt-6">
                  <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {results.artists.results.map((result) => (
                      <SearchResultCard key={result.id} result={result} type="artists" />
                    ))}
                  </div>
                </TabsContent>
              </Tabs>
            </div>
          )}

          {/* No Results */}
          {query && !loading && totalResults === 0 && (
            <div className="text-center py-12">
              <Search className="w-16 h-16 text-gray-300 mx-auto mb-4" />
              <h3 className="text-lg font-medium text-gray-900 mb-2">No results found</h3>
              <p className="text-gray-600">
                Try adjusting your search terms or browse our library
              </p>
            </div>
          )}

          {/* Empty State */}
          {!query && (
            <div className="text-center py-12">
              <Search className="w-16 h-16 text-gray-300 mx-auto mb-4" />
              <h3 className="text-lg font-medium text-gray-900 mb-2">Start searching</h3>
              <p className="text-gray-600">
                Enter a song title, artist name, or album to begin
              </p>
            </div>
          )}
        </div>
      </div>
    </>
  );
}
