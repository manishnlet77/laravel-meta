# Testing and Validation

The `laravel-meta` package provides robust testing tools to ensure your application can safely communicate with the Meta Graph API.

## 1. The Visual Sandbox UI
The easiest way to test your token and integration is via the **Sandbox UI**.
By default, this is enabled locally.

1. Open your browser and navigate to `/meta/sandbox` on your application's domain (e.g., `http://localhost:8000/meta/sandbox`).
2. The UI allows you to test tokens, fetch Leads, and publish to Facebook without writing any code.

*Note: Ensure `META_SANDBOX_ENABLED=true` is in your `.env`.*

## 2. Artisan CLI Tests
If you prefer the command line, the package includes diagnostic Artisan commands.

### Test Token Connection
Validates the current token in your `.env` against the Meta Graph API, returning the connected User/Page and assigned permissions.

```bash
php artisan meta:test-token
```

Output:
```text
=================================
 META TOKEN TEST
=================================
[PASS] Access token configuration found
[PASS] Graph API reachable
[PASS] Token is valid
[PASS] Token verified against App ID

Permissions granted:
  - leads_retrieval
  - pages_show_list
  - pages_read_engagement

=================================
 RESULT: PASS
=================================
```

You can also pass a token directly to the command:
```bash
php artisan meta:test-token --token="your-custom-token-here"
```

## 3. Automated Tests (PHPUnit)
If you are developing the package itself, you can run the internal PHPUnit test suite.

```bash
cd packages/laravel-meta
vendor/bin/phpunit
```

*More testing commands will be added as new modules (Facebook, Instagram, Ads) are completed.*
