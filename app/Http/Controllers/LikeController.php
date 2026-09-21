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

    protected function hydrateLikeTarget(Request $request): Request
    {
        $route = $request->route();

        if ($route) {
            $postId = $route->parameter('postId');
            $commentId = $route->parameter('commentId');

            if ($postId !== null && !$request->has('post_id')) {
                $request->merge(['post_id' => $postId]);
            }

            if ($commentId !== null && !$request->has('comment_id')) {
                $request->merge(['comment_id' => $commentId]);
            }
        }

        return $request;
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

    public function likes_count(Request $request)
    {
        $request = $this->hydrateLikeTarget($request);

        $validated = $request->validate([
            'post_id' => [
                'nullable',
                'integer',
                'required_without:comment_id',
                'prohibits:comment_id',
                'exists:posts,id',
            ],
            'comment_id' => [
                'nullable',
                'integer',
                'required_without:post_id',
                'prohibits:post_id',
                'exists:comments,id',
            ],
        ]);

        return response()->json([
            'post_id'    => $validated['post_id'] ?? null,
            'comment_id' => $validated['comment_id'] ?? null,
            'count'      => $this->likeService->likesCount($validated),
        ]);
    }

    public function toggle_like(Request $request)
    {
        $request = $this->hydrateLikeTarget($request);

        $data = $request->validate([
            'post_id' => [
                'nullable',
                'integer',
                'required_without:comment_id',
                'prohibits:comment_id',
                'exists:posts,id',
            ],
            'comment_id' => [
                'nullable',
                'integer',
                'required_without:post_id',
                'prohibits:post_id',
                'exists:comments,id',
            ],
        ]);

        $result = $this->likeService->like($data, $request->user()->id);

        return response()->json($result);
    }

    public function user_list(Request $request)
    {
        $request = $this->hydrateLikeTarget($request);

        $validated = $request->validate([
            'post_id' => [
                'nullable',
                'integer',
                'required_without:comment_id',
                'prohibits:comment_id',
                'exists:posts,id',
            ],
            'comment_id' => [
                'nullable',
                'integer',
                'required_without:post_id',
                'prohibits:post_id',
                'exists:comments,id',
            ],
        ]);

        return response()->json([
            'users' => $this->likeService->usersWhoLikedPost($validated),
        ]);
    }
}