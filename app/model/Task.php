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
    public function update_times(){
        die(var_dump($this->tracks));
    }
}
