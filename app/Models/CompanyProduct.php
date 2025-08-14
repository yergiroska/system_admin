<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyProduct extends Pivot

{
    protected $table = 'company_product';

    // Si tu tabla pivot tiene columna 'id' autoincremental:
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Pon en true si tu tabla pivot tiene created_at/updated_at
    public $timestamps = true;

    protected $fillable = [
        'id',
        'company_id',
        'product_id',
        'price',
    ];


    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getCompanyId()
    {
        return $this->attributes['company_id'];
    }

    public function getProductId()
    {
        return $this->attributes['product_id'];
    }

    public function getPrice()
    {
        return $this->attributes['price'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
