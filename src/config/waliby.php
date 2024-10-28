<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Waliby Phone Book
    |--------------------------------------------------------------------------
    */
    
    'phoneBookTable' => env('WALIBY_PHONE_BOOK', 'waliby'),
    'phoneNumberColumn' => env('WALIBY_PHONE_NUMBER_COLUMN', 'nomor'),
    'nameColumn' => env('WALIBY_NAME_COLUMN', 'nama'),
    'webhookMessageId' => env('WALIBY_WEBHOOK_MESSAGE_ID_KEY', 'id'),
    'webhookStatus' => env('WALIBY_WEBHOOK_STATUS_KEY', 'status'),

    /*
    |--------------------------------------------------------------------------
    | Waliby Default Models
    |--------------------------------------------------------------------------
    */
];