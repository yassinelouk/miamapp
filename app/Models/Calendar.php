<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{

    protected $fillable = [
      "event_name",
      "starting_date",
      "ending_date",
      "id_admin",
     ];

    public function employee() {
        return $this->hasOne('App\Models\Admin', 'id', 'id_admin');
    }
}
