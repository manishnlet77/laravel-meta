# Meta (Facebook & Instagram) API Integration Documentation

Welcome to the `laravel-meta` documentation! To make navigation easier, the documentation has been split into modular files based on feature sets. 

Please refer to the specific guides below depending on what you are trying to accomplish:

## 📚 Table of Contents

1. **[Installation & Setup](docs/01-installation.md)**
   How to install the package and configure your database.

2. **[Token & Authentication](docs/04-token-and-authentication.md)**
   How to authenticate with Meta, store OAuth tokens, and configure Webhooks.

3. **[Publishing to Social Media](docs/02-publishing.md)**
   How to publish Text, Images, Videos, Carousels, and Reels to Facebook Pages and Instagram Business Accounts. (Includes asynchronous uploads).

4. **[Fetching & Filtering Media](docs/03-fetching.md)**
   How to fetch your published media. Includes examples of fetching the "Latest" vs "Top Trending" media and mapping it to your local database.

5. **[Leads Retrieval](docs/06-leads-retrieval.md)**
   Documentation on retrieving Leads from Facebook Lead Ads using Lead IDs.

6. **[Account Access Check & Troubleshooting](docs/05-account-access-check.md)**
   How to verify System User Tokens, check for required permissions, and troubleshoot common API errors like `APP_NOT_FOUND`.

7. **[Ads & Ad Account Management](docs/07-ads-management.md)**
   *(Draft)* Blueprint for managing Ad Accounts, Campaigns, and fetching Ads insights in the next major release.

8. **[CRM Integration & Scaffolding](docs/08-crm-integration-guide.md)**
   How to automatically scaffold the database tables (logs, schedules) and controllers directly into your own CRM, including an AI prompt for UI generation.

9. **[End-to-End Testing](docs/testing.md)**
   How to run the built-in Sandbox UI and Artisan CLI test commands (`php artisan meta:e2e-test`) to verify your application works before deploying to production.
