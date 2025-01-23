<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'waliby_job_logs';
    public $fillable = [
        'job_id', 'reserved_at', 'finished_at', 'status', 'exception'
    ];
}
