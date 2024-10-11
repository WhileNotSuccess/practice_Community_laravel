<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNestedCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "commentId" =>['required', 'string'],
            "content" =>['required', 'string'],
        ];
    }

    protected function prepareForValidation(){
        $this->merge([
            'comment_id'=>$this->commentId,
            'author'=>$this->user()->nick_name
        ]);
    }
}
