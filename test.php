<?php
/**
 * ملف اختبار الدوال المضافة والمعدلة
 * 
 * هذا الملف يستخدم لاختبار الدوال المضافة والمعدلة في المشروع
 */

// استدعاء ملف الإعدادات
require_once 'config/config.php';

// استدعاء ملف الدوال المساعدة
require_once 'includes/functions.php';

// استدعاء ملف قاعدة البيانات
require_once 'includes/Database.php';

// استدعاء ملف الإحصائيات
require_once 'includes/statistics.php';

// إنشاء كائن قاعدة البيانات
$db = new Database();

// استدعاء النماذج
require_once 'models/Setting.php';
require_once 'models/Service.php';
require_once 'models/Project.php';
require_once 'models/Testimonial.php';
require_once 'models/Contact.php';
require_once 'models/Visitor.php';

// إنشاء كائنات النماذج
$setting = new Setting($db);
$service = new Service($db);
$project = new Project($db);
$testimonial = new Testimonial($db);
$contact = new Contact($db);
$visitor = new Visitor($db);

// بدء جلسة
session_start();

// تعريف دالة لعرض نتائج الاختبار
function display_test_result($test_name, $result, $message = '') {
    echo "<div style='margin: 10px 0; padding: 10px; border-radius: 5px; " . 
         ($result ? "background-color: #d4edda; color: #155724;" : "background-color: #f8d7da; color: #721c24;") . 
         "'>";
    echo "<strong>" . ($result ? "✓ نجاح: " : "✗ فشل: ") . $test_name . "</strong>";
    if (!empty($message)) {
        echo "<p>$message</p>";
    }
    echo "</div>";
}

