<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed|string $title
 */
class Note extends Model
{
    use SoftDeletes;

    protected $table = 'notes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'contents',
        'completed',
        'deleted_at'
    ];

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContents()
    {
        return $this->contents;
    }

    public function getCompleted()
    {
        return $this->completed;
    }

    public function isCompleted(): void
    {
        $this->completed = true;
    }

    public function isNotCompleted(): void
    {
        $this->completed = false;
    }

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }
}
