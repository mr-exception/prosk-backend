<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primary = 'id';
    protected $table = 'tasks';
    protected $fillable = ['title', 'description', 'user_id', 'start_time', 'finish_time', 'started_at', 'finished_at', 'status', 'poritory'];

    public function tracks(){
        return $this->hasMany('App\model\Track');
    }
    public function tags(){
        return $this->hasMany('App\model\Tag');
    }

    /**
     * updated the start time for a task, finds the started at variable
     */
    public function update_times(){
        $first_track = $this->tracks()->orderBy('started_at', 'ASC')->first();
        $this->started_at = $first_track->started_at;
        $this->save();
    }
}
