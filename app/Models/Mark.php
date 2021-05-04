<?php

namespace App\Models;

use App\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use UsesUuid;

    public $timestamps = false;

    protected $fillable = [
        'name', 'color'
    ];

    public function board()
    {
        return $this->belongsTo('App\Models\Board');
    }
    public function tasks()
    {
        return $this->belongsToMany('App\Models\Task');
    }
}
