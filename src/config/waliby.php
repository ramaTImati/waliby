<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Waliby Phone Book
    |--------------------------------------------------------------------------
    */

    // 'phoneBookType' => env('WALIBY_PHONE_BOOK_TYPE', 'single'),
    // 'phoneBook1' => env('WALIBY_PHONE_BOOK_1', 'phonebook1'),
    // 'phoneBook1PrimaryKey' => env('WALIBY_PHONE_BOOK_1_PRIMARY_KEY', 'id'),
    // 'phoneBook2' => env('WALIBY_PHONE_BOOK_2', 'phonebook2'),
    // 'phoneBook2ForeignKey' => env('WALIBY_PHONE_BOOK_2_FOREIGN_KEY', 'id'),
    // 'phoneNumberReceiverNameColumn' => env('WALIBY_PHONE_BOOK_RECEIVER_NAME_COLUMN', 'name'),
    'phoneBookTable' => env('WALIBY_PHONE_BOOK', 'waliby'),
    'phoneNumberColumn' => env('WALIBY_PHONE_NUMBER_COLUMN', 'phone'),
    'nameColumn' => env('WALIBY_NAME_COLUMN', 'name'),

    /*
    |--------------------------------------------------------------------------
    | Waliby Default Models
    |--------------------------------------------------------------------------
    */
];