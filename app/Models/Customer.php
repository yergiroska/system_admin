<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'identity_document',
    ];

    protected $hidden = ['birth_date'];
    protected $appends = ['formatted_birth_date'];

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }
}
