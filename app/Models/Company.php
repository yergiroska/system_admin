<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'deleted_at'
    ];

    protected $hidden = ['birth_date'];
    protected $appends = ['formatted_birth_date'];

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function getDescription()
    {
        return $this->attributes['description'];
    }

    public function setName($name)
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function setDescription($description)
    {
        $this->attributes['description'] = $description;
        return $this;
    }

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(CompanyProduct::class)   // modelo pivot
            ->as('companyProduct')           // alias para acceder al pivot
            ->withPivot('id');               // incluye el id del pivot
            // ->withTimestamps();           // descomenta si tu pivot tiene timestamps
    }
}
