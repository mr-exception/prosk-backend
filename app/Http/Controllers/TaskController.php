<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\Task;

class TaskController extends Controller
{
    public function create(Request $request){
        return 'create';
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
}
