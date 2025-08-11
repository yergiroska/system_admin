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
        'deleted_at'
    ];

    protected $hidden = ['birth_date'];
    protected $appends = ['formatted_birth_date'];

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getFirstName()
    {
        return ucfirst($this->attributes['first_name']);
    }

    public function getLastName()
    {
        return ucfirst($this->attributes['last_name']);
    }

    public function getFullName()
    {
        return ucfirst($this->attributes['first_name']) . ' ' . ucfirst($this->attributes['last_name']);
    }

    public function getBirthDate()
    {
        return Carbon::parse($this->attributes['birth_date'])->format('d-m-Y');
    }

    public function getIdentityDocument()
    {
        return $this->attributes['identity_document'];
    }


    public function setFirstName($name): Customer
    {
        $this->attributes['first_name'] = ucfirst($name);
        return $this;
    }

    public function setLastName($name)
    {
        $this->attributes['last_name'] = ucfirst($name);
        return $this;
    }

    public function setBirthDate($date)
    {
        $this->attributes['birth_date'] = Carbon::parse($date);
        return $this;
    }

    public function setIdentityDocument($document)
    {
        $this->attributes['identity_document'] = $document;
        return $this;
    }

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }

    final public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

}
