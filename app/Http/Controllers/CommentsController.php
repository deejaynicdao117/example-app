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

        $candidates = [
            $request->input('id'),
            $request->input('comment_id'),
            $id,
        ];

        $resolved = null;
        foreach ($candidates as $candidate) {
            if (is_numeric($candidate)) {
                $resolved = (int) $candidate;
                break;
            }
        }

        // Frontend sent a temp id like "local-..." with no real DB id
        if ($resolved === null && ($id !== null || $request->has('id') || $request->has('comment_id'))) {
            return response()->json([
                'message' => 'Invalid comment id. Use the integer id from view_comments, not a temporary local-* id.',
                'errors' => [
                    'id' => ['The id field must be an integer DB id.'],
                ],
            ], 422);
        }

        $data['id'] = $resolved;
        validator($data, [
            'id' => 'required|integer|exists:comments,id',
        ])->validate();

        return $this->commentService->edit_comment($data, $request->user()->id);
    }

    public function delete_comment(Request $request, $id = null)
    {
        $candidates = [
            $request->input('id'),
            $request->input('comment_id'),
            $id,
        ];

        $resolved = null;
        foreach ($candidates as $candidate) {
            if (is_numeric($candidate)) {
                $resolved = (int) $candidate;
                break;
            }
        }

        if ($resolved === null && ($id !== null || $request->has('id') || $request->has('comment_id'))) {
            return response()->json([
                'message' => 'Invalid comment id. Use the integer id from view_comments, not a temporary local-* id.',
                'errors' => [
                    'id' => ['The id field must be an integer DB id.'],
                ],
            ], 422);
        }

        $data = [
            'id' => $resolved,
        ];

        validator($data, [
            'id' => 'required|integer|exists:comments,id',
        ])->validate();

        return $this->commentService->delete_comment($data, $request->user()->id);
    }

    public function view_comments(Request $request, $id = null)
    {
        $request->merge([
            'id' => $id ?? $request->input('id') ?? $request->input('post_id'),
        ]);

        $data = $request->validate([
            'id' => 'required|integer|exists:posts,id',
        ]);

        return $this->commentService->view_comments($data);
    }
}
