<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int|mixed $user_id
 */
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

    protected $appends = ['full_name', 'birth_date_format'];

    protected $dateFormat = 'Y-m-d H:i:s';

    final public function getId(): int
    {
        return $this->id;
    }

    final public function getFirstNameAttribute($value): string
    {
        return is_null($value)
            ? ucfirst($this->user->getName())
            : ucfirst($value);
    }

    final public function getLastNameAttribute($value): string
    {
        return is_null($value)
            ? 'No tiene datos'
            : ucfirst($value);
    }

    final public function getFullNameAttribute(): string
    {
        $full_name = ucfirst($this->first_name); // ya procesado por laravel, llama a este metódo getFirstNameAttribute

        // si el atributo last_name existe lo concateno con el 'first_name'.
        if(isset($this->attributes['last_name'])){ // "$this->attributes['last_name']", valor de la base de datos directamente
            $full_name .= ' ' . ucfirst($this->attributes['last_name']);
        }
        return $full_name;
    }


    final public function getBirthDateFormatAttribute($value): ?string
    {
    
        return isset($this->birth_date)
            ? $this->birth_date->format('d-m-Y')
            : null;
    }

    final public function getBirthDateForm(): ?string
    {
        return $this->birth_date?->format('Y-m-d');
    }

    final public function getIdentityDocumentAttribute($value): string
    {
        return is_null($value)
            ? 'No tiene datos'
            : $value;
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
