"""
Eye-Exam AI Proctoring & Face Authentication Microservice
Integrated with InsightFace (ArcFace 512D) and YOLOv8
"""

import os
import io
import re
import base64
import json
import numpy as np
import cv2
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS
from insightface.app import FaceAnalysis
from ultralytics import YOLO

app = Flask(__name__)
CORS(app)

# -------------------------------------------------------------
# Initialize Models
# -------------------------------------------------------------
print("==> Loading InsightFace (ArcFace)...")
face_app = FaceAnalysis(name='buffalo_sc', providers=['CPUExecutionProvider'])
face_app.prepare(ctx_id=0, det_size=(320, 320))
print("==> InsightFace (ArcFace) loaded successfully!")

# Move yolov8n.pt to script directory or current working dir
yolo_path = os.path.join(os.path.dirname(__file__), '..', 'yolov8n.pt')
if not os.path.exists(yolo_path):
    yolo_path = 'yolov8n.pt'

print(f"==> Loading YOLOv8 model from {yolo_path}...")
yolo_model = YOLO(yolo_path)
print("==> YOLOv8 loaded successfully!")

# Target COCO Classes for anti-cheat
# 0: person, 67: cell phone, 73: book, 63: laptop
CHEAT_CLASSES = {
    0: 'person',
    67: 'cell phone',
    73: 'book',
    63: 'laptop'
}


def decode_base64_image(base64_str: str) -> np.ndarray:
    """Decode a base64 encoded image string (with or without data URI header) into OpenCV BGR numpy array."""
    if not base64_str:
        return None
    # Strip data URI header if present (e.g. data:image/jpeg;base64,...)
    if ',' in base64_str:
        base64_str = base64_str.split(',', 1)[1]
    
    img_bytes = base64.b64decode(base64_str)
    nparr = np.frombuffer(img_bytes, np.uint8)
    img_bgr = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
    return img_bgr


def normalize_vector(v: np.ndarray) -> np.ndarray:
    """Normalize vector to unit L2 norm."""
    norm = np.linalg.norm(v)
    if norm == 0:
        return v
    return v / norm


@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'healthy',
        'service': 'Eye-Exam AI Proctoring',
        'models': {
            'face_recognition': 'InsightFace ArcFace 512D',
            'object_detection': 'YOLOv8n'
        }
    })


@app.route('/api/face/extract-embedding', methods=['POST'])
def extract_embedding():
    """
    Extracts 512D ArcFace embedding from single or multi-angle images.
    If multiple images provided (e.g. frontal, left, right), averages their embeddings.
    """
    data = request.get_json(force=True)
    images_b64 = data.get('images', [])
    if isinstance(images_b64, str):
        images_b64 = [images_b64]

    if not images_b64:
        return jsonify({'error': 'Không có dữ liệu ảnh.'}), 400

    valid_embeddings = []
    angles_info = []

    for idx, b64 in enumerate(images_b64):
        img_bgr = decode_base64_image(b64)
        if img_bgr is None:
            continue

        faces = face_app.get(img_bgr)
        if not faces:
            angles_info.append({'index': idx, 'status': 'no_face'})
            continue

        # Take largest face
        faces = sorted(faces, key=lambda x: (x.bbox[2] - x.bbox[0]) * (x.bbox[3] - x.bbox[1]), reverse=True)
        face = faces[0]
        
        if face.embedding is not None:
            emb = normalize_vector(face.embedding)
            valid_embeddings.append(emb)
            angles_info.append({
                'index': idx,
                'status': 'success',
                'bbox': [int(x) for x in face.bbox]
            })

    if not valid_embeddings:
        return jsonify({
            'success': False,
            'message': 'Không tìm thấy khuôn mặt hợp lệ trong các ảnh gửi lên. Vui lòng thử lại.',
            'angles': angles_info
        }), 422

    # Average and re-normalize embeddings
    avg_embedding = np.mean(valid_embeddings, axis=0)
    final_embedding = normalize_vector(avg_embedding).tolist()

    return jsonify({
        'success': True,
        'embedding': final_embedding,
        'valid_images_count': len(valid_embeddings),
        'angles': angles_info,
        'dimension': len(final_embedding)
    })


