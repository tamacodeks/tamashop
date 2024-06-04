<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateTable extends Model
{
    protected $table = "rate_tables";
    protected $fillable = ["user_id","rate_group_id","cc_id","buying_price","sale_price",'sale_margin'];
}
