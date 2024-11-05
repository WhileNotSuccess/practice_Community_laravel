<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\PostCollection;
use App\Models\Comment;
use App\Models\NestedComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;

class SearchController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/search",
 *     tags={"post"},
 *     summary="게시글 검색",
 *     description="사용자의 검색내용을 띄워쓰기를 - 로 붙여서 보내면(url에 띄어쓰기가 안되니까), 백엔드에서는 다시 - 를 기준으로 띄워서 배열을 만들고 그 배열의 값중 하나라도 포함하면 응답에 포함함",
 *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="게시판 카테고리 없으면 전체 게시판에서 검색",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             example="자유게시판, 축제게시판, 공지사항"
 *         )
 *     ),
 *     @OA\Parameter(
 *             name="content",
 *             in="query",
 *             description="검색내용; 반드시 띄워쓰기를 - 로 바꿔서 넣을것",
 *             required=true,
 *             @OA\Schema(
 *                 type="string",
 *                 example="안녕하세요-감사해요-잘있어요-다시만나요"
 *             )
 *         ),
 *     @OA\Parameter(
 *             name="target",
 *             in="query",
 *             description="검색대상; 제목,내용,작성자중의 하나를 골라야함",
 *             required=true,
 *             @OA\Schema(
 *                 type="string",
 *                 example="title, content, author"
 *             )
 *         ),
 *     @OA\Parameter(
 *             name="limit",
 *             in="query",
 *             description="페이지당 게시글 수, 기본값 10",
 *             required=false,
 *             @OA\Schema(
 *                 type="integer",
 *                 example="10"
 *             )
 *         ),
 *     @OA\Response(
 *         response=200,
 *         description="게시글 배열들",
 *         @OA\JsonContent(
 *             type="object",
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
 *                     @OA\Property(property="createdAt", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z"),
 *                     @OA\Property(property="updatedAt", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z")
 *                 )
 *             ),
 *             @OA\Property(property="currentPage", type="integer", example=1),
 *             @OA\Property(property="totalPage", type="integer", example=10),
 *             @OA\Property(property="nextPage", type="string", example="http://localhost:8000/api/search?target=title&content=안녕하세요&limit=10&category=자유게시판&page=2"),
 *             @OA\Property(property="prevPage", type="string", example="http://localhost:8000/api/search?target=title&content=안녕하세요&limit=10&category=자유게시판&page=1")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="쿼리중 특정 값이 잘못된 경우; title이 없다거나, target이 title,author,content 중 하나여야 하는데 다른 값이 들어갔거나, content가 없거나..."
 *     )
 * )
 */
    public function index(SearchRequest $request)
    {
        $target = $request->query('target');
        $content = $request->query('content');
        $limit = $request->query('limit');
        if(!$limit){
            $limit = 10;
        }
        $contentArray = explode('-',$content);
        $regular = array_map(function($string){
            return '%'.$string.'%';
        },$contentArray);
        $query = DB::table('posts');

        foreach ($regular as $pattern) {
            $query->orWhere($target, 'like', $pattern);
        }
        if($category = $request->query('category')){
            $query->where('category',$category);
        }

        $posts = $query->latest()->paginate($limit);
        $currentPage = $posts->currentPage();
        $totalPage = $posts->lastPage();
        $page =  $posts->appends(['target' => $target,'content' => $content,'limit'=>$limit]);
        if($category){
            $page->appends(['category'=>$category]);
        }
        $nextPage = $page->nextPageUrl();
        $prevPage = $page->previousPageUrl();
        return response()->json(['data' => new PostCollection($posts),'currentPage'=>$currentPage,'totalPage'=>$totalPage,'nextPage'=>$nextPage,'prevPage'=>$prevPage]);
    }
    /**
     * @OA\Get(
     *     path="/api/find-post-by-comment",
     *     tags={"post"},
     *     summary="사용자가 댓글을 단 게시글 조회",
     *     description="사용자의 아이디를 받아서 그 사람이 댓글을 단 게시글 조회",  
     *     @OA\Parameter(
     *         name="nickName",
     *         in="header",
     *         description="사용자 닉네임",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="권기현"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="페이지당 게시글 수, 기본값 10",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example="10"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="사용자의 게시글 배열들",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="currentPage", type="string", example="2"),
     *            @OA\Property(property="totalPage", type="string", example="16"),
     *            @OA\Property(property="nextPage", type="string", example="http://localhost:8000/api/find-post-by-comment/1?limit=3&page=3"),
     *            @OA\Property(property="prevPage", type="string", example="http://localhost:8000/api/find-post-by-comment/1?limit=3&page=1"),
     *            @OA\Property(  
     *                property="data",
     *                type="array",
     *                @OA\Items(
     *                    type="object",
     *                    @OA\Property(property="id", type="integer", example=2),
     *                    @OA\Property(property="title", type="string", example="Prof."),
     *                    @OA\Property(property="content", type="string", example="Sunt excepturi ad officiis laudantium."),
     *                    @OA\Property(property="author", type="string", example="rylan65"),
     *                    @OA\Property(property="category", type="string", example="자유게시판"),
     *                    @OA\Property(property="createdAt", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z"),
     *                    @OA\Property(property="updatedAt", type="string", format="date-time", example="2024-09-30T05:45:46.000000Z")
     *                )
     *            )
     *         )
     *     )
     * )
     */
    public function userPost(Request $request){
        $userNickName = $request->header('nickName');
        if(!$userNickName){
            return response()->json(['message'=> '헤더에 유저정보가 없습니다.'],400);
        }
        $user = User::where('nick_name',$userNickName)->first();
        $limit = request()->query('limit');
        if(!$limit){
            $limit = 10;
        }
      
        $comments = Comment::where('author',$user->nick_name)->pluck('post_id');
        $nestedComment = NestedComment::where('author', $user->nick_name)->pluck('comment_id');
        $commentsFromNestedComment = Comment::whereIn('id', $nestedComment)->pluck('post_id');
        $postIds =  array_merge($commentsFromNestedComment->toArray(),$comments->toArray());
        $posts = Post::whereIn('id', $postIds)->paginate($limit);
        $currentPage = $posts->currentPage();
        $totalPage = $posts->lastPage();
        $nextPage = $posts->appends(['limit'=>$limit])->nextPageUrl();
        $prevPage = $posts->appends(['limit'=>$limit])->previousPageUrl();
        return response()->json([
            'data' => new PostCollection($posts),
            'currentPage'=>$currentPage,
            'totalPage'=>$totalPage,
            'nextPage'=>$nextPage,
            'prevPage'=>$prevPage
        ]);
    }
}
