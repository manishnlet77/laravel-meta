# Comprehensive API Testing Guide

The `laravel-meta` package provides robust testing tools to ensure your application can safely communicate with every aspect of the Meta Graph API. 

This guide explains how to test all API endpoints (Tokens, Facebook Pages, Instagram, and Leads) using the tools provided.

---

## 1. The Visual Sandbox UI (Recommended)

We have built a dedicated visual Sandbox directly into the package so you can test all API calls without writing a single line of code or needing external tools like Postman or Swagger.

By default, this is enabled locally.

1. Start your local Laravel server: `php artisan serve`
2. Open your browser and navigate to `/meta/sandbox` (e.g., `http://localhost:8000/meta/sandbox`).
3. You will see an interactive dashboard designed specifically for testing Meta APIs.

### What you can test in the Sandbox:

#### A. Test Token Validity & Permissions
Before making any API calls, you need to know if your token is valid and what it can do.
* **How to test:** Click "Test Token" on the Sandbox dashboard.
* **What it checks:** Validates the token, identifies the associated App ID, and lists exactly which permissions have been granted (e.g., `pages_manage_posts`, `leads_retrieval`).

#### B. Test Lead Retrieval (Lead Ads API)
Simulate exactly what happens when a lead submits a form on Facebook.
* **How to test:** In the Sandbox, under the "Lead Retrieval" section, enter a valid Lead ID.
* **What it checks:** Contacts the Graph API, fetches the lead, and displays the raw JSON data that Meta returns (including email, phone number, and custom questions).

#### C. Test Facebook Page Publishing (Facebook API)
Verify that your token has permission to post as a page.
* **How to test:** Under the "Publish to Facebook" section, enter your Page ID and a test message.
* **What it checks:** Attempts to publish a live post to the specified Facebook page and returns the new Post ID on success.

*(Note: Ensure `META_SANDBOX_ENABLED=true` is in your `.env` to access this UI.)*

---

## 2. Artisan CLI Tests

If you prefer the command line, the package includes diagnostic Artisan commands for quick testing.

### Test Token Connection
Validates the current token in your `.env` against the Meta Graph API.

```bash
php artisan meta:test-token
```

**Output Example:**
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
```

You can also test a specific token without saving it to `.env`:
```bash
php artisan meta:test-token --token="your-custom-token-here"
```

---

## 3. Writing Code to Test APIs (Direct API Mode)

Once you've visually tested the APIs using the Sandbox, you can write the actual PHP code in your application to perform these actions.

*(Note: These modules are currently in active development. Below is how the final API will look.)*

### Testing Facebook Page Publishing
```php
use Vendor\LaravelMeta\Facades\Meta;

// Fetch Page Details
$page = Meta::facebook()->page('1234567890');
echo $page->getName();

// Publish Text
$postId = Meta::facebook()->page('1234567890')->publishText('Hello from Laravel!');
```

### Testing Instagram Media Publishing
```php
// Publish an Image
$mediaId = Meta::instagram()->account('0987654321')->publishImage(
    'https://example.com/image.jpg', 
    'Check out our new product! #laravel'
);
```

### Testing Lead Retrieval
```php
// Fetch a Lead
$lead = Meta::leads()->get('5551234567');
echo $lead->getEmail();
```

---

## 4. Automated Tests (PHPUnit)

If you are contributing to the package itself or writing custom automated tests for your application, the package includes a mockable core.

To run the package's internal test suite:
```bash
cd vendor/meta-engine/laravel-meta
vendor/bin/phpunit
# Comprehensive API Testing Guide

The `laravel-meta` package provides robust testing tools to ensure your application can safely communicate with every aspect of the Meta Graph API. 

This guide explains how to test all API endpoints (Tokens, Facebook Pages, Instagram, and Leads) using the tools provided.

---

## 1. The Visual Sandbox UI (Recommended)

We have built a dedicated visual Sandbox directly into the package so you can test all API calls without writing a single line of code or needing external tools like Postman or Swagger.

By default, this is enabled locally.

1. Start your local Laravel server: `php artisan serve`
2. Open your browser and navigate to `/meta/sandbox` (e.g., `http://localhost:8000/meta/sandbox`).
3. You will see an interactive dashboard designed specifically for testing Meta APIs.

