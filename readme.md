# Waliby (WA Gateway Library for Laravel)

This library requires Laravel > 5.8

## Installation

Use Composer

```bash
composer require ramatimati/waliby
```

Runing Migration
```bash 
php artisan migrate
```
or you can specific run migration of this package
``` bash
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105403_create_message_templates_table.php
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105510_create_message_histories_table.php
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105515_create_events_table.php
```

## Configuration
.env
```php
WALIBY_PHONE_BOOK=your table or view
WALIBY_PHONE_NUMBER_COLUMN=
WALIBY_NAME_COLUMN=
```

## Routes

Waliby included some routes

```php
https://example.com/waliby/templates
https://example.com/waliby/event

```

## Usage

You can simply call Waliby Class to run this library

```php
use Ramatimati\Waliby\Waliby;

public function test(){
   Waliby::SendMessage(type, endpoint, header, payload)
}
```

## Method
1. `GetMessage(array)`
   - `string` templateId => Message template id
   - `string` phoneNumber => Receiver phone number
2. `GetEvent(array)`
   - `string` templateId => Message template id
   - `string` eventId => Event id
3. `SendMessage(type, endpoint, header, payload)` 
   - `string` type => "POST" / "GET" / "PUT" / "PATCH" / "DELETE"
   - `string` endpoint => "https://example.com/example"
   - `array` header => Your request header
   - `array` payload => Your request payload 



## License

[MIT](https://choosealicense.com/licenses/mit/)

