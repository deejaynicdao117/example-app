<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_like_toggle_and_metadata(): void
    {
        $owner = User::factory()->create();
        $liker = User::factory()->create();

        $post = Post::create([
            'user_id' => $owner->id,
            'title' => 'T',
            'content' => 'C',
        ]);

        $comment = Comment::create([
            'user_id' => $owner->id,
            'post_id' => $post->id,
            'content' => 'Nice',
        ]);

        $this->actingAs($liker, 'sanctum');

        // Like
        $like = $this->postJson("/api/likes/comment_like/{$comment->id}");
        $like->assertStatus(200)->assertJsonPath('type', 1);

        // Metadata on view_comments
        $view = $this->getJson("/api/comments/view_comments/{$post->id}");
        $view->assertStatus(200);
        $view->assertJsonPath('data.0.likes_count', 1);
        $view->assertJsonPath('data.0.user_liked', true);

        // Metadata on view_post embedded comments
        $posts = $this->getJson('/api/posts/view_post');
        $posts->assertStatus(200);
        $posts->assertJsonPath('data.0.comments.0.likes_count', 1);
        $posts->assertJsonPath('data.0.comments.0.user_liked', true);

        // Likers list
        $list = $this->getJson("/api/likes/comment_user_list/{$comment->id}");
        $list->assertStatus(200);
        $this->assertEquals($liker->id, $list->json('users.0.id'));

        // Unlike (toggle)
        $unlike = $this->postJson("/api/likes/comment_like/{$comment->id}");
        $unlike->assertStatus(200)->assertJsonPath('type', 0);

        $view2 = $this->getJson("/api/comments/view_comments/{$post->id}");
        $view2->assertJsonPath('data.0.likes_count', 0);
        $view2->assertJsonPath('data.0.user_liked', false);
    }

    public function test_post_like_route_parameter_is_accepted(): void
    {
        $owner = User::factory()->create();
        $liker = User::factory()->create();

        $post = Post::create([
            'user_id' => $owner->id,
            'title' => 'T',
            'content' => 'C',
        ]);

        $this->actingAs($liker, 'sanctum');

        $response = $this->postJson("/api/likes/post_like/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonPath('type', 1);
    }
}
