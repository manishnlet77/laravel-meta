<?php

namespace Vendor\LaravelMeta\Core;

class TokenManager
{
    protected MetaClient $client;

    public function __construct(MetaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Debug an access token to get information about it.
     * Requires an App Access Token or a valid access token in the client.
     */
    public function debugToken(string $inputToken): array
    {
        return $this->client->get('debug_token', [
            'input_token' => $inputToken,
        ]);
    }

    /**
     * Exchange a short-lived user access token for a long-lived one.
     */
    public function getLongLivedToken(string $shortLivedToken, string $appId, string $appSecret): array
    {
        return $this->client->get('oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'fb_exchange_token' => $shortLivedToken,
        ]);
    }
}
