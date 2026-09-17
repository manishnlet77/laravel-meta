# Fetching & Filtering Media

This document explains how to use the built-in Fetcher classes to retrieve your published media from Meta APIs.

## 1. Fetching from Instagram

The `InstagramFetcher` allows you to fetch media items. You can either get the **Latest** (chronologically newest) or the **Top Trending** (sorted by highest engagement: likes + comments).

```php
use Vendor\LaravelMeta\Facades\Meta;

$igUserId = 'YOUR_IG_USER_ID';

// --- LATEST (Chronological) ---
$latestVideos = Meta::instagramFetcher()->getLatest($igUserId, 10, 'VIDEO');
$latestReels  = Meta::instagramFetcher()->getLatest($igUserId, 10, 'REELS');
$latestImages = Meta::instagramFetcher()->getLatest($igUserId, 10, 'IMAGE');
$allLatest    = Meta::instagramFetcher()->getLatest($igUserId, 10); // All types mixed

// --- TOP TRENDING (Sorted by Likes + Comments) ---
$topVideos = Meta::instagramFetcher()->getTop($igUserId, 10, 'VIDEO');
$topReels  = Meta::instagramFetcher()->getTop($igUserId, 10, 'REELS');
$topImages = Meta::instagramFetcher()->getTop($igUserId, 10, 'IMAGE');
```

## 2. Fetching from Facebook

The `FacebookFetcher` works the exact same way for Facebook Pages.

```php
use Vendor\LaravelMeta\Facades\Meta;

$pageId = 'YOUR_PAGE_ID';

// --- LATEST (Chronological) ---
$latestPosts  = Meta::facebookFetcher()->getLatest($pageId, 'posts', 10);
$latestReels  = Meta::facebookFetcher()->getLatest($pageId, 'reels', 10);
$latestVideos = Meta::facebookFetcher()->getLatest($pageId, 'videos', 10);
$latestPhotos = Meta::facebookFetcher()->getLatest($pageId, 'photos', 10);

// --- TOP TRENDING (Sorted by Likes + Comments) ---
$topPosts  = Meta::facebookFetcher()->getTop($pageId, 'posts', 10);
$topReels  = Meta::facebookFetcher()->getTop($pageId, 'reels', 10);
$topVideos = Meta::facebookFetcher()->getTop($pageId, 'videos', 10);
```

## 3. Saving Fetched Data to Your Database

The Meta Graph API returns many fields for each piece of media. The easiest way to keep your local database in sync with your social profiles is to use Laravel's `updateOrInsert`.

Here is a full example of mapping the API response fields to a theoretical `social_posts` table:

```php
use Vendor\LaravelMeta\Facades\Meta;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$igUserId = 'YOUR_IG_USER_ID';

// Get the top 50 posts/reels/videos
$response = Meta::instagramFetcher()->getTop($igUserId, 50);

foreach ($response['data'] as $media) {
    DB::table('social_posts')->updateOrInsert(
        ['platform_media_id' => $media['id']], // Unique constraint
        [
            'caption'      => $media['caption'] ?? '',
            'media_url'    => $media['media_url'] ?? '',
            'media_type'   => $media['media_type'] ?? 'UNKNOWN', // IMAGE, VIDEO, CAROUSEL_ALBUM
            'product_type' => $media['media_product_type'] ?? null, // e.g. REELS
            'permalink'    => $media['permalink'] ?? '',
            'likes_count'  => $media['like_count'] ?? 0,
            'comments_count'=> $media['comments_count'] ?? 0,
            'posted_at'    => Carbon::parse($media['timestamp']),
            'updated_at'   => now(),
        ]
    );
}
```
