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

    protected $hidden = ['birth_date'];
    protected $appends = ['formatted_birth_date'];

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getTitle()
    {
        return $this->attributes['title'];
    }

    public function getContents()
    {
        return $this->attributes['contents'];
    }

    public function getCompleted()
    {
        return $this->attributes['completed'];
    }

    public function setTitle($title)
    {
        $this->attributes['title'] = $title;
        return $this;
    }

    public function setContents($contents)
    {
        $this->attributes['contents'] = $contents;
        return $this;
    }

    public function setCompleted($completed)
    {
        $this->attributes['completed'] = $completed;
    }

    public function isCompleted()
    {
        $this->attributes['completed'] = true;
    }

    public function isNotCompleted()
    {
        $this->attributes['completed'] = false;
    }

    public function getFormattedBirthDateAttribute()
    {
        return Carbon::parse($this->birth_date)->format('d-m-Y');
    }
}
