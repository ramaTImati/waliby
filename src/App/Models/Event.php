<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'waliby_events';
    public $fillable = [
        'event_name', 'event_type', 'message_template_id', 'receiver_params', 'last_processed', 'scheduled_every', 'scheduled_at'
    ];

    public function template(){
        return $this->hasOne(MessageTemplate::class, 'id', 'message_template_id');
    }

    public function job_log(){
        return $this->hasMany(JobLog::class, 'event_id', 'id');
    }
}