<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoardUser extends Model
{
    protected $fillable=[];

    public function role(){
        return $this->belongsTo('App\Role');
    }
}

