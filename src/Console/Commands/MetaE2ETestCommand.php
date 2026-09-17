<?php

namespace Vendor\LaravelMeta\Console\Commands;

use Illuminate\Console\Command;
use Vendor\LaravelMeta\Facades\Meta;
use Vendor\LaravelMeta\Core\Exceptions\MetaApiException;
use Vendor\LaravelMeta\Core\Exceptions\MetaAuthenticationException;

class MetaE2ETestCommand extends Command
{
    protected $signature = 'meta:e2e-test';
    protected $description = 'Run a full end-to-end test publishing, fetching, and deleting all supported media types to Facebook and Instagram.';

    protected array $createdFbPosts = [];
    protected array $createdIgPosts = [];

    public function handle()
    {
        $this->info("Starting Meta E2E Test...");

        try {
            // 1. Verify Credentials & Get Accounts
            $this->info("1. Verifying credentials and fetching accounts...");
            $accounts = Meta::client()->get('me/accounts');
            $pageId = $accounts['data'][0]['id'] ?? null;

            if (!$pageId) {
                $this->error("No Facebook Pages found. Ensure your System User Token has pages_show_list and pages_read_engagement permissions.");
                return 1;
            }
            $this->info("Found Facebook Page ID: {$pageId}");

            $igAccount = Meta::client()->get("{$pageId}", ['fields' => 'instagram_business_account']);
            $igUserId = $igAccount['instagram_business_account']['id'] ?? null;
            
            if ($igUserId) {
                $this->info("Found Instagram Business Account ID: {$igUserId}");
            } else {
                $this->warn("No linked Instagram account found for this Page. IG tests will be skipped.");
            }

            // 2. Create Dummy Media
            $this->info("2. Generating temporary dummy media...");
            $imagePath = $this->generateDummyImage();
            $videoPath = $this->generateDummyVideo();
            $imageUrl = url('meta-test-dummy.jpg'); // These URLs might not be publicly accessible if testing locally, 
            $videoUrl = url('meta-test-dummy.mp4'); // which will fail Meta API. So we will just use binary upload where possible, or a public placeholder for URLs.

            // Since local URLs won't work with Meta Graph API, we'll use public placeholders for URL-based endpoints
            $publicImageUrl = 'https://picsum.photos/800/800.jpg';
            $publicVideoUrl = 'https://www.w3schools.com/html/mov_bbb.mp4';

            // 3. Test Facebook Publishing
            $this->info("3. Testing Facebook Publishing...");
            
            // Text
            $this->line(" - Publishing Text...");
            $post = Meta::facebook()->publishText($pageId, "E2E Test: Text Post " . time());
            $this->createdFbPosts[] = $post['id'];
            $this->line("   [OK] Post ID: " . $post['id']);

            // Image
            $this->line(" - Publishing Image...");
            $photo = Meta::facebook()->publishImage($pageId, $publicImageUrl, "E2E Test: Image Post " . time());
            $this->createdFbPosts[] = $photo['id'];
            $this->line("   [OK] Photo ID: " . $photo['id']);

            // Reel (Binary Upload)
            $this->line(" - Publishing Reel (Binary 3-Step Chunking)...");
            $reel = Meta::facebook()->publishReel($pageId, $videoPath, "E2E Test: Reel " . time());
            if (isset($reel['video_id'])) {
                $this->createdFbPosts[] = $reel['video_id'];
                $this->line("   [OK] Reel ID: " . $reel['video_id']);
            }

            // 4. Test Instagram Publishing
            if ($igUserId) {
                $this->info("4. Testing Instagram Publishing...");
                
                // Image
                $this->line(" - Publishing Image...");
                $igPhoto = Meta::instagram()->publishImage($igUserId, $publicImageUrl, "E2E Test: IG Image " . time());
                $this->createdIgPosts[] = $igPhoto['id'];
                $this->line("   [OK] IG Photo ID: " . $igPhoto['id']);

                // Reel
                $this->line(" - Publishing Reel (Async)...");
                $igReel = Meta::instagram()->publishReel($igUserId, $publicVideoUrl, "E2E Test: IG Reel " . time());
                $this->createdIgPosts[] = $igReel['id'];
                $this->line("   [OK] IG Reel ID: " . $igReel['id']);
            }

            // 5. Test Fetching
            $this->info("5. Testing Fetchers...");
            $topFb = Meta::facebookFetcher()->getTop($pageId, 'posts', 1);
            $this->line("   [OK] FB Fetcher retrieved " . count($topFb['data'] ?? []) . " top posts.");

            if ($igUserId) {
                $topIg = Meta::instagramFetcher()->getTop($igUserId, 1);
                $this->line("   [OK] IG Fetcher retrieved " . count($topIg['data'] ?? []) . " top media items.");
            }

            // 6. Cleanup
            $this->info("6. Cleaning up test data...");
            foreach ($this->createdFbPosts as $fbId) {
                try {
                    Meta::client()->delete($fbId);
                    $this->line("   [DELETED] FB Post: {$fbId}");
                } catch (\Exception $e) {
                    $this->error("   [FAILED TO DELETE] FB Post {$fbId}: " . $e->getMessage());
                }
            }

            if (!empty($this->createdIgPosts)) {
                $this->warn("   [MANUAL CLEANUP REQUIRED] Instagram Graph API does not support deleting posts.");
                foreach ($this->createdIgPosts as $igId) {
                    $this->line("   Please manually delete IG Post: {$igId}");
                }
            }

            @unlink($imagePath);
            @unlink($videoPath);

            $this->info("✅ E2E Test Completed Successfully!");

        } catch (MetaAuthenticationException $e) {
            $this->error("Authentication Error: " . $e->getMessage());
            $this->error("Make sure your System User Token is valid and has the correct permissions.");
            return 1;
        } catch (MetaApiException $e) {
            $this->error("Graph API Error: " . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $this->error("Unexpected Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function generateDummyImage(): string
    {
        $path = public_path('meta-test-dummy.jpg');
        $img = imagecreatetruecolor(800, 800);
        $bg = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefill($img, 0, 0, $bg);
        imagejpeg($img, $path, 80);
        imagedestroy($img);
        return $path;
    }

    protected function generateDummyVideo(): string
    {
        $path = public_path('meta-test-dummy.mp4');
        // Because generating a valid MP4 from scratch in PHP is complex,
        // we'll download a tiny sample video.
        $videoData = file_get_contents('https://www.w3schools.com/html/mov_bbb.mp4');
        file_put_contents($path, $videoData);
        return $path;
    }
}
