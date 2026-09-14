<?php

namespace App\Services;

use App\Models\Like;

class LikeService extends BaseService
{
    public function userLikes($user_id)
    {
        return Like::where('user_id', $user_id)
            ->with(['post', 'comment'])
            ->latest()
            ->get();
    }

    public function likesCount(array $data)
    {
        $postId = $data['post_id'] ?? null;
        $commentId = $data['comment_id'] ?? null;

        return Like::query()
            ->when($postId, fn ($query) => $query->where('post_id', $postId))
            ->when($commentId, fn ($query) => $query->where('comment_id', $commentId))
            ->count();
    }

    public function like(array $data, $user_id)
    {
        $postId = $data['post_id'] ?? null;
        $commentId = $data['comment_id'] ?? null;

        $likeQuery = Like::where('user_id', $user_id)
            ->when($postId, fn ($q) => $q->where('post_id', $postId))
            ->when($commentId, fn ($q) => $q->where('comment_id', $commentId));

        $existingLike = $likeQuery->first();

        $targetName = $postId ? 'Post' : 'Comment';

        if ($existingLike) {
            $existingLike->delete();

            return [
                'message' => "{$targetName} unliked successfully.",
                'type'    => 0,
            ];
        }

        Like::create([
            'user_id'    => $user_id,
            'post_id'    => $postId,
            'comment_id' => $commentId,
        ]);

        return [
            'message' => "{$targetName} liked successfully.",
            'type'    => 1,
        ];
    }

    public function usersWhoLiked(array $data)
    {
        $postId = $data['post_id'] ?? null;
        $commentId = $data['comment_id'] ?? null;

        return Like::query()
            ->when($postId, fn ($q) => $q->where('post_id', $postId))
            ->when($commentId, fn ($q) => $q->where('comment_id', $commentId))
            ->with('user:id,name,email')
            ->latest()
            ->get()
            ->pluck('user');
    }
}