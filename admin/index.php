<?php
/**
 * ملف التحكم الرئيسي للوحة الإدارة
 * 
 * يتعامل مع تسجيل الدخول وعرض لوحة التحكم الرئيسية
 */

// تضمين ملفات الإعدادات والدوال المساعدة
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Database.php';

// بدء الجلسة
session_start();

// تضمين النماذج
require_once '../models/User.php';
require_once '../models/Service.php';
require_once '../models/Project.php';
require_once '../models/Testimonial.php';
require_once '../models/Visitor.php';
require_once '../models/Contact.php';
require_once '../models/Setting.php';

// إنشاء كائنات النماذج
$user = new User();
$service = new Service();
$project = new Project();
$testimonial = new Testimonial();
$visitor = new Visitor();
$contact = new Contact();
$setting = new Setting();

// التحقق من تسجيل الدخول
if(!$user->isLoggedIn() && !isset($_GET['login'])) {
    redirect('login.php');
}

// تسجيل الخروج
if(isset($_GET['logout'])) {
    $user->logout();
    redirect('login.php');
}

// الحصول على إعدادات الموقع
$site_settings = $setting->getSettings();

// تحديد الصفحة الحالية
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// عنوان الصفحة
$page_title = 'لوحة التحكم';

// تضمين الرأس
include 'includes/header.php';
?>

<div class="wrapper">
    <!-- القائمة الجانبية -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- المحتوى الرئيسي -->
    <div class="content-wrapper">
        <!-- رأس المحتوى -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?php echo $page_title; ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="index.php">الرئيسية</a></li>
                            <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- المحتوى -->
        <div class="content">
            <div class="container-fluid">
                <?php
                // عرض رسائل التنبيه
                if(has_flash_message()) {
                    echo display_flash_message();
                }

                // تضمين الصفحة المطلوبة
                switch($page) {
                    case 'dashboard':
                        include 'pages/dashboard.php';
                        break;
                    case 'users':
                        include 'pages/users.php';
                        break;
                    case 'services':
                        include 'pages/services.php';
                        break;
                    case 'projects':
                        include 'pages/projects.php';
                        break;
                    case 'testimonials':
                        include 'pages/testimonials.php';
                        break;
                    case 'contacts':
                        include 'pages/contacts.php';
                        break;
                    case 'statistics':
                        include 'pages/statistics.php';
                        break;
                    case 'settings':
                        include 'pages/settings.php';
                        break;
                    default:
                        include 'pages/dashboard.php';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- التذييل -->
    <?php include 'includes/footer.php'; ?>
</div>

<?php
// تضمين ملفات JavaScript
include 'includes/scripts.php';
?>
