<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntiCheatLog extends Model
{
    protected $fillable = [
        'attempt_id',
        'student_id',
        'event_type',
        'event_data',
        'duration_seconds',
        'ip_address',
        'user_agent',
        'occurred_at'
    ];

    protected $casts = [
        'event_data' => 'array',
        'occurred_at' => 'datetime',
        'duration_seconds' => 'integer'
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get human-readable event title and description
     */
    public function getEventInfoAttribute(): array
    {
        switch ($this->event_type) {
            case 'fullscreen_exit':
                return [
                    'title' => 'Thoát chế độ toàn màn hình',
                    'description' => 'Sinh viên đã rời khỏi chế độ toàn màn hình (ESC hoặc chuyển cửa sổ).',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'fullscreen_exit',
                    'severity' => 'high'
                ];
            case 'fullscreen_enter':
                return [
                    'title' => 'Vào lại toàn màn hình',
                    'description' => 'Sinh viên đã kích hoạt lại chế độ toàn màn hình để tiếp tục.',
                    'badge' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'icon' => 'fullscreen_enter',
                    'severity' => 'low'
                ];
            case 'tab_switch':
            case 'window_blur':
                $durationText = $this->duration_seconds ? " (Thời gian rời màn hình: {$this->duration_seconds} giây)" : "";
                return [
                    'title' => 'Rời tab / Mất tiêu điểm bài thi' . $durationText,
                    'description' => 'Sinh viên đã chuyển sang ứng dụng hoặc tab trình duyệt khác.',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'icon' => 'tab_switch',
                    'severity' => 'high'
                ];
            case 'window_focus':
                return [
                    'title' => 'Quay lại cửa sổ bài thi',
                    'description' => 'Sinh viên đã nhấp chuột quay lại màn hình thi.',
                    'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'icon' => 'window_focus',
                    'severity' => 'low'
                ];
            case 'copy':
                return [
                    'title' => 'Cố gắng Sao chép (Copy)',
                    'description' => 'Sinh viên nhấn phím tắt Ctrl+C hoặc thao tác sao chép nội dung.',
                    'badge' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'icon' => 'copy',
                    'severity' => 'medium'
                ];
            case 'paste':
                return [
                    'title' => 'Cố gắng Dán nội dung (Paste)',
                    'description' => 'Sinh viên nhấn phím tắt Ctrl+V hoặc thao tác dán dữ liệu vào bài.',
                    'badge' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'icon' => 'paste',
                    'severity' => 'medium'
                ];
            case 'cut':
                return [
                    'title' => 'Cố gắng Cắt nội dung (Cut)',
                    'description' => 'Sinh viên nhấn phím tắt Ctrl+X hoặc thao tác cắt dữ liệu.',
                    'badge' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'icon' => 'cut',
                    'severity' => 'medium'
                ];
            case 'right_click':
                return [
                    'title' => 'Nhấp chuột phải (Context Menu)',
                    'description' => 'Sinh viên cố gắng mở menu chuột phải.',
                    'badge' => 'bg-slate-100 text-slate-800 border-slate-200',
                    'icon' => 'right_click',
                    'severity' => 'low'
                ];
            case 'page_reload':
                return [
                    'title' => 'Tải lại trang (F5 / Reload)',
                    'description' => 'Sinh viên đã tải lại trang làm bài thi.',
                    'badge' => 'bg-orange-100 text-orange-800 border-orange-200',
                    'icon' => 'page_reload',
                    'severity' => 'medium'
                ];
            case 'connection_lost':
                return [
                    'title' => 'Mất kết nối mạng',
                    'description' => 'Mất tín hiệu kết nối Internet trong lúc làm bài.',
                    'badge' => 'bg-gray-100 text-gray-800 border-gray-200',
                    'icon' => 'connection_lost',
                    'severity' => 'medium'
                ];
            case 'connection_restored':
                return [
                    'title' => 'Kết nối mạng đã khôi phục',
                    'description' => 'Đã kết nối lại Internet thành công.',
                    'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'icon' => 'connection_restored',
                    'severity' => 'low'
                ];
            default:
                return [
                    'title' => 'Sự kiện vi phạm: ' . $this->event_type,
                    'description' => is_array($this->event_data) ? json_encode($this->event_data, JSON_UNESCAPED_UNICODE) : (string) $this->event_data,
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'warning',
                    'severity' => 'medium'
                ];
        }
    }
}
