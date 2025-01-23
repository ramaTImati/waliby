# Waliby (WA Gateway Library for Laravel)

Laravel > 5.8

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
php artisan migrate --path=/vendor/ramatimati/waliby/src/database/migrations/2024_08_17_105555_job_logs_table.php
```

## Configuration
This package required base table or database view that contain phone number and name !\
.env `required`
```php
# WALIBY PHONE BOOK PARAMS
WALIBY_PHONE_BOOK=your table or database view
WALIBY_PHONE_NUMBER_COLUMN=
WALIBY_NAME_COLUMN=
WALIBY_COLUMN_CONDITION_NAME_1=gender
WALIBY_COLUMN_CONDITION_NAME_2=faculty
WALIBY_COLUMN_CONDITION_NAME_3=class

# WALIBY WA GATEWAY
WALIBY_AUTH_TOKEN=u1PxBCwJXf9b2-UJ4m1M
WALIBY_ENDPOINT_BULK_MESSAGE=https://api.fonnte.com/send
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
   Waliby::SendMessage(type, endpoint, header, payload)
}
```

#### Available Method
1. `GetMessage($array)`
This function use to get message template base from `https://example.com/waliby/templates`
   - `string` templateName => Unique message template name
   - `array` phoneNumber => Receiver phone number. example ['0823xxxx....', '0853xxxx....']
2. `GetEvent($array)`
Under Construction
   - `string` templateId => Message template id
   - `string` eventId => Event id
3. `SendMessage($type, $endpoint, $header, $payload)` 
This function support single and multiple send message
   - `string` type => "POST" / "GET" / "PUT" / "PATCH" / "DELETE"
   - `string` endpoint => "https://example.com/example"
   - `array` header => Your request header
   - `array` payload => Your request payload 
4. `History($params)`
This function use to store message history from `SendMessage` function response
   - `array` params => array must contain some keys `message_id`, `phone_number`, `message_text`, `status`
      - `message_id` => required
      - `phone_number` => required
      - `message_text` => optional
      - `status` => required
   - If you want to store multiple, simply write multidimensional array like this
      ```php
      $params = [
         [
            'message_id' => 'e11e7377-ac7a-405e-b520-16dd1f22f204',
            'phone_number' => '62898xxx...',
            'message_text' => 'test',
            'status' => 'pending',
            'created_at' => new dateTime,
            'updated_at' => new dateTime,
         ],
         [
            'message_id' => 'ce947524-c641-4c54-90b5-9eb3b714a3fe',
            'phone_number' => '62858xxx...',
            'message_text' => 'test',
            'status' => 'pending',
            'created_at' => new dateTime,
            'updated_at' => new dateTime,
         ],
      ]
      ```


## License

[MIT](license)