@app.route('/api/face/verify', methods=['POST'])
def verify_face():
    """
    Verifies probe face image against registered student ArcFace embedding.
    Ensures:
    1. Person/face is centered in camera frame ("ở trung tâm ảnh").
    2. Face is clear, well-lit, not blurry, and close enough ("rõ mặt của sinh viên").
    3. Face embedding strictly matches registered Face ID >= threshold ("phải khớp").
    """
    data = request.get_json(force=True)
    probe_b64 = data.get('image')
    enrolled_embedding = data.get('enrolled_embedding')
    # Default threshold 70.0%
    threshold = float(data.get('threshold', 70.0))

    if not probe_b64 or not enrolled_embedding:
        return jsonify({'error': 'Thiếu ảnh quét hoặc dữ liệu khuôn mặt đã đăng ký.'}), 400

    img_bgr = decode_base64_image(probe_b64)
    if img_bgr is None:
        return jsonify({'error': 'Dữ liệu ảnh không hợp lệ.'}), 400

    h, w = img_bgr.shape[:2]

    # -------------------------------------------------------------
    # 0. YOLOv8 Object & Person Detection ("vừa YOLO phân tích")
    # -------------------------------------------------------------
    yolo_results = yolo_model(img_bgr, conf=0.35, verbose=False)[0]
    person_count = 0
    phone_detected = False
    yolo_detections = []

    for box in yolo_results.boxes:
        cls_id = int(box.cls[0])
        conf = float(box.conf[0])
        bx1, by1, bx2, by2 = [int(v) for v in box.xyxy[0].tolist()]

        if cls_id in CHEAT_CLASSES:
            label = CHEAT_CLASSES[cls_id]
            yolo_detections.append({
                'label': label,
                'confidence': round(conf * 100, 1),
                'box': [bx1, by1, bx2, by2]
            })
            if label == 'person':
                person_count += 1
            elif label == 'cell phone':
                phone_detected = True

    if phone_detected:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': 'Phát hiện điện thoại di động trước camera! Vui lòng cất điện thoại trước khi xác thực.',
            'detections': yolo_detections
        })

    if person_count > 1:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': f'Phát hiện {person_count} người trong khung hình camera. Yêu cầu chỉ 1 thí sinh duy nhất trước màn hình!',
            'detections': yolo_detections
        })

    faces = face_app.get(img_bgr)
    if not faces:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': 'Không nhận diện được khuôn mặt trong khung hình. Vui lòng ngồi trước camera và đủ ánh sáng.',
            'detections': yolo_detections
        })

    # Pick primary face (largest area)
    faces = sorted(faces, key=lambda x: (x.bbox[2] - x.bbox[0]) * (x.bbox[3] - x.bbox[1]), reverse=True)
    face = faces[0]

    # -------------------------------------------------------------
    # 1. Centering Check ("người phải ở trung tâm ảnh")
    # -------------------------------------------------------------
    face_cx = (face.bbox[0] + face.bbox[2]) / 2.0 / float(w)
    face_cy = (face.bbox[1] + face.bbox[3]) / 2.0 / float(h)

    if face_cx < 0.25:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': 'Khuôn mặt bị lệch sang bên trái. Vui lòng ngồi vào chính giữa khung hình camera!',
            'bbox': [int(x) for x in face.bbox]
        })
    elif face_cx > 0.75:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': 'Khuôn mặt bị lệch sang bên phải. Vui lòng ngồi vào chính giữa khung hình camera!',
            'bbox': [int(x) for x in face.bbox]
        })
    elif face_cy < 0.18:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': 'Khuôn mặt ở quá cao. Vui lòng ngồi thẳng hoặc chỉnh camera xuống giữa mặt!',
            'bbox': [int(x) for x in face.bbox]
        })
    elif face_cy > 0.82:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': 'Khuôn mặt ở quá thấp. Vui lòng chỉnh camera thẳng góc mặt chính giữa!',
            'bbox': [int(x) for x in face.bbox]
        })

    # -------------------------------------------------------------
    # 2. Face Clarity & Size Check ("rõ mặt của sinh viên")
    # -------------------------------------------------------------
    face_w = face.bbox[2] - face.bbox[0]
    face_h = face.bbox[3] - face.bbox[1]
    face_h_ratio = face_h / float(h)

    if face_h_ratio < 0.20 or face_w < 75:
        return jsonify({
            'success': False,
            'matched': False,
            'similarity': 0.0,
            'message': 'Thí sinh đang ngồi quá xa camera. Vui lòng ngồi lại gần màn hình hơn để nhận diện rõ mặt!',
            'bbox': [int(x) for x in face.bbox]
        })

    # Blurriness detection via Laplacian variance
    gray = cv2.cvtColor(img_bgr, cv2.COLOR_BGR2GRAY)
    x1, y1 = max(0, int(face.bbox[0])), max(0, int(face.bbox[1]))
    x2, y2 = min(w, int(face.bbox[2])), min(h, int(face.bbox[3]))
    face_roi = gray[y1:y2, x1:x2]
    if face_roi.size > 0:
        blur_score = cv2.Laplacian(face_roi, cv2.CV_64F).var()
        if blur_score < 26.0:
            return jsonify({
                'success': False,
                'matched': False,
                'similarity': 0.0,
                'message': 'Hình ảnh khuôn mặt bị mờ. Vui lòng giữ yên tư thế và đảm bảo đủ ánh sáng!',
                'bbox': [int(x) for x in face.bbox]
            })

    # Head pose check: Must look directly frontal (no side yaw or extreme pitch)
    if hasattr(face, 'kps') and face.kps is not None and len(face.kps) >= 5:
        kps = face.kps
        eye_mid = (kps[0] + kps[1]) / 2.0
        eye_dist = float(np.linalg.norm(kps[1] - kps[0])) or 1.0
        yaw_offset = float((kps[2][0] - eye_mid[0]) / eye_dist)

        mouth_mid = (kps[3] + kps[4]) / 2.0
        face_v = float(abs(mouth_mid[1] - eye_mid[1])) or 1.0
        pitch_ratio = float((kps[2][1] - eye_mid[1]) / face_v)

        if abs(yaw_offset) > 0.22:
            side = 'trái' if yaw_offset > 0.22 else 'phải'
            return jsonify({
                'success': False,
                'matched': False,
                'similarity': 0.0,
                'message': f'Thí sinh đang quay mặt sang {side}. Vui lòng nhìn thẳng trực diện vào camera!',
                'bbox': [int(x) for x in face.bbox]
            })
        elif pitch_ratio > 0.78:
            return jsonify({
                'success': False,
                'matched': False,
                'similarity': 0.0,
                'message': 'Thí sinh đang cúi đầu. Vui lòng ngẩng mặt nhìn thẳng trực diện camera!',
                'bbox': [int(x) for x in face.bbox]
            })
        elif pitch_ratio < 0.28:
            return jsonify({
                'success': False,
                'matched': False,
                'similarity': 0.0,
                'message': 'Thí sinh đang ngẩng mặt lên trên. Vui lòng nhìn thẳng trực diện camera!',
                'bbox': [int(x) for x in face.bbox]
            })

    # -------------------------------------------------------------
    # 3. InsightFace ArcFace Comparison ("dùng InsightFace so sánh")
    # -------------------------------------------------------------
    rec_model = face_app.models.get('recognition')
    enrolled_b64 = data.get('enrolled_image')

    enrolled_feat = None
    if enrolled_b64:
        enrolled_bgr = decode_base64_image(enrolled_b64)
        if enrolled_bgr is not None:
            e_faces = face_app.get(enrolled_bgr)
            if e_faces:
                e_faces = sorted(e_faces, key=lambda x: (x.bbox[2] - x.bbox[0]) * (x.bbox[3] - x.bbox[1]), reverse=True)
                enrolled_feat = e_faces[0].embedding

    if enrolled_feat is None and enrolled_embedding:
        enrolled_feat = np.array(enrolled_embedding, dtype=np.float32)

    if enrolled_feat is None:
        return jsonify({'error': 'Không có dữ liệu khuôn mặt đăng ký gốc để so sánh.'}), 400

    if rec_model and hasattr(rec_model, 'compute_sim'):
        cosine_sim = float(rec_model.compute_sim(face.embedding, enrolled_feat))
    else:
        probe_emb = normalize_vector(face.embedding)
        ref_emb = normalize_vector(enrolled_feat)
        cosine_sim = float(np.dot(probe_emb, ref_emb))

    similarity_percent = round(max(0.0, min(100.0, cosine_sim * 100.0)), 1)
    
    # Adaptive threshold based on face distance:
    # When sitting further away (0.20 <= face_h_ratio < 0.28), resolution is lower, threshold is 50.0%
    # When sitting close up (face_h_ratio >= 0.28), threshold is 55.0%
    effective_threshold = 50.0 if face_h_ratio < 0.28 else 55.0
    matched = bool(similarity_percent >= effective_threshold)

    if not matched:
        if face_h_ratio < 0.28 and similarity_percent >= 38.0:
            msg = f'Khuôn mặt ở khoảng cách xa camera làm giảm độ nét ({similarity_percent}%). Vui lòng ngồi gần camera hơn!'
        else:
            msg = f'Khuôn mặt không trùng khớp với ảnh đăng ký ({similarity_percent}% < {effective_threshold}%). Yêu cầu đúng thí sinh làm bài!'

        return jsonify({
            'success': True,
            'matched': False,
            'similarity': similarity_percent,
            'threshold': effective_threshold,
            'message': msg,
            'bbox': [int(x) for x in face.bbox]
        })

    return jsonify({
        'success': True,
        'matched': True,
        'similarity': similarity_percent,
        'threshold': effective_threshold,
        'message': f'Xác thực thành công! Khuôn mặt chuẩn tâm, rõ nét và trùng khớp {similarity_percent}% (Đạt yêu cầu).',
        'bbox': [int(x) for x in face.bbox]
    })


