<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EnrollStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'student_ids.required' => 'Vui lòng chọn ít nhất một sinh viên để ghi danh.',
            'student_ids.min' => 'Vui lòng chọn ít nhất một sinh viên để ghi danh.',
        ];
    }
}
