<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $primary = 'id';
    protected $table = 'tracks';
    protected $fillable = ['started_at', 'finished_at', 'duration', 'description', 'task_id'];
    public function task(){
        return $this->belongsTo('App\model\Task');
    }
}
