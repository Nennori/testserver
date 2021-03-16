<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [];
    public function boardusers(){
        return $this->hasMany('App\BoardUser');
    }
}
