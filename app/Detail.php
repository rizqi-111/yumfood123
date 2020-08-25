<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $fillable = ['dish_id','quantity'];
    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }

    public function dish(){
        return $this->hasOne('App\Dish');
    }
}
