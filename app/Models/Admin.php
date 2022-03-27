<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'username', 'email', 'password', 'first_name', 'last_name', 'image', 'status'
    ];


    public function role() {
      return $this->belongsTo('App\Models\Role');
    }
    public function calendars() {
        return $this->belongsTo('App\Models\Calendar');
    }

}
