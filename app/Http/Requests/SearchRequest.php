<?php

namespace App\Http\Requests;

use App\Rules\ValidCategory;
use App\Rules\ValidTarget;
use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'target'=>['required','string', new ValidTarget],
            'content'=>['required','string'],
            'category'=>['sometimes','required','string',new ValidCategory]
        ];
    }
}
