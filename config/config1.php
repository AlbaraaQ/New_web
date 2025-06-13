<?php
/**
 * ملف الإعدادات الرئيسي
 * 
 * يحتوي على إعدادات الاتصال بقاعدة البيانات وإعدادات عامة للموقع
 */

// معلومات الاتصال بقاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'abujassar_db');

// مسار الموقع
define('BASE_URL', 'http://localhost/abujassar');
define('ADMIN_URL', BASE_URL . '/admin');

// مسارات المجلدات
define('ROOT_PATH', dirname(__DIR__));
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('UPLOADS_PATH', ROOT_PATH . '/assets/uploads');
define('INCLUDES_PATH', ROOT_PATH . '/includes');

// إعدادات التحميل
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 ميجابايت
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// إعدادات الأمان
define('SALT', 'abujassar_salt_2025');
define('SESSION_NAME', 'abujassar_session');

// إعدادات الصفحات
define('ITEMS_PER_PAGE', 10);

// إعدادات التحكم بالأخطاء
define('DISPLAY_ERRORS', true);

if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// دالة للتعامل مع الأخطاء
function handleError($errno, $errstr, $errfile, $errline) {
    $error_message = "خطأ [$errno]: $errstr في الملف $errfile على السطر $errline";
    
    // تسجيل الخطأ في ملف السجل
    error_log($error_message, 3, ROOT_PATH . '/logs/error.log');
    
    if (DISPLAY_ERRORS) {
        echo "<div style='color:red; border:1px solid red; padding:10px; margin:10px;'>";
        echo "<h3>حدث خطأ</h3>";
        echo "<p>$error_message</p>";
        echo "</div>";
    }
    
    return true;
}

// تعيين دالة التعامل مع الأخطاء
set_error_handler('handleError');
