<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'identity_document',
        'deleted_at',
        'user_id',
    ];

    /**
     * Fechas que Eloquent tratará como instancias de Carbon.
     * (Con casts ya no es estrictamente necesario, pero puede declararse)
     */
    protected array $dates = [
        'birth_date',
        'deleted_at',
    ];

    /**
     * Casts de atributos.
     * - birth_date como 'date:Y-m-d' para que al convertir a array/json salga "YYYY-MM-DD".
     * - deleted_at con formato estándar de timestamp.
     */
    protected $casts = [
        'id' => 'integer',
        'birth_date' => 'date:Y-m-d',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    final public function getId(): int
    {
        return $this->id;
    }

    final public function getFirstName(): string
    {
        return is_null($this->first_name)
            ? $this->user->getName()
            : ucfirst($this->first_name);
    }

    final public function getLastName(): string
    {
        return is_null($this->last_name)
            ? 'No tiene datos'
            : ucfirst($this->last_name);
    }

    final public function getFullName(): string
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    final public function getBirthDate(): ?string
    {
        return $this->birth_date?->format('d-m-Y');
        // esto de arriba es lo mismo de abajo pero en php 8

        /*
        return $this->birth_date
            ? $this->birth_date->format('d-m-Y')
            : null;*/
    }

    final public function getBirthDateForm(): ?string
    {
        return $this->birth_date?->format('Y-m-d');
    }

    final public function getIdentityDocument(): string
    {
        return is_null($this->identity_document)
            ? 'No tiene datos'
            : $this->identity_document;
    }

    final public function setBirthDate(string $date): self
    {
        $this->birth_date = Carbon::parse($date);
        return $this;
    }

    final public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    // NUEVO: Relación inversa 1–1 con User
    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
