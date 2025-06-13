<?php
/**
 * ملف الدوال المساعدة
 * 
 * يحتوي على دوال مساعدة تستخدم في جميع أنحاء الموقع
 */

/**
 * تحويل النص إلى رابط مخصص
 * 
 * @param string $text النص المراد تحويله
 * @return string الرابط المخصص
 */
function slugify($text) {
    // تحويل الحروف العربية إلى حروف لاتينية
    $text = transliterate_arabic($text);
    
    // إزالة الأحرف الخاصة
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    
    // تحويل إلى حروف صغيرة
    $text = strtolower($text);
    
    // إزالة الشرطات من البداية والنهاية
    $text = trim($text, '-');
    
    // التحقق من وجود نص
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

/**
 * تحويل الحروف العربية إلى حروف لاتينية
 * 
 * @param string $text النص العربي
 * @return string النص اللاتيني
 */
function transliterate_arabic($text) {
    $arabic_chars = array(
        'أ' => 'a', 'إ' => 'e', 'آ' => 'a', 'ا' => 'a',
        'ب' => 'b', 'ت' => 't', 'ث' => 'th',
        'ج' => 'j', 'ح' => 'h', 'خ' => 'kh',
        'د' => 'd', 'ذ' => 'th',
        'ر' => 'r', 'ز' => 'z',
        'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd',
        'ط' => 't', 'ظ' => 'z',
        'ع' => 'a', 'غ' => 'gh',
        'ف' => 'f', 'ق' => 'q', 'ك' => 'k',
        'ل' => 'l', 'م' => 'm', 'ن' => 'n',
        'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ى' => 'a',
        'ة' => 'h', 'ء' => 'a',
        'ؤ' => 'o', 'ئ' => 'e'
    );
    
    return str_replace(array_keys($arabic_chars), array_values($arabic_chars), $text);
}

/**
 * تنظيف النص من الأكواد الضارة
 * 
 * @param string $data النص المراد تنظيفه
 * @return string النص النظيف
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * تحويل التاريخ إلى صيغة مقروءة
 * 
 * @param string $date التاريخ
 * @param boolean $with_time إضافة الوقت
 * @return string التاريخ المقروء
 */
function format_date($date, $with_time = false) {
    if($with_time) {
        return date('d/m/Y H:i', strtotime($date));
    } else {
        return date('d/m/Y', strtotime($date));
    }
}

/**
 * اختصار النص
 * 
 * @param string $text النص المراد اختصاره
 * @param int $length الطول المطلوب
 * @param string $append النص المضاف في النهاية
 * @return string النص المختصر
 */
function truncate_text($text, $length = 100, $append = '...') {
    if(strlen($text) > $length) {
        $text = substr($text, 0, $length);
        $text = substr($text, 0, strrpos($text, ' '));
        $text .= $append;
    }
    
    return $text;
}

/**
 * تحويل النص إلى HTML
 * 
 * @param string $text النص المراد تحويله
 * @return string النص بصيغة HTML
 */
function nl2p($text) {
    $text = '<p>' . str_replace("\n\n", '</p><p>', $text) . '</p>';
    $text = str_replace("\n", '<br>', $text);
    return $text;
}

/**
 * إنشاء رابط صفحة
 * 
 * @param string $page اسم الصفحة
 * @param array $params المعلمات (اختياري)
 * @return string الرابط
 */
function url($page, $params = array()) {
    $url = BASE_URL . '/' . $page;
    
    if(!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * إنشاء رابط للوحة التحكم
 * 
 * @param string $page اسم الصفحة
 * @param array $params المعلمات (اختياري)
 * @return string الرابط
 */
function admin_url($page, $params = array()) {
    $url = ADMIN_URL . '/' . $page;
    
    if(!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    return $url;
}

/**
 * إنشاء رابط للأصول
 * 
 * @param string $path المسار
 * @return string الرابط
 */
function asset_url($path) {
    return BASE_URL . '/assets/' . $path;
}

/**
 * إنشاء رابط للتحميلات
 * 
 * @param string $path المسار
 * @return string الرابط
 */
function upload_url($filename) {
    if(empty($filename)) return '';
    
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $base_url .= "://".$_SERVER['HTTP_HOST'];
    
    return $base_url.'/zwm/assets/uploads/'.$filename;
}
/**
 * التحقق من وجود رسالة خطأ
 * 
 * @param string $field اسم الحقل
 * @param array $errors مصفوفة الأخطاء
 * @return boolean وجود خطأ
 */
function has_error($field, $errors) {
    return isset($errors[$field]);
}

/**
 * الحصول على رسالة الخطأ
 * 
 * @param string $field اسم الحقل
 * @param array $errors مصفوفة الأخطاء
 * @return string رسالة الخطأ
 */
function get_error($field, $errors) {
    return isset($errors[$field]) ? $errors[$field] : '';
}

/**
 * الحصول على قيمة الحقل
 * 
 * @param string $field اسم الحقل
 * @param array $data مصفوفة البيانات
 * @param string $default القيمة الافتراضية
 * @return string قيمة الحقل
 */
function get_value($field, $data, $default = '') {
    return isset($data[$field]) ? $data[$field] : $default;
}

/**
 * التحقق من تحديد الخيار
 * 
 * @param string $field اسم الحقل
 * @param string $value القيمة
 * @param array $data مصفوفة البيانات
 * @return string سمة التحديد
 */
function is_selected($field, $value, $data) {
    return (isset($data[$field]) && $data[$field] == $value) ? 'selected' : '';
}

/**
 * التحقق من تحديد الخيار
 * 
 * @param string $field اسم الحقل
 * @param string $value القيمة
 * @param array $data مصفوفة البيانات
 * @return string سمة التحديد
 */
function is_checked($field, $value, $data) {
    return (isset($data[$field]) && $data[$field] == $value) ? 'checked' : '';
}

/**
 * تحميل ملف
 * 
 * @param array $file معلومات الملف
 * @param string $destination مجلد الوجهة
 * @param array $allowed_extensions الامتدادات المسموح بها
 * @param int $max_size الحجم الأقصى بالبايت
 * @return string|boolean اسم الملف أو false في حالة الفشل
 */
function upload_file($file, $destination, $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 5242880) {
    // التحقق من وجود خطأ في الرفع
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error: " . $file['error']);
        return false;
    }

    // التحقق من حجم الملف
    if ($file['size'] > $max_size) {
        error_log("File too large: " . $file['size']);
        return false;
    }

    // التحقق من امتداد الملف
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);

    if (!in_array($extension, $allowed_extensions)) {
        error_log("Invalid file extension: " . $extension);
        return false;
    }

    // إنشاء اسم فريد للملف
    $new_filename = uniqid() . '.' . $extension;
    $upload_path = rtrim($destination, '/') . '/' . $new_filename;

    // إنشاء المجلد إذا لم يكن موجوداً
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    // نقل الملف إلى المسار المطلوب
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // تغيير صلاحيات الملف بعد الرفع
        chmod($upload_path, 0644);
        return $new_filename;
    } else {
        error_log("Failed to move uploaded file to: " . $upload_path);
        return false;
    }
}

function upload_file_m($input_name, $destination, $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 5242880) {
    if (!isset($_FILES[$input_name])) {
        error_log("No file uploaded with input name: $input_name");
        return false;
    }

    $file = $_FILES[$input_name];

    // التحقق من وجود خطأ
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error for $input_name: " . $file['error']);
        return false;
    }

    // التحقق من الحجم
    if ($file['size'] > $max_size) {
        error_log("File too large: " . $file['size']);
        return false;
    }

    // التحقق من الامتداد
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);

    if (!in_array($extension, $allowed_extensions)) {
        error_log("Invalid file extension: " . $extension);
        return false;
    }

    // التحقق من أن الملف فعلاً تم رفعه عبر HTTP POST
    if (!is_uploaded_file($file['tmp_name'])) {
        error_log("File not uploaded via HTTP POST: " . $file['tmp_name']);
        return false;
    }

    // توليد اسم فريد
    $new_filename = uniqid('img_', true) . '.' . $extension;
    $upload_path = rtrim($destination, '/') . '/' . $new_filename;

    // إنشاء المجلد إن لم يكن موجوداً
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    // نقل الملف
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        chmod($upload_path, 0644);
        return $new_filename;
    } else {
        error_log("Failed to move uploaded file to: " . $upload_path);
        return false;
    }
}

