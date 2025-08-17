<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

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
        'deleted_at',
        'user_id',
    ];

    protected $casts = [
        'birth_date' => 'datetime',
    ];

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return is_null($this->first_name)
            ? 'No tiene datos'
            : ucfirst($this->first_name);
    }

    public function getLastName()
    {
        return is_null($this->last_name)
            ? 'No tiene datos'
            : ucfirst($this->last_name);
    }

    public function getFullName()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getBirthDate()
    {
        return $this->birth_date?->format('d-m-Y');
        // esto de arriba es lo mismo de abajo pero en php 8

        /*
        return $this->birth_date
            ? $this->birth_date->format('d-m-Y')
            : null;*/
    }

    public function getIdentityDocument()
    {
        return is_null($this->identity_document)
            ? 'No tiene datos'
            : $this->identity_document;
    }

    public function setFirstName($name): Customer
    {
        $this->first_name = ucfirst($name);
        return $this;
    }

    public function setLastName($name)
    {
        $this->last_name = ucfirst($name);
        return $this;
    }

    public function setBirthDate($date)
    {
        $this->birth_date = Carbon::parse($date);
        return $this;
    }

    public function setIdentityDocument($document)
    {
        $this->identity_document = $document;
        return $this;
    }

    public function setUserId(int $userId): Customer
    {
        $this->user_id = $userId;
        return $this;
    }
    public function getFormattedBirthDateAttribute()
    {
        return $this->birth_date->format('d-m-Y');
    }

    final public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    // NUEVO: Relación inversa 1–1 con User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
