<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'direction_id' => ['required', 'integer', 'exists:directions,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:courses,slug'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'duration_hours' => ['nullable', 'integer', 'min:0', 'max:32767'],
        ];
    }
}
