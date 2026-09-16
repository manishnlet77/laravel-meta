# Laravel Meta Integration Package

A modular, independent Meta (Facebook & Instagram) API SDK and integration package for Laravel.

This package provides a unified interface for interacting with the Meta Graph API, supporting everything from simple automated posts to complex Lead Ads webhooks.

## Key Philosophy: Complete Modularity

The most important architectural feature of this package is **Independence**. You can use only the modules you need without being forced to install database tables for features you don't use.

### Usage Modes

This package natively supports two usage modes depending on your application's architecture:

#### 1. Direct API Mode
Your CRM/Application directly calls Meta APIs through our services without requiring any local database tables from this package.
* Perfect for simple integrations (e.g., just fetching a single lead).
* No migrations required (`META_DATABASE_ENABLED=false`).
* Complete control over your own data mapping.

```php
// Example: Direct API Lead Retrieval
$lead = Meta::leads()->get('123456789');

// You map the data to your own CRM tables manually
CRMLead::create([
    'name' => $lead->getName(),
    'email' => $lead->getEmail(),
]);
```

#### 2. Persistent Integration Mode
Provides a robust synchronization layer using local database tables.
* Webhook storage and processing.
* Event dispatching (e.g., `MetaLeadReceived`).
* Automated Queue scheduling and retries for failed posts.
* Requires running package migrations (`META_DATABASE_ENABLED=true`).

---

## Supported Independent Modules

* **Facebook Module**: Pages discovery, text/image/video publishing.
* **Instagram Module**: IG Professional account discovery, media publishing (Reels, Carousels, Images).
* **Lead Ads Module**: Lead retrieval, Form retrieval, Normalized DTOs.
* **Ads Module**: Campaigns, Ad Sets, and Ads retrieval.
* **Webhooks Module**: Secure verification and processing of Meta Webhooks.

---

## Quick Installation

```bash
composer require vendor/laravel-meta
```

**Configuration (Optional if using Direct API Mode):**
Publish the config file:
```bash
php artisan vendor:publish --tag=meta-config
```

Set your credentials in `.env`:
```env
META_GRAPH_VERSION=v19.0
META_APP_ID=your_app_id
META_APP_SECRET=your_app_secret
META_APPSECRET_PROOF_ENABLED=true
```

## Documentation

Full documentation is available in the `docs/` directory:

* [01. Token and Authentication Guide](docs/04-token-and-authentication.md) *(Start here!)*
* *(More documentation guides will be added as modules are implemented)*

---

*This package is currently under active development. Some modules may not be fully implemented yet.*
