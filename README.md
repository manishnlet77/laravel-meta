# Laravel Meta Integration Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/meta-engine/laravel-meta.svg?style=flat-square)](https://packagist.org/packages/meta-engine/laravel-meta)
[![Total Downloads](https://img.shields.io/packagist/dt/meta-engine/laravel-meta.svg?style=flat-square)](https://packagist.org/packages/meta-engine/laravel-meta)
[![License](https://img.shields.io/packagist/l/meta-engine/laravel-meta.svg?style=flat-square)](https://packagist.org/packages/meta-engine/laravel-meta)

A modular, highly-independent Meta (Facebook & Instagram) API SDK and integration package for Laravel. 

This package provides a unified interface for interacting with the Meta Graph API, supporting everything from simple automated posts to complex Lead Ads webhooks, without forcing unnecessary database tables into your application.

### 🌟 Key Features
- **Facebook Page Publishing**: Publish Text, Images, Videos, Carousels, Stories, and Reels (including 3-step chunked upload).
- **Instagram Publishing**: Publish Images, Videos, Carousels, Stories, and Reels (using async container polling). *(Note: Meta API requires media for IG, no text-only posts).*
- **Lead Ads & Webhooks**: Integrated webhook support for real-time events.
- **Visual Sandbox**: Built-in UI to test tokens, fetch leads, and test posts directly.

---

## 🚀 1. Installation

Install the package globally via Composer:

```bash
composer require meta-engine/laravel-meta
```

*(For Laravel 10.x, 11.x, and 12.x)*

## ⚙️ 2. Configuration

Publish the package configuration file to your application's `config/` directory:

```bash
php artisan vendor:publish --tag=meta-config
```

Next, open your application's `.env` file and add the required Meta credentials:

```env
# Required for App Secret Proof (Security) and core API requests
META_GRAPH_VERSION=v19.0
META_APP_ID=your_meta_app_id
META_APP_SECRET=your_meta_app_secret
META_APPSECRET_PROOF_ENABLED=true

# (Optional) The token generated for your System User
META_SYSTEM_USER_TOKEN=your_long_lived_system_user_token

# (Optional) Enable the visual testing UI (Keep false in production!)
META_SANDBOX_ENABLED=true
```

## 🔑 3. Getting Your Token & Permissions
If you do not have a `META_SYSTEM_USER_TOKEN` yet, you must generate one from the Meta Business Settings. 

The token must be a **System User Access Token** (never a temporary user token) to ensure your server can make automated background requests without expiring.

> 👉 **[Click here to read the full Step-by-Step Token & Permissions Guide](docs/04-token-and-authentication.md)**

---

## 🧪 4. Testing Your Integration

This package ships with built-in tools to instantly verify if your connection and token are valid.

### Option A: The Visual Sandbox UI (Recommended)
If you added `META_SANDBOX_ENABLED=true` to your `.env`, you can test your integration directly from your browser!

1. Start your local server (`php artisan serve`).
2. Go to **`http://localhost:8000/meta/sandbox`**.
3. You will see a beautiful dashboard where you can:
   - Validate your token and instantly see what permissions it holds.
   - Fetch a test Lead ID directly from Meta.
   - Publish a test post to a Facebook Page.

### Option B: The Artisan CLI Test
If you prefer the command line, run the built-in diagnostic tool:

```bash
php artisan meta:test-token
```
You will receive a clear pass/fail report confirming if your App ID, Secret, and Token are correctly communicating with the Graph API.

---

## 🏗️ 5. Architecture & Usage Modes

This package natively supports two usage modes depending on your application's architecture:

### Direct API Mode (No Database Required)
Your CRM/Application directly calls Meta APIs through our services.
* Perfect for simple integrations (e.g., just fetching a single lead).
* No migrations required (`META_DATABASE_ENABLED=false`).

```php
// Example: Direct API Lead Retrieval
$lead = Meta::leads()->get('123456789');

// Map the data to your own CRM tables manually
CRMLead::create([
    'name' => $lead->getName(),
    'email' => $lead->getEmail(),
]);
```

### Persistent Integration Mode (Advanced)
Provides a robust synchronization layer using local database tables.
* Webhook storage and processing.
* Event dispatching (e.g., `MetaLeadReceived`).
* Automated Queue scheduling and retries for failed posts.
* Requires running package migrations (`META_DATABASE_ENABLED=true`).

---

## 📚 Advanced Documentation

Detailed documentation for specific modules can be found in the `docs/` folder:

* [Installation Guide](docs/01-installation.md)
* [Token and Authentication Guide](docs/04-token-and-authentication.md)
* [Testing Guide](docs/testing.md)

---
*Developed by ManishNlet77. Open source under the MIT License.*
