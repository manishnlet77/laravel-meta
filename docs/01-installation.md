# Installation Guide

To install the `laravel-meta` package into your Laravel application, follow these simple steps.

## Requirements
* PHP 8.2 or 8.3+
* Laravel 10.x, 11.x, or 12.x

## 1. Install via Composer

If you are installing from GitHub, require the latest `main` branch to get the most recent features (like CRM Scaffolding) until the next stable release is tagged:
```bash
composer require meta-engine/laravel-meta:dev-main
```

## 2. Publish Configuration

Publish the `meta.php` config file to your application's `config/` directory:

```bash
php artisan vendor:publish --tag=meta-config
```

## 3. Environment Variables

Open your application's `.env` file and add the following keys. You only need to configure what you actually plan to use.

```env
# Required for all API requests
META_GRAPH_VERSION=v19.0
META_APP_ID=your_app_id
META_APP_SECRET=your_app_secret
META_APPSECRET_PROOF_ENABLED=true

# For Direct API Mode (The token you generated for the System User)
META_SYSTEM_USER_TOKEN=your_token

# For Webhook Integration
META_WEBHOOK_VERIFY_TOKEN=your_custom_webhook_secret

# For Testing / Visual Sandbox UI
META_SANDBOX_ENABLED=true
```

## 4. Run Migrations (Optional)
If you intend to use the **Persistent Integration Mode** (to save webhooks and schedule posts locally), you will need to run the migrations.

Ensure `META_DATABASE_ENABLED=true` is set, then run:
```bash
php artisan migrate
```

*Note: If you only want to use Direct API Mode, you do not need to run this command.*

---

**Next Step:** Proceed to [Token and Authentication Guide](04-token-and-authentication.md) to learn how to get your `META_SYSTEM_USER_TOKEN`.
