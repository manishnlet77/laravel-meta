<?php

namespace Vendor\LaravelMeta\Fetching;

use Vendor\LaravelMeta\Core\MetaClient;

class FacebookFetcher
{
    protected MetaClient $client;

    public function __construct(MetaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch the latest posts from a Facebook Page.
     * Includes basic fields like message, created_time, permalink_url, and attachments.
     */
    public function getPosts(string $pageId, int $limit = 10): array
    {
        return $this->client->get("{$pageId}/posts", [
            'limit' => $limit,
            'fields' => 'id,message,created_time,permalink_url,attachments{media,media_type,url,title},shares,comments.summary(true),likes.summary(true)',
        ]);
    }

    /**
     * Fetch the latest published video reels from a Facebook Page.
     */
    public function getReels(string $pageId, int $limit = 10): array
    {
        return $this->client->get("{$pageId}/video_reels", [
            'limit' => $limit,
            'fields' => 'id,description,created_time,video_url,permalink_url,comments.summary(true),likes.summary(true)',
        ]);
    }

    /**
     * Fetch the latest standard videos from a Facebook Page.
     */
    public function getVideos(string $pageId, int $limit = 10): array
    {
        return $this->client->get("{$pageId}/videos", [
            'limit' => $limit,
            'fields' => 'id,description,created_time,source,permalink_url,comments.summary(true),likes.summary(true)',
        ]);
    }

    /**
     * Fetch the latest photos from a Facebook Page.
     */
    public function getPhotos(string $pageId, int $limit = 10): array
    {
        return $this->client->get("{$pageId}/photos", [
            'limit' => $limit,
            'type' => 'uploaded',
            'fields' => 'id,name,created_time,picture,link,comments.summary(true),likes.summary(true)',
        ]);
    }

    /**
     * Fetch the latest posts, videos, or reels.
     * Type can be 'posts', 'reels', 'videos', or 'photos'.
     */
    public function getLatest(string $pageId, string $type = 'posts', int $limit = 10): array
    {
        return match($type) {
            'reels' => $this->getReels($pageId, $limit),
            'videos' => $this->getVideos($pageId, $limit),
            'photos' => $this->getPhotos($pageId, $limit),
            default => $this->getPosts($pageId, $limit),
        };
    }

    /**
     * Fetch the top trending posts, videos, or reels by engagement.
     * Type can be 'posts', 'reels', 'videos', or 'photos'.
     */
    public function getTop(string $pageId, string $type = 'posts', int $limit = 5): array
    {
        // Fetch a larger pool to sort
        $fetchLimit = $limit * 3;
        
        $data = match($type) {
            'reels' => $this->getReels($pageId, $fetchLimit),
            'videos' => $this->getVideos($pageId, $fetchLimit),
            'photos' => $this->getPhotos($pageId, $fetchLimit),
            default => $this->getPosts($pageId, $fetchLimit),
        };

        if (empty($data['data'])) {
            return $data;
        }

        $items = $data['data'];

        // Calculate engagement for sorting
        usort($items, function($a, $b) {
            $aEngagement = ($a['likes']['summary']['total_count'] ?? 0) + ($a['comments']['summary']['total_count'] ?? 0);
            $bEngagement = ($b['likes']['summary']['total_count'] ?? 0) + ($b['comments']['summary']['total_count'] ?? 0);
            return $bEngagement <=> $aEngagement;
        });

        // Return the top items up to the requested limit
        $data['data'] = array_slice($items, 0, $limit);
        return $data;
    }
}
