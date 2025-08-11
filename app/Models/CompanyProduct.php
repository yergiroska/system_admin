<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyProduct extends Pivot

{
    protected $table = 'company_product';

    // Si tu tabla pivot tiene columna 'id' autoincremental:
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Pon en true si tu tabla pivot tiene created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'id',
        'company_id',
        'product_id',
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

}
