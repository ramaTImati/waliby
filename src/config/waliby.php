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
    
    /*
    |--------------------------------------------------------------------------
    | Waliby Phone Book
    |--------------------------------------------------------------------------
    */
    'responseType' => env('WALIBY_RESPONSE_TYPE', 'standalone'),
    'responseMessageIdKey' => env('WALIBY_RESPONSE_MESSAGE_ID_KEY', 'id'),
    'responsePhoneKey' => env('WALIBY_RESPONSE_PHONE_KEY', 'id'),
    'responseStatusKey' => env('WALIBY_RESPONSE_STATUS_KEY', 'status'),
    
    /*
    |--------------------------------------------------------------------------
    | Waliby Webhook
    |--------------------------------------------------------------------------
    */
    'webhookMessageId' => env('WALIBY_WEBHOOK_MESSAGE_ID_KEY', 'id'),
    'webhookStatus' => env('WALIBY_WEBHOOK_STATUS_KEY', 'status'),
];