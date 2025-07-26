<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'identity_document',
        'deleted_at'
    ];

    protected $hidden = ['birth_date'];
    protected $appends = ['formatted_birth_date'];

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }
}
