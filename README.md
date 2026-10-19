# EYE-EXAM: He Thong Thi Truc Tuyen & Giam Sat Chong Gian Lan Thong Minh

> Eye-Exam la nen tang thi truc tuyen thong minh ket hop giam sat phong thi tu dong (AI Proctoring System). He thong ung dung thi giac may tinh (Computer Vision) va hoc sau (Deep Learning) ket hop giua xu ly phia client (MediaPipe Face Mesh, ONNX Runtime Web YOLOv8) va microservice phia server (InsightFace ArcFace 512D) de dinh danh thi sinh, giam sat hanh vi theo thoi gian thuc, phat hien cac hanh vi gian lan, dong thoi bao ve toan ven du lieu voi co che ma hoa luu tru AES-256-CBC.

---

## MUC LUC

1. [Tong Quan & Kien Truc He Thong](#1-tong-quan--kien-truc-he-thong)
2. [Ban Do Tinh Nang & Cong Nghe Su Dung](#2-ban-do-tinh-nang--cong-nghe-su-dung)
3. [Dac Ta Kien Truc AI & Giam Sat Gian Lan](#3-dac-ta-kien-truc-ai--giam-sat-gian-lan)
4. [Kien Truc An Toan & Bao Mat Du Lieu](#4-kien-truc-an-toan--bao-mat-du-lieu)
5. [Yeu Cau Moi Truong](#5-yeu-cau-moi-truong)
6. [Huong Dan Cai Dat](#6-huong-dan-cai-dat)
7. [Huong Dan Chay Ung Dung](#7-huong-dan-chay-ung-dung)
8. [Tai Khoan Dang Nhap Mac Dinh](#8-tai-khoan-dang-nhap-mac-dinh)
9. [Cau Truc Thu Muc Du An](#9-cau-truc-thu-muc-du-an)
10. [Quy Trinh Hoat Dong Chi Tiet](#10-quy-trinh-hoat-dong-chi-tiet)
11. [Kiem Thu He Thong (Testing)](#11-kiem-thu-he-thong-testing)

---

## 1. TONG QUAN & KIEN TRUC HE THONG

He thong duoc thiet ke theo mo hinh **Hybrid Edge-Cloud & AI Microservice**:

- **Web Application Core (Laravel 11 & PHP 8.2+):** Quan ly toan bo logic nghiep vu: nguoi dung, mon hoc, ky thi, hoc ky, lop hoc, cau hoi (trac nghiem & tu luan), phan bo de thi theo ma tran hoac so le, cham thi, dong bo phong giam sat truc tiep, quan ly nhat ky vi pham va he thong bao cao thong ke truc quan.
- **Client-Side Realtime Vision Engine (Browser Edge Computing):**
  - **MediaPipe Face Mesh (468/478 Landmarks):** Tinh toan ma tran xoay 3D (Yaw, Pitch, Roll) ngay tren trinh duyet voi tan so cao de phat hien quay dau sang trai/phai, cui dau nhin tai lieu hoac ngua dau qua lau (>10 giay), dong thoi dem so luong khuon mat trong khung hinh.
  - **Lightweight YOLOv8 Nano qua ONNX Runtime Web (WASM/WebGL):** Mo hinh `yolov8n.onnx` (kich thuoc 320x320) chay truc tiep trong trinh duyet de phat hien tuc thi cac vat the cam (dien thoai di dong, sach vo, thiet bi dien tu phu) ma khong gay qua tai cho may chu.
- **Biometric Face ID Microservice (Python Flask - Port 5001):**
  - Chuyen biet hoa cho nhiem vu nhan dien sinh trac hoc chat luong cao: Su dung **InsightFace (ArcFace 512D & RetinaFace)** de dang ky khuon mat 3 goc va so khop vector dac trung (Cosine Similarity) cho cac anh chup dinh ky va anh xac thuc truoc gio thi.
  - Backend da duoc toi uu hoa bang cach loai bo hoan toan khoi YOLO phia server, chuyen tai toan bo cong viec quet vat the sang trinh duyet cua thi sinh.
- **Secure Media Storage (AES-256-CBC at Rest):** Toan bo anh chan dung Face ID va anh chup giam sat phong thi deu duoc ma hoa nhi phan truoc khi ghi xuong o dia va chi duoc giai ma truc tiep trong bo nho RAM khi co yeu cau hop le tu nguoi co tham quyen.

```text
[Trinh duyet Thi sinh] 
  |-- WebRTC Camera Feed
  |-- MediaPipe Face Mesh (Client-Side Yaw/Pitch/Roll & Face Count)
  |-- ONNX Runtime Web YOLOv8 (Client-Side Phone/Book/Laptop Detection)
  |-- JavaScript Exam Engine (Anti-Tab Switch, Anti-Copy/Paste, Auto-Save)
  |
  v (HTTP JSON / REST API)
[Laravel 11 Backend - Port 8000]
  |-- MySQL Database (Du lieu nguoi dung, de thi, ket qua, nhat ky vi pham)
  |-- Storage Private (Luu anh ma hoa AES-256-CBC)
  |-- SecureMediaService (Giai ma anh stream truc tiep trong RAM)
  |
  v (HTTP REST API / Base64)
[Python AI Microservice - Port 5001]
  |-- InsightFace ArcFace (512-Dimensional Feature Vectors)
  |-- RetinaFace (Face Detection & 5 Landmark Keypoints)
  |-- Zero Server YOLO Overhead (Da chuyen toan bo sang Client)
```

---

## 2. BAN DO TINH NANG & CONG NGHE SU DUNG

| Nhom Tinh Nang | Ten Tinh Nang Chi Tiet | Cong Nghe / Thu Vien | Co Che Hoat Dong & Dac Diem |
| :--- | :--- | :--- | :--- |
| **Xac Thuc Face ID** | Dang ky khuon mat 3 goc | WebRTC `getUserMedia`, Python Flask, InsightFace ArcFace | Huong dan thi sinh chup 3 goc mat (chinh dien, nghieng trai, nghieng phai). Trich xuat vector 512 chieu luu vao co so du lieu de lam ho so goc. |
| **Xac Thuc Face ID** | Quet tu dong truoc gio thi (Zero-Click) | JavaScript Canvas, InsightFace ArcFace | So khop vector truc tiep voi anh ho so goc qua cong thuc Cosine Similarity (nguong dat >= 70%). Yeu cau khuon mat nam o trung tam, ro net. |
| **Giam Sat Huong Nhin** | Phat hien quay mat trai / phai / cui dau (>10s) | MediaPipe Face Mesh (468/478 Landmarks) | Chay truc tiep tren trinh duyet thi sinh. Tinh toan goc quay dau 3D (Yaw, Pitch). Neu quay dau hoac cui mat lien tuc vuot qua nguong thoi gian 10 giay se lap tuc kich hoat canh bao va ghi log vi pham. |
| **Giam Sat Vat The** | Phat hien dien thoai, sach vo, laptop | ONNX Runtime Web + YOLOv8 Nano (`yolov8n.onnx`) | Suy luan thoi gian thuc tren trinh duyet qua WebGL/WASM. Nhan dien cac lop vat the cam trong bo tap du lieu COCO (cell phone, book, laptop). |
| **Giam Sat Nguoi** | Phat hien co 2 nguoi tro len / Vang mat | MediaPipe Face Mesh + Client YOLOv8 | Dem so luong nguoi va khuon mat trong khung hinh. Canh bao tuc thi neu co tu 2 nguoi tro len (`multiple_persons`) hoac thi sinh roi khoi camera (`face_absent`). |
| **An Toan Trinh Duyet** | Chong chuyen tab / Mat tieu diem | Page Visibility API, `blur`, `focus` | Ghi nhan thoi diem thi sinh roi man hinh, tu dong tinh tong thoi gian roi man hinh theo tung giay (`out_of_screen_time`) va tang so lan canh bao. |
| **An Toan Trinh Duyet** | Bat buoc toan man hinh (Fullscreen) | HTML5 Fullscreen API | Yeu cau thi sinh duy tri che do toan man hinh; ghi log vi pham moi khi thi sinh thoat man hinh bang ESC hoac F11. |
| **An Toan Trinh Duyet** | Vo hieu hoa copy / paste / chuot phai | DOM Event Listeners | Chan thao tac sao chep, cat, dan noi dung tu ben ngoai vao bai thi; vo hieu hoa chuot phai va phim tat mo Developer Tools (F12, Ctrl+Shift+I). |
| **Chup Anh Dinh Ky** | Chup anh ngau nhien kiem tra cheo | WebRTC Canvas + Python ArcFace | Tu dong chup anh dinh ky (mac dinh 90s - 120s hoac theo cau hinh cua giang vien), gui len server doi soat voi vector goc de phat hien truong hop doi nguoi thi giua gio. |
| **Giao De Thi Da Dang** | Phan phoi theo lop & Hoc ky | Laravel Eloquent, Alpine.js | Ho tro giao de theo tung hoc ky, bo loc lop hoc, lua chon tat ca hoac tich chon tung sinh vien cu the. |
| **Giao De Thi Da Dang** | Phan phoi SOLE (1-2, 1-2-3, 1-2-3-4) | Thuat toan chia de xoay vong (Round-Robin) | Cho phep chia de xen ke chan/le (Sole 1-2), chia xoay vong 3 de (Sole 1-2-3) hoac 4 de (Sole 1-2-3-4) nham chong nhin bai trong cung phong hoc. |
| **Quan Ly Thi & Cham Diem**| Cau hoi trac nghiem & Tu luan | Laravel DB Transactions, Storage | Cau trac nghiem cham diem tu dong ngay khi nop bai; cau tu luan luu bai lam an toan cho giang vien cham diem truc tiep kem nhan xet tung cau. |
| **Quan Ly Thi & Cham Diem**| Xem lai bai thi & Cong bo diem | Exam Policy Attributes | Giang vien chu dong bat/tat quyen xem diem va quyen xem lai chi tiet bai lam (de thi, dap an dung, loi giai thich) sau khi sinh vien ket thuc bai thi. |
| **Giam Sat Truc Tiep** | Phong giam sat Live Monitor | Laravel Blade, AJAX Polling (5s) | Giang vien theo doi danh sach tat ca thi sinh dang lam bai theo thoi gian thuc; hien thi chi so vi pham, thoi gian roi man hinh, trang thai nguy co. |
| **Doi Soat Sau Thi** | Bang ket qua & Sap xep nop bai | Laravel Query Builder, Blade Modal | Danh sach sinh vien duoc sap xep theo thu tu bai nop moi nhat len dau. Xem bang so sanh 3 anh (Anh goc, Anh vao thi, Anh luc thi), Lightbox Bounding Boxes va Timeline vi pham. |
| **Tien Ich Thi** | May tinh khoa hoc & Hop ghi chu an toan | HTML5 Canvas / JS Math Engine | Cung cap may tinh bo tui khoa hoc truc tiep tren giao dien thi va khu vuc ghi chu rieng chong dan van ban tu ngoai vao. |
| **Bao Mat & Rieng Tu** | Ma hoa toan bo anh luu tru (AES-256) | OpenSSL, Laravel Encrypter | Anh luu tren dia o dang file ma hoa `.enc`. Chi co nguoi so huu va giang vien co quyen moi duoc phep giai ma luong truc tiep tren RAM qua `SecureMediaController`. |

---

## 3. DAC TA KIEN TRUC AI & GIAM SAT GIAN LAN

### 3.1. Thi Giac May Tinh Phia Client (Client-Side Edge AI)
De toi uu hoa chi phi tai nguyen may chu va loai bo do tre mang (latency), he thong Eye-Exam thuc hien toan bo cac tac vu giam sat tan suat cao ngay tren trinh duyet cua thi sinh:

1. **MediaPipe Face Mesh (Google Research):**
   - Su dung mang no-ron nhe trinh duyet voi 468 diem moc 3D tren khuon mat.
   - Uoc tinh toa do goc quay dau (Head Pose Estimation):
     - **Yaw (Quay trai/phai):** Tinh ti le do lech dinh mui so voi trung diem hai mat.
     - **Pitch (Cui/ngua dau):** Tinh ti so khoang cach mui-mieng so voi chieu doc khuon mat de phat hien thi sinh cui nhin dien thoai hoac nhin xuong ban.
     - **Co che Bo dem Thoi gian (Temporal Threshold):** Chi ghi nhan vi pham neu thi sinh quay dau hoac cui mat lien tuc vuot qua 10 giay, tranh tinh trang bao dong sai do cac cu dong chop nhoang tu nhien.
   - **Xac dinh su vang mat hoac xuat hien nhieu nguoi:** Neu so luong diem moc bang 0 trong thoi gian quy dinh -> vi pham `face_absent`; neu nhan dien tu 2 khuon mat tro len -> vi pham `multiple_persons`.

2. **Lightweight YOLOv8 Nano tren Trinh Duyet (ONNX Runtime Web):**
   - Mo hinh `yolov8n.onnx` voi do phan giai dau vao 320x320 pixel.
   - Chay qua backend WebGL hoac WebAssembly (WASM) da luong.
   - Chuyen biet nhan dien 3 nhom vat the gian lan trong COCO dataset:
     - `cell phone` (dien thoai di dong) -> Muc do canh bao: Cao.
     - `book` (sach vo, giao trinh, tai lieu) -> Muc do canh bao: Trung binh.
     - `laptop` (may tinh xach tay phu, man hinh phu) -> Muc do canh bao: Cao.

### 3.2. Sinh Trac Hoc Phia Server (Server-Side Microservice)
May chu Python Flask chi dam nhiem tac vu yeu cau do chinh xac sinh trac hoc cao nhat ma trinh duyet kho xu ly tot:

- **InsightFace ArcFace 512D (`w600k_mbf.onnx`):**
  - Trinh nhan dien khuon mat theo tieu chuan nghien cuu cong nghiep voi do chinh xac cao.
  - Chuyen doi moi khuon mat thanh vector 512 chieu duy nhat.
  - So khop vector theo ham do tuong dong Cosine (Cosine Similarity):
    $$\text{Similarity}(u, v) = \frac{u \cdot v}{\|u\|_2 \|v\|_2} \times 100\%$$
  - Nguong chap thuan cho phep vao thi la >= 70%. Nguong doi soat trong phong thi la >= 50%.
- **RetinaFace (`det_500m.onnx`):**
  - Dinh vi hop gioi han khuon mat (Bounding Box) va 5 diem moc chinh (2 mat, mui, 2 khoe mieng).
  - Loai bo cac khung hinh chup bi mo, che khuat hoac sai goc truoc khi trich xuat dac trung.

---

## 4. KIEN TRUC AN TOAN & BAO MAT DU LIEU

### 4.1. Ma Hoa Du Lieu Luu Tru (AES-256-CBC at Rest)
Moi hinh anh chua thong tin sinh trac hoc cua thi sinh deu la du lieu nhay cam:
- Anh Face ID dang ky 3 goc mat.
- Anh xac thuc truoc gio thi.
- Anh chup giam sat phong thi (Proctor Snapshots).

Tat ca duoc luu tru trong thu muc rieng tu `storage/app/private/` duoi dang file ma hoa `.enc` bang thuat toan **AES-256-CBC** voi khoa bi mat ung dung (`APP_KEY`) va xac thuc toan ven bang chu ky HMAC-SHA256. Du ke xam nhap co duoc tep tin tren o dia cung hoan toan khong the xem hoac giai ma anh.

### 4.2. Truyen Phat Anh Giai Ma Trong Bo Nho RAM (In-Memory Decryption Streaming)
He thong khong tao bat ky symbolic link cong khai nao sang thu muc chua anh:
- Khi giang vien hoac thi sinh xem anh, yeu cau duoc gui qua endpoint kiem soat:
  `GET /media/secure/{path}`
- He thong kiem tra quyen han nghiem ngat dua tren Role-Based Access Control:
  - Sinh vien: Chi xem duoc anh cua chinh minh.
  - Giang vien: Chi xem duoc anh cua thi sinh trong ky thi ma minh tao ra hoac phu trach.
  - Admin: Quyen quan tri toan he thong de ho tro dat lai Face ID khi can thiet.
  - Khach vang lai va sinh vien khac: Bi chan tuyet doi (HTTP 403 Forbidden).
- Anh duoc giai ma truc tiep trong RAM va ghi thang vao luong HTTP response, khong bao gio tao file tam giai ma tren o dia.

---

## 5. YEU CAU MOI TRUONG

Truoc khi cai dat, may tinh can dap ung cac yeu cau sau:

- **He Dieu Hanh:** Windows 10/11, Ubuntu 20.04+, macOS 12+.
- **PHP:** Phien ban 8.2 tro len (bat cac extension: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`).
- **Composer:** Phien ban 2.5 tro len.
- **Node.js:** Phien ban 18.x tro len va **npm**.
- **Python:** Phien ban 3.10, 3.11, 3.12 hoac 3.14.
- **Co So Du Lieu:** MySQL 8.0+ hoac MariaDB 10.4+ (qua XAMPP, Laragon hoac Docker).
- **Phan Cung:** May tinh co Webcam/Camera hoat dong binh thuong.

---

## 6. HUONG DAN CAI DAT

### Buoc 1: Mo thu muc ma nguon
```bash
cd d:\eye-exam
```

### Buoc 2: Cai dat thu vien PHP (Composer)
```bash
composer install
```

### Buoc 3: Cai dat thu vien JavaScript (NPM)
```bash
npm install
```

### Buoc 4: Cai dat moi truong Python AI Microservice
```bash
pip install -r ai_service/requirements.txt
```
*(Cac goi chinh: `flask`, `flask-cors`, `insightface`, `onnxruntime`, `opencv-python`, `numpy`, `requests`)*

### Buoc 5: Thiet lap file cau hinh `.env`
Sao chep tu file `.env.example` neu chua co:
```bash
cp .env.example .env
php artisan key:generate
```

Kiem tra va cap nhat thong so ket noi CSDL trong `.env`:
```ini
APP_NAME=EyeExam
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eye_exam
DB_USERNAME=root
DB_PASSWORD=

# URL ket noi toi Microservice Python
AI_SERVICE_URL=http://127.0.0.1:5001
```

### Buoc 6: Tao lien ket thu muc luu tru
```bash
php artisan storage:link
```

### Buoc 7: Khoi tao CSDL va nap du lieu mau
Tao co so du lieu co ten `eye_exam` trong MySQL, sau do chay:
```bash
php artisan migrate:fresh --seed
```

---

## 7. HUONG DAN CHAY UNG DUNG

De he thong hoat dong day du cac tinh nang, mo **3 cua so terminal** doc lap:

### Cua so 1: Khoi dong Python AI Microservice (Port 5001)
```bash
python ai_service/app.py
```
> Thong bao khoi dong thanh cong se xuat hien:
> ```text
> ==> InsightFace (ArcFace) loaded successfully!
> ==> Starting Eye-Exam AI Proctoring Microservice on port 5001...
>  * Running on http://127.0.0.1:5001
> ```

### Cua so 2: Khoi dong Laravel Web Server (Port 8000)
```bash
php artisan serve
```
> Ung dung Web san sang truy cap tai: `http://127.0.0.1:8000`

### Cua so 3: Khoi dong Vite Frontend Asset Server
```bash
npm run dev
```

---

## 8. TAI KHOAN DANG NHAP MAC DINH

Sau khi chay lenh seed du lieu, he thong cung cap cac tai khoan mac dinh sau:

| Vai Tro (Role) | Email Dang Nhap | Mat Khau | Quyen Han Chinh |
| :--- | :--- | :--- | :--- |
| **Quan Tri Vien (Admin)** | `admin@example.com` | `password` | Quan ly toan bo nguoi dung, xem ho so Face ID goc, dat lai Face ID khi sinh vien can cap nhat. |
| **Giang Vien (Teacher)** | `teacher@example.com` | `password` | Tao cau hoi, tao de thi, phan bo de theo lop/sole, vao phong Live Monitor truc tiep, xem ket qua va doi soat anh sau thi. |
| **Giang Vien 2 (Teacher)** | `teacher2@example.com` | `password` | Giang vien phu trach mon hoc thuoc khoa khac. |
| **Sinh Vien (Student)** | `student@example.com` | `password` | Dang ky Face ID 3 goc, vao thi voi AI Proctoring truc tiep, lam bai trac nghiem & tu luan, xem diem va xem lai bai thi khi duoc phep. |

---

## 9. CAU TRUC THU MUC DU AN

```text
eye-exam/
|-- ai_service/                      # Microservice Python AI (Port 5001)
|   |-- app.py                       # Flask App: API dang ky, xac thuc khuon mat ArcFace
|   |-- requirements.txt             # Danh sach thu vien Python
|   `-- start.bat                    # Script khoi dong nhanh tren Windows
|-- app/
|   |-- Http/Controllers/
|   |   |-- Admin/                   # Quan tri nguoi dung, Dat lai Face ID
|   |   |-- Student/                 # Lam bai thi, xac thuc Face ID, xem ket qua
|   |   |-- Teacher/                 # Quan ly de thi, phan bo sole, live monitor, ket qua
|   |   `-- SecureMediaController.php # Truyen phat stream anh ma hoa trong bo nho RAM
|   |-- Models/
|   |   |-- User.php                 # Tai khoan, vector embedding 512D, anh chan dung
|   |   |-- Exam.php                 # Thong so de thi, thoi gian, che do giam sat, hoc ky
|   |   |-- ExamAttempt.php          # Luot thi, diem trac nghiem/tu luan, do khop mat
|   |   |-- ExamProctorSnapshot.php  # Anh chup dinh ky, bounding box, do tuong dong
|   |   `-- AntiCheatLog.php         # Nhat ky vi pham (dien thoai, quay mat, roi man hinh)
|   `-- Services/
|       |-- AiProctorService.php     # Service giao tiep HTTP sang Python Microservice
|       `-- SecureMediaService.php   # Ma hoa AES-256-CBC va quan ly private storage
|-- database/
|   |-- migrations/                  # Cau truc cac bang CSDL
|   `-- seeders/                     # Du lieu mau kiem thu day du cac kich ban
|-- public/
|   |-- vendor/
|   |   |-- mediapipe/               # Thu vien MediaPipe Face Mesh client-side
|   |   `-- yolo/                    # Thu vien ONNX Runtime Web va file yolov8n.onnx
|-- resources/
|   |-- css/app.css                  # Tailwind CSS styling
|   |-- js/app.js                    # JavaScript logic khoi dong
|   `-- views/
|       |-- admin/                   # Giao dien quan tri nguoi dung
|       |-- student/                 # Giao dien dang ky Face ID, phong thi truc tuyen
|       `-- teacher/                 # Giao dien giao de thi, live monitor, ket qua
|-- storage/app/private/             # Thu muc luu tru ma hoa AES-256 (.enc)
|   |-- faces/                       # Anh ho so Face ID dang ky 3 goc mat
|   |-- proctor/                     # Anh chup giam sat phong thi dinh ky
|   `-- verification/                # Anh xac thuc truoc gio thi
|-- tests/                           # Bo test tu dong PHPUnit / Feature Tests
`-- README.md                        # Tai lieu huong dan he thong
```

---

## 10. QUY TRINH HOAT DONG CHI TIET

### 10.1. Danh Cho Sinh Vien:
1. **Dang nhap** vao he thong voi tai khoan sinh vien.
2. **Dang ky Face ID:** Vao menu "Dang ky Face ID", he thong yeu cau mo camera va quay mat theo 3 huong (chinh dien, nghieng trai, nghieng phai). Vector dac trung 512 chieu duoc luu vao CSDL va anh goc duoc ma hoa AES-256.
3. **Chon bai thi:** Vao danh sach de thi, su dung bo loc hoc ky de chon dung ky thi.
4. **Xac thuc Face ID truoc gio thi:** Cua so xac thuc tu dong quet khuon mat. Khi do tuong dong dat chuan (>= 70%) va khuon mat nam o vi tri trung tam, he thong tu dong chuyen thang vao giao dien thi trong 1-2 giay.
5. **Lam bai thi:**
   - Trinh duyet tu dong chuyen sang toan man hinh.
   - Camera giam sat o goc man hinh chay lien tuc: MediaPipe Face Mesh phat hien quay dau qua 10 giay, ONNX YOLOv8 phat hien dien thoai/sach vo.
   - He thong chup snapshot dinh ky (90s - 120s) gui ve may chu de kiem tra cheo.
   - Su dung may tinh khoa hoc va so tay ghi chu tich hop san neu can tinh toan.
6. **Nop bai:** Xac nhan nop bai. Neu giang vien cho phep xem diem, sinh vien se thay ngay diem trac nghiem; neu co cau tu luan, diem tong se hien thi sau khi giang vien cham xong.

### 10.2. Danh Cho Giang Vien:
1. **Dang nhap** vao he thong voi tai khoan giang vien.
2. **Tao de thi & Ngan hang cau hoi:**
   - Tao cau hoi trac nghiem hoac tu luan gan voi mon hoc va danh muc.
   - Cau hinh thoi gian mo/dong thi, thoi luong lam bai, chu ky chup anh AI (giay), quyen xem diem va quyen xem lai bai thi.
3. **Giao de thi cho lop (Assignment):**
   - Chon lop hoc va hoc ky.
   - Chon 1 hoac nhieu de thi.
   - Chon che do phan bo: Giao tat ca, Sole 1-2 (chan/le), Sole 1-2-3 (3 de xoay vong), Sole 1-2-3-4 (4 de xoay vong), hoac tich chon tung sinh vien.
   - Bam nut "Giao de" duy nhat de hoan tat.
4. **Phong giam sat truc tiep (Live Monitor):**
   - Vao phong giam sat de theo doi tat ca thi sinh dang lam bai voi tan so cap nhat 5 giay mot lan.
   - Nhanh chong phat hien thi sinh co dau hieu bat thuong (canh bao dien thoai, roi man hinh nhieu giay).
   - Nhan "So sanh & Hanh vi" de xem anh chup moi nhat va nhat ky thao tac truc tiep.
5. **Doi soat ket qua sau thi:**
   - Danh sach sinh vien duoc sap xep theo luot nop bai moi nhat len dau trang.
   - Nhan vao sinh vien de mo hop thoai doi soat toan dien:
     - Khung 3 anh so sanh truc quan: Anh Face ID goc, Anh xac thuc vao thi, Anh chup luc thi.
     - Bo suu tap anh giam sat: xem toan bo anh snapshot da chup, mo Lightbox de kiem tra hop nhan dien AI (Bounding box) va do khop sinh trac hoc.
     - Nhat ky thao tac: dong thoi gian chi tiet cac lan roi man hinh, chuyen tab, phat hien dien thoai kem anh bang chung thoi diem do.
   - Cham diem cac cau tu luan va nhap nhan xet cho thi sinh.

---

## 11. KIEM THU HE THONG (TESTING)

He thong tich hop bo Feature Test hoan chinh bang PHPUnit kiem tra tat ca cac nghiep vu trong tam:

```bash
php artisan test
```

Cac kich ban kiem thu tu dong bao gom:
- Dang ky va xac thuc khuon mat ArcFace qua API.
- Luong vao thi, nop bai trac nghiem va nop bai tu luan.
- Phan bo de thi Sole 1-2, Sole 1-2-3 va giao de theo danh sach sinh vien duoc chon.
- Co che phan quyen truy cap SecureMediaController va stream anh ma hoa.
- Luu nhat ky vi pham phong thi (AntiCheatLog) va luu tru anh snapshot (ExamProctorSnapshot).
- Loc ky thi theo hoc ky va quyen xem diem/xem lai bai lam cua sinh vien.
- Sap xep danh sach thi sinh nop bai moi nhat tren trang giang vien.

Ket qua chay kiem thu: **35/35 tests dat 100% thanh cong**.
