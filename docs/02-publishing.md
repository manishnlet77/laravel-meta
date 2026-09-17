# Publishing to Social Media

This package allows you to publish various media types to both Facebook Pages and Instagram Business Accounts seamlessly.

## Facebook Publishing
To publish to a Facebook Page, you need the Facebook Page ID.

```php
use Vendor\LaravelMeta\Facades\Meta;

$pageId = '1234567890';

// 1. Text Post
Meta::facebook()->publishText($pageId, 'Hello World!');

// 2. Single Image
Meta::facebook()->publishImage($pageId, 'https://example.com/image.jpg', 'Check this out!');

// 3. Standard Video
Meta::facebook()->publishVideo($pageId, 'https://example.com/video.mp4', 'Awesome video!');

// 4. Video Reel (3-Step Chunked Binary Upload)
// Note: For Reels, pass the absolute path to the local video file.
Meta::facebook()->publishReel($pageId, storage_path('app/videos/reel.mp4'), 'My amazing Reel!');
```

## Instagram Publishing
To publish to Instagram, you need the Instagram Business Account ID linked to your Facebook Page. Note that Instagram does not support text-only posts via the Graph API.

```php
use Vendor\LaravelMeta\Facades\Meta;

$igUserId = '0987654321';

// 1. Single Image
Meta::instagram()->publishImage($igUserId, 'https://example.com/image.jpg', 'Beautiful scenery! #nature');

// 2. Video (Standard Post)
// Note: Videos are processed asynchronously. The package automatically handles polling until finished.
Meta::instagram()->publishVideo($igUserId, 'https://example.com/video.mp4', 'Great video! #video');

// 3. Reels
Meta::instagram()->publishReel($igUserId, 'https://example.com/reel.mp4', 'My first IG Reel! #reels');
```
