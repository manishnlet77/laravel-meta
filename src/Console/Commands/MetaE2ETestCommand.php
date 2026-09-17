<?php

namespace Vendor\LaravelMeta\Console\Commands;

use Illuminate\Console\Command;
use Vendor\LaravelMeta\Facades\Meta;
use Vendor\LaravelMeta\Core\Exceptions\MetaApiException;
use Vendor\LaravelMeta\Core\Exceptions\MetaAuthenticationException;

class MetaE2ETestCommand extends Command
{
    protected $signature = 'meta:e2e-test {--publish : Run only publishing tests} {--fetch : Run only fetching tests} {--all : Run all available tests}';
    protected $description = 'Run an end-to-end test against the Meta Graph API based on specific use cases.';

    protected array $createdFbPosts = [];
    protected array $createdIgPosts = [];

    public function handle()
    {
        $this->info("Starting Modular Meta API Test...");

        // Determine which tests to run
        $runAll = $this->option('all') || (!$this->option('publish') && !$this->option('fetch'));
        $runPublish = $this->option('publish') || $runAll;
        $runFetch = $this->option('fetch') || $runAll;

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

            // 2. Publishing Tests
            if ($runPublish) {
                $this->info("====================================");
                $this->info("2. PUBLISHING TESTS");
                $this->info("====================================");
                
                $imagePath = $this->generateDummyImage();
                $videoPath = $this->generateDummyVideo();
                $publicImageUrl = 'https://picsum.photos/800/800.jpg';
                $publicVideoUrl = 'https://www.w3schools.com/html/mov_bbb.mp4';

                // Facebook Publishing
                $this->line("\n[Facebook]");
                $post = Meta::facebook()->publishText($pageId, "API Test: Text Post " . time());
                $this->createdFbPosts[] = $post['id'];
                $this->line("   [OK] Text Post ID: " . $post['id']);

                $photo = Meta::facebook()->publishImage($pageId, $publicImageUrl, "API Test: Image Post " . time());
                $this->createdFbPosts[] = $photo['id'];
                $this->line("   [OK] Photo ID: " . $photo['id']);

                $reel = Meta::facebook()->publishReel($pageId, $videoPath, "API Test: Reel " . time());
                if (isset($reel['video_id'])) {
                    $this->createdFbPosts[] = $reel['video_id'];
                    $this->line("   [OK] Reel ID: " . $reel['video_id']);
                }

                // Instagram Publishing
                if ($igUserId) {
                    $this->line("\n[Instagram]");
                    $igPhoto = Meta::instagram()->publishImage($igUserId, $publicImageUrl, "API Test: IG Image " . time());
                    $this->createdIgPosts[] = $igPhoto['id'];
                    $this->line("   [OK] IG Photo ID: " . $igPhoto['id']);

                    $igReel = Meta::instagram()->publishReel($igUserId, $publicVideoUrl, "API Test: IG Reel " . time());
                    $this->createdIgPosts[] = $igReel['id'];
                    $this->line("   [OK] IG Reel ID: " . $igReel['id']);
                }

                // Cleanup Dummy Local Files
                @unlink($imagePath);
                @unlink($videoPath);
            }

            // 3. Fetching Tests
            if ($runFetch) {
                $this->info("====================================");
                $this->info("3. FETCHING TESTS");
                $this->info("====================================");
                
                $this->line("\n[Facebook]");
                $topFb = Meta::facebookFetcher()->getTop($pageId, 'posts', 1);
                $this->line("   [OK] Successfully fetched " . count($topFb['data'] ?? []) . " top Facebook posts.");

                if ($igUserId) {
                    $this->line("\n[Instagram]");
                    $topIg = Meta::instagramFetcher()->getTop($igUserId, 1);
                    $this->line("   [OK] Successfully fetched " . count($topIg['data'] ?? []) . " top Instagram media items.");
                }
            }

            // 4. API Cleanup
            if (!empty($this->createdFbPosts) || !empty($this->createdIgPosts)) {
                $this->info("====================================");
                $this->info("4. POST-TEST CLEANUP");
                $this->info("====================================");
                
                foreach ($this->createdFbPosts as $fbId) {
                    try {
                        Meta::client()->delete($fbId);
                        $this->line("   [DELETED] FB Post: {$fbId}");
                    } catch (\Exception $e) {
                        $this->error("   [FAILED TO DELETE] FB Post {$fbId}: " . $e->getMessage());
                    }
                }

                if (!empty($this->createdIgPosts)) {
                    $this->warn("\n   [MANUAL CLEANUP REQUIRED]");
                    $this->warn("   Instagram Graph API does not support programmatic deletion.");
                    foreach ($this->createdIgPosts as $igId) {
                        $this->line("   Please manually delete IG Post: {$igId}");
                    }
                }
            }

            $this->info("\n✅ Requested Tests Completed Successfully!");

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
