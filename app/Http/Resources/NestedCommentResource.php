<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NestedCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "commentId"=>$this->comment_id,
            "author"=>$this->author,
            "content"=>$this->content,
            "createdAt"=>$this->created_at,
            "updatedAt"=>$this->updated_at,
        ];
    }
}
