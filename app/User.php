<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Request;
use App\model\Track;
use DB;
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

    public function tracks(){
        return Track::whereHas('task', function($query){
            $query->where('user_id', $this->id);
        });
    }
    public static function get(){
        return User::where('token', Request::header('token'))->first();
    }

    public function validateTrackTime($started_at, $finished_at, $track_id=0){
        $tracks = DB::table('tracks')->join('tasks', 'tasks.id', '=', 'tracks.task_id')
            ->select('tracks.*', 'tasks.user_id')
            ->whereRaw("
                    ((tracks.started_at <= '$started_at' AND tracks.finished_at >= '$started_at') OR
                    (tracks.started_at <= '$finished_at' AND tracks.finished_at >= '$finished_at') OR
                    (tracks.started_at <= '$started_at' AND tracks.finished_at >= '$finished_at') OR
                    (tracks.started_at >= '$started_at' AND tracks.finished_at <= '$finished_at')
                    ) AND tasks.user_id = ".$this->id."
                    AND tracks.id <> $track_id
                ")
            ->get();
        return sizeof($tracks);
    }
    public function validateTrackStartTime($started_at){
        $tracks = DB::table('tracks')->join('tasks', 'tasks.id', '=', 'tracks.task_id')
            ->select('tracks.*', 'tasks.user_id')
            ->whereRaw("
                    (tracks.started_at <= '$started_at' AND tracks.finished_at >= '$started_at')
                    AND tasks.user_id = ".$this->id."
                    AND tracks.id <> $track_id
                ")
            ->get();
        return sizeof($tracks);
    }
}
