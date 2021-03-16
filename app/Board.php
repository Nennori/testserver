<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use Concerns\UsesUuid;
    protected $fillable = [
        'name',
    ];
    public function users(){
        return $this->belongsToMany(User::class)->withPivot('role_id');
    }
    public function tasks(){
        return $this->hasMany('App\Task');
    }
    public function statuses(){
        return $this->hasMany('App\Status');
    }
}
