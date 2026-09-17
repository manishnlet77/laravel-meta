<?php

namespace Vendor\LaravelMeta\Publishing;

use Vendor\LaravelMeta\Core\MetaClient;

class FacebookPublisher
{
    protected MetaClient $client;

    public function __construct(MetaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Publish a text post to a Facebook Page.
     */
    public function publishText(string $pageId, string $message): array
    {
        return $this->client->post("{$pageId}/feed", [
            'message' => $message,
        ]);
    }

    /**
     * Publish a single image to a Facebook Page.
     */
    public function publishImage(string $pageId, string $imageUrl, string $message = ''): array
    {
        return $this->client->post("{$pageId}/photos", [
            'url' => $imageUrl,
            'message' => $message,
        ]);
    }

    /**
     * Publish a standard video to a Facebook Page.
     */
    public function publishVideo(string $pageId, string $videoUrl, string $message = ''): array
    {
        return $this->client->post("{$pageId}/videos", [
            'file_url' => $videoUrl,
            'description' => $message,
        ]);
    }

    /**
     * Publish a Story to a Facebook Page (Image or Video).
     */
    public function publishStory(string $pageId, string $mediaUrl, bool $isVideo = false): array
    {
        $endpoint = $isVideo ? "{$pageId}/video_stories" : "{$pageId}/photo_stories";
        return $this->client->post($endpoint, [
            $isVideo ? 'video_url' : 'photo_url' => $mediaUrl,
        ]);
    }

    /**
     * Publish a Carousel to a Facebook Page.
     */
    public function publishCarousel(string $pageId, array $imageUrls, string $message = '', string $link = null): array
    {
        $mediaIds = [];
        
        // 1. Upload images as unpublished
        foreach ($imageUrls as $url) {
            $response = $this->client->post("{$pageId}/photos", [
                'url' => $url,
                'published' => false,
            ]);
            
            if (isset($response['id'])) {
                $mediaIds[] = ['media_fbid' => $response['id']];
            }
        }
        
        // 2. Publish the carousel feed post
        $payload = [
            'message' => $message,
            'attached_media' => json_encode($mediaIds),
        ];
        
        if ($link) {
            $payload['link'] = $link;
        }
        
        return $this->client->post("{$pageId}/feed", $payload);
    }

    /**
     * Publish a Reel to a Facebook Page (3-Step Process).
     * Note: This requires the video file to be accessible locally for binary upload.
     */
    public function publishReel(string $pageId, string $videoPath, string $description = ''): array
    {
        if (!file_exists($videoPath)) {
            throw new \Exception("Video file not found at path: {$videoPath}");
        }

        $fileSize = filesize($videoPath);

        // Step 1: Initialize
        $initResponse = $this->client->post("{$pageId}/video_reels", [
            'upload_phase' => 'start',
        ]);
        
        $videoId = $initResponse['video_id'] ?? null;
        $uploadUrl = $initResponse['upload_url'] ?? null;
        
        if (!$videoId || !$uploadUrl) {
            throw new \Exception("Failed to initialize Reel upload.");
        }

        // Step 2: Binary Upload
        $this->client->requestBinaryUpload($uploadUrl, $videoPath, $fileSize);

        // Step 3: Finish and Publish
        return $this->client->post("{$pageId}/video_reels", [
            'upload_phase' => 'finish',
            'video_id' => $videoId,
            'video_state' => 'PUBLISHED',
            'description' => $description,
        ]);
    }
}
