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
        'image_url',
        'deleted_at'
    ];

    public function getImageUrlAttribute($value)
    {
        return $value ? asset('storage/images/' . $value) : null;
    }

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

    public function companiesProducts(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->using(CompanyProduct::class) // modelo pivot
            ->as('companyProduct') // alias para acceder al pivot
            ->withPivot(['id', 'price']) // incluye el id del pivot
            ->withTimestamps(); // descomenta si tu pivot tiene timestamps
    }

    public function getImageNameAttribute()
    {
        return $this->attributes['image_url'];// Accede directamente al atributo de la base de datos
        //return $this->image_url; // Usa el accesor para obtener la URL completa
    }
}
