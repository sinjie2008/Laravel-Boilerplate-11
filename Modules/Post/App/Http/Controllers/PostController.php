<?php

declare(strict_types=1);

namespace Modules\Post\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Post\App\Models\Post;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Post::select(['id', 'title']);
            return DataTables::of($data)
                    ->addColumn('action', function($row){
                           $editUrl = route('admin.posts.edit', $row->id);
                           $deleteUrl = route('admin.posts.destroy', $row->id);
                           $csrf = csrf_field();
                           $method = method_field('DELETE');

                           $btn = '<a href="'.$editUrl.'" class="btn btn-primary btn-sm">Edit</a> ';
                           $btn .= '<form action="'.$deleteUrl.'" method="POST" style="display:inline-block;">'.$csrf.$method.'<button type="submit" class="btn btn-danger btn-sm">Delete</button></form>';

                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('post::index');
    }

    public function create(): View
    {
        return view('post::create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Post::create($request->only('title', 'content'));

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully.');
    }

    public function show(Post $post): View
    {
        return view('post::show', compact('post'));
    }

    public function edit(Post $post): View
    {
        return view('post::edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($request->only('title', 'content'));

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }
}
