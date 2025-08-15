<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_connected',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    function getId()
    {
        return $this->attributes['id'];
    }

    function getName()
    {
        return $this->attributes['name'];
    }

    function getEmail()
    {
        return $this->attributes['email'];
    }

    function getPassword()
    {
        return $this->attributes['password'];
    }

    function getIsConnected()
    {
        return $this->attributes['is_connected'];
    }

    function setName($name)
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    function setEmail($email)
    {
        $this->attributes['email'] = $email;
        return $this;
    }

    function setPassword($password)
    {
        $this->attributes['password'] = $password;
        return $this;
    }

    function setIsConnected($is_connected)
    {
        $this->attributes['is_connected'] = $is_connected;
        return $this;
    }

    /**
     * Define a one-to-many relationship with the Log model.
     *
     * @return HasMany
     */
    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function userLogin()
    {
        return $this->hasMany(UserLogin::class);
    }

    // NUEVO: Relación 1–1 con Customer
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
