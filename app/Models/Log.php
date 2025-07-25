<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'action',
        'detail',
        'ip',
        'user_id',
    ];

    public function getNameUserAttribute()
    {
        return User::find($this->user_id)->name;
    }
}
