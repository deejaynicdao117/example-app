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
        ]);

        $result = $this->PostService->new_post($data, $request->user()->id);

        return $result;
    }

    public function view_post(Request $request){
        $result = $this->PostService->view_post();

        return $result;
    }

    public function update_post(Request $request)
    {
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
