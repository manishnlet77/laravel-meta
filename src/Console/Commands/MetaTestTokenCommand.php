<?php

namespace Vendor\LaravelMeta\Console\Commands;

use Illuminate\Console\Command;
use Vendor\LaravelMeta\Core\MetaClient;
use Vendor\LaravelMeta\Core\TokenManager;

class MetaTestTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'meta:test-token {--token= : The access token to test}';

    /**
     * The console command description.
     */
    protected $description = 'Test the validity of the Meta System User Access Token';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=================================");
        $this->info(" META TOKEN TEST");
        $this->info("=================================\n");

        $token = $this->option('token') ?: config('meta.token');

        if (!$token) {
            $this->error("[FAIL] No access token provided or found in config (META_SYSTEM_USER_TOKEN).");
            $this->printFail();
            return self::FAILURE;
        }

        $this->info("[PASS] Access token configuration found");

        $client = new MetaClient($token);
        
        // Disable App Secret Proof for the debug_token call because it's a special endpoint
        // that takes input_token and is authenticated differently sometimes.
        // Actually, we can just call /me to test it easily.

        try {
            $response = $client->get('/me', ['fields' => 'id,name']);
            
            $this->info("[PASS] Graph API reachable");
            $this->info("[PASS] Token is valid (Connected to: {$response['name']} [{$response['id']}])");

            // Optionally, debug token if App ID is set
            $appId = config('meta.app.id');
            if ($appId) {
                $tokenManager = new TokenManager($client);
                $debug = $tokenManager->debugToken($token);
                
                if (isset($debug['data']['is_valid']) && $debug['data']['is_valid']) {
                    $this->info("[PASS] Token verified against App ID");
                    
                    if (isset($debug['data']['scopes'])) {
                        $this->line("\nPermissions granted:");
                        foreach ($debug['data']['scopes'] as $scope) {
                            $this->line("  - " . $scope);
                        }
                    }
                }
            }

            $this->printPass();
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("[FAIL] Token validation failed");
            $this->line("\nHTTP Error / Exception:");
            $this->error($e->getMessage());

            $this->printFail();
            return self::FAILURE;
        }
    }

    protected function printPass()
    {
        $this->info("\n=================================");
        $this->info(" RESULT: PASS");
        $this->info("=================================");
    }

    protected function printFail()
    {
        $this->error("\n=================================");
        $this->error(" RESULT: FAIL");
        $this->error("=================================");
    }
}
