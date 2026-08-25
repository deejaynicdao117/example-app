<?php

namespace App\Http\Controllers;

use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function add_comment(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'content' => 'required|string',
        ]);

        return $this->commentService->add_comment($data, $request->user()->id);
    }

    public function edit_comment(Request $request, $id = null)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $data['id'] = $id ?? $request->input('id');
        validator($data, [
            'id' => 'required|integer|exists:comments,id',
        ])->validate();

        return $this->commentService->edit_comment($data, $request->user()->id);
    }

    public function delete_comment(Request $request, $id = null)
    {
        $data = [
            'id' => $id ?? $request->input('id'),
        ];

        validator($data, [
            'id' => 'required|integer|exists:comments,id',
        ])->validate();

        return $this->commentService->delete_comment($data, $request->user()->id);
    }

    public function view_comments($postId = null)
    {
        $data = [
            'post_id' => $postId,
        ];

        validator($data, [
            'post_id' => 'required|integer|exists:posts,id',
        ])->validate();

        return $this->commentService->view_comments($data);
    }
}
