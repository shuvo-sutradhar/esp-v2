<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $id = $this->route('tags');
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('tags', 'name')->ignore($id)],
            'color' => ['nullable', 'string', 'max:20'],
        ];
    }
}


