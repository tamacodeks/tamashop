<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelecomCountry extends Model
{
    protected $fillable = [
        'country_id',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
}
