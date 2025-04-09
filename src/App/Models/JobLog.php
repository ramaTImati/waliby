<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    use SoftDeletes;

    protected $table = 'waliby_job_logs';
    public $fillable = [
        'event_id', 'phone_number', 'text', 'reserved_at', 'finished_at', 'status', 'exception'
    ];
}
