<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\Task;
use App\model\Tag;
use App\User;
use DB;

class TagController extends Controller
{
    public function create(Task $task, Request $request){
        if($task->user_id != User::get()->id)
            return abort(403);
       
        DB::table('tags')->where('task_id', $task->id)->delete();

        $tags = [];
        foreach($request->tags as $tag_title){
            $tag = Tag::create([
                'task_id'   => $task->id,
                'title'     => $tag_title
            ]);
            array_push($tags, $tag);
        }
        $task->tags = $task->tags;
        return [
            'ok'    => true,
            'task'  => $task
        ];
    }

    public function retrive(Request $request){
        $tags = Tag::whereHas('task', function($query){
            return $query->where('user_id', User::get()->id);
        });
        if($request->has('title'))
            $tags = $tags->where('title', 'LIKE', '%'. $request->title .'%');
        $results = [];
        foreach($tags->get() as $tag)
            if(!in_array($tag, $results))
                array_push($results, $tag->title);
        return $results;
    }
}
