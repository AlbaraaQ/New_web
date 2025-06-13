<?php
/**
 * ملف اختبار الوظائف والتكامل
 * 
 * يحتوي على دوال اختبار مختلف وظائف الموقع للتأكد من عملها بشكل صحيح
 */

// دالة اختبار الاتصال بقاعدة البيانات
function test_database_connection() {
    global $db;
    
    $result = [
        'status' => false,
        'message' => ''
    ];
    
    try {
        // محاولة تنفيذ استعلام بسيط
        $query = "SELECT 1";
        $db_result = $db->query($query);
        
        if ($db_result) {
            $result['status'] = true;
            $result['message'] = 'تم الاتصال بقاعدة البيانات بنجاح';
        } else {
            $result['message'] = 'فشل الاتصال بقاعدة البيانات: ' . $db->error();
        }
    } catch (Exception $e) {
        $result['message'] = 'حدث خطأ أثناء الاتصال بقاعدة البيانات: ' . $e->getMessage();
    }
    
    return $result;
}

// دالة اختبار وجود الجداول الرئيسية
function test_database_tables() {
    global $db;
    
    $required_tables = [
        'users',
        'services',
        'projects',
        'testimonials',
        'visitors',
        'contacts',
        'settings'
    ];
    
    $result = [
        'status' => true,
        'message' => 'تم التحقق من وجود جميع الجداول المطلوبة',
        'details' => []
    ];
    
    try {
        // الحصول على قائمة الجداول الموجودة
        $query = "SHOW TABLES";
        $db_result = $db->query($query);
        
        if ($db_result) {
            $existing_tables = [];
            while ($row = $db->fetch_row($db_result)) {
                $existing_tables[] = $row[0];
            }
            
            // التحقق من وجود الجداول المطلوبة
            foreach ($required_tables as $table) {
                if (!in_array($table, $existing_tables)) {
                    $result['status'] = false;
                    $result['details'][] = "الجدول {$table} غير موجود";
                } else {
                    $result['details'][] = "الجدول {$table} موجود";
                }
            }
            
            if (!$result['status']) {
                $result['message'] = 'بعض الجداول المطلوبة غير موجودة';
            }
        } else {
            $result['status'] = false;
            $result['message'] = 'فشل في الحصول على قائمة الجداول: ' . $db->error();
        }
    } catch (Exception $e) {
        $result['status'] = false;
        $result['message'] = 'حدث خطأ أثناء التحقق من الجداول: ' . $e->getMessage();
    }
    
    return $result;
}

// دالة اختبار مجلدات التحميل
function test_upload_directories() {
    $required_directories = [
        'uploads',
        'uploads/services',
        'uploads/projects',
        'uploads/testimonials',
        'uploads/users'
    ];
    
    $result = [
        'status' => true,
        'message' => 'تم التحقق من وجود جميع مجلدات التحميل المطلوبة',
        'details' => []
    ];
    
    foreach ($required_directories as $directory) {
        if (!is_dir($directory)) {
            // محاولة إنشاء المجلد إذا لم يكن موجوداً
            if (!mkdir($directory, 0755, true)) {
                $result['status'] = false;
                $result['details'][] = "فشل في إنشاء المجلد {$directory}";
            } else {
                $result['details'][] = "تم إنشاء المجلد {$directory}";
            }
        } else {
            // التحقق من صلاحيات الكتابة
            if (!is_writable($directory)) {
                $result['status'] = false;
                $result['details'][] = "المجلد {$directory} موجود ولكن لا يمكن الكتابة فيه";
            } else {
                $result['details'][] = "المجلد {$directory} موجود ويمكن الكتابة فيه";
            }
        }
    }
    
    if (!$result['status']) {
        $result['message'] = 'بعض مجلدات التحميل غير موجودة أو لا يمكن الكتابة فيها';
    }
    
    return $result;
}

