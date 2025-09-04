<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'unique:books,isbn', 'max:13'],
            'description' => ['nullable', 'string'],
            'genre' => ['nullable', 'string', 'max:100'],
            'published_date' => ['nullable', 'date'],
            'total_pages' => ['nullable', 'integer', 'min:1'],
            'available_copies' => ['nullable', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:active,inactive'],
            'author_id' => ['required', 'exists:authors,id'],
        ];
    }
}
