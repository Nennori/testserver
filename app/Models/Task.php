<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Concerns\UsesUuid;

class Task extends Model
{
    use UsesUuid;
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
        return $this->belongsTo('App\Models\Board');
    }
    public function users(){
        return $this->belongsToMany('App\Models\User')->withPivot('is_author');
    }
    public function status(){
        return $this->belongsTo('App\Models\Status');
    }
    public function marks(){
        return $this->belongsToMany('App\Models\Marks');
    }
}
