# Driver Creation Steps

This is a guide on what files you have to create/edit 
to implement a new driver for sending out SMS messages using this platform. 

> **Notice** &middot; Work in progress

This guide is intended for developers who wish to implement a new carrier provider API.

## Identity the API authentication requirements

This could be some combination of:

- `username`
- `password`
- `api_key`
- `account_id`
- `secret_token`
- `application_id`
- etc..

> This will vary from API to API

## Create Database Migration

Create a new migration by running the Laravel command:

```
php artisan make:migration add_example_fields_to_carrier --table=carriers
```

### database/migrations/YYYY_MM_DD_HHIISS_add_example_fields_to_carrier.php

Implement the `up()` and `down()` functions to include the fields needed 
to authenticate and make API calls for the API in question. 

`up()`:

```php
    Schema::table('carriers', function (Blueprint $table) {
        $table->string('example_api_username')->nullable();
        $table->text('example_api_password')->nullable();
        $table->text('example_api_account_id')->nullable();
        $table->text('example_api_application_id')->nullable();
    });
```

`down()`:

```php
    Schema::table('carriers', function (Blueprint $table) {
        $table->dropColumn([
            'example_api_username',
            'example_api_password', 
            'example_api_account_id', 
            'example_api_application_id'
        ]);
    });
```

> Use the *text* field type for secrets that should be encrypted

## app/Drivers/ExampleSMSDriver.php

1. Copy `app/Drivers/WebhookDriver.php` to `app/Drivers/ExampleSMSDriver.php`
2. Rename the class name to `ExampleSMSDriver`
3. Re-implement the methods based on the `SMSDriver` class interface

## app/Jobs/SendExampleSMS.php

1. Copy `app/Jobs/SendWebhookSMS.php` to `app/Jobs/SendExampleSMS.php`
2. Rename the class name to `SendExampleSMS`
3. Re-implement the job-class to send the SMS using the example API.

```diff
    public static array $supportedDrivers = [
        'twilio' => TwilioSMSDriver::class,
        'thinq' => ThinQSMSDriver::class,
        'webhook' => WebhookSMSDriver::class,
        'sunwire' => SunwireSMSDriver::class,
        'bandwidth' => BandwidthSMSDriver::class,
+       'example' => ExampleSMSDriver::class
    ];

```

Add the `ExampleSMSDriver` to the return types of the `loadDriver()` function:

```diff
-   public function loadDriver(): TwilioSMSDriver|ThinQSMSDriver|WebhookSMSDriver|SunwireSMSDriver|BandwidthSMSDriver
+   public function loadDriver(): TwilioSMSDriver|ThinQSMSDriver|WebhookSMSDriver|SunwireSMSDriver|BandwidthSMSDriver|ExampleSMSDriver
         
```

## Add Carrier UI

This section walks through adding the UI portions of adding/managing a carrier.

### public/images/example-badge.svg

Add an image (SVG/PNG) to be used for the carrier:

```
public/images/example-badge.svg
```

### resources/views/carriers/example-verify.blade.php

1. Copy `resources/views/carriers/webhook-verify.blade.php` to `resources/views/carriers/example-verify.blade.php`
2. Change the form to match the authentication values created

### resources/views/carriers/modals/forms/example.blade.php

1. Copy `resources/views/carriers/modals/forms/webhook.blade.php` to `resources/views/carriers/modals/forms/example.blade.php`
2. Change the form to match the authentication values created

### resources/views/carriers/modals/nav/example.blade.php

1. Copy `resources/views/carriers/modals/nav/webhook.blade.php` to `resources/views/carriers/modals/nav/example.blade.php`
2. Change the nav to customize the header shown on the UI carrier creation

### app/Drivers/DriverFactory.php

Create a *slug*, like `example`, to refer to the API driver 
and correlate it to the `ExampleSMSDriver::class` you copied:

### resources/views/carriers/modals/verify.blade.php

Add the `carriers.modals.nav.example` and `carriers.modals.forms.example` views:

```diff
     <ul class="nav nav-pills mb-3 nav-justified text-center" id="carriers-tab" role="tablist">
         @include('carriers.modals.nav.twilio')
         @include('carriers.modals.nav.thinq')
         @include('carriers.modals.nav.bandwidth')
         @include('carriers.modals.nav.sunwire')
         @include('carriers.modals.nav.webhook')
+        @include('carriers.modals.nav.example')
     </ul>
     <div class="tab-content" id="pills-tabContent">
         @include('carriers.modals.forms.twilio')
         @include('carriers.modals.forms.thinq')
         @include('carriers.modals.forms.bandwidth')
         @include('carriers.modals.forms.sunwire')
         @include('carriers.modals.forms.webhook')
+        @include('carriers.modals.forms.example')
     </div>
 ```
