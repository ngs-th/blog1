<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $post = $this->route('post');

        if (! $post instanceof Post) {
            return false;
        }

        return auth()->check() && auth()->user()->can('update', $post);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string', 'min:10'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A post title is required.',
            'title.max' => 'The post title cannot exceed 255 characters.',
            'content.required' => 'Post content is required.',
            'content.min' => 'Post content must be at least 10 characters long.',
            'published_at.date' => 'The publication date must be a valid date.',
        ];
    }
}
