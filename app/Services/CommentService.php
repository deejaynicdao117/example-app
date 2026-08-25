<?php 

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentService extends BaseService
{
    public function add_comment($data, $user_id = null)
    {
        $comment = null;
        DB::transaction(function () use ($data, $user_id, &$comment) {
            $comment = Comment::create([
                'user_id' => $user_id,
                'post_id' => $data['post_id'],
                'content' => $data['content'],
            ]);
        });

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => $comment,
        ], 201);
    }

    public function edit_comment($data, $user_id = null)
    {
        $comment = Comment::where('id', $data['id'])
            ->where('user_id', $user_id)
            ->firstOrFail();

        $comment->update([
            'content' => $data['content'],
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => $comment->fresh(),
        ]);
    }

    public function delete_comment($data, $user_id = null)
    {
        Comment::where('id', $data['id'])
            ->where('user_id', $user_id)
            ->firstOrFail()
            ->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}