<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;
    protected $table = 'notes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'contents',
        'completed',
        'deleted_at'
    ];

    protected $hidden = ['birth_date'];
    protected $appends = ['formatted_birth_date'];

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }
}