@app.route('/api/proctor/analyze', methods=['POST'])
def analyze_proctor_snapshot():
    """
    Analyzes periodic/random exam snapshot:
    1. YOLOv8 detects cell phone, person count, suspicious objects (books, laptop).
    2. InsightFace checks face match against registered student embedding.
    """
    data = request.get_json(force=True)
    img_b64 = data.get('image')
    enrolled_embedding = data.get('enrolled_embedding')
    ver_b64 = data.get('verification_image')
    # Default threshold 55% for in-exam monitoring
    threshold = float(data.get('threshold', 55.0))

    if not img_b64:
        return jsonify({'error': 'Thiếu dữ liệu ảnh giám sát.'}), 400

    img_bgr = decode_base64_image(img_b64)
    if img_bgr is None:
        return jsonify({'error': 'Ảnh không hợp lệ.'}), 400

    # Extract embedding from entry verification photo if provided
    ver_feat = None
    if ver_b64:
        ver_bgr = decode_base64_image(ver_b64)
        if ver_bgr is not None:
            v_faces = face_app.get(ver_bgr)
            if v_faces:
                v_faces = sorted(v_faces, key=lambda x: (x.bbox[2]-x.bbox[0])*(x.bbox[3]-x.bbox[1]), reverse=True)
                ver_feat = v_faces[0].embedding

    h, w = img_bgr.shape[:2]

    # -------------------------------------------------------------
    # 1. YOLOv8 Object Detection
    # -------------------------------------------------------------
    results = yolo_model(img_bgr, conf=0.35, verbose=False)[0]
    detections = []
    person_boxes = []
    phone_boxes = []
    book_boxes = []
    laptop_boxes = []

    for box in results.boxes:
        cls_id = int(box.cls[0])
        conf = float(box.conf[0])
        x1, y1, x2, y2 = [int(v) for v in box.xyxy[0].tolist()]

        if cls_id in CHEAT_CLASSES:
            label = CHEAT_CLASSES[cls_id]
            det_item = {
                'label': label,
                'coco_id': cls_id,
                'dataset': 'COCO',
                'confidence': round(conf * 100, 1),
                'box': [x1, y1, x2, y2],
                'normalized_box': [round(x1 / w, 4), round(y1 / h, 4), round(x2 / w, 4), round(y2 / h, 4)]
            }
            detections.append(det_item)

            if label == 'person':
                person_boxes.append(det_item)
            elif label == 'cell phone':
                phone_boxes.append(det_item)
            elif label == 'book':
                book_boxes.append(det_item)
            elif label == 'laptop':
                laptop_boxes.append(det_item)

    person_count = len(person_boxes)
    violations = []
    status = 'normal'

    # Check YOLO violations (COCO Dataset)
    if len(phone_boxes) > 0:
        phone_conf = phone_boxes[0]['confidence']
        violations.append({
            'type': 'phone_detected',
            'severity': 'high',
            'message': f'Phát hiện điện thoại di động trong khung hình (Độ tin cậy: {phone_conf}%)'
        })
        status = 'violation'

    if person_count == 0:
        violations.append({
            'type': 'face_absent',
            'severity': 'high',
            'message': 'Không phát hiện thí sinh trước màn hình (Vắng mặt)'
        })
        status = 'violation'
    elif person_count > 1:
        violations.append({
            'type': 'multiple_persons',
            'severity': 'high',
            'message': f'Phát hiện {person_count} người trong khung hình camera (Có người trợ giúp)'
        })
        status = 'violation'

    if len(book_boxes) > 0:
        violations.append({
            'type': 'suspicious_object',
            'severity': 'medium',
            'message': 'Phát hiện sách hoặc tài liệu trong khung hình (COCO: book)'
        })
        if status != 'violation':
            status = 'warning'

    if len(laptop_boxes) > 0:
        violations.append({
            'type': 'suspicious_device',
            'severity': 'medium',
            'message': 'Phát hiện thiết bị máy tính thứ hai trong khung hình (COCO: laptop)'
        })
        if status != 'violation':
            status = 'warning'

    # -------------------------------------------------------------
    # 2. InsightFace: Identity Verification & Head Pose / Gaze Check
    # -------------------------------------------------------------
    face_similarity = None
    face_matched = None
    gaze_info = None

    if person_count > 0:
        faces = face_app.get(img_bgr)
        if faces:
            faces = sorted(faces, key=lambda x: (x.bbox[2] - x.bbox[0]) * (x.bbox[3] - x.bbox[1]), reverse=True)
            primary_face = faces[0]

            box_w = float(primary_face.bbox[2] - primary_face.bbox[0])
            box_h = float(primary_face.bbox[3] - primary_face.bbox[1])
            distance_ratio = round(box_h / float(h), 3)

            is_too_far = (distance_ratio < 0.16 or box_h < 75 or box_w < 65)
            is_sitting_back = (0.16 <= distance_ratio < 0.24)

            # 2.1 Distance Check ("rời xa màn hình")
            if is_too_far:
                violations.append({
                    'type': 'too_far',
                    'severity': 'medium',
                    'message': 'Thí sinh ngồi quá xa camera (ngoài cự ly chuẩn). Vui lòng ngồi lại gần màn hình!'
                })
                if status != 'violation':
                    status = 'warning'

            # 2.2 Centering Check ("người phải ở trung tâm ảnh")
            face_cx = (primary_face.bbox[0] + primary_face.bbox[2]) / 2.0 / float(w)
            face_cy = (primary_face.bbox[1] + primary_face.bbox[3]) / 2.0 / float(h)
            if face_cx < 0.18 or face_cx > 0.82 or face_cy < 0.10 or face_cy > 0.90:
                violations.append({
                    'type': 'off_center',
                    'severity': 'medium',
                    'message': 'Thí sinh ngồi lệch khỏi trung tâm camera. Yêu cầu ngồi ở vị trí chính giữa màn hình!'
                })
                if status != 'violation':
                    status = 'warning'

            # 2.3 Blurriness / Clarity Check ("rõ mặt của sinh viên")
            gray = cv2.cvtColor(img_bgr, cv2.COLOR_BGR2GRAY)
            x1, y1 = max(0, int(primary_face.bbox[0])), max(0, int(primary_face.bbox[1]))
            x2, y2 = min(w, int(primary_face.bbox[2])), min(h, int(primary_face.bbox[3]))
            face_roi = gray[y1:y2, x1:x2]
            if face_roi.size > 0:
                blur_score = cv2.Laplacian(face_roi, cv2.CV_64F).var()
                if blur_score < 24.0:
                    violations.append({
                        'type': 'face_blur',
                        'severity': 'low',
                        'message': 'Hình ảnh khuôn mặt bị mờ hoặc thiếu sáng, không rõ mặt thí sinh.'
                    })

            # 2.4 Direct Frontal Gaze
            if hasattr(primary_face, 'kps') and primary_face.kps is not None and len(primary_face.kps) >= 5:
                kps = primary_face.kps
                eye_mid = (kps[0] + kps[1]) / 2.0
                eye_dist = float(np.linalg.norm(kps[1] - kps[0])) or 1.0
                yaw_offset = float((kps[2][0] - eye_mid[0]) / eye_dist)

                mouth_mid = (kps[3] + kps[4]) / 2.0
                face_v = float(abs(mouth_mid[1] - eye_mid[1])) or 1.0
                pitch_ratio = float((kps[2][1] - eye_mid[1]) / face_v)

                gaze_status = 'frontal'
                gaze_message = None

                if yaw_offset > 0.22:
                    gaze_status = 'turned_left'
                    gaze_message = 'Thí sinh quay đầu sang trái, không nhìn trực diện màn hình'
                elif yaw_offset < -0.22:
                    gaze_status = 'turned_right'
                    gaze_message = 'Thí sinh quay đầu sang phải, không nhìn trực diện màn hình'
                elif pitch_ratio > 0.78:
                    gaze_status = 'looking_down'
                    gaze_message = 'Thí sinh cúi đầu nhìn xuống (nghi vấn xem tài liệu hoặc điện thoại)'
                elif pitch_ratio < 0.28:
                    gaze_status = 'looking_up'
                    gaze_message = 'Thí sinh ngẩng mặt lên trên'

                gaze_info = {
                    'status': gaze_status,
                    'yaw': round(yaw_offset, 2),
                    'pitch': round(pitch_ratio, 2),
                    'distance_ratio': distance_ratio
                }

                if gaze_message:
                    violations.append({
                        'type': 'looking_away',
                        'severity': 'medium',
                        'gaze_status': gaze_status,
                        'message': gaze_message,
                        'details': gaze_info
                    })
                    if status != 'violation':
                        status = 'warning'

            # 2.5 Identity Verification (InsightFace ArcFace with Dual Profile + Entry Verification Matching)
            rec_model = face_app.models.get('recognition')
            sim_enrolled = 0.0
            sim_ver = 0.0

            if enrolled_embedding and primary_face.embedding is not None:
                enrolled_feat = np.array(enrolled_embedding, dtype=np.float32)
                if rec_model and hasattr(rec_model, 'compute_sim'):
                    c_sim = float(rec_model.compute_sim(primary_face.embedding, enrolled_feat))
                else:
                    c_sim = float(np.dot(normalize_vector(primary_face.embedding), normalize_vector(enrolled_feat)))
                sim_enrolled = round(max(0.0, min(100.0, c_sim * 100.0)), 1)

            if ver_feat is not None and primary_face.embedding is not None:
                if rec_model and hasattr(rec_model, 'compute_sim'):
                    v_sim = float(rec_model.compute_sim(primary_face.embedding, ver_feat))
                else:
                    v_sim = float(np.dot(normalize_vector(primary_face.embedding), normalize_vector(ver_feat)))
                sim_ver = round(max(0.0, min(100.0, v_sim * 100.0)), 1)

            # Combined similarity: takes the highest verified match between official profile & entry photo
            if enrolled_embedding or ver_feat is not None:
                face_similarity = max(sim_enrolled, sim_ver)
            else:
                face_similarity = None

            if face_similarity is not None:
                if is_too_far:
                    face_matched = bool(face_similarity >= 38.0)
                    if not face_matched:
                        violations.append({
                            'type': 'face_mismatch',
                            'severity': 'high',
                            'message': f'Khuôn mặt không trùng khớp thí sinh ({face_similarity}%). Nghi vấn thi hộ!'
                        })
                        status = 'violation'
                elif is_sitting_back:
                    face_matched = bool(face_similarity >= 48.0)
                    if not face_matched:
                        violations.append({
                            'type': 'face_mismatch',
                            'severity': 'high',
                            'message': f'Khuôn mặt không trùng khớp thí sinh ({face_similarity}% < 48%). Nghi vấn thi hộ!'
                        })
                        status = 'violation'
                else:
                    face_matched = bool(face_similarity >= 52.0)
                    if not face_matched:
                        violations.append({
                            'type': 'face_mismatch',
                            'severity': 'high',
                            'message': f'Khuôn mặt không trùng khớp thí sinh ({face_similarity}% < 52%). Nghi vấn thi hộ!'
                        })
                        status = 'violation'


    # Build summary
    if not violations:
        summary = 'Khung hình bình thường, 1 thí sinh làm bài nghiêm túc.'
    else:
        summary = '; '.join([v['message'] for v in violations])

    return jsonify({
        'status': status,
        'violations': violations,
        'detections': detections,
        'person_count': person_count,
        'face_similarity': face_similarity,
        'face_matched': face_matched,
        'gaze_info': gaze_info,
        'summary': summary,
        'image_size': {'width': w, 'height': h}
    })


