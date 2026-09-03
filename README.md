# EYE-EXAM: Hệ Thống Thi Trực Tuyến & Giám Sát Chống Gian Lận Bằng AI

> **Eye-Exam** là nền tảng thi trực tuyến thông minh kết hợp giám sát thi tự động (AI Proctoring System). Hệ thống ứng dụng thị giác máy tính (Computer Vision) và học sâu (Deep Learning) để định danh thí sinh qua Face ID, giám sát hành vi trong phòng thi theo thời gian thực và tự động phát hiện các biểu hiện gian lận (sử dụng điện thoại, có người trợ giúp, quay mặt/cúi đầu xem tài liệu, chuyển tab trình duyệt...).

---

## BẢNG NỘI DUNG
1. [Kiến Trúc Tổng Thể](#1-kiến-trúc-tổng-thể)
2. [Bản Đồ Tính Năng & Công Nghệ Sử Dụng (Feature to Tech Mapping)](#2-bản-đồ-tính-năng--công-nghệ-sử-dụng)
3. [Yêu Cầu Môi Trường (Prerequisites)](#3-yêu-cầu-môi-trường)
4. [Hướng Dẫn Cài Đặt (Installation)](#4-hướng-dẫn-cài-đặt)
5. [Hướng Dẫn Chạy Ứng Dụng (How to Run)](#5-hướng-dẫn-chạy-ứng-dụng)
6. [Tài Khoản Đăng Nhập Mặc Định (Seed Data)](#6-tài-khoản-đăng-nhập-mặc-định)
7. [Cấu Trúc Thư Mục Dự Án](#7-cấu-trúc-thư-mục-dự-án)
8. [Quy Trình Hoạt Động Của Thí Sinh & Giảng Viên](#8-quy-trình-hoạt-động)

---

## 1. KIẾN TRÚC TỔNG THỂ

Hệ thống được thiết kế theo mô hình **Hybrid Web & AI Microservice**:
- **Web Application (Core Platform):** Xây dựng trên nền tảng **Laravel 11 (PHP 8.2+)**, quản lý dữ liệu người dùng, môn học, ngân hàng câu hỏi, đề thi, phiên làm bài (exam attempts), nhật ký vi phạm (anti-cheat logs) và bộ giao diện người dùng hiện đại với **Tailwind CSS & Blade**.
- **AI Proctoring Microservice:** Xây dựng bằng **Python Flask (cổng 5001)**, tích hợp các mô hình thị giác máy tính chuyên sâu: **InsightFace (ArcFace 512D & RetinaFace)** và **Ultralytics YOLOv8 (COCO dataset)**.
- **Giao tiếp:** Web Laravel gọi các endpoint RESTful API của AI Microservice qua HTTP JSON (`multipart/base64`).

graph TD
    A[Trình duyệt Thí sinh / Giảng viên] -->|HTTP / WebRTC| B[Laravel 11 Backend - Port 8000]
    B -->|MySQL Database| C[(Cơ sở dữ liệu)]
    B -->|Storage Local / Public| D[Lưu trữ Ảnh Face ID & Snapshots]
    B -->|REST API JSON| E[Python AI Proctoring Microservice - Port 5001]
    E -->|Nhận diện & Landmark 5D| F[InsightFace ArcFace + RetinaFace]
    E -->|Phát hiện vật thể gian lận| G[YOLOv8 COCO Model]

---

## 2. BẢN ĐỒ TÍNH NĂNG & CÔNG NGHỆ SỬ DỤNG

Dưới đây là bảng tổng hợp chi tiết giải thích **tính năng nào sử dụng công nghệ / mô hình / thuật toán gì**:

| Nhóm Tính Năng | Tên Tính Năng Chi Tiết | Công Nghệ / Thư Viện Sử Dụng | Mô Hình / Dataset / Thuật Toán | Cơ Chế Hoạt Động & Vai Trò |
| :--- | :--- | :--- | :--- | :--- |
| **Xác thực Face ID** | Đăng ký khuôn mặt sinh viên | WebRTC `getUserMedia`, Canvas, Python Flask, InsightFace | Mô hình ArcFace (`w600k_mbf.onnx`) & RetinaFace (`det_500m.onnx`) | Chụp chân dung 3 góc (chính diện, nghiêng trái, nghiêng phải). Trích xuất vector đặc trưng 512 chiều (`embedding`) và lưu vào CSDL. Hỗ trợ nhận diện khi đeo kính hoặc tháo kính. |
| **Xác thực Face ID** | Quét tự động trước giờ thi (Zero-Click, No-Bypass) | JavaScript Canvas Loop, YOLOv8 + InsightFace ArcFace | Cosine Similarity ($\ge 70\%$) + YOLO COCO Classes | Phân tích đồng thời 2 tầng: **(1) YOLOv8:** Quét phát hiện điện thoại, kiểm tra số lượng người (chặn thi hộ nếu có >1 người); **(2) InsightFace ArcFace:** So khớp với vector hồ sơ gốc ban đầu, bắt buộc khuôn mặt ở vị trí chính giữa khung hình, rõ nét (không mờ nhòe) và nhìn thẳng trực diện camera. |
| **Giám sát Vật thể** | Phát hiện điện thoại di động | Ultralytics YOLOv8 | `yolov8n.pt` pretrained trên **COCO Dataset** (Lớp 67: `cell phone`) | Quét từng ảnh snapshot. Nếu phát hiện điện thoại trong khung hình $\rightarrow$ tạo vi phạm `phone_detected` (mức độ cao), tăng cảnh báo gian lận và ghi log kèm ảnh bằng chứng. |
| **Giám sát Vật thể** | Phát hiện tài liệu / Sách vở | Ultralytics YOLOv8 | **COCO Dataset** (Lớp 73: `book`) | Phát hiện thí sinh mở giáo trình, tài liệu trên bàn $\rightarrow$ cảnh báo `suspicious_object`. |
| **Giám sát Vật thể** | Phát hiện laptop / Màn hình phụ | Ultralytics YOLOv8 | **COCO Dataset** (Lớp 63: `laptop`) | Phát hiện thí sinh dùng máy tính phụ trợ giúp $\rightarrow$ cảnh báo `suspicious_device`. |
| **Giám sát Người** | Phát hiện có 2 người trở lên (`multiple_persons`) | Ultralytics YOLOv8 | **COCO Dataset** (Lớp 0: `person`) | Đếm số lượng người trong khung hình (`person_count`). Nếu có từ 2 người trở lên $\rightarrow$ ghi log `multiple_persons` ("Phát hiện X người trong khung hình camera - Có người trợ giúp"), lưu snapshot và tăng cảnh báo. |
| **Giám sát Người** | Phát hiện thí sinh vắng mặt (`face_absent`) | YOLOv8 + InsightFace | Số lượng person = 0 hoặc không có Face Bounding Box | Cảnh báo khi thí sinh rời khỏi bàn thi hoặc che camera. |
| **Góc Nhìn & Cự Ly** | Chống quay mặt trái / phải (Head Yaw) | InsightFace Facial Keypoints | 5 Điểm mốc khuôn mặt (Mắt trái `kps[0]`, Mắt phải `kps[1]`, Mũi `kps[2]`) | Tính độ lệch ngang đỉnh mũi so với trung điểm 2 mắt: $\text{yaw} = \frac{\text{nose}_x - \text{eye\_mid}_x}{\text{eye\_dist}}$. Nếu $\vert \text{yaw} \vert > 0.22 \rightarrow$ Cảnh báo quay đầu sang trái / phải (`looking_away`). |
| **Góc Nhìn & Cự Ly** | Chống cúi đầu xem tài liệu / Ngẩng đầu (Head Pitch) | InsightFace Facial Keypoints | 5 Điểm mốc (Mắt `kps[0,1]`, Mũi `kps[2]`, Khóe miệng `kps[3,4]`) | Tính tỉ lệ dọc: $\text{pitch} = \frac{\text{nose}_y - \text{eye\_mid}_y}{\text{face\_v}}$. Nếu $\text{pitch} > 0.78 \rightarrow$ Cúi đầu nhìn xuống bàn/điện thoại; nếu $\text{pitch} < 0.28 \rightarrow$ Ngẩng đầu lên trần. |
| **Góc Nhìn & Cự Ly** | Ước tính khoảng cách trực diện (Face Distance) | InsightFace Bounding Box | Tỉ lệ chiều cao mặt: $\frac{\text{box\_h}}{\text{frame\_h}}$ | Nếu tỉ lệ $< 0.16 \rightarrow$ Cảnh báo thí sinh ngồi quá xa màn hình camera. |
| **An toàn Trình duyệt** | Chống chuyển tab / Mất tiêu điểm | JavaScript Page Visibility API | `visibilitychange`, `window.blur`, `window.focus` | Tự động tính thời gian rời màn hình theo giây (`out_of_screen_time`) và ghi log `tab_switch`. |
| **An toàn Trình duyệt** | Bắt buộc toàn màn hình | HTML5 Fullscreen API | `fullscreenchange` | Tự động cảnh báo và ghi log `fullscreen_exit` khi thoát F11 hoặc ESC. |
| **An toàn Trình duyệt** | Chống sao chép, dán, xem mã | DOM Event Listeners | `copy`, `paste`, `cut`, `contextmenu`, `keydown` | Vô hiệu hóa chuột phải, phím tắt Ctrl+C, Ctrl+V, Ctrl+X, F12. Ghi nhận nhật ký mỗi lần vi phạm. |
| **Chụp Ảnh Định Kỳ** | Snapshot ngẫu nhiên trong bài thi | WebRTC MediaStream + Canvas + AI Pipeline | Random Interval: $90s - 120s$, YOLOv8 + ArcFace | Tự động chụp ngầm định kỳ: đồng thời phân tích vật thể YOLO (điện thoại, tài liệu, người thứ 2) và đối soát ArcFace xem có đúng thí sinh theo hồ sơ gốc ban đầu hay không, cảnh báo nếu lệch tâm hoặc mờ mặt. |
| **Giám sát Trực tiếp** | Phòng giám sát thi của Giảng viên | Laravel Blade, AJAX Polling | Chu kỳ đồng bộ 5 giây | Bảng theo dõi trạng thái tất cả thí sinh theo thời gian thực (Bình thường / Cảnh báo / Nguy hiểm), số lần vi phạm, thời gian rời màn hình. |
| **Đối Soát Sau Thi** | Đối chiếu ảnh chân dung 3 chiều | Laravel Controller & Blade Modal | ArcFace Verification Record | Khung so sánh: (1) Ảnh Face ID gốc đăng ký $\leftrightarrow$ (2) Ảnh quét xác thực lúc vào thi $\leftrightarrow$ (3) Ảnh chụp mới nhất trong giờ thi. |
| **Đối Soát Sau Thi** | Bộ sưu tập ảnh & Hộp nhận diện (Lightbox) | JavaScript SVG Canvas Dynamic Layer | Normalized Bounding Boxes (xyxy) | Giảng viên nhấp vào bất kỳ ảnh snapshot nào để phóng to và xem các hộp màu nhận diện AI (đỏ: điện thoại/sách; vàng: người thứ hai; xanh: hợp lệ). |
| **Đối Soát Sau Thi** | Dòng thời gian sự kiện (Timeline) gắn ảnh | Laravel Eloquent ORM + Storage | Liên kết `ExamAntiCheatLog` với `ExamProctorSnapshot` | Mỗi dòng log vi phạm hiển thị trực tiếp ảnh chụp thumbnail tại đúng thời điểm vi phạm để làm bằng chứng xác thực. |
| **Bảo Mật & Riêng Tư**| Mã hóa toàn bộ ảnh chụp & Face ID khi lưu trữ | Laravel Cryptography, OpenSSL | **AES-256-CBC Encryption at Rest** + HMAC Signature | Toàn bộ ảnh Face ID và ảnh chụp phòng thi trên đĩa đều được mã hóa nhị phân bằng AES-256. Trình đọc ảnh hoặc kẻ xâm nhập máy chủ không thể đọc được. |
| **Bảo Mật & Riêng Tư**| Kênh truyền phát ảnh giải mã có kiểm soát quyền | SecureMediaController, Private Storage (`storage/app/private`) | **On-the-fly In-Memory Decryption Streaming** | Không dùng public symlink. Chỉ sinh viên sở hữu, giảng viên chấm thi và Admin mới có quyền giải mã ảnh trong RAM để xem; chặn hoàn toàn khách vãng lai và sinh viên khác (HTTP 403). |
| **Bảo Mật & Riêng Tư**| Bảo mật Avatar & Thông tin cá nhân sinh viên | Role-Based Access Control + Attribute Hiding | `role:student` middleware + `$hidden` biometric vectors | Bảo vệ danh sách lớp chỉ hiển thị họ tên, ẩn vĩnh viễn vector sinh trắc học và đường dẫn nội bộ khỏi JSON. |
| **Quản trị Tài khoản**| Quản lý Avatar riêng biệt (Tách rời khỏi Face ID)| Admin UserController & SecureMediaService | Modal Upload Avatar qua Admin, mã hóa AES-256 | Ảnh đại diện (Avatar) được upload và cập nhật riêng bởi Admin, hoàn toàn tách biệt với ảnh quét sinh trắc học Face ID (không dùng ảnh Face ID làm avatar). |
| **Quản trị Face ID** | Đặt lại Face ID từ Admin | Laravel Admin UserController | Route `POST /admin/users/{id}/reset-face` | Xóa vector embedding và ảnh Face ID cũ khi sinh viên đổi ngoại hình, hỏng camera hoặc cần đăng ký lại. |

---

## 3. YÊU CẦU MÔI TRƯỜNG

Trước khi cài đặt, hãy đảm bảo máy tính đã cài đặt các phần mềm sau:
- **PHP:** Phiên bản $\ge 8.2$ (bật các extension: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`).
- **Composer:** Phiên bản $\ge 2.5$.
- **Node.js:** Phiên bản $\ge 18.x$ và **npm**.
- **Python:** Phiên bản $\ge 3.10$ (khuyến nghị Python 3.11 hoặc 3.12, 3.14).
- **Cơ sở dữ liệu:** **MySQL $\ge 8.0$** hoặc **MariaDB $\ge 10.4$** (thông qua XAMPP, Laragon, Docker hoặc MySQL độc lập).
- **Phần cứng:** Máy tính có Webcam/Camera kết nối hoạt động bình thường.

---

## 4. HƯỚNG DẪN CÀI ĐẶT

### Bước 1: Mở thư mục dự án
```bash
cd d:\eye-exam
```

### Bước 2: Cài đặt thư viện PHP (Composer)
```bash
composer install
```

### Bước 3: Cài đặt thư viện Frontend (NPM)
```bash
npm install
```

### Bước 4: Cài đặt thư viện Python AI
```bash
pip install -r ai_service/requirements.txt
```
*(Các thư viện chính gồm: `flask`, `flask-cors`, `insightface`, `onnxruntime`, `opencv-python`, `ultralytics`, `numpy`, `requests`)*

### Bước 5: Cấu hình file môi trường `.env`
Nếu chưa có file `.env`, sao chép từ `.env.example`:
```bash
cp .env.example .env
php artisan key:generate
```
Mở file `.env` và kiểm tra cấu hình Database cùng URL dịch vụ AI:
```ini
APP_NAME=EyeExam
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Cấu hình kết nối Microservice AI Proctoring
AI_SERVICE_URL=http://127.0.0.1:5001
```

### Bước 6: Tạo liên kết thư mục lưu trữ ảnh (Storage Link)
```bash
php artisan storage:link
```
*Lệnh này tạo symlink từ `storage/app/public` sang `public/storage` để phục vụ hiển thị ảnh chân dung Face ID và ảnh chụp snapshot phòng thi.*

### Bước 7: Khởi tạo Database và nạp dữ liệu mẫu
Tạo cơ sở dữ liệu có tên `eye_exam` trong MySQL, sau đó chạy:
```bash
php artisan migrate:fresh --seed
```

---

## 5. HƯỚNG DẪN CHẠY ỨNG DỤNG

Để hệ thống hoạt động đầy đủ tính năng (Web + AI Vision + Giao diện động), bạn cần chạy đồng thời **3 dịch vụ** trên 3 cửa sổ terminal riêng biệt:

### Cửa sổ 1: Khởi động Python AI Microservice (Cổng 5001)
```bash
python ai_service/app.py
```
> Khi khởi động thành công, màn hình sẽ hiển thị:
> ```text
> ==> InsightFace (ArcFace) loaded successfully!
> ==> Loading YOLOv8 model from yolov8n.pt...
> ==> YOLOv8 loaded successfully!
> ==> Starting Eye-Exam AI Proctoring Microservice on port 5001...
>  * Running on http://127.0.0.1:5001
> ```

### Cửa sổ 2: Khởi động Laravel Web Server (Cổng 8000)
```bash
php artisan serve
```
> Ứng dụng web sẽ khả dụng tại: `http://127.0.0.1:8000`

### Cửa sổ 3: Khởi động Vite Asset Dev Server
```bash
npm run dev
```

---

## 6. TÀI KHOẢN ĐĂNG NHẬP MẶC ĐỊNH

Hệ thống đã chuẩn bị sẵn các tài khoản thử nghiệm sau khi chạy `php artisan db:seed`:

| Vai Trò (Role) | Email Đăng Nhập | Mật Khẩu | Quyền Hạn & Tính Năng Nổi Bật |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@example.com` | `password` | Quản lý toàn bộ người dùng, xem ảnh khuôn mặt đã đăng ký, **Đặt lại Face ID** khi sinh viên gặp sự cố. |
| **Giảng viên (Teacher)** | `teacher@example.com` | `password` | Quản lý ngân hàng câu hỏi, tạo đề thi, **Vào phòng giám sát trực tiếp (Live Monitor)**, xem kết quả, **đối soát ảnh snapshot và bounding box sau khi thi**. |
| **Giảng viên 2 (Teacher)**| `teacher2@example.com`| `password` | Giảng viên phụ trách khoa khác. |
| **Sinh viên (Student)** | `student@example.com` | `password` | **Đăng ký Face ID (3 góc mặt)**, vào thi trắc nghiệm có camera AI giám sát, xem lại điểm số. |

---

## 7. CẤU TRÚC THƯ MỤC DỰ ÁN

```text
eye-exam/
├── ai_service/                      # Microservice Python AI
│   ├── app.py                       # Flask App: API /api/face/register, /api/face/verify, /api/proctor/analyze
│   ├── requirements.txt             # Danh sách dependencies của Python
│   └── start.bat                    # Script khởi động nhanh AI service trên Windows
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/                   # UserController (quản lý user, reset Face ID)
│   │   ├── Student/                 # ExamController, FaceAuthController (đăng ký & xác thực Face ID)
│   │   ├── Teacher/                 # ExamController (tạo đề, monitor trực tiếp, kết quả & đối soát hành vi)
│   │   └── SecureMediaController.php # Endpoint stream ảnh giải mã trong RAM có phân quyền (Role-based)
│   ├── Models/
│   │   ├── User.php                 # Lưu face_embedding, frontal_face_path, face_registered
│   │   ├── Exam.php                 # Cấu hình đề thi, thời gian, câu hỏi
│   │   ├── ExamAttempt.php          # Lượt thi, điểm, face_similarity, cheat_warnings
│   │   ├── ExamProctorSnapshot.php  # Ảnh chụp camera, trạng thái AI, bounding boxes YOLO
│   │   └── AntiCheatLog.php         # Nhật ký vi phạm chi tiết (multiple_persons, phone, looking_away...)
│   └── Services/
│       ├── AiProctorService.php     # Service trung gian gọi HTTP sang Python Microservice
│       └── SecureMediaService.php   # Dịch vụ mã hóa AES-256-CBC và lưu trữ private storage
├── database/
│   ├── migrations/                  # Định nghĩa cấu trúc bảng CSDL
│   └── seeders/                     # Dữ liệu tài khoản & đề thi mẫu
├── resources/
│   └── views/
│       ├── admin/users/index.blade.php      # Quản lý người dùng, xem chân dung Face ID, nút reset
│       ├── student/
│       │   ├── face/register.blade.php      # Giao diện quét 3 góc mặt đăng ký Face ID
│       │   ├── exams/take.blade.php         # Giao diện làm bài thi + camera PIP góc trái
│       │   └── exams/partials/face_verify_modal.blade.php # Modal quét tự động trước khi vào thi
│       └── teacher/exams/
│           ├── monitor.blade.php            # Phòng giám sát trực tiếp thời gian thực
│           └── results.blade.php            # Bảng kết quả, đối soát 3 ảnh & bộ sưu tập snapshot sau thi
├── storage/app/private/             # Thư mục lưu trữ riêng tư được bảo vệ (Private Storage)
│   ├── faces/                       # Lưu ảnh Face ID mã hóa AES-256 (.enc)
│   ├── proctor/                     # Lưu toàn bộ ảnh snapshot phòng thi mã hóa AES-256 (.enc)
│   └── verification/                # Lưu ảnh xác thực vào thi mã hóa AES-256 (.enc)
├── yolov8n.pt                       # Trọng số mô hình YOLOv8 Nano
└── README.md                        # Tài liệu hướng dẫn dự án
```

---

## 8. QUY TRÌNH HOẠT ĐỘNG

### Quy trình dành cho Sinh viên:
1. **Đăng nhập** bằng tài khoản `student@example.com`.
2. **Đăng ký Face ID:** Vào mục *"Đăng ký Face ID"*, cho phép camera, xoay mặt theo chỉ dẫn (chính diện $\rightarrow$ nghiêng trái $\rightarrow$ nghiêng phải) để lưu vector đặc trưng. Sau khi đăng ký thành công, liên kết đăng ký sẽ tự động ẩn đi.
3. **Vào phòng thi:** Chọn đề thi đang mở. Cửa sổ quét Face ID tự động hiện lên: chỉ cần nhìn thẳng vào camera, hệ thống sẽ tự động xác minh và chuyển thẳng vào làm bài trong 1-2 giây.
4. **Làm bài thi:** Trình duyệt tự động chuyển sang toàn màn hình. Camera giám sát thu nhỏ cố định ở góc dưới bên trái màn hình. Hệ thống sẽ tự động chụp ảnh ngẫu nhiên mỗi 90s - 120s để giám sát gian lận.
5. **Nộp bài:** Nhấn nút Nộp bài để hoàn tất bài thi.

### Quy trình dành cho Giảng viên:
1. **Đăng nhập** bằng tài khoản `teacher@example.com`.
2. **Giám sát trực tiếp:** Vào mục *"Quản lý kỳ thi"* $\rightarrow$ chọn đề thi đang diễn ra $\rightarrow$ bấm *"Vào phòng giám sát trực tiếp"*. Hệ thống sẽ hiển thị bảng thí sinh đang làm bài, cập nhật mỗi 5 giây, cảnh báo ngay khi phát hiện điện thoại, quay mặt hoặc có nhiều người.
3. **Xem lại & Đối soát sau khi thi xong:** Bấm vào nút *"Xem kết quả"* của đề thi $\rightarrow$ bấm vào bất kỳ thí sinh nào:
   - Đối chiếu đồng thời 3 ảnh: Ảnh gốc hồ sơ, Ảnh quét trước giờ thi, Ảnh chụp mới nhất.
   - Chuyển sang Tab *"Bộ sưu tập Ảnh AI"* để duyệt tất cả ảnh camera đã chụp, bấm vào để phóng to và xem các hộp nhận diện AI (Bounding boxes).
   - Chuyển sang Tab *"Nhật ký Thao tác"* để xem dòng thời gian các lần vi phạm (rời màn hình, quay mặt, điện thoại...) kèm ảnh chụp bằng chứng trực tiếp.

