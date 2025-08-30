<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla de compras.
 * @property int $company_product_id
 * @property float|mixed $unit_price
 * @property int|mixed $quantity
 * @property float|mixed $total
 */
class Purchase extends Model
{
    protected $fillable = [
        'customer_id',
        'company_product_id',
        'unit_price',
        'quantity',
        'total',
        'created_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getId()
    {
        return $this->id;
    }

    public function getUnitPrice()
    {
        return $this->unit_price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotal()
    {
        return $this->total;
    }
    public function getCreatedAt()
    {
        return $this->created_at->format('d-m-Y');
    }
    /**
     * Define la relación de pertenencia con el modelo Customer.
     *
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function companyProduct()
    {
        return $this->belongsTo(CompanyProduct::class, 'company_product_id');
    }

}
