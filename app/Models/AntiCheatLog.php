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
        'snapshot_path',
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
            case 'phone_detected':
                return [
                    'title' => 'Phát hiện điện thoại di động',
                    'description' => 'AI (YOLO) phát hiện thiết bị điện thoại trong khung hình camera.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'smartphone',
                    'severity' => 'high'
                ];
            case 'multiple_persons':
                return [
                    'title' => 'Phát hiện nhiều người trước camera',
                    'description' => 'AI phát hiện nhiều người cùng xuất hiện trong không gian thi.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'group',
                    'severity' => 'high'
                ];
            case 'face_absent':
                return [
                    'title' => 'Không có thí sinh trước màn hình',
                    'description' => 'Camera không phát hiện thấy thí sinh ngồi trước màn hình.',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'icon' => 'person_off',
                    'severity' => 'high'
                ];
            case 'face_mismatch':
                return [
                    'title' => 'Khuôn mặt không trùng khớp (Nghi vấn thi hộ)',
                    'description' => 'Độ tương đồng khuôn mặt qua ArcFace thấp hơn ngưỡng an toàn.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'face_retouching_off',
                    'severity' => 'high'
                ];
            case 'too_far':
                $msg = is_array($this->event_data) ? ($this->event_data['message'] ?? null) : null;
                return [
                    'title' => 'Ngồi quá xa camera (Ngoài cự ly chuẩn)',
                    'description' => $msg ?: 'Thí sinh ngồi quá xa camera (ngoài cự ly chuẩn). Yêu cầu ngồi lại gần màn hình.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'zoom_out',
                    'severity' => 'high'
                ];
            case 'off_center':
                $msg = is_array($this->event_data) ? ($this->event_data['message'] ?? null) : null;
                return [
                    'title' => 'Ngồi lệch khỏi trung tâm camera',
                    'description' => $msg ?: 'Thí sinh ngồi lệch khỏi trung tâm camera. Yêu cầu ngồi ở vị trí chính giữa màn hình.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'center_focus_weak',
                    'severity' => 'high'
                ];
            case 'looking_away':
            case 'head_turned_sustained':
                $msg = is_array($this->event_data) ? ($this->event_data['message'] ?? $this->event_data['summary'] ?? null) : null;
                $dirText = is_array($this->event_data) ? ($this->event_data['direction_text'] ?? null) : null;
                $durText = $this->duration_seconds ? " ({$this->duration_seconds}s)" : "";
                return [
                    'title' => ($dirText ? "Quay mặt liên tục: {$dirText}{$durText}" : "Quay mặt / Không nhìn trực diện{$durText}"),
                    'description' => $msg ?: 'AI phát hiện thí sinh quay đầu liên tục trong thời gian thi (hành vi bất thường).',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'visibility_off',
                    'severity' => 'high'
                ];
            case 'suspicious_device':
                return [
                    'title' => 'Phát hiện thiết bị máy tính phụ',
                    'description' => 'AI phát hiện thiết bị laptop hoặc màn hình thứ hai trong khung hình.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'laptop',
                    'severity' => 'high'
                ];
            case 'suspicious_object':
                return [
                    'title' => 'Phát hiện tài liệu / Sách vở',
                    'description' => 'AI phát hiện sách vở hoặc tài liệu trong khung hình.',
                    'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'icon' => 'menu_book',
                    'severity' => 'medium'
                ];
            case 'proctor_violation':
                $detail = is_array($this->event_data) ? ($this->event_data['summary'] ?? json_encode($this->event_data, JSON_UNESCAPED_UNICODE)) : (string) $this->event_data;
                return [
                    'title' => 'Cảnh báo giám sát AI: ' . ($this->event_data['violation_type'] ?? 'Hành vi bất thường'),
                    'description' => $detail ?: 'Hệ thống AI giám sát phát hiện dấu hiệu bất thường.',
                    'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'icon' => 'warning',
                    'severity' => 'high'
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

    public function getSnapshotUrlAttribute(): ?string
    {
        if (empty($this->snapshot_path)) {
            return null;
        }
        if (str_starts_with($this->snapshot_path, 'http')) {
            return $this->snapshot_path;
        }
        return route('secure.media.log', $this->id);
    }
}
