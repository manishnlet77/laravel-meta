<?php

namespace Vendor\LaravelMeta\Core;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Vendor\LaravelMeta\Core\Exceptions\MetaApiException;
use Vendor\LaravelMeta\Core\Exceptions\MetaAuthenticationException;
use Vendor\LaravelMeta\Core\Exceptions\MetaConfigurationException;

class MetaClient
{
    protected string $graphVersion;
    protected string $baseUrl = 'https://graph.facebook.com/';
    protected ?string $accessToken = null;
    protected ?string $appSecret = null;
    protected bool $appSecretProofEnabled = false;

    public function __construct(?string $accessToken = null)
    {
        $this->graphVersion = config('meta.graph_version', 'v19.0');
        $this->appSecret = config('meta.app.secret');
        $this->appSecretProofEnabled = config('meta.app.proof_enabled', true);
        
        if ($accessToken) {
            $this->setAccessToken($accessToken);
        }
    }

    /**
     * Set the access token for the client.
     */
    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;
        return $this;
    }

    /**
     * Get the current access token.
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Make a GET request to the Graph API.
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->request('get', $endpoint, $params);
    }

    /**
     * Make a POST request to the Graph API.
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('post', $endpoint, $data);
    }

    /**
     * Make a DELETE request to the Graph API.
     */
    public function delete(string $endpoint, array $params = []): array
    {
        return $this->request('delete', $endpoint, $params);
    }

    /**
     * Execute the API request.
     */
    protected function request(string $method, string $endpoint, array $payload = []): array
    {
        if (!$this->accessToken) {
            throw new MetaConfigurationException("An access token is required to make Graph API requests.");
        }

        $url = $this->buildUrl($endpoint);
        
        // Append access token and app secret proof
        $payload['access_token'] = $this->accessToken;
        
        if ($this->appSecretProofEnabled && $this->appSecret) {
            $payload['appsecret_proof'] = $this->generateAppSecretProof($this->accessToken, $this->appSecret);
        }

        $timeout = config('meta.http.timeout', 30);
        $retries = config('meta.http.retry.times', 3);
        $sleep = config('meta.http.retry.sleep', 100);

        $response = Http::timeout($timeout)
            ->retry($retries, $sleep)
            ->{$method}($url, $payload);

        return $this->handleResponse($response);
    }

    /**
     * Build the full API URL.
     */
    protected function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');
        
        if (str_starts_with($endpoint, $this->graphVersion)) {
            return $this->baseUrl . $endpoint;
        }
        
        return $this->baseUrl . $this->graphVersion . '/' . $endpoint;
    }

    /**
     * Generate the app secret proof for added security.
     */
    protected function generateAppSecretProof(string $accessToken, string $appSecret): string
    {
        return hash_hmac('sha256', $accessToken, $appSecret);
    }

    /**
     * Handle the response and throw specific exceptions for errors.
     */
    protected function handleResponse(Response $response): array
    {
        $data = $response->json() ?? [];

        if ($response->failed()) {
            $this->handleError($response->status(), $data);
        }

        return $data;
    }

    /**
     * Upload binary data for things like Facebook Reels.
     */
    public function requestBinaryUpload(string $uploadUrl, string $filePath, int $fileSize): array
    {
        if (!$this->accessToken) {
            throw new MetaConfigurationException("An access token is required to make Graph API requests.");
        }

        $stream = fopen($filePath, 'r');
        if (!$stream) {
            throw new \Exception("Could not open file stream: {$filePath}");
        }

        // Meta's binary upload for Reels requires an 'OAuth' prefix rather than Bearer in some instances, 
        // and specific headers for file size and offset.
        $response = Http::withHeaders([
            'Authorization' => 'OAuth ' . $this->accessToken,
            'offset' => 0,
            'file_size' => $fileSize,
        ])
        ->withBody($stream, 'application/octet-stream')
        ->post($uploadUrl);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $this->handleResponse($response);
    }

    /**
     * Normalize errors and throw the correct exception.
     */
    protected function handleError(int $statusCode, array $data): void
    {
        $error = $data['error'] ?? [];
        $message = $error['message'] ?? 'Unknown Meta API Error';
        $code = $error['code'] ?? $statusCode;
        $type = $error['type'] ?? 'UnknownException';

        // Code 190 is typically an OAuthException (invalid token, expired, etc.)
        if ($code == 190 || str_contains($type, 'OAuthException')) {
            throw new MetaAuthenticationException($message, $code, $error);
        }

        // Add more specific error handling here as needed (Permissions, Rate limits, etc.)

        throw new MetaApiException($message, $code, $error);
    }
}
