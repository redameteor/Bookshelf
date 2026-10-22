<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title'        => 'required|string|max:255',
            'author'       => 'required|string|max:255',
            'isbn'         => ['nullable', 'string', 'digits:13', Rule::unique('books', 'isbn')->ignore($bookId)],
            'published_at' => 'nullable|date',
            'genre_ids'    => 'required|array|min:1',
            'genre_ids.*'  => 'exists:genres,id',
            'image_url'    => 'nullable|url|max:255',
            'description'  => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'author.required' => '著者名を入力してください。',
            'author.max' => '著者名は255文字以内で入力してください。',
            'isbn.digits' => 'ISBNは13桁の数字で入力してください。',
            'isbn.unique' => 'このISBNは既に使用されています。',
            'published_at.date' => '出版日は有効な日付である必要があります。',
            'genre_ids.required' => 'ジャンルは１つ以上選択してください。',
            'genre_ids.min' => 'ジャンルは１つ以上選択してください。',
            'genre_ids.*.exists' => '選択されたジャンルが存在しません。',
            'image_url.url' => '画像URLは有効なURL形式である必要があります。',
            'image_url.max' => '画像URLの長さは255文字以内にしてください。',
        ];
    }
}
