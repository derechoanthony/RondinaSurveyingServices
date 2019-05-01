<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    //
    protected $fillable = ['id','appointment_id','official_receipt','amount_render'];
}
