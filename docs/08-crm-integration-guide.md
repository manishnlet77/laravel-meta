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

## 2. Generating the UI (The AI Prompt)

Because every CRM uses a different frontend stack (Vue, React, Livewire, Blade), we don't force a specific UI on you. Instead, **copy the prompt below** and paste it into your favorite AI coding assistant (like Gemini, ChatGPT, or Claude) inside your project. The AI will instantly generate the perfect UI for your stack!

---
**Copy and paste this prompt into your AI IDE:**

> "I am working on a custom CRM (located at `http://your-local-url/admin/dashboard`). I have already installed a package via `composer require meta-engine/laravel-meta`, run `php artisan meta:install-crm`, and added my Meta App credentials to my `.env` file. 
>
> The backend is completely ready. I now have a `MetaCrmController` and three models: `MetaAccount`, `MetaPostSchedule`, and `MetaApiLog`.
> 
> Please update my CRM's frontend to integrate this:
> 
> 1. **Sidebar Menu:** Add a new menu item in my CRM's sidebar called 'Meta Social Manager' with a Facebook/Instagram icon. Under it, add two submenus: 'Accounts' and 'Post Scheduler'.
> 2. **Accounts View:** Create a view that lists all linked Facebook and Instagram accounts from the `meta_accounts` table.
> 3. **Post Scheduler View:** Create a view with a form to schedule a post. The form should have:
>    - Account Dropdown (fetching from `meta_accounts`)
>    - Media Type Dropdown (Text, Image, Video, Reel)
>    - Media URL input
>    - Message/Caption textarea
>    - Scheduled Date & Time picker
> 4. **Logs View:** Add a simple table at the bottom of the scheduler page that reads from `meta_api_logs` to show if a post succeeded or failed.
>
> Please generate the exact [Vue/React/Blade] templates and Sidebar HTML/CSS required to add this to my existing CRM."

---

## 3. Uninstallation (Rollback)

If you ever decide to remove the Meta integration from your CRM, you can easily clean up the database and codebase with a single command. 

*Warning: This will drop the tables and delete the generated models and controllers.*

```bash
php artisan meta:uninstall-crm
```