// تعريف دالة لعرض معلومات
function display_info($title, $content) {
    echo "<div style='margin: 10px 0; padding: 10px; border-radius: 5px; background-color: #cce5ff; color: #004085;'>";
    echo "<strong>$title</strong>";
    echo "<pre>$content</pre>";
    echo "</div>";
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار الدوال المضافة والمعدلة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            font-family: 'Cairo', 'Tajawal', sans-serif;
            padding: 20px;
        }
        h1, h2, h3 {
            margin-bottom: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">اختبار الدوال المضافة والمعدلة</h1>
        
        <div class="test-section">
            <h2>1. اختبار دوال الإحصائيات</h2>
            
            <?php
            // اختبار دالة get_client_ip
            $ip = get_client_ip();
            display_test_result("دالة get_client_ip", !empty($ip), "عنوان IP: $ip");
            
            // اختبار دالة get_browser_info
            $browser_info = get_browser_info($_SERVER['HTTP_USER_AGENT']);
            display_test_result("دالة get_browser_info", is_array($browser_info) && !empty($browser_info), 
                "المتصفح: " . $browser_info['browser'] . 
                ", نظام التشغيل: " . $browser_info['os'] . 
                ", نوع الجهاز: " . $browser_info['device_type']);
            
            // اختبار دالة record_visitor في ملف statistics.php
            try {
                $result = record_visitor($db);
                display_test_result("دالة record_visitor", $result !== false, "تم تسجيل الزيارة بنجاح");
            } catch (Exception $e) {
                display_test_result("دالة record_visitor", false, "خطأ: " . $e->getMessage());
            }
            
            // اختبار دالة get_total_visitors
            try {
                $total_visitors = get_total_visitors($db, 'all');
                display_test_result("دالة get_total_visitors", is_numeric($total_visitors), "إجمالي الزوار: $total_visitors");
            } catch (Exception $e) {
                display_test_result("دالة get_total_visitors", false, "خطأ: " . $e->getMessage());
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>2. اختبار دوال تسجيل الزيارات</h2>
            
            <?php
            // اختبار دالة recordVisit في فئة Visitor
            try {
                $data = array(
                    'ip_address' => get_client_ip(),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'page_visited' => $_SERVER['REQUEST_URI'],
                    'referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                    'country' => '',
                    'city' => '',
                    'device_type' => $browser_info['device_type'],
                    'browser' => $browser_info['browser'],
                    'os' => $browser_info['os'],
                    'session_id' => session_id(),
                    'is_unique' => 1
                );
                
                $result = $visitor->recordVisit($data);
                display_test_result("دالة recordVisit", $result, "تم تسجيل الزيارة بنجاح");
            } catch (Exception $e) {
                display_test_result("دالة recordVisit", false, "خطأ: " . $e->getMessage());
            }
            
            // اختبار دالة hasVisitedBefore في فئة Visitor
            try {
                $result = $visitor->hasVisitedBefore(session_id(), $_SERVER['REQUEST_URI']);
                display_test_result("دالة hasVisitedBefore", is_bool($result), "نتيجة الاختبار: " . ($result ? "نعم" : "لا"));
            } catch (Exception $e) {
                display_test_result("دالة hasVisitedBefore", false, "خطأ: " . $e->getMessage());
            }
            
            // اختبار دالة getTodayStats في فئة Visitor
            try {
                $stats = $visitor->getTodayStats();
                display_test_result("دالة getTodayStats", is_array($stats), "تم الحصول على إحصائيات اليوم بنجاح");
                display_info("إحصائيات اليوم", print_r($stats, true));
            } catch (Exception $e) {
                display_test_result("دالة getTodayStats", false, "خطأ: " . $e->getMessage());
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>3. اختبار الدوال المضافة في ملف functions.php</h2>
            
            <?php
            // اختبار دالة pagination
            $pagination = pagination(100, 10, 1, "?page=%d");
            display_test_result("دالة pagination", !empty($pagination), "تم إنشاء روابط الصفحات بنجاح");
            echo "<div>$pagination</div>";
            
            // اختبار دالة js_escape
            $text = "نص عربي للاختبار";
            $escaped = js_escape($text);
            display_test_result("دالة js_escape", !empty($escaped), "النص الأصلي: $text, النص المهرب: $escaped");
            
            // اختبار دالة format_phone
            $phone = "0501234567";
            $formatted = format_phone($phone);
            display_test_result("دالة format_phone", !empty($formatted), "رقم الهاتف الأصلي: $phone, رقم الهاتف المنسق: $formatted");
            
            // اختبار دالة format_price
            $price = 1234.56;
            $formatted = format_price($price);
            display_test_result("دالة format_price", !empty($formatted), "السعر الأصلي: $price, السعر المنسق: $formatted");
            
            // اختبار دالة number_to_arabic_words
            $number = 1234;
            $words = number_to_arabic_words($number);
            display_test_result("دالة number_to_arabic_words", !empty($words), "الرقم: $number, الكلمات: $words");
            ?>
        </div>
        
        <div class="test-section">
            <h2>4. اختبار طريقة العرض RTL</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">اختبار النص من اليمين إلى اليسار</h5>
                            <p class="card-text">هذا نص عربي للتأكد من أن اتجاه النص من اليمين إلى اليسار يعمل بشكل صحيح.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">اختبار الأيقونات</h5>
                            <p class="card-text">
                                <i class="fas fa-angle-right ms-2"></i> أيقونة السهم لليمين<br>
                                <i class="fas fa-angle-left ms-2"></i> أيقونة السهم لليسار
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h5>اختبار النموذج</h5>
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="name" placeholder="أدخل اسمك">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" placeholder="أدخل بريدك الإلكتروني">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">الرسالة</label>
                        <textarea class="form-control" id="message" rows="3" placeholder="أدخل رسالتك"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">إرسال</button>
                </form>
            </div>
            
            <div class="mt-4">
                <h5>اختبار الجدول</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>أحمد محمد</td>
                            <td>ahmed@example.com</td>
                            <td>0501234567</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>محمد علي</td>
                            <td>mohamed@example.com</td>
                            <td>0507654321</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>علي أحمد</td>
                            <td>ali@example.com</td>
                            <td>0501122334</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