/**
 * حذف ملف
 * 
 * @param string $filename اسم الملف
 * @param string $directory المجلد
 * @return boolean نجاح أو فشل الحذف
 */
function delete_file($filename, $directory) {
    $file_path = $directory . '/' . $filename;
    
    if(file_exists($file_path)) {
        return unlink($file_path);
    }
    
    return false;
}

/**
 * إعادة توجيه
 * 
 * @param string $url الرابط
 */
function redirect($url) {
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit();
    } else {
        echo '<script>window.location.href="'.$url.'";</script>';
        exit();
    }
}

/**
 * إنشاء رسالة تنبيه
 * 
 * @param string $message الرسالة
 * @param string $type نوع الرسالة
 */
function set_flash_message($message, $type = 'success') {
    $_SESSION['flash_message'] = array(
        'message' => $message,
        'type' => $type
    );
}

/**
 * عرض رسالة التنبيه
 * 
 * @return string رسالة التنبيه
 */
function display_flash_message() {
    if(isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message']['message'];
        $type = $_SESSION['flash_message']['type'];
        
        unset($_SESSION['flash_message']);
        
        return '<div class="alert alert-' . $type . '">' . $message . '</div>';
    }
    
    return '';
}

/**
 * التحقق من وجود رسالة تنبيه
 * 
 * @return boolean وجود رسالة تنبيه
 */
