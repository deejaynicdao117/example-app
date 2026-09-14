<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Support\Facades\DB;

class PostService extends BaseService
{
    public function new_post($data, $user_id = null)
    {
        $post = null;

        DB::transaction(function () use ($data, $user_id, &$post) {
            $post = Post::create([
                'user_id' => $user_id,
                'title' => $data['title'],
                'content' => $data['content'],
            ]);

            if (isset($data['images']) && count($data["images"]))
            {
                foreach($data["images"] as $image) {
                    // $folder = "/uploads/post_images_".$post->id;

                    $file_url = $image->store("uploads", "public");

                    $url_to_save = url("/storage/" . $file_url);

                    PostAttachment::create([
                        'post_id'=> $post->id,
                        'url'=> $url_to_save
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'New Post created successfully',
            'data' => $post,
        ], 201);
    }

    public function view_post($data = [], $user_id = null)
    {
        $posts = Post::with('user', 'comments.attachments', 'attachments')
            ->withCount('likes', 'comments')
            ->latest()->get();

        return response()->json([
            'data' => $posts,
        ]);
    }

    public function update_post($data, $user_id = null)
    {
        $id = $data['id'];
        $post = null;

        DB::transaction(function () use ($data, $user_id, &$post, $id) {
            $post = Post::where('id', $id)->update([
                'title' => $data['title'],
                'content' => $data['content'],
            ]);
        });

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    public function delete_post($data)
    {
        $id = $data['id'];
        $post = null;

        DB::transaction(function () use ($data, &$post, $id) {
            $post = Post::where('id', $id)->delete();
        });

        return response()->json([
            'message' => 'Post deleted successfully',
            'data' => $post,
        ]);
    }
}
