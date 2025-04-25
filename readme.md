# Waliby (WA Gateway Library for Laravel)

## Requirement
- Laravel > 5.8
- Cron Jobs configuration, this package use laravel built in task scheduling to handle queued messages
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
- Laravel Queue configuration, use supervisor to keep your process running
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## Installation

#### Use Composer

```bash
composer require ramatimati/waliby

# or join the development program

composer require ramatimati/waliby:dev-main
```

#### Runing Migration
```bash 
php artisan migrate
```
or you can specific run migration of this package
``` bash
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105350_create_waliby_metas_table.php
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105403_create_message_templates_table.php
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105510_create_message_histories_table.php
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105515_create_events_table.php
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105545_create_jobs_table.php
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105555_create_job_logs_table.php
```

## Configuration
This package required base table or database view that contain phone number and name !\
.env `required`
```php
QUEUE_CONNECTION=database

# WALIBY PHONE BOOK PARAMS
WALIBY_PHONE_BOOK_CONNECTION=mysql
WALIBY_PHONE_BOOK=your table or database view
WALIBY_PHONE_NUMBER_COLUMN=
WALIBY_NAME_COLUMN=
WALIBY_COLUMN_CONDITION_NAME_1=required
#WALIBY_COLUMN_CONDITION_NAME_2=optional
#WALIBY_COLUMN_CONDITION_NAME_3=optional
#WALIBY_EVENT_PRIORITY_ID=optional // used to prioritize event in the waliby queue

# WALIBY WA GATEWAY
WALIBY_AUTH_TOKEN=
WALIBY_ENDPOINT_SINGLE_MESSAGE=https://example.com/send
WALIBY_WEBHOOK_MESSAGE_ID_KEY=id
WALIBY_WEBHOOK_STATUS_KEY=status
```

If you want to customize view, simply publish blade template from this package. Published file located in `resource/views/vendor/Waliby`
```bash
php artisan vendor:publish --tag=Waliby
```

## Routes

Waliby included some routes

```php
https://example.com/waliby/templates
https://example.com/waliby/events
https://example.com/waliby/history
```
Carefull with content in this page, very sensitif information and you should secure it from public!
```php
https://example.com/waliby/metas
```

#### Webhook
Waliby stores all activity on SendMessage function and included webhook url. Use this url to set webhook for updating message status. This url support `POST` or `GET` method
```php
https://example.com/api/waliby/history/stats
```

## Usage

You can simply call Waliby Class to run this library

```php
use Ramatimati\Waliby\Waliby;

public function test(){
   Waliby::SendMessage($phoneNumber, $messageTemplateName)
}
```
#### Task Scheduling
Waliby come with built in task scheduling every hour at 7, you can configure at *Waliby Event*, make sure you have configure cron job in your apllication

#### Available Method
1. `SendMessage($phoneNumber, $messageTemplateName)` 
This function only support sent single message
   - `string` phoneNumber => "62812xxxxxxxx"
   - `string` messageTemplateName => "template1"
2. `RemoveFromQueue($phoneNumber, $eventId)`
This function used to remove single pending message in waliby queue
   - `string` phoneNumber => "62812xxxxxxxx"
   - `string` eventId => nullable
## License

[MIT](license)