function has_flash_message() {
    return isset($_SESSION['flash_message']);
}

/**
 * الحصول على معلومات المتصفح والجهاز
 * 
 * @param string $user_agent معلومات المتصفح
 * @return array معلومات المتصفح والجهاز
 */
function get_browser_info($user_agent) {
    $browser = 'Unknown';
    $os = 'Unknown';
    $device_type = 'Desktop';
    
    // تحديد نوع الجهاز
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $user_agent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4))) {
        $device_type = 'Mobile';
    } elseif(preg_match('/android|ipad|playbook|silk/i', $user_agent)) {
        $device_type = 'Tablet';
    }
    
    // تحديد نوع المتصفح
    if(preg_match('/MSIE/i', $user_agent) || preg_match('/Trident/i', $user_agent)) {
        $browser = 'Internet Explorer';
    } elseif(preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif(preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Chrome';
    } elseif(preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif(preg_match('/Opera/i', $user_agent)) {
        $browser = 'Opera';
    } elseif(preg_match('/Netscape/i', $user_agent)) {
        $browser = 'Netscape';
    }
    
    // تحديد نظام التشغيل
    if(preg_match('/windows|win32/i', $user_agent)) {
        $os = 'Windows';
    } elseif(preg_match('/macintosh|mac os x/i', $user_agent)) {
        $os = 'Mac OS';
    } elseif(preg_match('/linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif(preg_match('/android/i', $user_agent)) {
        $os = 'Android';
    } elseif(preg_match('/iphone|ipad|ipod/i', $user_agent)) {
        $os = 'iOS';
    }
    
    return array(
        'browser' => $browser,
        'os' => $os,
        'device_type' => $device_type
    );
}

/**
 * الحصول على عنوان IP الحقيقي للزائر
 * 
 * @return string عنوان IP
 */
function get_client_ip() {
    $ip = '';
    
    if(isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif(isset($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } elseif(isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // التحقق من صحة عنوان IP
    if(filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }
    
    return '0.0.0.0';
}

/**
 * تسجيل زيارة
 */
function record_visit() {
    // التحقق من وجود جلسة
    if(!isset($_SESSION['visitor_id'])) {
        $_SESSION['visitor_id'] = session_id();
    }
    
    $visitor = new Visitor();
    $page_visited = $_SERVER['REQUEST_URI'];
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip_address = get_client_ip();
    $session_id = $_SESSION['visitor_id'];
    
    // التحقق من وجود زيارة سابقة لهذه الصفحة في نفس الجلسة
    $is_unique = !$visitor->hasVisitedBefore($session_id, $page_visited);
    
    // الحصول على معلومات المتصفح والجهاز
    $browser_info = get_browser_info($user_agent);
    
    // تسجيل الزيارة
    $data = array(
        'ip_address' => $ip_address,
        'user_agent' => $user_agent,
        'page_visited' => $page_visited,
        'referrer' => $referrer,
        'country' => '', // يمكن استخدام خدمة خارجية للحصول على البلد
        'city' => '', // يمكن استخدام خدمة خارجية للحصول على المدينة
        'device_type' => $browser_info['device_type'],
        'browser' => $browser_info['browser'],
        'os' => $browser_info['os'],
        'session_id' => $session_id,
        'is_unique' => $is_unique
    );
    
    $visitor->recordVisit($data);
}

/**
 * تحويل التقييم النجمي إلى نجوم
 * 
 * @param int $rating التقييم
 * @return string النجوم
 */
function rating_stars($rating) {
    $stars = '';
    
    for($i = 1; $i <= 5; $i++) {
        if($i <= $rating) {
            $stars .= '<i class="fas fa-star"></i>';
        } else {
            $stars .= '<i class="far fa-star"></i>';
        }
    }
    
    return $stars;
}

/**
 * تحويل النص إلى HTML آمن
 * 
 * @param string $text النص
 * @return string HTML
 */
function safe_html($text) {
    return htmlspecialchars_decode(nl2p($text));
}

/**
 * الحصول على الصفحة الحالية
 * 
 * @return int رقم الصفحة
 */
function get_current_page() {
    return isset($_GET['page']) ? (int)$_GET['page'] : 1;
}

/**
 * إنشاء روابط الصفحات
 * 
 * @param int $total_items إجمالي العناصر
 * @param int $items_per_page عدد العناصر في الصفحة
 * @param int $current_page الصفحة الحالية
 * @param string $url_pattern نمط الرابط
 * @return string روابط الصفحات
 */
function pagination($total_items, $items_per_page, $current_page, $url_pattern) {
    $total_pages = ceil($total_items / $items_per_page);
    
    if($total_pages <= 1) {
        return '';
    }
    
    $pagination = '<ul class="pagination">';
    
    // زر الصفحة السابقة
    if($current_page > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $current_page - 1) . '">&raquo;</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>';
    }
    
    // أرقام الصفحات
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);
    
    if($start_page > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, 1) . '">1</a></li>';
        
        if($start_page > 2) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }
    
    for($i = $start_page; $i <= $end_page; $i++) {
        if($i == $current_page) {
            $pagination .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
        } else {
            $pagination .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $i) . '">' . $i . '</a></li>';
        }
    }
    
    if($end_page < $total_pages) {
        if($end_page < $total_pages - 1) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        
        $pagination .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $total_pages) . '">' . $total_pages . '</a></li>';
    }
    
    // زر الصفحة التالية
    if($current_page < $total_pages) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $current_page + 1) . '">&laquo;</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>';
    }
    
    $pagination .= '</ul>';
    
    return $pagination;
}

