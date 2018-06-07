<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $primary = 'id';
    protected $table = 'tags';
    protected $fillable = ['title', 'task_id'];
    public function task(){
        return $this->belongsTo('App\model\Task');
    }
}
