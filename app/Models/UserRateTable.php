<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRateTable extends Model
{
    protected $table = "user_rate_tables";
    protected $fillable = ['user_id','rate_group_id'];
}
