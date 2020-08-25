<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = ['name'];
    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}