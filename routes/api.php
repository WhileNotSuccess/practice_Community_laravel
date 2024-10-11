<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NestedCommentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('posts',PostController::class);
Route::apiResource('comments',CommentController::class);
Route::apiResource('nested-comments',NestedCommentController::class);
Route::get('category',[CategoryController::class, 'index']);