### What you can test in the Sandbox:

#### A. Test Token Validity & Permissions
Before making any API calls, you need to know if your token is valid and what it can do.
* **How to test:** Click "Test Token" on the Sandbox dashboard.
* **What it checks:** Validates the token, identifies the associated App ID, and lists exactly which permissions have been granted (e.g., `pages_manage_posts`, `leads_retrieval`).

#### B. Test Lead Retrieval (Lead Ads API)
Simulate exactly what happens when a lead submits a form on Facebook.
* **How to test:** In the Sandbox, under the "Lead Retrieval" section, enter a valid Lead ID.
* **What it checks:** Contacts the Graph API, fetches the lead, and displays the raw JSON data that Meta returns (including email, phone number, and custom questions).

#### C. Test Facebook Page Publishing (Facebook API)
Verify that your token has permission to post as a page.
* **How to test:** Under the "Publish to Facebook" section, enter your Page ID and a test message.
* **What it checks:** Attempts to publish a live post to the specified Facebook page and returns the new Post ID on success.

*(Note: Ensure `META_SANDBOX_ENABLED=true` is in your `.env` to access this UI.)*

---

## 2. Artisan CLI Tests

If you prefer the command line, the package includes diagnostic Artisan commands for quick testing.

### Test Token Connection
Validates the current token in your `.env` against the Meta Graph API.

```bash
php artisan meta:test-token
```

**Output Example:**
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
```

You can also test a specific token without saving it to `.env`:
```bash
php artisan meta:test-token --token="your-custom-token-here"
```

---

## 3. Writing Code to Test APIs (Direct API Mode)

Once you've visually tested the APIs using the Sandbox, you can write the actual PHP code in your application to perform these actions.

*(Note: These modules are currently in active development. Below is how the final API will look.)*

### Testing Facebook Page Publishing
```php
use Vendor\LaravelMeta\Facades\Meta;

// Fetch Page Details
$page = Meta::facebook()->page('1234567890');
echo $page->getName();

// Publish Text
$postId = Meta::facebook()->page('1234567890')->publishText('Hello from Laravel!');
```

### Testing Instagram Media Publishing
```php
// Publish an Image
$mediaId = Meta::instagram()->account('0987654321')->publishImage(
    'https://example.com/image.jpg', 
    'Check out our new product! #laravel'
);
```

### Testing Lead Retrieval
```php
// Fetch a Lead
$lead = Meta::leads()->get('5551234567');
echo $lead->getEmail();
```

---

## 4. Automated Tests (PHPUnit)

If you are contributing to the package itself or writing custom automated tests for your application, the package includes a mockable core.

To run the package's internal test suite:
```bash
cd vendor/meta-engine/laravel-meta
vendor/bin/phpunit
```

## 5. End-to-End Automated Testing

The package also includes a comprehensive, built-in automated testing command. This command verifies that your credentials work, generates lightweight dummy media (`.jpg` and `.mp4`), and attempts to publish all possible formats (Text, Images, Videos, Reels) to Facebook and Instagram. It then fetches them to verify and cleans up Facebook posts automatically.

### Running the Test Locally

If you have installed the package via Composer (`composer require meta-engine/laravel-meta`), you can run the test directly from your artisan console:

```bash
# Run all tests (Publishing, Fetching, Cleanup)
php artisan meta:e2e-test

# Run ONLY Publishing tests
php artisan meta:e2e-test --publish

# Run ONLY Fetching tests
php artisan meta:e2e-test --fetch
```

**Requirements:**
1. Your `.env` file must be populated with `META_DEVELOPER_APP_ID`, `META_DEVELOPER_APP_SECRET`, and a valid `META_SYSTEM_USERS_ACCESS_TOKEN`.
2. The System User Token must have the correct permissions (`pages_manage_posts`, `instagram_content_publish`, etc.).

*Note: Instagram's Graph API does not support programmatic deletion, so the test script will output the IDs of the generated Instagram posts and ask you to delete them manually via the Instagram app.*

By using the Sandbox UI and these testing strategies, you can confidently integrate with Meta's APIs without the headache of manual API debugging!
