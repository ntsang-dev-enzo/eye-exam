<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:classes,code',
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'semester_id' => 'required|exists:semesters,id',
            'teacher_id' => [
                'required',
                'exists:users,id',
                Rule::exists('subject_teacher', 'teacher_id')->where(function ($query) {
                    $query->where('subject_id', $this->subject_id);
                }),
            ],
            'room' => 'nullable|string|max:100',
            'max_students' => 'required|integer|min:1|max:300',
            'description' => 'nullable|string',
            'status' => 'nullable|in:open,ongoing,closed',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã lớp học phần.',
            'code.unique' => 'Mã lớp học phần đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên lớp học phần.',
            'subject_id.required' => 'Vui lòng chọn Môn học cho lớp học phần.',
            'semester_id.required' => 'Vui lòng chọn Học kỳ áp dụng.',
            'teacher_id.required' => 'Vui lòng chọn Giảng viên đứng lớp.',
            'teacher_id.exists' => 'Giảng viên được chọn chưa được cấp quyền / chưa đủ điều kiện dạy môn học này.',
            'max_students.required' => 'Vui lòng nhập sĩ số tối đa của lớp.',
            'max_students.min' => 'Sĩ số tối thiểu là 1 sinh viên.',
        ];
    }
}