/**
 * تحويل النص العربي إلى نص آمن للاستخدام في JavaScript
 * 
 * @param string $text النص العربي
 * @return string النص الآمن
 */
function js_escape($text) {
    return json_encode($text);
}

/**
 * تنسيق رقم الهاتف
 * 
 * @param string $phone رقم الهاتف
 * @return string رقم الهاتف المنسق
 */
function format_phone($phone) {
    // إزالة الأحرف غير الرقمية
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // التحقق من طول رقم الهاتف
    if(strlen($phone) == 10) {
        // تنسيق رقم هاتف محلي
        return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
    } elseif(strlen($phone) > 10) {
        // تنسيق رقم هاتف دولي
        return '+' . substr($phone, 0, strlen($phone) - 9) . ' ' . substr($phone, -9, 3) . '-' . substr($phone, -6, 3) . '-' . substr($phone, -3);
    }
    
    // إرجاع الرقم كما هو إذا لم يكن بالتنسيق المتوقع
    return $phone;
}

/**
 * تحويل السعر إلى تنسيق مقروء
 * 
 * @param float $price السعر
 * @param string $currency رمز العملة
 * @return string السعر المنسق
 */
function format_price($price, $currency = 'ريال') {
    return number_format($price, 2) . ' ' . $currency;
}

/**
 * تحويل العدد إلى كلمات عربية
 * 
 * @param int $number العدد
 * @return string الكلمات العربية
 */
function number_to_arabic_words($number) {
    $ones = array('', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر');
    $tens = array('', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون');
    $hundreds = array('', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة');
    $thousands = array('', 'ألف', 'ألفان', 'ثلاثة آلاف', 'أربعة آلاف', 'خمسة آلاف', 'ستة آلاف', 'سبعة آلاف', 'ثمانية آلاف', 'تسعة آلاف');
    
    if($number == 0) {
        return 'صفر';
    }
    
    if($number < 0) {
        return 'سالب ' . number_to_arabic_words(abs($number));
    }
    
    $words = '';
    
    // الآلاف
    if($number >= 1000) {
        if($number < 10000) {
            $words .= $thousands[floor($number / 1000)] . ' ';
        } else {
            $words .= number_to_arabic_words(floor($number / 1000)) . ' ألف ';
        }
        $number %= 1000;
    }
    
    // المئات
    if($number >= 100) {
        $words .= $hundreds[floor($number / 100)] . ' ';
        $number %= 100;
    }
    
    // العشرات والآحاد
    if($number > 0) {
        if($words != '') {
            $words .= 'و';
        }
        
        if($number < 20) {
            $words .= $ones[$number];
        } else {
            $words .= $ones[$number % 10];
            if($number % 10 != 0) {
                $words .= ' و';
            }
            $words .= $tens[floor($number / 10)];
        }
    }
    
    return $words;
}

