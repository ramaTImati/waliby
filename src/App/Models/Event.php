<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    use SoftDeletes;

    protected $table = 'waliby_events';
    public $fillable = [
        'event_name', 'message_template_id', 'to', 'event_status'
    ];

    public function template(){
        return $this->hasOne(MessageTemplate::class, 'id', 'message_template_id');
    }
}