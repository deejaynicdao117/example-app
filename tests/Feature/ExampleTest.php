<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_editing_another_users_comment_returns_forbidden_instead_of_model_not_found(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $post = Post::create([
            'user_id' => $owner->id,
            'title' => 'Example title',
            'content' => 'Example content',
        ]);

        $comment = Comment::create([
            'user_id' => $owner->id,
            'post_id' => $post->id,
            'content' => 'Original comment',
        ]);

        $this->actingAs($other, 'sanctum');

        $response = $this->putJson('/api/comments/edit_comments/' . $comment->id, [
            'content' => 'Edited by someone else',
        ]);

        $response->assertStatus(403);
    }

    public function test_comment_view_endpoint_returns_comment_image_attachments(): void
    {
        $owner = User::factory()->create();
        $post = Post::create([
            'user_id' => $owner->id,
            'title' => 'Example title',
            'content' => 'Example content',
        ]);

        $comment = Comment::create([
            'user_id' => $owner->id,
            'post_id' => $post->id,
            'content' => 'Original comment',
        ]);

        PostAttachment::create([
            'comment_id' => $comment->id,
            'url' => 'http://example.test/storage/uploads/comment.png',
        ]);

        $this->actingAs($owner, 'sanctum');

        $response = $this->getJson('/api/comments/view_comments/' . $post->id);

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.attachments.0.comment_id', $comment->id);
    }
}
