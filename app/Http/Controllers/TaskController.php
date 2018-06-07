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
        return 'retrive';
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
