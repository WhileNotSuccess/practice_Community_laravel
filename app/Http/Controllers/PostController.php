<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Support\Facades\Auth;

/**
* @OA\Info(
*     title="community", version="0.1", description="API Documentation",
*     @OA\Contact(
*         email="borygashill608@gmail.com",
*         name="MunSeongYun"
*     )
* )
*/
class PostController extends Controller
{
 /**
 * @OA\Get(
 *     path="/api/posts",
 *     tags={"post"},
 *     summary="posts 테이블",
 *     description="posts 테이블에 등록된 모든 데이터를 data라는 키를 가진 배열로 반환, 전체페이지, 현재페이지, 다음페이지url, 이전페이지url 제공",
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="게시판 카테고리",
 *         required=false,
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="게시글 배열들",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="currentPage", type="string", example="2"),
 *             @OA\Property(property="totalPage", type="string", example="16"),
 *             @OA\Property(property="nextPage", type="string", example="http://localhost:8000/api/posts?category=%EC%9E%90%EC%9C%A0%EA%B2%8C%EC%8B%9C%ED%8C%90&page=3"),
 *             @OA\Property(property="prevPage", type="string", example="http://localhost:8000/api/posts?category=%EC%9E%90%EC%9C%A0%EA%B2%8C%EC%8B%9C%ED%8C%90&page=1"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=2),
 *                     @OA\Property(property="title", type="string", example="Prof."),
 *                     @OA\Property(property="content", type="string", example="Sunt excepturi ad officiis laudantium."),
 *                     @OA\Property(property="author", type="string", example="rylan65"),
 *                     @OA\Property(property="category", type="string", example="자유게시판"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="카테고리가 없는 경우"
 *     )
 * )
 */

public function index()
{
    if ($category = request()->query('category')) {
        $data = Post::where('category', $category)->paginate(10);
        $currentPage = $data->currentPage();
        $totalPage = $data->lastPage();
        $nextPage = $data->appends(['category' => $category])->nextPageUrl();
        $prevPage = $data->appends(['category' => $category])->previousPageUrl();
        
        return response()->json([
            'currentPage' => $currentPage,
            'totalPage' => $totalPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'data' => new PostCollection($data)
        ], 200);
    } else {
        return response()->json(['message' => 'category missing'], 400);
    }
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
 *     path="/api/posts",
 *     tags={"post"},
 *     summary="새로운 게시글 생성",
 *     description="요청 본문에 포함된 데이터를 기반으로 새로운 게시글을 생성합니다.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="title", type="string", description="게시글 제목"),
 *             @OA\Property(property="content", type="string", description="게시글 내용"),
 *             @OA\Property(property="category", type="string", description="게시글 카테고리")
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

    public function store(StorePostRequest $request)
    {
        Post::create($request->all());
        return response()->json(['message'=>'store successfully']);
    }
/**
 * @OA\Get(
 *     path="/api/posts/{post}",
 *     tags={"post"},
 *     summary="특정 게시글 조회",
 *     description="특정 게시글의 세부 정보를 반환합니다.",
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="게시글 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="게시글 세부 정보",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Dr."),
 *                 @OA\Property(property="content", type="string", example="Perspiciatis facilis voluptates error architecto mollitia ex sit."),
 *                 @OA\Property(property="author", type="string", example="naomi.jacobson"),
 *                 @OA\Property(property="category", type="string", example="축제게시판"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="게시글을 찾을 수 없음"
 *     )
 * )
 */

    public function show(Post $post)
    {
        return response()->json(['data'=>new PostResource(Post::findOrFail($post->id))],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
 * @OA\Put(
 *     path="/api/posts/{post}",
 *     tags={"post"},
 *     summary="특정 게시글 업데이트",
 *     description="요청 본문에 포함된 데이터를 기반으로 특정 게시글을 업데이트합니다.",
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="게시글 ID",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="title", type="string", description="게시글 제목"),
 *             @OA\Property(property="content", type="string", description="게시글 내용"),
 *             @OA\Property(property="category", type="string", description="게시글 카테고리")
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
 *         description="게시글을 찾을 수 없음"
 *     )
 * )
 */

    public function update(UpdatePostRequest $request, Post $post)
    {
        $userEmail = Auth::user()->email;
        $data = Post::findOrFail($post->id);
        if($data && $data->author === $userEmail){
            $post->update($request->all());
            return response()->json(['message' => 'updated successfully']);
        }else{
            return response()->json(['message'=>'unauthorized'],401);
        }
    }

    /**
 * @OA\Delete(
 *     path="/api/posts/{post}",
 *     tags={"post"},
 *     summary="특정 게시글 삭제",
 *     description="특정 게시글을 삭제합니다.",
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="게시글 ID",
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
 *         description="게시글을 찾을 수 없음"
 *     )
 * )
 */

    public function destroy(Post $post)
    {
        $userEmail = Auth::user()->email;
        $data = Post::findOrFail($post->id);
        if($data && $data->author === $userEmail){
            $post->delete();
            return response()->json(['message' => 'deleted successfully']);
        }else{
            return response()->json(['message'=>'unauthorized'],401);
        }
    }
}
