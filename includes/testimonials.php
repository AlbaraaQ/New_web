<?php
/**
 * ملف نظام التقييمات الديناميكي
 * 
 * يحتوي على الدوال الخاصة بإضافة وعرض وإدارة تقييمات العملاء
 */

// دالة إضافة تقييم جديد
function add_testimonial($data) {
    global $db;
    
    // تنظيف البيانات
    $client_name = $db->escape($data['client_name']);
    $client_position = isset($data['client_position']) ? $db->escape($data['client_position']) : '';
    $client_image = isset($data['client_image']) ? $db->escape($data['client_image']) : '';
    $content = $db->escape($data['content']);
    $rating = intval($data['rating']);
    $status = isset($data['status']) ? $db->escape($data['status']) : 'pending';
    
    // التحقق من البيانات
    if(empty($client_name) || empty($content) || $rating < 1 || $rating > 5) {
        return false;
    }
    
    // إضافة التقييم إلى قاعدة البيانات
    $query = "INSERT INTO testimonials (client_name, client_position, client_image, content, rating, status, created_at) 
              VALUES ('$client_name', '$client_position', '$client_image', '$content', $rating, '$status', NOW())";
    
    return $db->query($query);
}

// دالة الحصول على جميع التقييمات
function get_all_testimonials($limit = null, $status = null) {
    global $db;
    
    $query = "SELECT * FROM testimonials";
    
    // إضافة شرط الحالة إذا تم تحديدها
    if($status !== null) {
        $status = $db->escape($status);
        $query .= " WHERE status = '$status'";
    }
    
    // ترتيب التقييمات حسب تاريخ الإضافة (الأحدث أولاً)
    $query .= " ORDER BY created_at DESC";
    
    // إضافة حد للنتائج إذا تم تحديده
    if($limit !== null && is_numeric($limit)) {
        $limit = intval($limit);
        $query .= " LIMIT $limit";
    }
    
    $result = $db->query($query);
    
    if($result && $db->num_rows($result) > 0) {
        $testimonials = [];
        while($row = $db->fetch_assoc($result)) {
            $testimonials[] = $row;
        }
        return $testimonials;
    }
    
    return [];
}

// دالة الحصول على التقييمات المعتمدة فقط
function get_approved_testimonials($limit = null) {
    return get_all_testimonials($limit, 'approved');
}

// دالة الحصول على تقييم محدد بواسطة المعرف
function get_testimonial_by_id($id) {
    global $db;
    
    $id = intval($id);
    $query = "SELECT * FROM testimonials WHERE id = $id";
    $result = $db->query($query);
    
    if($result && $db->num_rows($result) > 0) {
        return $db->fetch_assoc($result);
    }
    
    return null;
}

// دالة تحديث حالة التقييم
function update_testimonial_status($id, $status) {
    global $db;
    
    $id = intval($id);
    $status = $db->escape($status);
    
    $query = "UPDATE testimonials SET status = '$status', updated_at = NOW() WHERE id = $id";
    return $db->query($query);
}

// دالة حذف تقييم
function delete_testimonial($id) {
    global $db;
    
    $id = intval($id);
    $query = "DELETE FROM testimonials WHERE id = $id";
    return $db->query($query);
}

// دالة الحصول على إجمالي عدد التقييمات
function get_total_testimonials($status = null) {
    global $db;
    
    $query = "SELECT COUNT(*) as total FROM testimonials";
    
    // إضافة شرط الحالة إذا تم تحديدها
    if($status !== null) {
        $status = $db->escape($status);
        $query .= " WHERE status = '$status'";
    }
    
    $result = $db->query($query);
    
    if($result && $db->num_rows($result) > 0) {
        $row = $db->fetch_assoc($result);
        return $row['total'];
    }
    
    return 0;
}

// دالة الحصول على متوسط التقييمات
function get_average_rating() {
    global $db;
    
    $query = "SELECT AVG(rating) as average FROM testimonials WHERE status = 'approved'";
    $result = $db->query($query);
    
    if($result && $db->num_rows($result) > 0) {
        $row = $db->fetch_assoc($result);
        return round($row['average'], 1);
    }
    
    return 0;
}

// دالة عرض نجوم التقييم
function display_rating_stars($rating) {
    $rating = intval($rating);
    $html = '';
    
    for($i = 1; $i <= 5; $i++) {
        if($i <= $rating) {
            $html .= '<i class="fas fa-star text-warning"></i>';
        } else {
            $html .= '<i class="far fa-star text-warning"></i>';
        }
    }
    
    return $html;
}

// دالة معالجة رفع صورة العميل
function upload_testimonial_image($file) {
    // التحقق من وجود الملف
    if(!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // التحقق من نوع الملف
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if(!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // التحقق من حجم الملف (الحد الأقصى 2 ميجابايت)
    $max_size = 2 * 1024 * 1024; // 2MB
    if($file['size'] > $max_size) {
        return false;
    }
    
    // إنشاء اسم فريد للملف
    $file_name = uniqid('testimonial_') . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    
    // مسار حفظ الملف
    $upload_dir = 'uploads/testimonials/';
    
    // التأكد من وجود المجلد
    if(!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // حفظ الملف
    $upload_path = $upload_dir . $file_name;
    if(move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $upload_path;
    }
    
    return false;
}