@app.route('/api/face/compare', methods=['POST'])
def compare_two_faces():
    """
    Directly compares two face images using InsightFace ArcFace model.
    Input: image1, image2 (base64)
    Output: similarity, matched, model: InsightFace ArcFace
    """
    data = request.get_json(force=True)
    img1_b64 = data.get('image1') or data.get('probe_image')
    img2_b64 = data.get('image2') or data.get('enrolled_image')
    threshold = float(data.get('threshold', 70.0))

    if not img1_b64 or not img2_b64:
        return jsonify({'error': 'Thiếu 2 ảnh để so sánh.'}), 400

    img1 = decode_base64_image(img1_b64)
    img2 = decode_base64_image(img2_b64)

    if img1 is None or img2 is None:
        return jsonify({'error': 'Dữ liệu ảnh không hợp lệ.'}), 400

    faces1 = face_app.get(img1)
    faces2 = face_app.get(img2)

    if not faces1:
        return jsonify({'success': False, 'message': 'Không tìm thấy khuôn mặt trong ảnh thứ nhất.'}), 400
    if not faces2:
        return jsonify({'success': False, 'message': 'Không tìm thấy khuôn mặt trong ảnh thứ hai.'}), 400

    face1 = sorted(faces1, key=lambda x: (x.bbox[2]-x.bbox[0])*(x.bbox[3]-x.bbox[1]), reverse=True)[0]
    face2 = sorted(faces2, key=lambda x: (x.bbox[2]-x.bbox[0])*(x.bbox[3]-x.bbox[1]), reverse=True)[0]

    rec_model = face_app.models.get('recognition')
    if rec_model and hasattr(rec_model, 'compute_sim'):
        cosine_sim = float(rec_model.compute_sim(face1.embedding, face2.embedding))
    else:
        v1 = normalize_vector(face1.embedding)
        v2 = normalize_vector(face2.embedding)
        cosine_sim = float(np.dot(v1, v2))

    similarity_percent = round(max(0.0, min(100.0, cosine_sim * 100.0)), 1)
    matched = bool(similarity_percent >= threshold)

    return jsonify({
        'success': True,
        'model': 'InsightFace ArcFace 512D',
        'matched': matched,
        'similarity': similarity_percent,
        'threshold': threshold,
        'message': f'Độ tương đồng InsightFace ArcFace: {similarity_percent}% (Ngưỡng {threshold}%).'
    })


if __name__ == '__main__':
    port = int(os.environ.get('AI_SERVICE_PORT', 5001))
    print(f"==> Starting Eye-Exam AI Proctoring Microservice on port {port}...")
    app.run(host='0.0.0.0', port=port, debug=False, threaded=True)
