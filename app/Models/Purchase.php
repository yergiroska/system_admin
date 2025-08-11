<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para la tabla de compras.
 */
class Purchase extends Model
{
    protected $fillable = [
        'customer_id',
        'company_product_id',
    ];

    public function getId()
    {
        return $this->attributes['id'];
    }

    /**
     * Define la relación de pertenencia con el modelo Customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function companyProduct()
    {
        return $this->belongsTo(CompanyProduct::class, 'company_product_id');
    }

    final public function setCustomerId(int $customerId)
    {
        $this->attributes['customer_id'] = $customerId;
        return $this;
    }

    final public function setCompanyProductId(int $companyProductId)
    {
        $this->attributes['company_product_id'] = $companyProductId;
        return $this;
    }
}
