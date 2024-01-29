<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return Response::json($tasks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return Response::json(['error' => $validator->errors()], 400);
        }

        $task = new Task;
        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->date = $request->input('date');
        $task->save();

        return Response::json($task, 201);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return Response::json(['message' => 'Task deleted'], 200);
    }
}
