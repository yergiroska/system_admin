<?php

namespace App\Models;

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

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(CompanyProduct::class)   // modelo pivot
            ->as('companyProduct')           // alias para acceder al pivot
            ->withPivot(['id', 'price'])               // incluye el id del pivot
            ->withTimestamps();           // descomenta si tu pivot tiene timestamps
    }
}
