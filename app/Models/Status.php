<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Concerns\UsesUuid;

class Status extends Model
{
    use UsesUuid;
    protected $fillable = [
        'name'
    ];
    public $incrementing = false;
    protected $keyType = 'string';
    public function tasks(){
        return $this->hasMany('App\Models\Task');
    }
    public function board(){
        return $this->belongsTo('App\Models\Board');
    }
}
