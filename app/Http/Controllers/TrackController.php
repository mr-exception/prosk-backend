<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Errors;
use Validator;
use DB;

use App\model\Track;
use App\model\Task;
use App\User;

class TrackController extends Controller
{
    public function start(Request $request, Task $task){

        return $task;
    }
    public function finish(Request $request, Track $track){

    }
    public function insert(Request $request, Task $task){
        if($task->user_id != User::get()->id)
            return abort(403);

        $validation = $this->validateInsertedTask($request);
        if(!$validation){
            $inputs = $request->all();
            $inputs['task_id'] = $task->id;
            $inputs['duration'] = strtotime($inputs['finished_at']) - strtotime($inputs['started_at']);
            if($this->validateTrackTime($inputs['started_at'], $inputs['finished_at']))
                return [
                    'ok'        => false,
                    'errors'    => Errors::generate([1002])
                ];
            $track = Track::create($inputs);
            // $task->update_times();
            return [
                'ok'        => true,
                'track'     => $track,
            ];
        }else{
            return [
                'ok'        => false,
                'errors'    => $validation
            ];
        }
    }
    public function update(Request $request, Track $track){

    }
    public function delete(Track $track){

    }

    public function validateTrackTime($started_at, $finished_at){
        $tracks = DB::table('tracks')->join('tasks', 'tasks.id', '=', 'tracks.task_id')
            ->select('tracks.*', 'tasks.user_id')
            ->whereRaw("
                    ((tracks.started_at <= '$started_at' AND tracks.finished_at >= '$started_at') OR
                    (tracks.started_at <= '$finished_at' AND tracks.finished_at >= '$finished_at') OR
                    (tracks.started_at <= '$started_at' AND tracks.finished_at >= '$finished_at') OR
                    (tracks.started_at >= '$started_at' AND tracks.finished_at <= '$finished_at')
                    ) AND tasks.user_id = ".User::get()->id."
                ")
            ->get();
        return sizeof($tracks);
    }
    public function validateInsertedTask(Request $request){
        $validation =  Validator::make($request->all(), [
            'description'   => 'required|string',
            'started_at'    => 'required|date',
            'finished_at'   => 'required|date',
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
