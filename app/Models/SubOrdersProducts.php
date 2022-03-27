<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubOrdersProducts extends Model
{
    protected $fillable = ['orders_item_id','sub_order_id','quantity'];
    //	orders_item_id created_at	updated_at	OrderItem

    public function item() {
        return $this->hasOne('App\Models\OrderItem','id','orders_item_id');
    }

}
