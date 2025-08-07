<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{

    protected $table = 'user_logins';

    protected $fillable = [
        'start_connection',
        'end_connection',
        'user_id',
    ];

    public function user()
    {
        // En una relación con el modelo User(user), donde laravel
        // interpreta la función de la siguiente forma:
        // $log->usuario->name,
        // se debe indicar la relación con el campo user_id explícitamente,
        // ya que laravel intentara hacer la relación de esta forma usuario_id
        // el cual no existe en la tabla logs
        return $this->belongsTo(User::class); // Indica explícitamente el campo
    }
}
