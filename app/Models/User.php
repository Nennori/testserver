<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
use App\Concerns\UsesUuid;


class User extends Authenticable implements JWTSubject

{
    use Notifiable;
    use UsesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name', 'email',  'about'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return mixed
     * @var array
     */

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function boards() {
        return $this->belongsToMany(Board::class)->withPivot('role_id');
    }

    public function tasks() {
        return $this->belongsToMany(Task::class);
    }
}
