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
    public function retrive(Request $request){
        $tracks = User::get()->tracks();
        
        if($request->has('task_id'))
            $tracks = $tracks->where('task_id', $request->input('task_id'));

        if($request->has('started_min'))
            $tracks = $tracks->where('started_at', '>', $request->input('started_min'));
        if($request->has('started_max'))
            $tracks = $tracks->where('started_at', '<', $request->input('started_max'));
        
        if($request->has('finished_min'))
            $tracks = $tracks->where('finished_at', '>', $request->input('finished_min'));
        if($request->has('finished_max'))
            $tracks = $tracks->where('finished_at', '<', $request->input('finished_max'));
        
        if($request->has('description'))
            $tracks = $tracks->where('description', 'LIKE', '%'. $request->description .'%');
        
        if($request->has('offset'))
            $tracks = $tracks->skip($request->offset);
        else
            $tracks = $tracks->skip(0);
        
        if($request->has('limit')){
            $tracks = $tracks->limit($request->limit);
        }else
            $tracks = $tracks->limit(10);
        
        $tracks = $tracks->get();
        for($i=0; $i<sizeof($tracks); $i++)
            $tracks[$i]->task = $tracks[$i]->task;
        return $tracks;
    }
    public function count(Request $request){
        $tracks = User::get()->tracks();
        
        if($request->has('task_id'))
            $tracks = $tracks->where($request->input('task_id'));

        if($request->has('started_min'))
            $tracks = $tracks->where('started_at', '>', $request->input('started_min'));
        if($request->has('started_max'))
            $tracks = $tracks->where('started_at', '<', $request->input('started_max'));
        
        if($request->has('finished_min'))
            $tracks = $tracks->where('finished_at', '>', $request->input('finished_min'));
        if($request->has('finished_max'))
            $tracks = $tracks->where('finished_at', '<', $request->input('finished_max'));
        
        if($request->has('description'))
            $tracks = $tracks->where('description', 'LIKE', '%'. $request->description .'%');
        
        return [
            'ok'    => true,
            'count' => $tracks->count()
        ];
    }
    public function start(Request $request, Task $task){
        if($task->user_id != User::get()->id)
            return abort(403);

        $validation = $this->validateStartedTask($request);
        if(!$validation){
            $inputs = $request->all();
            $inputs['task_id'] = $task->id;
            $inputs['finished_at'] = null;
            $inputs['duration'] = null;

            if(User::get()->validateTrackStartTime($inputs['started_at']))
                return [
                    'ok'        => false,
                    'errors'    => Errors::generate([1002])
                ];
            $track = Track::create($inputs);
            $task->update_times();
            return [
                'ok'        => true,
                'track'     => $track,
            ];
        }else{
            return $validation;
        }
    }
    public function finish(Request $request, Track $track){
        $validation = $this->validateFinishedTask($request);
        if(!$validation){

            $inputs = $request->all();
            $track->finished_at = $inputs['finished_at'];
            $track->description = $inputs['description'];
            $track->duration = strtotime($track->finished_at) - strtotime($track->started_at);
            
            if(User::get()->validateTrackTime($track->started_at, $track->finished_at, $track->id))
                return [
                    'ok'        => false,
                    'errors'    => Errors::generate([1002])
                ];
            $track->save();
            // $task->update_times();
            return [
                'ok'        => true,
                'track'     => $track,
            ];
        }else{
            return $validation;
        }
    }
    public function insert(Request $request, Task $task){
        if($task->user_id != User::get()->id)
            return abort(403);

        $validation = $this->validateInsertedTask($request);
        if(!$validation){
            $inputs = $request->all();
            $inputs['task_id'] = $task->id;
            $inputs['duration'] = strtotime($inputs['finished_at']) - strtotime($inputs['started_at']);
            if($inputs['duration'] < 0)
                return [
                    'ok'        => false,
                    'errors'    => [
                        ['code' => 1000, 'message' => 'started_at must be less than finished_at']
                    ]
                ];
            if(User::get()->validateTrackTime($inputs['started_at'], $inputs['finished_at']))
                return [
                    'ok'        => false,
                    'errors'    => Errors::generate([1002])
                ];
            $track = Track::create($inputs);
            $task->update_times();
            return [
                'ok'        => true,
                'track'     => $track,
            ];
        }else{
            return $validation;
        }
    }
    public function update(Request $request, Track $track){
        if($track->task->user_id != User::get()->id)
            return abort(403);
        $track->fill($request->all());
        $track->duration = strtotime($track->finished_at) - strtotime($track->started_at);
        if(User::get()->validateTrackTime($track->started_at, $track->finished_at, $track->id))
            return [
                'ok'        => false,
                'errors'    => Errors::generate([1002])
            ];
        $track->save();
        $track->task->update_times();
        return [
            'ok'        => true,
            'track'     => $track,
        ];
    }
    public function delete(Track $track){
        if($track->task->user_id != User::get()->id)
            return abort(403);
        $track->delete();
        return[
            'ok'    => true
        ];
    }


    public function sum(Request $request){
        $count = Track::
                    where('started_at', '>', gmdate('Y-m-d H:i:s', time() - $request->input('offset', 60*60*24*7)))
                    ->count();
        return [
            'ok'    => true,
            'count' => $count
        ];
    }

    private function validateInsertedTask(Request $request){
        $validation = Validator::make($request->all(), [
            'description'   => 'required|string',
            'started_at'    => 'required|date',
            'finished_at'   => 'required|date|after:started_at',
        ]);
        return $this->validationResponse($validation);
    }

    private function validateStartedTask(Request $request){
        $validation = Validator::make($request->all(), [
            'description'   => 'required|string',
            'started_at'    => 'required|date',
        ]);
        return $this->validationResponse($validation);
    }
    private function validateFinishedTask(Request $request){
        $validation = Validator::make($request->all(), [
            'finished_at'   => 'required|date',
            'description'   => 'required|string',
        ]);
        return $this->validationResponse($validation);
    }

    private function validationResponse($validation){
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
