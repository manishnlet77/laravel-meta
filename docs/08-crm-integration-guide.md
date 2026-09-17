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

> "I have installed the `meta-engine/laravel-meta` package and scaffolded the backend using `php artisan meta:install-crm`. I now have a `MetaCrmController` with methods `index()`, `getAnalytics()`, and `schedulePost()`.
> 
> Please generate a beautiful UI component for my CRM. The UI should have a sidebar with a 'Meta Dashboard' link. The main dashboard should contain three sections:
> 1. **Accounts Linked:** A table or card view displaying the linked Facebook and Instagram accounts (fetched from `MetaAccount`).
> 2. **Post Scheduler:** A form with fields for `meta_account_id` (dropdown), `media_type` (dropdown: text, image, video, reel), `media_url`, `message`, and `scheduled_for` (datetime picker). When submitted, it should POST to the `schedulePost` method.
> 3. **Recent Analytics:** A section that displays the top performing posts fetched from the `getAnalytics` method.
> 
> Please generate this UI using [INSERT YOUR STACK HERE: e.g., Vue.js with TailwindCSS / Laravel Blade with Bootstrap]."

---

## 3. Uninstallation (Rollback)

If you ever decide to remove the Meta integration from your CRM, you can easily clean up the database and codebase with a single command. 

*Warning: This will drop the tables and delete the generated models and controllers.*

```bash
php artisan meta:uninstall-crm
```
