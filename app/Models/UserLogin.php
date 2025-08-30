<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * Establece la conexión inicial configurando un valor timestamp con la fecha y hora actuales.
     *
     * Propósito:
     * Este método se encarga de definir el tiempo actual en la ubicación 'Europe/Madrid' como el valor
     * para la propiedad `$start_connection`, lo que podría ser útil para llevar registro del momento
     * exacto en el que se inicia una conexión o proceso.
     *
     * Contexto en proyectos reales:
     * - Este método es particularmente útil en aplicaciones donde es importante registrar o monitorear
     *   eventos temporales, como auditorías, logs o procesos que requieren control de tiempo.
     * - En sistemas distribuidos o en aplicaciones que trabajan en zonas horarias específicas, el método
     *   utiliza los servicios de Carbon para establecer tiempos unificados y coherentes.
     */
    public function setStartConnection(): void
    {
        $this->start_connection = Carbon::now('Europe/Madrid');
    }

    /**
     * Define el tiempo de finalización configurando un valor timestamp con la fecha y hora actuales.
     *
     * Propósito:
     * Este método asigna el tiempo actual en la zona horaria 'Europe/Madrid' a la propiedad `$end_connection`.
     * Su objetivo principal es registrar el momento exacto en que una conexión o proceso ha finalizado.
     *
     * Contexto en proyectos reales:
     * - Este método es útil en aplicaciones donde es necesario capturar la duración o el momento de cierre
     *   de ciertos procesos, como transacciones, sesiones de usuario o trabajos en cola.
     * - Es especialmente relevante en entornos donde el registro de eventos debe ser preciso y alineado
     *   con una zona horaria específica, lo cual es común en aplicaciones multi-región o procesos de auditoría.
     */
    public function setEndConnection(): void
    {
        $this->end_connection = Carbon::now('Europe/Madrid');
    }

}
