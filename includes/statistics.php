<?php
/**
 * ملف نظام عداد الزوار والإحصائيات
 * 
 * يحتوي على الدوال الخاصة بتتبع الزوار وعرض الإحصائيات المتقدمة
 */

/**
 * دالة تسجيل زيارة جديدة
 * 
 * @param Database $db كائن قاعدة البيانات
 * @return boolean نجاح أو فشل العملية
 */
function record_visitor($db) {
    // الحصول على معلومات الزائر
    $ip_address = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $page_url = $_SERVER['REQUEST_URI'];
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    
    // محاولة الحصول على معلومات إضافية عن الزائر
    $visitor_info = get_visitor_info($ip_address);
    $country = isset($visitor_info['country']) ? $visitor_info['country'] : '';
    $city = isset($visitor_info['city']) ? $visitor_info['city'] : '';
    
    // تحديد نوع الجهاز
    $browser_info = get_browser_info($user_agent);
    $device_type = $browser_info['device_type'];
    $browser = $browser_info['browser'];
    $os = $browser_info['os'];
    
    // إضافة الزيارة إلى قاعدة البيانات
    $db->query("INSERT INTO visitors (ip_address, user_agent, page_visited, referrer, country, city, device_type, browser, os, visit_date, visit_time, session_id, is_unique) 
                VALUES (:ip_address, :user_agent, :page_url, :referer, :country, :city, :device_type, :browser, :os, CURDATE(), CURTIME(), :session_id, :is_unique)");
    
    // التحقق من وجود جلسة
    if(!isset($_SESSION['visitor_id'])) {
        $_SESSION['visitor_id'] = session_id();
    }
    $session_id = $_SESSION['visitor_id'];
    
    // التحقق من وجود زيارة سابقة لهذه الصفحة في نفس الجلسة
    $db->query("SELECT id FROM visitors WHERE session_id = :session_id AND page_visited = :page_url AND visit_date = CURDATE()");
    $db->bind(':session_id', $session_id);
    $db->bind(':page_url', $page_url);
    $db->execute();
    $is_unique = ($db->rowCount() == 0) ? 1 : 0;
    
    // ربط القيم بالاستعلام
    $db->query("INSERT INTO visitors (ip_address, user_agent, page_visited, referrer, country, city, device_type, browser, os, visit_date, visit_time, session_id, is_unique) 
                VALUES (:ip_address, :user_agent, :page_url, :referer, :country, :city, :device_type, :browser, :os, CURDATE(), CURTIME(), :session_id, :is_unique)");
    
    $db->bind(':ip_address', $ip_address);
    $db->bind(':user_agent', $user_agent);
    $db->bind(':page_url', $page_url);
    $db->bind(':referer', $referer);
    $db->bind(':country', $country);
    $db->bind(':city', $city);
    $db->bind(':device_type', $device_type);
    $db->bind(':browser', $browser);
    $db->bind(':os', $os);
    $db->bind(':session_id', $session_id);
    $db->bind(':is_unique', $is_unique);
    
    // تنفيذ الاستعلام
    $result = $db->execute();
    
    // تحديث الإحصائيات اليومية
    if($result) {
        update_daily_stats($db, $ip_address, $page_url, $referer, $country, $device_type, $browser, $os, $is_unique);
    }
    
    return $result;
}

/**
 * تحديث الإحصائيات اليومية
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param string $ip_address عنوان IP
 * @param string $page_url عنوان الصفحة
 * @param string $referer المصدر
 * @param string $country البلد
 * @param string $device_type نوع الجهاز
 * @param string $browser المتصفح
 * @param string $os نظام التشغيل
 * @param int $is_unique زيارة فريدة
 * @return boolean نجاح أو فشل العملية
 */
function update_daily_stats($db, $ip_address, $page_url, $referer, $country, $device_type, $browser, $os, $is_unique) {
    // التحقق من وجود إحصائيات لهذا اليوم
    $db->query("SELECT * FROM visitor_stats WHERE date = CURDATE()");
    $stats = $db->single();
    
    if($stats) {
        // تحديث الإحصائيات الموجودة
        $page_views = json_decode($stats['page_views'], true);
        $referrers = json_decode($stats['referrers'], true);
        $browsers = json_decode($stats['browsers'], true);
        $devices = json_decode($stats['devices'], true);
        $os_stats = json_decode($stats['os'], true);
        $countries = json_decode($stats['countries'], true);
        
        // تحديث عدد الزيارات
        $total_visits = $stats['total_visits'] + 1;
        $unique_visits = $stats['unique_visits'];
        
        if($is_unique) {
            $unique_visits++;
        }
        
        // تحديث الصفحات المزارة
        if(isset($page_views[$page_url])) {
            $page_views[$page_url]++;
        } else {
            $page_views[$page_url] = 1;
        }
        
        // تحديث المصادر
        if(!empty($referer)) {
            if(isset($referrers[$referer])) {
                $referrers[$referer]++;
            } else {
                $referrers[$referer] = 1;
            }
        }
        
        // تحديث المتصفحات
        if(isset($browsers[$browser])) {
            $browsers[$browser]++;
        } else {
            $browsers[$browser] = 1;
        }
        
        // تحديث الأجهزة
        if(isset($devices[$device_type])) {
            $devices[$device_type]++;
        } else {
            $devices[$device_type] = 1;
        }
        
        // تحديث أنظمة التشغيل
        if(isset($os_stats[$os])) {
            $os_stats[$os]++;
        } else {
            $os_stats[$os] = 1;
        }
        
        // تحديث البلدان
        if(!empty($country)) {
            if(isset($countries[$country])) {
                $countries[$country]++;
            } else {
                $countries[$country] = 1;
            }
        }
        
        // تحديث الإحصائيات في قاعدة البيانات
        $db->query("UPDATE visitor_stats SET 
                    total_visits = :total_visits, 
                    unique_visits = :unique_visits, 
                    page_views = :page_views, 
                    referrers = :referrers, 
                    browsers = :browsers, 
                    devices = :devices, 
                    os = :os, 
                    countries = :countries 
                    WHERE date = CURDATE()");
        
        $db->bind(':total_visits', $total_visits);
        $db->bind(':unique_visits', $unique_visits);
        $db->bind(':page_views', json_encode($page_views));
        $db->bind(':referrers', json_encode($referrers));
        $db->bind(':browsers', json_encode($browsers));
        $db->bind(':devices', json_encode($devices));
        $db->bind(':os', json_encode($os_stats));
        $db->bind(':countries', json_encode($countries));
        
        return $db->execute();
    } else {
        // إنشاء إحصائيات جديدة لهذا اليوم
        $page_views = array($page_url => 1);
        $referrers = array();
        $browsers = array($browser => 1);
        $devices = array($device_type => 1);
        $os_stats = array($os => 1);
        $countries = array();
        
        if(!empty($referer)) {
            $referrers[$referer] = 1;
        }
        
        if(!empty($country)) {
            $countries[$country] = 1;
        }
        
        $db->query("INSERT INTO visitor_stats (
                    date, 
                    total_visits, 
                    unique_visits, 
                    page_views, 
                    referrers, 
                    browsers, 
                    devices, 
                    os, 
                    countries
                    ) VALUES (
                    CURDATE(), 
                    1, 
                    :unique_visits, 
                    :page_views, 
                    :referrers, 
                    :browsers, 
                    :devices, 
                    :os, 
                    :countries
                    )");
        
        $db->bind(':unique_visits', $is_unique ? 1 : 0);
        $db->bind(':page_views', json_encode($page_views));
        $db->bind(':referrers', json_encode($referrers));
        $db->bind(':browsers', json_encode($browsers));
        $db->bind(':devices', json_encode($devices));
        $db->bind(':os', json_encode($os_stats));
        $db->bind(':countries', json_encode($countries));
        
        return $db->execute();
    }
}

/**
 * الحصول على معلومات الزائر من عنوان IP
 * 
 * @param string $ip عنوان IP
 * @return array معلومات الزائر
 */
function get_visitor_info($ip) {
    // يمكن استخدام خدمة API مثل ipinfo.io أو geoip
    // هذه نسخة مبسطة للتوضيح فقط
    $info = [];
    
    // في بيئة الإنتاج، يمكن استخدام API حقيقي
    // $url = "https://ipinfo.io/{$ip}/json";
    // $response = file_get_contents($url);
    // $info = json_decode($response, true);
    
    // للاختبار فقط
    $info['country'] = 'غير معروف';
    $info['city'] = 'غير معروف';
    
    return $info;
}

/**
 * الحصول على إجمالي عدد الزوار
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param string $period الفترة الزمنية
 * @return int إجمالي عدد الزوار
 */
function get_total_visitors($db, $period = 'all') {
    $query = "SELECT COUNT(DISTINCT ip_address) as total FROM visitors";
    
    // إضافة شرط الفترة الزمنية
    switch ($period) {
        case 'today':
            $query .= " WHERE DATE(visit_date) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " WHERE DATE(visit_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            break;
    }
    
    $db->query($query);
    $result = $db->single();
    
    return $result ? $result['total'] : 0;
}

/**
 * الحصول على إجمالي عدد الزيارات
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param string $period الفترة الزمنية
 * @return int إجمالي عدد الزيارات
 */
function get_total_visits($db, $period = 'all') {
    $query = "SELECT COUNT(*) as total FROM visitors";
    
    // إضافة شرط الفترة الزمنية
    switch ($period) {
        case 'today':
            $query .= " WHERE DATE(visit_date) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " WHERE DATE(visit_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            break;
    }
    
    $db->query($query);
    $result = $db->single();
    
    return $result ? $result['total'] : 0;
}

/**
 * الحصول على الصفحات الأكثر زيارة
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param int $limit عدد النتائج
 * @param string $period الفترة الزمنية
 * @return array الصفحات الأكثر زيارة
 */
function get_most_visited_pages($db, $limit = 10, $period = 'all') {
    $query = "SELECT page_visited, COUNT(*) as visits FROM visitors";
    
    // إضافة شرط الفترة الزمنية
    switch ($period) {
        case 'today':
            $query .= " WHERE DATE(visit_date) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " WHERE DATE(visit_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            break;
    }
    
    $query .= " GROUP BY page_visited ORDER BY visits DESC LIMIT :limit";
    
    $db->query($query);
    $db->bind(':limit', $limit);
    
    return $db->resultSet();
}

/**
 * الحصول على توزيع الزوار حسب الدول
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param int $limit عدد النتائج
 * @param string $period الفترة الزمنية
 * @return array توزيع الزوار حسب الدول
 */
function get_visitors_by_country($db, $limit = 10, $period = 'all') {
    $query = "SELECT country, COUNT(DISTINCT ip_address) as visitors FROM visitors WHERE country != ''";
    
    // إضافة شرط الفترة الزمنية
    switch ($period) {
        case 'today':
            $query .= " AND DATE(visit_date) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " AND DATE(visit_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " AND DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " AND DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $query .= " AND DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            break;
    }
    
    $query .= " GROUP BY country ORDER BY visitors DESC LIMIT :limit";
    
    $db->query($query);
    $db->bind(':limit', $limit);
    
    return $db->resultSet();
}

/**
 * الحصول على توزيع الزوار حسب نوع الجهاز
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param string $period الفترة الزمنية
 * @return array توزيع الزوار حسب نوع الجهاز
 */
function get_visitors_by_device($db, $period = 'all') {
    $query = "SELECT device_type, COUNT(DISTINCT ip_address) as visitors FROM visitors";
    
    // إضافة شرط الفترة الزمنية
    switch ($period) {
        case 'today':
            $query .= " WHERE DATE(visit_date) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " WHERE DATE(visit_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $query .= " WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            break;
    }
    
    $query .= " GROUP BY device_type ORDER BY visitors DESC";
    
    $db->query($query);
    
    return $db->resultSet();
}

/**
 * الحصول على إحصائيات الزوار حسب الوقت
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param string $period الفترة الزمنية
 * @return array إحصائيات الزوار حسب الوقت
 */
function get_visitors_by_time($db, $period = 'all') {
    switch ($period) {
        case 'today':
            $query = "SELECT HOUR(visit_time) as hour, COUNT(DISTINCT ip_address) as visitors FROM visitors WHERE DATE(visit_date) = CURDATE() GROUP BY HOUR(visit_time) ORDER BY hour";
            break;
        case 'week':
            $query = "SELECT DATE(visit_date) as date, COUNT(DISTINCT ip_address) as visitors FROM visitors WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(visit_date) ORDER BY date";
            break;
        case 'month':
            $query = "SELECT DATE(visit_date) as date, COUNT(DISTINCT ip_address) as visitors FROM visitors WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(visit_date) ORDER BY date";
            break;
        case 'year':
            $query = "SELECT MONTH(visit_date) as month, YEAR(visit_date) as year, COUNT(DISTINCT ip_address) as visitors FROM visitors WHERE DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY) GROUP BY MONTH(visit_date), YEAR(visit_date) ORDER BY year, month";
            break;
        default:
            $query = "SELECT MONTH(visit_date) as month, YEAR(visit_date) as year, COUNT(DISTINCT ip_address) as visitors FROM visitors GROUP BY MONTH(visit_date), YEAR(visit_date) ORDER BY year, month";
    }
    
    $db->query($query);
    
    return $db->resultSet();
}

/**
 * الحصول على مصادر الزيارات
 * 
 * @param Database $db كائن قاعدة البيانات
 * @param int $limit عدد النتائج
 * @param string $period الفترة الزمنية
 * @return array مصادر الزيارات
 */
function get_visit_sources($db, $limit = 10, $period = 'all') {
    $query = "SELECT referrer, COUNT(*) as visits FROM visitors WHERE referrer != ''";
    
    // إضافة شرط الفترة الزمنية
    switch ($period) {
        case 'today':
            $query .= " AND DATE(visit_date) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " AND DATE(visit_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " AND DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " AND DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            break;
        case 'year':
            $query .= " AND DATE(visit_date) >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            break;
    }
    
    $query .= " GROUP BY referrer ORDER BY visits DESC LIMIT :limit";
    
    $db->query($query);
    $db->bind(':limit', $limit);
    
    return $db->resultSet();
}

/**
 * تحويل البيانات إلى تنسيق مناسب للرسوم البيانية
 * 
 * @param array $data البيانات
 * @param string $label_key مفتاح التسمية
 * @param string $value_key مفتاح القيمة
 * @return array البيانات المنسقة
 */
function prepare_chart_data($data, $label_key, $value_key) {
    $labels = [];
    $values = [];
    
    foreach ($data as $item) {
        $labels[] = $item[$label_key];
        $values[] = $item[$value_key];
    }
    
    return [
        'labels' => $labels,
        'values' => $values
    ];
}

/**
 * تحديث عداد الزوار في الصفحة الرئيسية
 * 
 * @param Database $db كائن قاعدة البيانات
 * @return int عدد الزوار الحالي
 */
function update_visitor_counter($db) {
    // الحصول على عدد الزوار الحالي
    $db->query("SELECT value FROM settings WHERE name = 'visitor_counter'");
    $result = $db->single();
    
    if($result) {
        $current_count = intval($result['value']);
        
        // زيادة العداد
        $new_count = $current_count + 1;
        
        // تحديث العداد في قاعدة البيانات
        $db->query("UPDATE settings SET value = :new_count WHERE name = 'visitor_counter'");
        $db->bind(':new_count', $new_count);
        $db->execute();
        
        return $new_count;
    }
    
    return 0;
}

