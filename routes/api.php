<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    PostController,
    LikeController,
};

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/get_users', [AuthController::class, 'get_users']);
// Route::put('/update_users', [App\Http\Controllers\AuthController::class, 'update_user']);
// Route::delete('/delete_users', [App\Http\Controllers\AuthController::class, 'delete_user']);

Route::prefix('user')
    ->middleware('auth:sanctum')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/account', 'account');
        Route::put('/update_users', 'update_user');
        Route::delete('/delete_users', 'delete_user');
        Route::post('/create_users', 'create_user');
    });

// Public list endpoint (no auth required)
// Route::get('/posts', [PostController::class, 'view_post']);

Route::prefix('posts')
    ->middleware('auth:sanctum')
    ->controller(PostController::class)
    ->group(function () {
        Route::post('/new_post', 'new_post');
        Route::get('/view_post/{id?}', 'view_post');
        Route::put('/update_post/{id?}', 'update_post');
        Route::put('/edit_post/{id?}', 'update_post');
        Route::delete('/delete_post/{id?}', 'delete_post');
    });

Route::prefix('comments')
    ->middleware('auth:sanctum')
    ->controller(App\Http\Controllers\CommentsController::class)
    ->group(function () {
        Route::post('/add_comments', 'add_comment');
        Route::post('/add_comment', 'add_comment');
        Route::put('/edit_comment/{id?}', 'edit_comment');
        Route::put('/edit_comments/{id?}', 'edit_comment');
        Route::delete('/delete_comment/{id?}', 'delete_comment');
        Route::delete('/delete_comments/{id?}', 'delete_comment');
        Route::get('/view_comments/{id?}', 'view_comments');
        Route::get('/view_comment/{id?}', 'view_comments');
    });

Route::prefix('likes')
    ->middleware('auth:sanctum')
    ->controller(LikeController::class)
    ->group(function () {
        Route::get('/user_likes/{userId?}', 'user_likes');
        Route::get('/likes_count/{postId?}', 'likes_count');
        Route::post('/post_like/{postId?}', 'toggle_like');
        Route::post('/toggle_like/{postId?}', 'toggle_like');
        Route::get('/user_list/{postId?}', 'user_list');
        // Comment likes (same toggle/count/list logic, comment_id target).
        Route::post('/comment_like/{commentId?}', 'toggle_like');
        Route::get('/comment_likes_count/{commentId?}', 'likes_count');
        Route::get('/comment_user_list/{commentId?}', 'user_list');
    });