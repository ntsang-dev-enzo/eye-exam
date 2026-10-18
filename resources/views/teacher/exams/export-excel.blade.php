{!! "\xEF\xBB\xBF" !!}<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Bảng Điểm - {{ $exam->title }} - Trường Đại học Kỹ thuật - Công nghệ Cần Thơ</title>
    @php
        echo '<!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>' . htmlspecialchars(mb_substr(preg_replace('/[\\\\\/?*\[\]]/', '', $exam->title), 0, 31)) . '</x:Name>
                        <x:WorksheetOptions>
                            <x:DisplayGridlines/>
                            <x:Print>
                                <x:ValidPrinterInfo/>
                                <x:PaperSizeIndex>9</x:PaperSizeIndex>
                            </x:Print>
                        </x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->';
    @endphp
    <style>
        body, table {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        .main-title {
            font-size: 15pt;
            font-weight: bold;
            color: #000000;
            text-align: center;
        }
        .sub-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            color: #000000;
        }
        .header-meta {
            font-size: 10.5pt;
            color: #333333;
        }
        
        .table-data {
            border-collapse: collapse;
            width: 100%;
        }
        .th-header {
            background-color: #f1f5f9;
            color: #000000;
            font-weight: bold;
            border: 1px solid #000000;
            text-align: center;
            padding: 8px 6px;
        }
        .th-sub {
            background-color: #f8fafc;
            color: #000000;
            font-weight: bold;
            border: 1px solid #000000;
            text-align: left;
            padding: 6px 8px;
        }
        .td-data {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
            color: #000000;
        }
        
        .mso-text { mso-number-format: "\@"; }
        .mso-score { mso-number-format: "0\.00"; }
        
        .label-cell {
            border: 1px solid #000000;
            background-color: #fafafa;
            font-weight: bold;
            padding: 5px 8px;
        }
    </style>
