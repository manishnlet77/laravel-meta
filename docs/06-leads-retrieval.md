# Leads Retrieval

This package provides a streamlined API for fetching lead information submitted via Facebook Lead Ads.

## Fetching a Lead by ID

When a user submits a lead form on Facebook or Instagram, Meta will trigger a Webhook to your application. This webhook payload contains a `leadgen_id`. 

You can use the package to fetch the full details of this lead:

```php
use Vendor\LaravelMeta\Facades\Meta;

$leadId = '123456789012345';

try {
    $lead = Meta::leads()->get($leadId);
    
    // The Lead object automatically parses standard fields
    $email = $lead->getEmail();
    $phone = $lead->getPhoneNumber();
    $name  = $lead->getFullName();
    
    // You can also access custom questions/fields
    $companyName = $lead->getField('company_name');
    
    // Or just get all fields as an array
    $allData = $lead->getAllFields();
    
} catch (\Vendor\LaravelMeta\Core\Exceptions\MetaApiException $e) {
    // Handle API Error (e.g., Lead expired or token lacks permissions)
}
```

## Mapping Webhooks

Currently, you must set up a route in your application to receive the webhook POST payload from Meta. 

Once you extract the `leadgen_id` from the payload, you can dispatch a Job that uses `Meta::leads()->get($leadgen_id)` to pull the data and save it to your CRM.

*(Note: Ensure your Meta App has the `leads_retrieval` permission and that you have subscribed your webhook to the `leadgen` field in the Meta App Dashboard).*