// دالة اختبار تسجيل الدخول
function test_login() {
    global $db;
    
    $result = [
        'status' => false,
        'message' => ''
    ];
    
    try {
        // التحقق من وجود مستخدم افتراضي
        $query = "SELECT * FROM users WHERE username = 'admin' LIMIT 1";
        $db_result = $db->query($query);
        
        if ($db_result && $db->num_rows($db_result) > 0) {
            $result['status'] = true;
            $result['message'] = 'تم التحقق من وجود المستخدم الافتراضي';
        } else {
            // إنشاء مستخدم افتراضي إذا لم يكن موجوداً
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, password, email, name, role, status) VALUES ('admin', '{$password_hash}', 'admin@example.com', 'المدير', 'admin', 'active')";
            $db_result = $db->query($query);
            
            if ($db_result) {
                $result['status'] = true;
                $result['message'] = 'تم إنشاء المستخدم الافتراضي بنجاح';
            } else {
                $result['message'] = 'فشل في إنشاء المستخدم الافتراضي: ' . $db->error();
            }
        }
    } catch (Exception $e) {
        $result['message'] = 'حدث خطأ أثناء اختبار تسجيل الدخول: ' . $e->getMessage();
    }
    
    return $result;
}

// دالة اختبار إضافة خدمة
function test_add_service() {
    global $db;
    
    $result = [
        'status' => false,
        'message' => ''
    ];
    
    try {
        // بيانات خدمة اختبارية
        $service_data = [
            'title' => 'خدمة اختبارية',
            'description' => 'هذه خدمة اختبارية للتأكد من عمل النظام بشكل صحيح',
            'features' => "ميزة 1\nميزة 2\nميزة 3",
            'price' => 'حسب الطلب',
            'status' => 'active'
        ];
        
        // إضافة الخدمة
        $title = $db->escape($service_data['title']);
        $description = $db->escape($service_data['description']);
        $features = $db->escape($service_data['features']);
        $price = $db->escape($service_data['price']);
        $status = $db->escape($service_data['status']);
        
        $query = "INSERT INTO services (title, description, features, price, status, created_at, updated_at) 
                  VALUES ('{$title}', '{$description}', '{$features}', '{$price}', '{$status}', NOW(), NOW())";
        
        $db_result = $db->query($query);
        
        if ($db_result) {
            $service_id = $db->insert_id();
            $result['status'] = true;
            $result['message'] = "تم إضافة الخدمة الاختبارية بنجاح (ID: {$service_id})";
        } else {
            $result['message'] = 'فشل في إضافة الخدمة الاختبارية: ' . $db->error();
        }
    } catch (Exception $e) {
        $result['message'] = 'حدث خطأ أثناء اختبار إضافة خدمة: ' . $e->getMessage();
    }
    
    return $result;
}

// دالة اختبار إضافة مشروع
function test_add_project() {
    global $db;
    
    $result = [
        'status' => false,
        'message' => ''
    ];
    
    try {
        // بيانات مشروع اختباري
        $project_data = [
            'title' => 'مشروع اختباري',
            'description' => 'هذا مشروع اختباري للتأكد من عمل النظام بشكل صحيح',
            'client' => 'عميل اختباري',
            'location' => 'موقع اختباري',
            'status' => 'active'
        ];
        
        // إضافة المشروع
        $title = $db->escape($project_data['title']);
        $description = $db->escape($project_data['description']);
        $client = $db->escape($project_data['client']);
        $location = $db->escape($project_data['location']);
        $status = $db->escape($project_data['status']);
        
        $query = "INSERT INTO projects (title, description, client, location, status, created_at, updated_at) 
                  VALUES ('{$title}', '{$description}', '{$client}', '{$location}', '{$status}', NOW(), NOW())";
        
        $db_result = $db->query($query);
        
        if ($db_result) {
            $project_id = $db->insert_id();
            $result['status'] = true;
            $result['message'] = "تم إضافة المشروع الاختباري بنجاح (ID: {$project_id})";
        } else {
            $result['message'] = 'فشل في إضافة المشروع الاختباري: ' . $db->error();
        }
    } catch (Exception $e) {
        $result['message'] = 'حدث خطأ أثناء اختبار إضافة مشروع: ' . $e->getMessage();
    }
    
    return $result;
}

