<?php

namespace App;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'table_no',
        'status',
        'client_session_id',
        'assigned_waiter'
    ];
}
