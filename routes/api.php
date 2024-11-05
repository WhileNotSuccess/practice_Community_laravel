<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NestedCommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('posts',PostController::class);
Route::apiResource('comments',CommentController::class);
Route::apiResource('nested-comments',NestedCommentController::class);
Route::get('category',[CategoryController::class, 'index']);
Route::get('search', [SearchController::class,'index']);
Route::get('/find-post-by-comment',[SearchController::class,'userPost']);

Route::post('/image-upload',[ImageController::class,'upload']);
Route::delete('/image-delete',[ImageController::class,'destroy']);