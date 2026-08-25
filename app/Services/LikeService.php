<?php

namespace App\Services;

use App\Models\Like;

class LikeService extends BaseService
{
    public function userLikes($user_id)
    {
        return Like::where('user_id', $user_id)
            ->with('post')
            ->latest()
            ->get();
    }

    public function likesCount($post_id)
    {
        return Like::where('post_id', $post_id)->count();
    }

    public function like($data, $user_id)
    {
        $post_id = $data['post_id'];

        // Check if the user has already liked the post
        $existingLike = Like::where('user_id', $user_id)
            ->where('post_id', $post_id)
            ->first();

        if ($existingLike) {
            // If the like already exists, remove it (unlike)
            $existingLike->delete();
            return response()->json([
                'message' => 'Post unliked successfully.',
                'type' => 0,
            ], 200);
        } else {
            // If the like doesn't exist, create a new like
            Like::create([
                'user_id' => $user_id,
                'post_id' => $post_id,
            ]);
            return response()->json([
                'message' => 'Post liked successfully.',
                'type' => 1,
            ], 201);
        }
    }
}