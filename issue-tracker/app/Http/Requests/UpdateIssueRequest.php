<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIssueRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'project_id' => ['required', Rule::exists('projects', 'id')->where('owner_id', auth()->id())],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:10000'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'closed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date'],
            'members' => ['sometimes', 'array'],
            'members.*' => ['integer', 'distinct', Rule::exists('users', 'id')],
        ];
    }
}
