<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'deleted_at'
    ];

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

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->using(CompanyProduct::class) // modelo pivot
            ->as('companyProduct') // alias para acceder al pivot
            ->withPivot(['id', 'price']) // incluye el id del pivot
            ->withTimestamps(); // descomenta si tu pivot tiene timestamps
    }
}
