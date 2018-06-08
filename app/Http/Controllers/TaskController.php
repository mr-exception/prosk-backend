<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\Task;
use App\User;
use App\Http\Requests\TaskRequest;
use Validator;

class TaskController extends Controller
{
    public function create(Request $request){
        $validation = $this->validateTask($request);
        if(!$validation){
            $inputs = $request->all();
            $inputs['user_id'] = User::get()->id;
            $task = Task::create($inputs);
            return [
                'ok'        => true,
                'task'      => $task
            ];

        }else{
            return [
                'ok'        => false,
                'errors'    => $validation
            ];
        }
    }
    public function retrive(Request $request){
        $tasks = User::get()->tasks();

        if($request->has('tags'))
            $tasks = $tasks->whereHas('tags', function($query){
                global $request;
                $query->whereIn('title', $request->tags);
            });
        if($request->has('started_min'))
            $tasks = $tasks->where('started_at', '>', $request->input('started_min'));
        if($request->has('started_max'))
            $tasks = $tasks->where('started_at', '<', $request->input('started_max'));
        
        if($request->has('finished_min'))
            $tasks = $tasks->where('finished_at', '>', $request->input('finished_min'));
        if($request->has('finished_max'))
            $tasks = $tasks->where('finished_at', '<', $request->input('finished_max'));
        
        if($request->has('start_min'))
            $tasks = $tasks->where('start_time', '>', $request->input('start_min'));
        if($request->has('start_max'))
            $tasks = $tasks->where('start_time', '<', $request->input('start_max'));
        
        if($request->has('finish_min'))
            $tasks = $tasks->where('finish_time', '>', $request->input('finish_min'));
        if($request->has('finish_max'))
            $tasks = $tasks->where('finish_time', '<', $request->input('finish_max'));
        
        if($request->has('status'))
            $tasks = $tasks->where('status', $request->status);
        
        if($request->has('poritory'))
            $tasks = $tasks->where('poritory', $request->poritory);
        
        if($request->has('poritory_min'))
            $tasks = $tasks->where('poritory', '>', $request->input('poritory_min'));
        if($request->has('poritory_max'))
            $tasks = $tasks->where('poritory', '<', $request->input('poritory_max'));
        
        if($request->has('title'))
            $tasks = $tasks->where('title', 'LIKE', '%'. $request->title .'%');
        if($request->has('description'))
            $tasks = $tasks->where('description', 'LIKE', '%'. $request->description .'%');
        
        if($request->has('offset'))
            $tasks = $tasks->skip($request->offset);
        else
            $tasks = $tasks->skip(0);
        
        if($request->has('limit'))
            $tasks = $tasks->limit($request->limit);
        else
            $tasks = $tasks->limit(10);
        
        $tasks = $tasks->get();
        for($i=0; $i<sizeof($tasks); $i++)
            $tasks[$i]->tags = $tasks[$i]->tags;
        return $tasks;
    }

    public function count(Request $request){
        $tasks = User::get()->tasks();

        if($request->has('tags'))
            $tasks = $tasks->whereHas('tags', function($query){
                global $request;
                $query->whereIn('title', $request->tags);
            });
        if($request->has('started_min'))
            $tasks = $tasks->where('started_at', '>', $request->input('started_min'));
        if($request->has('started_max'))
            $tasks = $tasks->where('started_at', '<', $request->input('started_max'));
        
        if($request->has('finished_min'))
            $tasks = $tasks->where('finished_at', '>', $request->input('finished_min'));
        if($request->has('finished_max'))
            $tasks = $tasks->where('finished_at', '<', $request->input('finished_max'));
        
        if($request->has('start_min'))
            $tasks = $tasks->where('start_time', '>', $request->input('start_min'));
        if($request->has('start_max'))
            $tasks = $tasks->where('start_time', '<', $request->input('start_max'));
        
        if($request->has('finish_min'))
            $tasks = $tasks->where('finish_time', '>', $request->input('finish_min'));
        if($request->has('finish_max'))
            $tasks = $tasks->where('finish_time', '<', $request->input('finish_max'));
        
        if($request->has('status'))
            $tasks = $tasks->where('status', $request->status);
        
        if($request->has('poritory'))
            $tasks = $tasks->where('poritory', $request->poritory);
        
        if($request->has('poritory_min'))
            $tasks = $tasks->where('poritory', '>', $request->input('poritory_min'));
        if($request->has('poritory_max'))
            $tasks = $tasks->where('poritory', '<', $request->input('poritory_max'));
        
        if($request->has('title'))
            $tasks = $tasks->where('title', 'LIKE', '%'. $request->title .'%');
        if($request->has('description'))
            $tasks = $tasks->where('description', 'LIKE', '%'. $request->description .'%');
        
        return [
            'ok'    => true,
            'count' => $tasks->count()
        ];
    }


    public function update(Request $request, Task $task){
        if($task->user_id != User::get()->id)
            return abort(403);
        $task->fill($request->all());
        $task->save();
        return [
            'ok'    => true,
            'task'  => $task
        ];
    }
    public function delete(Task $task){
        if($task->user_id != User::get()->id)
            return abort(403);
        $task->delete();
        return[
            'ok'    => true
        ];
    }

    public function finish(Task $task){
        if($task->user_id != User::get()->id)
            return abort(403);
        $task->status = Task::STATUS_FINISHED;
        $task->save();
        return [
            'ok'    => true,
            'task'  => $task
        ];
    }

    public function validateTask(Request $request){
        $validation =  Validator::make($request->all(), [
            'title'         => 'required|string',
            'description'   => 'required|string',
            'start_time'    => 'required|date',
            'finish_time'   => 'required|date',
            'poritory'      => 'required|numeric|min:0|max:9',
            'status'        => 'numeric|in:1,2,3',
        ]);
        if($validation->fails()){
            $errors = [];
            foreach($validation->errors()->all() as $message){
                array_push($errors, [
                    'code'      => 1000,
                    'message'   => $message
                ]);
            }
            return [
                'ok'        => false,
                'errors'    => $errors,
            ];
        }else{
            return false;
        }
    }
}
