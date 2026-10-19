<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|string|max:50|unique:subjects,code',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:15',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Vui lòng chọn Bộ môn trực thuộc cho môn học.',
            'department_id.exists' => 'Bộ môn được chọn không hợp lệ.',
            'code.required' => 'Vui lòng nhập mã môn học.',
            'code.unique' => 'Mã môn học này đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên môn học.',
            'credits.required' => 'Vui lòng nhập số tín chỉ.',
            'credits.min' => 'Số tín chỉ tối thiểu là 1.',
            'credits.max' => 'Số tín chỉ tối đa là 15.',
        ];
    }
}
