<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'waliby_jobs';
    public $fillable = [
        'event_id', 'phone_number', 'text'
    ];
}
