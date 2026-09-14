<?php 

namespace App\Services;

use App\Models\Comment;
use App\Models\PostAttachment;
use App\Services\BaseService;
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
            if (isset($data['images']) && count($data["images"]))
            {
                foreach($data["images"] as $image) {
                    // $folder = "/uploads/post_images_".$post->id;

                    $file_url = $image->store("uploads", "public");

                    $url_to_save = url("/storage/" . $file_url);

                    PostAttachment::create([
                        'comment_id'=> $comment->id,
                        'url'=> $url_to_save
                    ]);
                }
            }
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
        $comment = Comment::with('post')->findOrFail($data['id']);

        $isOwner = (int) $comment->user_id === (int) $user_id;
        $isPostOwner = $comment->post && (int) $comment->post->user_id === (int) $user_id;

        if (!$isOwner && !$isPostOwner) {
            abort(403, 'You are not allowed to delete this comment.');
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }

    public function view_comments($data = [])
    {
        $comments = Comment::with(['user', 'attachments'])
            ->where('post_id', $data['id'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $comments,
        ]);
    }
}