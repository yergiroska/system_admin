<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setContents($contents)
    {
        $this->contents = $contents;
        return $this;
    }

    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    public function isCompleted()
    {
        $this->completed = true;
    }

    public function isNotCompleted()
    {
        $this->completed = false;
    }

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }
}
