<?php

namespace App\Http\Requests\Api\Books;

use App\Enums\Roles;
use Illuminate\Foundation\Http\FormRequest;

class SearchGoogleBooksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(Roles::ADMIN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => 'required|string|min:2|max:255',
            // As per API docs, maxResults can be between 0 and 40
            'maxResults' => 'nullable|integer|min:0|max:40',
            // As per API docs, startIndex start from 0
            'startIndex' => 'nullable|integer|min:0',
        ];
    }
}
