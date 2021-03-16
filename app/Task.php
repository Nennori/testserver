<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use Concerns\UsesUuid;
    //
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_number', 'name', 'description', 'expired_at',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expired_at' => 'datetime',
    ];
    public $incrementing = false;
    protected $keyType = 'string';

    public function board(){
        return $this->belongsTo('App\Board');
    }
    public function users(){
        return $this->belongsToMany('App\User');
    }
    public function status(){
        return $this->belongsTo('App\Status');
    }
}
