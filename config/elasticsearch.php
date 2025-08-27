<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Elasticsearch connection settings.
    |
    */

    'hosts' => [
        env('ELASTICSEARCH_SCHEME', 'http') . '://' . env('ELASTICSEARCH_HOST', 'localhost') . ':' . env('ELASTICSEARCH_PORT', 9200),
    ],

    'retries' => env('ELASTICSEARCH_RETRIES', 3),

    'connection_pool' => env('ELASTICSEARCH_CONNECTION_POOL', 'StaticNoPingConnectionPool'),

    'connection_factory' => env('ELASTICSEARCH_CONNECTION_FACTORY', 'Elasticsearch\Connections\ConnectionFactory'),

    'serializer' => env('ELASTICSEARCH_SERIALIZER', 'Elasticsearch\Serializers\SmartSerializer'),

    'connection' => [
        'timeout' => env('ELASTICSEARCH_TIMEOUT', 30),
        'connect_timeout' => env('ELASTICSEARCH_CONNECT_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Index Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for Elasticsearch indices.
    |
    */

    'indices' => [
        'songs' => [
            'name' => 'songs',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'default' => [
                            'type' => 'standard',
                        ],
                        'autocomplete' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'autocomplete_filter'],
                        ],
                        'autocomplete_search' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase'],
                        ],
                    ],
                    'filter' => [
                        'autocomplete_filter' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1,
                            'max_gram' => 20,
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'artist_name' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'album_title' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'genre' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'duration' => ['type' => 'integer'],
                    'track_number' => ['type' => 'integer'],
                    'disc_number' => ['type' => 'integer'],
                    'release_year' => ['type' => 'integer'],
                    'lyrics' => ['type' => 'text'],
                    'external_id' => ['type' => 'keyword'],
                    'external_link' => ['type' => 'keyword'],
                    'preview_url' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ],
            ],
        ],
        'albums' => [
            'name' => 'albums',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'default' => [
                            'type' => 'standard',
                        ],
                        'autocomplete' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'autocomplete_filter'],
                        ],
                        'autocomplete_search' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase'],
                        ],
                    ],
                    'filter' => [
                        'autocomplete_filter' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1,
                            'max_gram' => 20,
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'artist_name' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'genre' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'release_year' => ['type' => 'integer'],
                    'total_duration' => ['type' => 'integer'],
                    'track_count' => ['type' => 'integer'],
                    'cover_image_url' => ['type' => 'keyword'],
                    'external_id' => ['type' => 'keyword'],
                    'external_link' => ['type' => 'keyword'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ],
            ],
        ],
        'artists' => [
            'name' => 'artists',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'default' => [
                            'type' => 'standard',
                        ],
                        'autocomplete' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'autocomplete_filter'],
                        ],
                        'autocomplete_search' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase'],
                        ],
                    ],
                    'filter' => [
                        'autocomplete_filter' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1,
                            'max_gram' => 20,
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'bio' => ['type' => 'text'],
                    'image_url' => ['type' => 'keyword'],
                    'external_id' => ['type' => 'keyword'],
                    'external_link' => ['type' => 'keyword'],
                    'song_count' => ['type' => 'integer'],
                    'album_count' => ['type' => 'integer'],
                    'total_duration' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                ],
            ],
        ],
    ],
];
