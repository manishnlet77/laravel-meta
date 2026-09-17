<?php

namespace Vendor\LaravelMeta\Publishing;

use Vendor\LaravelMeta\Core\MetaClient;
use Illuminate\Support\Facades\Log;

class InstagramPublisher
{
    protected MetaClient $client;

    public function __construct(MetaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Publish an image post to an Instagram Business Account.
     */
    public function publishImage(string $igUserId, string $imageUrl, string $caption = ''): array
    {
        // 1. Create Image Container
        $container = $this->client->post("{$igUserId}/media", [
            'image_url' => $imageUrl,
            'caption' => $caption,
        ]);
        
        $containerId = $container['id'] ?? null;
        if (!$containerId) {
            throw new \Exception("Failed to create IG image container.");
        }

        // 2. Publish Container
        return $this->client->post("{$igUserId}/media_publish", [
            'creation_id' => $containerId,
        ]);
    }

    /**
     * Publish a video post to an Instagram Business Account (Async Polling).
     */
    public function publishVideo(string $igUserId, string $videoUrl, string $caption = ''): array
    {
        // 1. Create Video Container
        $container = $this->client->post("{$igUserId}/media", [
            'media_type' => 'VIDEO',
            'video_url' => $videoUrl,
            'caption' => $caption,
        ]);
        
        $containerId = $container['id'] ?? null;
        if (!$containerId) {
            throw new \Exception("Failed to create IG video container.");
        }

        // 2. Poll until FINISHED
        $this->pollContainerStatus($containerId);

        // 3. Publish Container
        return $this->client->post("{$igUserId}/media_publish", [
            'creation_id' => $containerId,
        ]);
    }

    /**
     * Publish a Reel to an Instagram Business Account (Async Polling).
     */
    public function publishReel(string $igUserId, string $videoUrl, string $caption = ''): array
    {
        // 1. Create Reel Container
        $container = $this->client->post("{$igUserId}/media", [
            'media_type' => 'REELS',
            'video_url' => $videoUrl,
            'caption' => $caption,
        ]);
        
        $containerId = $container['id'] ?? null;
        if (!$containerId) {
            throw new \Exception("Failed to create IG reel container.");
        }

        // 2. Poll until FINISHED
        $this->pollContainerStatus($containerId);

        // 3. Publish Container
        return $this->client->post("{$igUserId}/media_publish", [
            'creation_id' => $containerId,
        ]);
    }

    /**
     * Publish a Story to an Instagram Business Account (Async Polling).
     */
    public function publishStory(string $igUserId, string $mediaUrl, bool $isVideo = false): array
    {
        // 1. Create Story Container
        $payload = [
            'media_type' => 'STORIES',
        ];
        
        if ($isVideo) {
            $payload['video_url'] = $mediaUrl;
        } else {
            $payload['image_url'] = $mediaUrl;
        }

        $container = $this->client->post("{$igUserId}/media", $payload);
        
        $containerId = $container['id'] ?? null;
        if (!$containerId) {
            throw new \Exception("Failed to create IG story container.");
        }

        // 2. Poll until FINISHED (only strictly necessary for video, but safe for both)
        if ($isVideo) {
            $this->pollContainerStatus($containerId);
        }

        // 3. Publish Container
        return $this->client->post("{$igUserId}/media_publish", [
            'creation_id' => $containerId,
        ]);
    }

    /**
     * Publish a Carousel to an Instagram Business Account.
     */
    public function publishCarousel(string $igUserId, array $mediaUrls, string $caption = ''): array
    {
        $childrenIds = [];
        
        // 1. Create un-published item containers
        foreach ($mediaUrls as $url) {
            $isVideo = str_ends_with(strtolower($url), '.mp4') || str_ends_with(strtolower($url), '.mov');
            
            $payload = [
                'is_carousel_item' => 'true',
            ];
            
            if ($isVideo) {
                $payload['media_type'] = 'VIDEO';
                $payload['video_url'] = $url;
            } else {
                $payload['image_url'] = $url;
            }

            $response = $this->client->post("{$igUserId}/media", $payload);
            
            if (isset($response['id'])) {
                if ($isVideo) {
                    $this->pollContainerStatus($response['id']);
                }
                $childrenIds[] = $response['id'];
            }
        }
        
        // 2. Create the main Carousel container
        $carouselContainer = $this->client->post("{$igUserId}/media", [
            'media_type' => 'CAROUSEL',
            'children' => implode(',', $childrenIds),
            'caption' => $caption,
        ]);

        $carouselId = $carouselContainer['id'] ?? null;
        if (!$carouselId) {
            throw new \Exception("Failed to create IG carousel master container.");
        }

        // 3. Publish Container
        return $this->client->post("{$igUserId}/media_publish", [
            'creation_id' => $carouselId,
        ]);
    }

    /**
     * Poll the status of a media container until it finishes processing.
     */
    protected function pollContainerStatus(string $containerId, int $maxAttempts = 15, int $sleepSeconds = 3): void
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $statusResponse = $this->client->get($containerId, [
                'fields' => 'status_code'
            ]);
            
            $status = $statusResponse['status_code'] ?? 'UNKNOWN';
            
            if ($status === 'FINISHED') {
                return;
            }
            
            if ($status === 'ERROR') {
                throw new \Exception("Instagram Media Container processing failed. Container ID: {$containerId}");
            }
            
            sleep($sleepSeconds);
        }
        
        throw new \Exception("Timeout waiting for Instagram Media Container to finish processing. Container ID: {$containerId}");
    }
}
