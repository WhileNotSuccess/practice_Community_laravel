<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /** 
 * @OA\post(
 *     path="/api/image-upload",
 *     tags={"image"},
 *     summary="s3버킷에 이미지 추가",
 *     description="s3버킷에 이미지를 추가하고 추가된 파일의 url을 반환함",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="image"
 *         )
 *     ),
 *     @OA\Response(response="200", description="이미지가 s3버킷에 업로드 됨")
 * )
 **/
    public function upload(Request $request){
        try {
            // 파일 검증
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,gif',
            ]);

            // 파일 이름 설정
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            // 파일 저장
            if(!Storage::disk('s3')->put("/{$fileName}", file_get_contents($request->file('image')))){
                // 실패했을때 실패했다고 알려줌
                return response()->json(['message'=>'fail'],400);
            };
            $url = Storage::disk('s3')->url("/{$fileName}");
            // 성공하면 나중에 접근할 수 있도록, 파일 이름을 응답으로 줍니다.
            return $url;
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
/** 
 * @OA\delete(
 *     path="/api/imageDelete",
 *     tags={"image"},
 *     summary="s3버킷에 이미지 삭제",
 *     description="s3버킷에 이미지를 삭제함",
 *     @OA\Response(response="200", description="이미지가 s3버킷에서 삭제됨")
 * )
 **/
    public function destroy(Request $request){
        try {
            // URL 검증
            $request->validate([
                'url' => 'required|url',
            ]);

            $url = $request->query('url');
            
            // URL에서 파일 경로 추출
            $path = parse_url($url, PHP_URL_PATH);
            $fileName = basename($path);
            
            // 이미지 삭제
            if (Storage::disk('s3')->exists($fileName)) {
                Storage::disk('s3')->delete($fileName);
                return response()->json(['message' => 'success'], 200);
            } else {
                return response()->json(['message' => 'File not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['result' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
