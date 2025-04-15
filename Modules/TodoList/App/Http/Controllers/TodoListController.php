<?php

namespace Modules\TodoList\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// Remove Response import if not used elsewhere, keep for now
use Modules\TodoList\App\Models\Todo;
use Yajra\DataTables\Facades\DataTables; // Import DataTables facade

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Todo::select(['id', 'title', 'description', 'completed']); // Select necessary columns
            return DataTables::of($data)
                    ->addColumn('action', function($row){
                           $showUrl = route('admin.todolist.show', $row->id);
                           $editUrl = route('admin.todolist.edit', $row->id);
                           $deleteUrl = route('admin.todolist.destroy', $row->id);
                           $csrf = csrf_field();
                           $method = method_field('DELETE');

                           $btn = '<a href="'.$showUrl.'" class="btn btn-info btn-sm">Show</a> ';
                           $btn .= '<a href="'.$editUrl.'" class="btn btn-primary btn-sm">Edit</a> ';
                           $btn .= '<form action="'.$deleteUrl.'" method="POST" style="display:inline-block;">'.$csrf.$method.'<button type="submit" class="btn btn-danger btn-sm">Delete</button></form>';

                            return $btn;
                    })
                    ->editColumn('completed', function($row) {
                        return $row->completed ? 'Yes' : 'No';
                    })
                    ->rawColumns(['action']) // Allow HTML in action column
                    ->make(true);
        }

        // If not an AJAX request, just load the view
        return view('todolist::index');
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
