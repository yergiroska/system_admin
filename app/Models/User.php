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
        return $this->id;
    }

    function getName()
    {
        return $this->name;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getIsConnected()
    {
        return $this->is_connected;
    }

    function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    function setIsConnected($is_connected)
    {
        $this->is_connected = $is_connected;
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
