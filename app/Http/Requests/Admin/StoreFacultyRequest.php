<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:faculties,code',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã Khoa.',
            'code.unique' => 'Mã Khoa đã tồn tại trong hệ thống.',
            'name.required' => 'Vui lòng nhập tên Khoa.',
            'status.required' => 'Vui lòng chọn trạng thái hoạt động.',
        ];
    }
}