// دالة اختبار إضافة تقييم
function test_add_testimonial() {
    global $db;
    
    $result = [
        'status' => false,
        'message' => ''
    ];
    
    try {
        // بيانات تقييم اختباري
        $testimonial_data = [
            'client_name' => 'عميل اختباري',
            'client_position' => 'موقع اختباري',
            'content' => 'هذا تقييم اختباري للتأكد من عمل النظام بشكل صحيح',
            'rating' => 5,
            'status' => 'approved'
        ];
        
        // إضافة التقييم
        $client_name = $db->escape($testimonial_data['client_name']);
        $client_position = $db->escape($testimonial_data['client_position']);
        $content = $db->escape($testimonial_data['content']);
        $rating = intval($testimonial_data['rating']);
        $status = $db->escape($testimonial_data['status']);
        
        $query = "INSERT INTO testimonials (client_name, client_position, content, rating, status, created_at) 
                  VALUES ('{$client_name}', '{$client_position}', '{$content}', {$rating}, '{$status}', NOW())";
        
        $db_result = $db->query($query);
        
        if ($db_result) {
            $testimonial_id = $db->insert_id();
            $result['status'] = true;
            $result['message'] = "تم إضافة التقييم الاختباري بنجاح (ID: {$testimonial_id})";
        } else {
            $result['message'] = 'فشل في إضافة التقييم الاختباري: ' . $db->error();
        }
    } catch (Exception $e) {
        $result['message'] = 'حدث خطأ أثناء اختبار إضافة تقييم: ' . $e->getMessage();
    }
    
    return $result;
}

// دالة اختبار تسجيل زيارة
function test_record_visitor() {
    global $db;
    
    $result = [
        'status' => false,
        'message' => ''
    ];
    
    try {
        // بيانات زيارة اختبارية
        $visitor_data = [
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test User Agent',
            'page_url' => '/index.php',
            'device_type' => 'desktop'
        ];
        
        // تسجيل الزيارة
        $ip_address = $db->escape($visitor_data['ip_address']);
        $user_agent = $db->escape($visitor_data['user_agent']);
        $page_url = $db->escape($visitor_data['page_url']);
        $device_type = $db->escape($visitor_data['device_type']);
        
        $query = "INSERT INTO visitors (ip_address, user_agent, page_url, device_type, visit_date, visit_time) 
                  VALUES ('{$ip_address}', '{$user_agent}', '{$page_url}', '{$device_type}', CURDATE(), CURTIME())";
        
        $db_result = $db->query($query);
        
        if ($db_result) {
            $visitor_id = $db->insert_id();
            $result['status'] = true;
            $result['message'] = "تم تسجيل الزيارة الاختبارية بنجاح (ID: {$visitor_id})";
        } else {
            $result['message'] = 'فشل في تسجيل الزيارة الاختبارية: ' . $db->error();
        }
    } catch (Exception $e) {
        $result['message'] = 'حدث خطأ أثناء اختبار تسجيل زيارة: ' . $e->getMessage();
    }
    
    return $result;
}

// دالة اختبار إنشاء ملفات SEO
function test_generate_seo_files() {
    $result = [
        'status' => true,
        'message' => 'تم إنشاء ملفات SEO بنجاح',
        'details' => []
    ];
    
    try {
        // إنشاء ملف sitemap.xml
        $sitemap_content = generate_sitemap();
        if (file_put_contents('sitemap.xml', $sitemap_content)) {
            $result['details'][] = 'تم إنشاء ملف sitemap.xml بنجاح';
        } else {
            $result['status'] = false;
            $result['details'][] = 'فشل في إنشاء ملف sitemap.xml';
        }
        
        // إنشاء ملف robots.txt
        $robots_content = generate_robots_txt();
        if (file_put_contents('robots.txt', $robots_content)) {
            $result['details'][] = 'تم إنشاء ملف robots.txt بنجاح';
        } else {
            $result['status'] = false;
            $result['details'][] = 'فشل في إنشاء ملف robots.txt';
        }
    } catch (Exception $e) {
        $result['status'] = false;
        $result['message'] = 'حدث خطأ أثناء إنشاء ملفات SEO: ' . $e->getMessage();
    }
    
    if (!$result['status']) {
        $result['message'] = 'فشل في إنشاء بعض ملفات SEO';
    }
    
    return $result;
}

