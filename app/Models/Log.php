<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'action',     // Acción realizada
        'detail',     // Detalles de la acción en formato JSON
        'ip',         // Dirección IP desde donde se realizó la acción
        'user_id',    // ID del usuario que realizó la acción
    ];

    /**
     * Obtiene el nombre del usuario que realizó la acción.
     *
     * @return string
     */
    public function getNameUserAttribute()
    {
        // Esta es una de las forma de usar un campo llamado name_user, en el modelo Log
        // se usario en una vista dee sta forma $log->name_user
        return User::find($this->user_id)->name;
    }

    /**
     * Relación con el modelo User.
     * Un log pertenece a un usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // En una relación con el modelo User(user), donde laravel
        // interpreta la función de la siguiente forma:
        // $log->user->name, al usar el nombre de la función user()
        // laravel realiza la relación (tabla)user_(id)id
        // y junta la función buscando ya u campo llamado user_id, en la tabla logs
        return $this->belongsTo(User::class);
    }

    /**
     * Relación alternativa con el modelo User.
     * Un log pertenece a un usuario (con clave foránea explícita).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        // En una relación con el modelo User(user), donde laravel
        // interpreta la función de la siguiente forma:
        // $log->usuario->name,
        // se debe indicar la relación con el campo user_id explícitamente,
        // ya que laravel intentara hacer la relación de esta forma usuario_id
        // el cual no existe en la tabla logs
        return $this->belongsTo(User::class, 'user_id'); // Indica explícitamente el campo
    }
}
