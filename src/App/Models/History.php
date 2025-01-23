<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class History extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'waliby_message_histories';
    public $fillable = [
        'id', 'message_id', 'phone_number', 'message_text', 'status'
    ];
}