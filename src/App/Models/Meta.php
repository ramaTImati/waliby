<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use SoftDeletes;

    protected $table = 'waliby_metas';
    public $fillable = [
        'name', 'value'
    ];
}
