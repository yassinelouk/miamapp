<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    
  protected $fillable = [
        "product_orders_id",
        'type',
        'state'
       ];


    public function order() {
        return $this->belongsTo('App\Models\ProductOrder','product_orders_id');
    }

    public function products() {
        return $this->hasMany('App\Models\SubOrdersProducts','sub_order_id');
    }
}
