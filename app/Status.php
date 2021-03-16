<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use Concerns\UsesUuid;
    protected $fillable = [
        'name'
    ];
    public $incrementing = false;
    protected $keyType = 'string';
    public function tasks(){
        return $this->hasMany('App\Task');
    }
    public function board(){
        return $this->belongsTo('App\Board');
    }
}