// دالة اختبار توافق الاستضافة
function test_hosting_compatibility() {
    $result = [
        'status' => true,
        'message' => 'الموقع متوافق مع الاستضافة المجانية',
        'details' => []
    ];
    
    // التحقق من إصدار PHP
    $php_version = phpversion();
    $min_php_version = '7.0.0';
    if (version_compare($php_version, $min_php_version, '<')) {
        $result['status'] = false;
        $result['details'][] = "إصدار PHP الحالي ({$php_version}) أقل من الإصدار المطلوب ({$min_php_version})";
    } else {
        $result['details'][] = "إصدار PHP الحالي ({$php_version}) متوافق";
    }
    
    // التحقق من وجود امتدادات PHP المطلوبة
    $required_extensions = ['mysqli', 'gd', 'json', 'mbstring', 'fileinfo'];
    foreach ($required_extensions as $extension) {
        if (!extension_loaded($extension)) {
            $result['status'] = false;
            $result['details'][] = "امتداد PHP المطلوب ({$extension}) غير متوفر";
        } else {
            $result['details'][] = "امتداد PHP المطلوب ({$extension}) متوفر";
        }
    }
    
    // التحقق من حدود الذاكرة
    $memory_limit = ini_get('memory_limit');
    $min_memory = '64M';
    if ($memory_limit != '-1' && return_bytes($memory_limit) < return_bytes($min_memory)) {
        $result['status'] = false;
        $result['details'][] = "حد الذاكرة الحالي ({$memory_limit}) أقل من الحد المطلوب ({$min_memory})";
    } else {
        $result['details'][] = "حد الذاكرة الحالي ({$memory_limit}) كافٍ";
    }
    
    // التحقق من حدود تحميل الملفات
    $upload_max_filesize = ini_get('upload_max_filesize');
    $min_upload = '8M';
    if (return_bytes($upload_max_filesize) < return_bytes($min_upload)) {
        $result['status'] = false;
        $result['details'][] = "الحد الأقصى لحجم الملفات المرفوعة ({$upload_max_filesize}) أقل من الحد المطلوب ({$min_upload})";
    } else {
        $result['details'][] = "الحد الأقصى لحجم الملفات المرفوعة ({$upload_max_filesize}) كافٍ";
    }
    
    if (!$result['status']) {
        $result['message'] = 'هناك بعض مشاكل التوافق مع الاستضافة المجانية';
    }
    
    return $result;
}

// دالة مساعدة لتحويل حجم الذاكرة إلى بايت
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = floatval($val);
    
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    
    return $val;
}

// دالة تشغيل جميع الاختبارات
function run_all_tests() {
    $tests = [
        'database_connection' => test_database_connection(),
        'database_tables' => test_database_tables(),
        'upload_directories' => test_upload_directories(),
        'login' => test_login(),
        'add_service' => test_add_service(),
        'add_project' => test_add_project(),
        'add_testimonial' => test_add_testimonial(),
        'record_visitor' => test_record_visitor(),
        'generate_seo_files' => test_generate_seo_files(),
        'hosting_compatibility' => test_hosting_compatibility()
    ];
    
    $overall_status = true;
    foreach ($tests as $test) {
        if (!$test['status']) {
            $overall_status = false;
            break;
        }
    }
    
    return [
        'status' => $overall_status,
        'message' => $overall_status ? 'جميع الاختبارات ناجحة' : 'فشلت بعض الاختبارات',
        'tests' => $tests
    ];
}

// دالة عرض نتائج الاختبارات
function display_test_results($results) {
    $html = '<div class="test-results">';
    
    if ($results['status']) {
        $html .= '<div class="alert alert-success">' . $results['message'] . '</div>';
    } else {
        $html .= '<div class="alert alert-danger">' . $results['message'] . '</div>';
    }
    
    $html .= '<table class="table table-bordered">';
    $html .= '<thead><tr><th>الاختبار</th><th>النتيجة</th><th>التفاصيل</th></tr></thead>';
    $html .= '<tbody>';
    
    foreach ($results['tests'] as $name => $test) {
        $status_class = $test['status'] ? 'success' : 'danger';
        $status_text = $test['status'] ? 'نجاح' : 'فشل';
        
        $html .= '<tr>';
        $html .= '<td>' . ucfirst(str_replace('_', ' ', $name)) . '</td>';
        $html .= '<td><span class="badge bg-' . $status_class . '">' . $status_text . '</span></td>';
        $html .= '<td>' . $test['message'];
        
        if (isset($test['details']) && is_array($test['details'])) {
            $html .= '<ul>';
            foreach ($test['details'] as $detail) {
                $html .= '<li>' . $detail . '</li>';
            }
            $html .= '</ul>';
        }
        
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '</div>';
    
    return $html;
}
