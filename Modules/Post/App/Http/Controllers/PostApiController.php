<?php

declare(strict_types=1);

namespace Modules\Post\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Post\App\Models\Post;

class PostApiController extends Controller
{
    public function index(): Response
    {
        return response(Post::all());
    }

    public function store(Request $request): Response
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create($data);

        return response($post, 201);
    }

    public function show(Post $post): Response
    {
        return response($post);
    }

    public function update(Request $request, Post $post): Response
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $post->update($data);

        return response($post);
    }

    public function destroy(Post $post): Response
    {
        $post->delete();
        return response()->noContent();
    }
}
