<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getIsConnected()
    {
        return $this->is_connected;
    }


    public function setConnected()
    {
        $this->is_connected = 1;
        return $this;
    }

    public function setDisConnected()
    {
        $this->is_connected = 0;
        return $this;
    }

    public function setLastSession()
    {
        $this->last_session = Carbon::now('Europe/Madrid');
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
