<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'faculty_id' => 'required|exists:faculties,id',
            'code' => 'required|string|max:50|unique:departments,code',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'faculty_id.required' => 'Vui lòng chọn Khoa trực thuộc.',
            'faculty_id.exists' => 'Khoa được chọn không hợp lệ.',
            'code.required' => 'Vui lòng nhập mã Bộ môn.',
            'code.unique' => 'Mã Bộ môn đã tồn tại trong hệ thống.',
            'name.required' => 'Vui lòng nhập tên Bộ môn.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ];
    }
}
