<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/comments",
 *     tags={"comment"},
 *     summary="특정 게시글의 댓글 목록 조회",
 *     description="특정 게시글의 댓글 목록을 반환합니다.",
 *     @OA\Parameter(
 *         name="post-id",
 *         in="query",
 *         description="게시글 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="댓글 목록",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=11),
 *                     @OA\Property(property="post_id", type="integer", example=3),
 *                     @OA\Property(property="author", type="string", example="aokon"),
 *                     @OA\Property(property="content", type="string", example="Rerum perferendis velit qui et consequatur ipsa."),
 *                     @OA\Property(property="createdAt", type="string", format="date-time", example="2024-09-30T05:45:48.000000Z"),
 *                     @OA\Property(property="updatedAt", type="string", format="date-time", example="2024-09-30T05:45:48.000000Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="post id가 없는 경우"
 *     )
 * )
 */

    public function index()
    {
        if($postId = request()->query('post-id')){
            $data = Comment::where('post_id',$postId)->get();
            return response()->json(['data'=>new CommentCollection($data)],200);
        }else{
            return response()->json(['message'=>'missing post id'],400);
        };
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
 * @OA\Post(
 *     path="/api/comments",
 *     tags={"comment"},
 *     summary="새로운 댓글 생성",
 *     description="요청 본문에 포함된 데이터를 기반으로 새로운 댓글을 생성합니다.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="postId", type="string", description="게시글 ID"),
 *             @OA\Property(property="content", type="string", description="댓글 내용")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="store successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="store successfully")
 *         )
 *     )
 * )
 */
    public function store(StoreCommentRequest $request)
    {
        Comment::create($request->all());
        return response()->json(['message'=>'store successfully']);
    }

    /**
 * @OA\Get(
 *     path="/api/comments/{comment}",
 *     tags={"comment"},
 *     summary="특정 댓글 조회",
 *     description="특정 댓글의 세부 정보를 반환합니다.",
 *     @OA\Parameter(
 *         name="comment",
 *         in="path",
 *         description="댓글 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="댓글 세부 정보",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="postId", type="string", example="1"),
 *                 @OA\Property(property="content", type="string", example="Perspiciatis facilis voluptates error architecto mollitia ex sit."),
 *                 @OA\Property(property="author", type="string", example="naomi.jacobson"),
 *                 @OA\Property(property="createdAt", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z"),
 *                 @OA\Property(property="updatedAt", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="댓글을 찾을 수 없음"
 *     )
 * )
 */
    public function show(Comment $comment)
    {
        return response()->json(['data'=>new CommentResource(Comment::findOrFail($comment->id))],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

       /**
 * @OA\Put(
 *     path="/api/comments/{comment}",
 *     tags={"comment"},
 *     summary="특정 댓글 업데이트",
 *     description="요청 본문에 포함된 데이터를 기반으로 특정 댓글을 업데이트합니다.",
 *     @OA\Parameter(
 *         name="comment",
 *         in="path",
 *         description="댓글 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="content", type="string", description="댓글 내용")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="updated successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="unauthorized",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="unauthorized")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="댓글을 찾을 수 없음"
 *     )
 * )
 */

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $userNickName = Auth::user()->nick_name;
        $data = Comment::findOrFail($comment->id);
        if($data && $data->author === $userNickName){
            $comment->update($request->all());
            return response()->json(['message' => 'updated successfully']);
        }else{
            return response()->json(['message'=>'unauthorized'],401);
        }
    }

    /**
 * @OA\Delete(
 *     path="/api/comments/{comment}",
 *     tags={"comment"},
 *     summary="특정 댓글 삭제",
 *     description="특정 댓글을 삭제합니다.",
 *     @OA\Parameter(
 *         name="comment",
 *         in="path",
 *         description="댓글 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="unauthorized",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="unauthorized")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="댓글을 찾을 수 없음"
 *     )
 * )
 */
    public function destroy(Comment $comment)
    {
        $userNickName = Auth::user()->nick_name;
        $data = Comment::findOrFail($comment->id);
        if($data && $data->author === $userNickName){
            $comment->delete();
            return response()->json(['message' => 'deleted successfully']);
        }else{
            return response()->json(['message'=>'unauthorized'],401);
        }
    }
}
