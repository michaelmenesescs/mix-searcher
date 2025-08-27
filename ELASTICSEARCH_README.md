# Elasticsearch Integration

This document describes the Elasticsearch integration for the Mix Searcher application.

## Overview

The application now uses Elasticsearch for fast, full-text search capabilities while maintaining PostgreSQL as the primary database. This hybrid approach provides:

- **Fast search**: Elasticsearch provides sub-second search results
- **Full-text search**: Advanced text analysis and fuzzy matching
- **Autocomplete**: Real-time search suggestions
- **Fallback**: Database search when Elasticsearch is unavailable
- **Data consistency**: Automatic syncing between PostgreSQL and Elasticsearch

## Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   React Frontend │    │   Laravel API   │    │   PostgreSQL    │
│                 │    │                 │    │   (Primary DB)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                                 ▼
                    ┌─────────────────┐
                    │   Elasticsearch │
                    │   (Search Index)│
                    └─────────────────┘
```

## Features

### Search Capabilities
- **Multi-field search**: Search across song titles, artist names, album titles, genres, and lyrics
- **Fuzzy matching**: Find results even with typos or partial matches
- **Relevance scoring**: Results ranked by relevance
- **Highlighting**: Search terms highlighted in results
- **Autocomplete**: Real-time search suggestions

### Content Types
- **Songs**: Title, artist, album, genre, lyrics, duration
- **Albums**: Title, artist, genre, release year, track count
- **Artists**: Name, bio, song count, album count

### Data Synchronization
- **Automatic indexing**: New records automatically indexed
- **Real-time updates**: Changes immediately reflected in search
- **Soft deletes**: Deleted records removed from search index
- **Bulk operations**: Efficient batch indexing

## Setup

### 1. Start Services

```bash
# Start all services including Elasticsearch
make docker-up

# Or start with fresh setup
make setup
```

### 2. Create Elasticsearch Indices

```bash
# Create indices with proper mappings
make elasticsearch-setup
```

### 3. Index Existing Data

```bash
# Reindex all data
make elasticsearch-reindex

# Or use artisan command
docker exec -it laravel_app_dev php artisan elasticsearch:index reindex --force
```

## Usage

### Search API Endpoints

```bash
# Search across all content types
POST /search
{
  "query": "search term",
  "limit": 20
}

# Search specific content types
POST /search/songs
POST /search/albums
POST /search/artists

# Autocomplete suggestions
POST /search/autocomplete
{
  "query": "partial",
  "type": "songs",
  "limit": 10
}

# Search statistics
GET /search/stats
```

### Frontend Search

Visit `/search` to use the search interface with:
- Real-time search as you type
- Tabbed results (All, Songs, Albums, Artists)
- Highlighted search terms
- Relevance scores
- Responsive design

## Configuration

### Environment Variables

```env
ELASTICSEARCH_HOST=elasticsearch
ELASTICSEARCH_PORT=9200
ELASTICSEARCH_SCHEME=http
ELASTICSEARCH_RETRIES=3
ELASTICSEARCH_TIMEOUT=30
ELASTICSEARCH_CONNECT_TIMEOUT=10
```

### Index Configuration

Index settings are defined in `config/elasticsearch.php`:

- **Analyzers**: Custom text analysis for better search
- **Mappings**: Field types and search behavior
- **Settings**: Shards, replicas, and performance tuning

## Management Commands

### Artisan Commands

```bash
# Check Elasticsearch status
php artisan elasticsearch:index status

# Create indices
php artisan elasticsearch:index create --force

# Delete indices
php artisan elasticsearch:index delete --force

# Reindex data
php artisan elasticsearch:index reindex --force

# Reindex specific content type
php artisan elasticsearch:index reindex --type=songs --force
```

### Make Commands

```bash
# Setup Elasticsearch
make elasticsearch-setup

# Check status
make elasticsearch-status

# Reindex data
make elasticsearch-reindex

# Reset everything
make elasticsearch-reset
```

## Monitoring

### Health Checks

```bash
# Check cluster health
curl http://localhost:9200/_cluster/health

# Check indices
curl http://localhost:9200/_cat/indices?v
```

### Kibana

Access Kibana at `http://localhost:5601` for:
- Index management
- Search testing
- Performance monitoring
- Query analysis

## Performance

### Optimization Tips

1. **Index Settings**: Configure appropriate shard and replica counts
2. **Mapping**: Use proper field types for optimal search
3. **Bulk Operations**: Use batch indexing for large datasets
4. **Caching**: Implement application-level caching for frequent queries
5. **Connection Pooling**: Configure appropriate connection settings

### Monitoring

- Monitor index size and growth
- Track search response times
- Watch for failed indexing operations
- Monitor cluster health and resource usage

## Troubleshooting

### Common Issues

1. **Connection Errors**
   ```bash
   # Check if Elasticsearch is running
   docker ps | grep elasticsearch
   
   # Check logs
   docker logs laravel_elasticsearch_dev
   ```

2. **Index Not Found**
   ```bash
   # Recreate indices
   make elasticsearch-reset
   ```

3. **Search Not Working**
   ```bash
   # Check if data is indexed
   make elasticsearch-status
   
   # Reindex data
   make elasticsearch-reindex
   ```

4. **Performance Issues**
   ```bash
   # Check cluster health
   curl http://localhost:9200/_cluster/health
   
   # Check index stats
   curl http://localhost:9200/_cat/indices?v
   ```

### Logs

Check Laravel logs for Elasticsearch errors:
```bash
docker exec -it laravel_app_dev tail -f storage/logs/laravel.log
```

## Development

### Adding New Search Fields

1. Update the model observer to include new fields
2. Modify the search service indexing method
3. Update the Elasticsearch mapping configuration
4. Reindex the data

### Custom Analyzers

Define custom analyzers in `config/elasticsearch.php` for:
- Language-specific text analysis
- Domain-specific terminology
- Custom tokenization rules

### Search Queries

The search service supports various query types:
- **Multi-match**: Search across multiple fields
- **Fuzzy**: Handle typos and variations
- **Range**: Numeric and date ranges
- **Aggregations**: Group and analyze results

## Security

- Elasticsearch runs in single-node mode for development
- No authentication enabled (development only)
- Production should use proper security configuration
- Consider using Elasticsearch security features in production

## Production Considerations

1. **Security**: Enable authentication and authorization
2. **Backup**: Implement regular index backups
3. **Monitoring**: Set up proper monitoring and alerting
4. **Scaling**: Plan for horizontal scaling
5. **Disaster Recovery**: Implement proper backup and recovery procedures
