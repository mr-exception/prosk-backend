<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Request;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token'
    ];
    protected $primary = 'id';
    protected $table = 'users';
    public function tasks(){
        return $this->hasMany('App\model\Task');
    }
    public static function get(){
        return User::where('token', Request::header('token'))->first();
    }
}
