<?php

namespace Vendor\LaravelMeta;

use Vendor\LaravelMeta\Core\MetaClient;
use Vendor\LaravelMeta\Publishing\FacebookPublisher;
use Vendor\LaravelMeta\Publishing\InstagramPublisher;

class MetaManager
{
    protected MetaClient $client;

    public function __construct(MetaClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get the base Meta API Client.
     */
    public function client(): MetaClient
    {
        return $this->client;
    }

    /**
     * Access the Facebook Publisher.
     */
    public function facebook(): FacebookPublisher
    {
        return new FacebookPublisher($this->client);
    }

    /**
     * Access the Instagram Publisher.
     */
    public function instagram(): InstagramPublisher
    {
        return new InstagramPublisher($this->client);
    }

    /**
     * Access the Facebook Fetcher.
     */
    public function facebookFetcher(): \Vendor\LaravelMeta\Fetching\FacebookFetcher
    {
        return new \Vendor\LaravelMeta\Fetching\FacebookFetcher($this->client);
    }

    /**
     * Access the Instagram Fetcher.
     */
    public function instagramFetcher(): \Vendor\LaravelMeta\Fetching\InstagramFetcher
    {
        return new \Vendor\LaravelMeta\Fetching\InstagramFetcher($this->client);
    }
}
