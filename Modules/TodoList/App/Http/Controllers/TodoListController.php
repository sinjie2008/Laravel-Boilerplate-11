<?php

namespace Modules\TodoList\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\TodoList\App\Models\Todo as Todo;

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = Todo::all();
        return view('todolist::index', compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('todolist::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
        ]);

        $data = $request->all();
        $data['completed'] = isset($data['completed']) ? true : false;

        Todo::create($data);

        return redirect()->route('admin.todolist.index')
                         ->with('success','Todo created successfully.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $todo = Todo::find($id);
        return view('todolist::show', compact('todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $todo = Todo::find($id);
        return view('todolist::edit', compact('todo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
        ]);

        $todo = Todo::find($id);
         $data = $request->all();
        $data['completed'] = isset($data['completed']) ? true : false;
        $todo->update($data);

        return redirect()->route('admin.todolist.index')
                         ->with('success','Todo updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $todo = Todo::find($id);
        $todo->delete();

        return redirect()->route('admin.todolist.index')
                         ->with('success','Todo deleted successfully');
    }
}
