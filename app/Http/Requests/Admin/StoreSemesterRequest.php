<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSemesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:semesters,code',
            'name' => 'required|string|max:50',
            'academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã học kỳ (VD: HK1_2025_2026).',
            'code.unique' => 'Mã học kỳ đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên học kỳ (VD: Học kỳ 1).',
            'academic_year.required' => 'Vui lòng nhập năm học.',
            'academic_year.regex' => 'Năm học phải theo định dạng YYYY-YYYY (VD: 2025-2026).',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }
}
