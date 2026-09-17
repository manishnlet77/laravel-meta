# CRM Integration Guide & Blueprint

The `laravel-meta` package comes with an automated scaffolding system that allows you to instantly integrate Meta features directly into your own custom CRM (Customer Relationship Management) system.

## 1. Automated Scaffolding

Instead of writing database tables, models, and controllers from scratch, you can run a single command to generate the foundational backend required for a CRM.

```bash
php artisan meta:install-crm
```

### What does this do?
1. **Migrations:** Publishes three tables to your `database/migrations` folder:
   - `meta_accounts`: Stores connected Facebook/Instagram accounts and tokens.
   - `meta_post_schedules`: Stores scheduled posts (images, videos, reels).
   - `meta_api_logs`: A table to log successful and failed API requests for debugging.
2. **Models:** Publishes `MetaAccount`, `MetaPostSchedule`, and `MetaApiLog` to your `app/Models` directory.
3. **Controller:** Publishes `MetaCrmController` to your `app/Http/Controllers` directory. This controller includes methods for fetching accounts, getting analytics, and scheduling posts.

*(After running the command, simply run `php artisan migrate` to create the tables in your database).*

## 2. Building the UI

Because every CRM uses a different frontend stack (Vue, React, Livewire, Blade), we don't force a specific UI on you. 

Once you have scaffolded the backend, you simply need to build the following views in your application:

1. **Sidebar Menu:** A link to your Meta Dashboard.
2. **Accounts View:** A page that lists all connected accounts (fetch from the `MetaAccount` model).
3. **Post Scheduler View:** A form that submits to the `MetaCrmController@schedulePost` method. The form should include fields for `meta_account_id`, `media_type`, `media_url`, `message`, and `scheduled_for`.
4. **Logs View:** A simple table reading from the `MetaApiLog` model to show the status of API requests.

## 3. Uninstallation (Rollback)

If you ever decide to remove the Meta integration from your CRM, you can easily clean up the database and codebase with a single command. 

*Warning: This will drop the tables and delete the generated models and controllers.*

```bash
php artisan meta:uninstall-crm
```
