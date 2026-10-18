<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Điểm Thi - {{ $exam->title }} - Trường Đại học Kỹ thuật - Công nghệ Cần Thơ</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            color: #000000;
            line-height: 1.4;
            font-size: 11pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Thanh điều khiển trên cùng (Ẩn hoàn toàn khi in) */
        .action-bar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #1e293b;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            font-family: system-ui, -apple-system, sans-serif;
        }
        .action-bar-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #f8fafc;
        }
        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-success {
            background: #059669;
            color: #ffffff;
        }
        .btn-success:hover {
            background: #047857;
        }
        .btn-secondary {
            background: #475569;
            color: #ffffff;
        }
        .btn-secondary:hover {
            background: #334155;
        }

        /* Khung tài liệu chuẩn khổ A4 */
        .document-wrapper {
            max-width: 210mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 20mm 15mm;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        /* Header hành chính chuẩn cơ quan Việt Nam */
        .official-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #000000;
            padding-bottom: 12px;
        }
        .header-col-left {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .header-col-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .inst-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .inst-sub {
            font-size: 10pt;
            margin-top: 2px;
        }
        .national-motto {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .national-sub {
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 2px;
            display: inline-block;
            border-bottom: 1px solid #000000;
            padding-bottom: 1px;
        }

        /* Tiêu đề văn bản */
        .doc-title-box {
            text-align: center;
            margin: 18px 0;
        }
        .doc-main-title {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .doc-sub-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .doc-meta {
            font-size: 10pt;
            font-style: italic;
        }

        /* Bảng thông tin bài thi */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-table td {
            padding: 4px 6px;
            font-size: 10.5pt;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 20%;
        }
        .info-value {
            width: 30%;
        }

        /* Tiêu đề mục */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 14px 0 6px 0;
        }

        /* Bảng số liệu và bảng điểm */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 16px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        /* Bảng thống kê phân bố điểm */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 14px;
        }
        .summary-table th, .summary-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
        }
        .summary-table th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
        }

        /* Khu vực ký tên */
        .signature-section {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            display: table;
        }
        .sign-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sign-date {
            font-size: 10.5pt;
            font-style: italic;
            margin-bottom: 4px;
        }
        .sign-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sign-note {
            font-size: 9.5pt;
            font-style: italic;
        }
        .sign-space {
            height: 60px;
        }
        .sign-name {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Định dạng in ấn */
        @media print {
            body {
                background: #ffffff !important;
                font-size: 10.5pt !important;
            }
            .no-print, .action-bar {
                display: none !important;
            }
            .document-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 12mm 15mm 12mm;
            }
            tr {
                page-break-inside: avoid;
            }
            .signature-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Thanh công cụ hỗ trợ người dùng (chỉ hiển thị trên trình duyệt, không in ra giấy/PDF) -->
    <div class="action-bar no-print">
        <div class="action-bar-title">
            Báo Cáo Điểm Thi: {{ $exam->title }} (Môn: {{ $exam->subject->name ?? 'N/A' }})
        </div>
        <div class="action-buttons">
            <button type="button" onclick="window.print()" class="btn btn-primary">
                Lưu PDF / In Báo Cáo
            </button>
            <a href="{{ route('teacher.exams.export-excel', $exam) }}" class="btn btn-success">
                Tải File Excel
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-secondary">
                Quay Lại
            </a>
            <button type="button" onclick="window.close()" class="btn btn-secondary">
                Đóng
            </button>
        </div>
    </div>

    <!-- Khung văn bản A4 -->
    <div class="document-wrapper">
        <!-- Phần tiêu đề cơ quan và quốc hiệu -->
        <div class="official-header">
            <div class="header-col-left">
                <p class="inst-name">TRƯỜNG ĐẠI HỌC KỸ THUẬT - CÔNG NGHỆ CẦN THƠ</p>
                <p class="inst-sub">HỆ THỐNG KHẢO THÍ VÀ QUẢN LÝ KỲ THI</p>
            </div>
            <div class="header-col-right">
                <p class="national-motto">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
                <p class="national-sub">Độc lập - Tự do - Hạnh phúc</p>
            </div>
        </div>

        <!-- Tên báo cáo -->
        <div class="doc-title-box">
            <h1 class="doc-main-title">BẢNG ĐIỂM VÀ BÁO CÁO KẾT QUẢ KỲ THI</h1>
            <p class="doc-sub-title">BÀI THI: {{ mb_strtoupper($exam->title) }}</p>
            <p class="doc-meta">Môn học: {{ $exam->subject->name ?? 'N/A' }} | Mã môn: {{ $exam->subject->code ?? 'N/A' }} | Ngày xuất: {{ $generated_at }}</p>
        </div>

        <!-- Thông tin chi tiết bài thi và môn học -->
        <table class="info-table">
            <tr>
                <td class="info-label">Mã đề thi:</td>
                <td class="info-value font-bold">{{ $exam->code }}</td>
                <td class="info-label">Khối / Danh mục:</td>
                <td class="info-value">{{ $exam->category->name ?? 'Mặc định' }}</td>
            </tr>
            <tr>
                <td class="info-label">Môn học:</td>
                <td class="info-value font-bold">{{ $exam->subject->name ?? 'N/A' }}</td>
                <td class="info-label">Giảng viên phụ trách:</td>
                <td class="info-value font-bold">{{ $teacher_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Cấu trúc đề:</td>
                <td class="info-value">{{ $exam->total_questions }} câu hỏi ({{ $exam->duration_minutes }} phút)</td>
                <td class="info-label">Lớp học:</td>
                <td class="info-value">{{ $assignedClasses }}</td>
            </tr>
            <tr>
                <td class="info-label">Thời gian thi:</td>
                <td class="info-value" colspan="3">
                    {{ $exam->start_at ? 'Từ ' . $exam->start_at->format('d/m/Y H:i') : 'Mở tự do' }} 
                    {{ $exam->end_at ? 'đến ' . $exam->end_at->format('d/m/Y H:i') : ' (không giới hạn thời gian đóng đề)' }}
                </td>
            </tr>
        </table>

        <!-- Mục 1: Tổng hợp kết quả và phân bố điểm số -->
        <div class="section-title">I. TỔNG HỢP KẾT QUẢ VÀ PHÂN BỐ ĐIỂM SỐ</div>
        <table class="summary-table">
            <tr>
                <td style="width: 25%; font-weight: bold;">Tổng số sinh viên nộp bài:</td>
                <td style="width: 25%; text-align: center; font-weight: bold;">{{ $stats['total'] }} sinh viên</td>
                <td style="width: 25%; font-weight: bold;">Tỷ lệ đạt (từ 5.0 điểm trở lên):</td>
                <td style="width: 25%; text-align: center; font-weight: bold;">{{ $stats['pass_rate'] }}% ({{ $stats['passed'] }} sinh viên)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Điểm trung bình toàn bài:</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($stats['average'], 2) }}</td>
                <td style="font-weight: bold;">Tỷ lệ chưa đạt (dưới 5.0 điểm):</td>
                <td style="text-align: center; font-weight: bold;">{{ $stats['fail_rate'] }}% ({{ $stats['failed'] }} sinh viên)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Điểm cao nhất:</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($stats['max'], 2) }}</td>
                <td style="font-weight: bold;">Điểm thấp nhất:</td>
                <td style="text-align: center; font-weight: bold;">{{ number_format($stats['min'], 2) }}</td>
            </tr>
        </table>

        <!-- Bảng phân bố dải điểm chi tiết -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Phân loại học lực</th>
                    <th>Xuất sắc (9.0 - 10.0)</th>
                    <th>Giỏi (8.0 - cận 9.0)</th>
                    <th>Khá (6.5 - cận 8.0)</th>
                    <th>Trung bình (5.0 - cận 6.5)</th>
                    <th>Chưa đạt (dưới 5.0)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-bold text-center">Số lượng</td>
                    <td class="text-center">{{ $stats['brackets']['excellent'] }} sinh viên</td>
                    <td class="text-center">{{ $stats['brackets']['good'] }} sinh viên</td>
                    <td class="text-center">{{ $stats['brackets']['fair'] }} sinh viên</td>
                    <td class="text-center">{{ $stats['brackets']['average'] }} sinh viên</td>
                    <td class="text-center">{{ $stats['brackets']['weak'] }} sinh viên</td>
                </tr>
                <tr>
                    <td class="font-bold text-center">Tỷ lệ</td>
                    <td class="text-center">{{ $stats['total'] > 0 ? round(($stats['brackets']['excellent'] / $stats['total']) * 100, 1) : 0 }}%</td>
                    <td class="text-center">{{ $stats['total'] > 0 ? round(($stats['brackets']['good'] / $stats['total']) * 100, 1) : 0 }}%</td>
                    <td class="text-center">{{ $stats['total'] > 0 ? round(($stats['brackets']['fair'] / $stats['total']) * 100, 1) : 0 }}%</td>
                    <td class="text-center">{{ $stats['total'] > 0 ? round(($stats['brackets']['average'] / $stats['total']) * 100, 1) : 0 }}%</td>
                    <td class="text-center">{{ $stats['total'] > 0 ? round(($stats['brackets']['weak'] / $stats['total']) * 100, 1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>

        <!-- Mục 2: Bảng điểm chi tiết từng sinh viên -->
        <div class="section-title">II. DANH SÁCH ĐIỂM THI CHI TIẾT</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">STT</th>
                    <th style="width: 85px;">Mã Sinh Viên</th>
                    <th>Họ và Tên</th>
                    <th style="width: 70px;">Lớp</th>
                    <th style="width: 55px;">Đúng</th>
                    <th style="width: 50px;">Sai</th>
                    <th style="width: 65px;">Thời Lượng</th>
                    <th style="width: 60px;">Cảnh Báo</th>
                    <th style="width: 60px;">Điểm Số</th>
                    <th style="width: 80px;">Xếp Loại</th>
                    <th style="width: 75px;">Kết Quả</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $item)
                    <tr>
                        <td class="text-center">{{ $item['index'] }}</td>
                        <td class="text-center font-bold">{{ $item['student_code'] }}</td>
                        <td class="font-bold text-left">{{ $item['student_name'] }}</td>
                        <td class="text-center">{{ $item['class_name'] }}</td>
                        <td class="text-center">{{ $item['correct_answers'] }}</td>
                        <td class="text-center">{{ $item['wrong_answers'] }}</td>
                        <td class="text-center">{{ $item['duration_text'] }}</td>
                        <td class="text-center">{{ $item['cheat_warnings'] }} lần</td>
                        <td class="text-center font-bold" style="font-size: 11pt;">{{ $item['score'] }}</td>
                        <td class="text-center">{{ $item['classification'] }}</td>
                        <td class="text-center font-bold">{{ $item['status_result'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 16px; font-style: italic;">
                            Chưa có sinh viên nào hoàn thành hoặc nộp bài thi này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Mục 3: Chữ ký và xác nhận -->
        <div class="signature-section">
            <div class="sign-col">
                <p class="sign-title">TRƯỞNG BỘ MÔN / PHÒNG KHẢO THÍ</p>
                <p class="sign-note">(Ký, ghi rõ họ tên và đóng dấu)</p>
                <div class="sign-space"></div>
            </div>
            <div class="sign-col">
                <p class="sign-date">Cần Thơ, ngày ..... tháng ..... năm 20....</p>
                <p class="sign-title">GIẢNG VIÊN PHỤ TRÁCH / NGƯỜI LẬP BẢNG</p>
                <p class="sign-note">(Ký và ghi rõ họ tên)</p>
                <div class="sign-space"></div>
                <p class="sign-name">{{ $teacher_name }}</p>
            </div>
        </div>
    </div>

    <!-- Tự động mở hộp thoại in nếu có tham số ?print=1 -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1' || urlParams.get('auto') === '1') {
                setTimeout(() => window.print(), 400);
            }
        });
    </script>
</body>
</html>
