<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'portfolio_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('portfolios', 'id')->whereNull('deleted_at'),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'body' => ['sometimes', 'nullable', 'string'],
            'project_date' => ['sometimes', 'nullable', 'date'],
            'cover_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'status' => ['sometimes', 'nullable', 'string', 'in:draft,published'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}