</head>
<body>
    <table class="table-data" cellpadding="0" cellspacing="0" border="0">
        <!-- ĐƠN VỊ & TIÊU NGỮ -->
        <tr>
            <td colspan="5" class="text-center font-bold" style="font-size: 11pt;">
                TRƯỜNG ĐẠI HỌC KỸ THUẬT - CÔNG NGHỆ CẦN THƠ<br>
                <span style="font-size: 10pt; font-weight: normal;">HỆ THỐNG KHẢO THÍ VÀ QUẢN LÝ KỲ THI</span>
            </td>
            <td colspan="7" class="text-center font-bold" style="font-size: 11pt;">
                CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>
                <span style="font-size: 10pt; font-weight: normal; text-decoration: underline;">Độc lập - Tự do - Hạnh phúc</span>
            </td>
        </tr>
        <tr><td colspan="12" style="height: 12px;"></td></tr>

        <!-- TIÊU ĐỀ BÁO CÁO -->
        <tr>
            <td colspan="12" class="main-title">
                BẢNG ĐIỂM VÀ BÁO CÁO KẾT QUẢ KỲ THI
            </td>
        </tr>
        <tr>
            <td colspan="12" class="sub-title">
                BÀI THI: {{ mb_strtoupper($exam->title) }}
            </td>
        </tr>
        <tr>
            <td colspan="12" class="text-center header-meta" style="font-style: italic;">
                (Môn học: {{ $exam->subject->name ?? 'N/A' }} - Ngày xuất báo cáo: {{ $generated_at }})
            </td>
        </tr>
        <tr><td colspan="12" style="height: 10px;"></td></tr>

        <!-- THÔNG TIN CHI TIẾT BÀI THI & MÔN HỌC -->
        <tr>
            <td colspan="2" class="label-cell">Tên bài thi:</td>
            <td colspan="4" class="td-data font-bold">{{ $exam->title }}</td>
            <td colspan="2" class="label-cell">Mã đề thi:</td>
            <td colspan="4" class="td-data mso-text font-bold">{{ $exam->code }}</td>
        </tr>
        <tr>
            <td colspan="2" class="label-cell">Môn học:</td>
            <td colspan="4" class="td-data font-bold">{{ $exam->subject->name ?? 'N/A' }} (Mã môn: {{ $exam->subject->code ?? 'N/A' }})</td>
            <td colspan="2" class="label-cell">Danh mục kỳ thi:</td>
            <td colspan="4" class="td-data">{{ $exam->category->name ?? 'Mặc định' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="label-cell">Giảng viên phụ trách:</td>
            <td colspan="4" class="td-data font-bold">{{ $teacher_name }}</td>
            <td colspan="2" class="label-cell">Lớp học:</td>
            <td colspan="4" class="td-data">{{ $assignedClasses }}</td>
        </tr>
        <tr>
            <td colspan="2" class="label-cell">Cấu trúc bài thi:</td>
            <td colspan="4" class="td-data">{{ $exam->total_questions }} câu hỏi, thời gian làm bài {{ $exam->duration_minutes }} phút, thang điểm 10.0</td>
            <td colspan="2" class="label-cell">Thời gian thi:</td>
            <td colspan="4" class="td-data">
                {{ $exam->start_at ? 'Từ ' . $exam->start_at->format('d/m/Y H:i') : 'Mở tự do' }} 
                {{ $exam->end_at ? 'đến ' . $exam->end_at->format('d/m/Y H:i') : ' (không giới hạn)' }}
            </td>
        </tr>
        <tr><td colspan="12" style="height: 12px;"></td></tr>

        <!-- TỔNG KẾT THỐNG KÊ (KPIs) -->
        <tr>
            <td colspan="12" class="th-sub">
                I. TỔNG HỢP KẾT QUẢ VÀ PHÂN BỐ ĐIỂM SỐ
            </td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Tổng số sinh viên nộp bài:</td>
            <td colspan="3" class="td-data text-center font-bold">{{ $stats['total'] }} sinh viên</td>
            <td colspan="3" class="label-cell">Tỷ lệ đạt (từ 5.0 điểm trở lên):</td>
            <td colspan="3" class="td-data text-center font-bold">{{ $stats['pass_rate'] }}% ({{ $stats['passed'] }} sinh viên)</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Điểm trung bình toàn bài:</td>
            <td colspan="3" class="td-data text-center font-bold mso-score">{{ number_format($stats['average'], 2) }}</td>
            <td colspan="3" class="label-cell">Tỷ lệ chưa đạt (dưới 5.0 điểm):</td>
            <td colspan="3" class="td-data text-center font-bold">{{ $stats['fail_rate'] }}% ({{ $stats['failed'] }} sinh viên)</td>
        </tr>
        <tr>
            <td colspan="3" class="label-cell">Điểm cao nhất:</td>
            <td colspan="3" class="td-data text-center font-bold mso-score">{{ number_format($stats['max'], 2) }}</td>
            <td colspan="3" class="label-cell">Điểm thấp nhất:</td>
            <td colspan="3" class="td-data text-center font-bold mso-score">{{ number_format($stats['min'], 2) }}</td>
        </tr>
        
        <!-- Bảng phân bố dải điểm -->
        <tr>
            <td colspan="12" class="td-data" style="background-color: #fafafa; font-size: 10.5pt; padding: 6px 10px;">
                <strong>Phân loại học lực:</strong>
                Xuất sắc (9.0 đến 10.0 điểm): {{ $stats['brackets']['excellent'] }} sinh viên ({{ $stats['total'] > 0 ? round(($stats['brackets']['excellent'] / $stats['total']) * 100, 1) : 0 }}%) |
                Giỏi (8.0 đến cận 9.0 điểm): {{ $stats['brackets']['good'] }} sinh viên ({{ $stats['total'] > 0 ? round(($stats['brackets']['good'] / $stats['total']) * 100, 1) : 0 }}%) |
                Khá (6.5 đến cận 8.0 điểm): {{ $stats['brackets']['fair'] }} sinh viên ({{ $stats['total'] > 0 ? round(($stats['brackets']['fair'] / $stats['total']) * 100, 1) : 0 }}%) |
                Trung bình (5.0 đến cận 6.5 điểm): {{ $stats['brackets']['average'] }} sinh viên ({{ $stats['total'] > 0 ? round(($stats['brackets']['average'] / $stats['total']) * 100, 1) : 0 }}%) |
                Chưa đạt (dưới 5.0 điểm): {{ $stats['brackets']['weak'] }} sinh viên ({{ $stats['total'] > 0 ? round(($stats['brackets']['weak'] / $stats['total']) * 100, 1) : 0 }}%)
            </td>
        </tr>
        <tr><td colspan="12" style="height: 12px;"></td></tr>

        <!-- BẢNG ĐIỂM CHI TIẾT TỪNG SINH VIÊN -->
        <tr>
            <td colspan="12" class="th-sub">
                II. DANH SÁCH ĐIỂM THI CHI TIẾT
            </td>
        </tr>
        <thead>
            <tr>
                <th class="th-header" style="width: 45px;">STT</th>
                <th class="th-header" style="width: 105px;">Mã Sinh Viên</th>
                <th class="th-header" style="width: 210px;">Họ và Tên</th>
                <th class="th-header" style="width: 95px;">Lớp Học</th>
                <th class="th-header" style="width: 75px;">Điểm Số</th>
                <th class="th-header" style="width: 80px;">Số Câu Đúng</th>
                <th class="th-header" style="width: 80px;">Số Câu Sai</th>
                <th class="th-header" style="width: 80px;">Chưa Làm</th>
                <th class="th-header" style="width: 135px;">Thời Gian Nộp</th>
                <th class="th-header" style="width: 85px;">Thời Lượng</th>
                <th class="th-header" style="width: 90px;">Cảnh Báo</th>
                <th class="th-header" style="width: 90px;">Kết Quả</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attempts as $item)
                <tr>
                    <td class="td-data text-center">{{ $item['index'] }}</td>
                    <td class="td-data text-center mso-text font-bold">{{ $item['student_code'] }}</td>
                    <td class="td-data font-bold text-left">{{ $item['student_name'] }}</td>
                    <td class="td-data text-center">{{ $item['class_name'] }}</td>
                    <td class="td-data text-center font-bold mso-score" style="font-size: 11pt;">
                        {{ $item['score'] }}
                    </td>
                    <td class="td-data text-center">{{ $item['correct_answers'] }}</td>
                    <td class="td-data text-center">{{ $item['wrong_answers'] }}</td>
                    <td class="td-data text-center">{{ $item['unanswered'] }}</td>
                    <td class="td-data text-center">{{ $item['submitted_at'] }}</td>
                    <td class="td-data text-center">{{ $item['duration_text'] }}</td>
                    <td class="td-data text-center">
                        {{ $item['cheat_warnings'] }} lần
                    </td>
                    <td class="td-data text-center font-bold">
                        {{ $item['status_result'] }} ({{ $item['classification'] }})
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="td-data text-center" style="padding: 16px; font-style: italic;">
                        Chưa có sinh viên nào hoàn thành hoặc nộp bài thi này.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <!-- CHỮ KÝ XÁC NHẬN -->
        <tr><td colspan="12" style="height: 20px;"></td></tr>
        <tr>
            <td colspan="6" class="text-center"></td>
            <td colspan="6" class="text-center font-bold" style="font-style: italic;">
                Cần Thơ, ngày ..... tháng ..... năm 20....
            </td>
        </tr>
        <tr>
            <td colspan="6" class="text-center font-bold" style="font-size: 11pt;">
                TRƯỞNG BỘ MÔN / PHÒNG KHẢO THÍ<br>
                <span style="font-size: 10pt; font-weight: normal; font-style: italic;">(Ký và ghi rõ họ tên)</span>
            </td>
            <td colspan="6" class="text-center font-bold" style="font-size: 11pt;">
                GIẢNG VIÊN PHỤ TRÁCH / NGƯỜI LẬP BẢNG<br>
                <span style="font-size: 10pt; font-weight: normal; font-style: italic;">(Ký và ghi rõ họ tên)</span>
            </td>
        </tr>
        <tr><td colspan="12" style="height: 55px;"></td></tr>
        <tr>
            <td colspan="6" class="text-center"></td>
            <td colspan="6" class="text-center font-bold">
                {{ $teacher_name }}
            </td>
        </tr>
    </table>
</body>
</html>
