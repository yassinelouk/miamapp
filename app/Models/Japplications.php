<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Japplications extends Model
{
    public $timestamps = false;

    protected $fillable = [
      "subject", 
      "slug", 
      "cv_name", 
      "content",
      "language_id"
     ];

     public function language() {
        return $this->belongsTo('App\Models\Language');
    }
}
