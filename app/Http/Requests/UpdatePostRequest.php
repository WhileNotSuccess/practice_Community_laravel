<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
        if($this->method() == 'PUT'){
            return [
                'title'=>['required', 'string'],
                'content'=>['required', 'string'],
                'category'=>['required', 'string'],
            ];
        }else{
            return [
                'title'=>['sometimes','required', 'string'],
                'content'=>['sometimes','required', 'string'],
                'category'=>['sometimes','required', 'string'],
            ];
        }
       
    }
}
