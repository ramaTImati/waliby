<?php

namespace Ramatimati\Waliby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'waliby_meta';
    public $fillable = [
        'name', 'value'
    ];
}
