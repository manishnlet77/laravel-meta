# Meta Account Access Check

When communicating with the Meta Graph API, token validity and permissions are the number one cause of errors. This package provides tools to proactively verify access before attempting API calls.

## Visual Sandbox
The easiest way to check access is by starting your local server (`php artisan serve`) and navigating to `/meta/sandbox`. Click "Test Token" to see a visual readout of the token's validity, linked App ID, and a checklist of all granted permissions.

## CLI Testing
You can also verify access via the terminal. This is useful for deployment verification.

```bash
php artisan meta:test-token
```
*(This command checks the `META_SYSTEM_USERS_ACCESS_TOKEN` in your `.env` file).*

## Required Permissions

To use all features of this package, your System User Token must have the following permissions granted via the Meta Business Manager:

1. `pages_show_list`: Allows the app to see the list of Facebook Pages you manage.
2. `pages_read_engagement`: Required to read basic data (likes/comments) and fetch Facebook posts.
3. `pages_manage_posts`: Required to publish content (Text, Image, Video, Reel) to Facebook Pages.
4. `instagram_basic`: Allows the app to read basic information about connected Instagram Business accounts.
5. `instagram_content_publish`: Required to publish content to Instagram Business accounts.
6. `leads_retrieval`: Required to fetch Lead Ads data.

## Common Errors & Troubleshooting

### Error: `APP_NOT_FOUND` or "Error validating application"
```json
{"error":{"message":"Error validating application. Application has been deleted.","type":"OAuthException","code":190}}
```
**Cause:** The `META_DEVELOPER_APP_ID` in your `.env` file does not exist or has been deleted on Facebook's end. 
**Solution:** Go to developers.facebook.com, find your active App ID, update your `.env`, and generate a new System User Token associated with the new app.

### Error: "Unsupported post request. Object with ID does not exist"
**Cause:** You are trying to publish to a Page ID or Instagram Account ID that the System User Token does not have access to.
**Solution:** Ensure the System User has been assigned to the Facebook Page and Instagram Account in the Meta Business Manager settings under "Assigned Assets".

### Error: "An active access token must be used to query information"
**Cause:** Your token has expired.
**Solution:** System User Tokens can be set to "Never Expire". Generate a new token in the Business Manager and select the "Never Expire" option.
