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
        
        $tasks = $tasks->get();
        return $tasks;
    }
    public function update(Task $task){
        return 'update';
    }
    public function delete(Task $task){
        return 'delete';
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
