<?php

namespace Ramatimati\Waliby\App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model {
    use SoftDeletes;

    protected $table = 'waliby_message_templates';
    public $incrementing = false;
    public $fillable = [
        'id', 'message', 'table', 'created_by'
    ];
}