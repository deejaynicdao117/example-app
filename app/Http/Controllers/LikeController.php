<?php

namespace App\Http\Controllers;

use App\Services\LikeService;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    protected $likeService;

    public function __construct(LikeService $likeService)
    {
        $this->likeService = $likeService;
    }

    public function user_likes(Request $request, $userId = null)
    {
        $userId = $userId ?? $request->user()->id;

        validator(['user_id' => $userId], [
            'user_id' => 'required|integer|exists:users,id',
        ])->validate();

        return response()->json([
            'likes' => $this->likeService->userLikes($userId),
        ]);
    }

    public function likes_count(Request $request, $postId = null)
    {
        $postId = $postId ?? $request->input('post_id');

        validator(['post_id' => $postId], [
            'post_id' => 'required|integer|exists:posts,id',
        ])->validate();

        return response()->json([
            'post_id' => (int) $postId,
            'count' => $this->likeService->likesCount($postId),
        ]);
    }

    public function post_like(Request $request, $postId = null)
    {
        $request->merge([
            'post_id' => $postId ?? $request->input('post_id'),
        ]);

        $data = $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
        ]);

        $result = $this->likeService->like($data, $request->user()->id);

        return $result;
    }

}