<?php

namespace App\Models;

use App\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use UsesUuid;

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role_id');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function statuses()
    {
        return $this->hasMany('App\Models\Status');
    }

    public function marks()
    {
        return $this->hasMany('App\Models\Mark');
    }
}
