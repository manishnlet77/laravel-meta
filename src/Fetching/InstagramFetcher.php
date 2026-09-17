<?php

namespace Vendor\LaravelMeta\Fetching;

use Vendor\LaravelMeta\Core\MetaClient;

class InstagramFetcher
{
    protected MetaClient $client;

    public function __construct(MetaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch media from an Instagram Business Account, optionally filtering by type.
     * 
     * @param string $igUserId
     * @param int $limit
     * @param string|null $mediaType Filter by 'IMAGE', 'VIDEO', 'REELS', or 'CAROUSEL_ALBUM'
     */
    public function getMedia(string $igUserId, int $limit = 10, ?string $mediaType = null): array
    {
        // If we need to filter, fetch more upfront to ensure we get enough of the desired type
        $fetchLimit = $mediaType ? $limit * 3 : $limit;
        
        $response = $this->client->get("{$igUserId}/media", [
            'limit' => $fetchLimit,
            'fields' => 'id,caption,media_type,media_product_type,media_url,thumbnail_url,permalink,timestamp,like_count,comments_count,children{media_type,media_url}',
        ]);

        if ($mediaType && !empty($response['data'])) {
            $filtered = array_filter($response['data'], function($item) use ($mediaType) {
                if (strtoupper($mediaType) === 'REELS') {
                    return isset($item['media_product_type']) && $item['media_product_type'] === 'REELS';
                }
                return isset($item['media_type']) && $item['media_type'] === strtoupper($mediaType);
            });
            
            $response['data'] = array_values(array_slice($filtered, 0, $limit));
        }

        return $response;
    }

    /**
     * Get only Image media.
     */
    public function getImages(string $igUserId, int $limit = 10): array
    {
        return $this->getMedia($igUserId, $limit, 'IMAGE');
    }

    /**
     * Get only Video media.
     */
    public function getVideos(string $igUserId, int $limit = 10): array
    {
        return $this->getMedia($igUserId, $limit, 'VIDEO');
    }

    /**
     * Get only Reels media.
     */
    public function getReels(string $igUserId, int $limit = 10): array
    {
        return $this->getMedia($igUserId, $limit, 'REELS');
    }

    /**
     * Fetch the latest media items, optionally filtered by type.
     * (This is an alias for getMedia, since Graph API returns latest by default).
     */
    public function getLatest(string $igUserId, int $limit = 10, ?string $mediaType = null): array
    {
        return $this->getMedia($igUserId, $limit, $mediaType);
    }

    /**
     * Fetch the top media items based on engagement (likes + comments).
     */
    public function getTop(string $igUserId, int $limit = 5, ?string $mediaType = null): array
    {
        // We fetch a larger pool (e.g. 30 items) so we can sort them accurately to find the "top" 5
        $poolLimit = $limit * 6;
        $response = $this->getMedia($igUserId, $poolLimit, $mediaType);

        if (empty($response['data'])) {
            return $response;
        }

        $items = $response['data'];

        // Sort by total engagement (likes + comments)
        usort($items, function($a, $b) {
            $aEngagement = ($a['like_count'] ?? 0) + ($a['comments_count'] ?? 0);
            $bEngagement = ($b['like_count'] ?? 0) + ($b['comments_count'] ?? 0);
            return $bEngagement <=> $aEngagement;
        });

        // Slice to requested limit
        $response['data'] = array_slice($items, 0, $limit);
        return $response;
    }
}
