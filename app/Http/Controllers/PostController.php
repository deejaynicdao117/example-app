<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;

class PostController extends Controller
{
    protected $PostService;

    public function __construct(PostService $PostService)
    {
        $this->PostService = $PostService;
    }

    public function new_post(Request $request)
    {
        if ($request->isJson()) {
            $request->merge($request->json()->all());
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images' => 'sometimes|required|array',
            'images.*' => 'file|image',
        ]);

        $result = $this->PostService->new_post($data, $request->user()->id);

        return $result;
    }

    public function view_post(Request $request){
        // Public route has no auth middleware, so $request->user() (default
        // web guard) is always null here. Resolve via sanctum explicitly so
        // `user_liked` flags hydrate when a Bearer token is present.
        $result = $this->PostService->view_post(auth('sanctum')->user()?->id);

        return $result;
    }

    public function update_post(Request $request, $id = null)
    {
        if ($id !== null && !$request->has('id')) {
            $request->merge(['id' => $id]);
        }

        $data = $request->validate([
            'id' => 'required|integer|exists:posts,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $result = $this->PostService->update_post($data, $request->user()->id);

        return $result;
    }

    public function delete_post(Request $request) {
        
        $data = $request->validate([
            'id' => 'required|integer|exists:posts,id',
        ]);

        $result = $this->PostService->delete_post($data);

        return $result;
    }
}
