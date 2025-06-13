<?php
/**
 * ملف القالب الرئيسي للموقع
 * 
 * هذا الملف يحتوي على الهيكل الأساسي للموقع ويتم استدعاؤه في جميع الصفحات
 */

// استدعاء ملف الإعدادات
require_once 'config/config.php';

// استدعاء ملف الدوال المساعدة
require_once 'includes/functions.php';

// استدعاء ملف قاعدة البيانات
require_once 'includes/Database.php';

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

// الحصول على إعدادات الموقع
$site_settings = $setting->getSettings();

// تسجيل زيارة جديدة
$visitor->recordVisit();

// تحديد الصفحة الحالية
$current_page = isset($_GET['page']) ? $_GET['page'] : 'home';

// تحديد عنوان الصفحة
$page_title = '';
switch ($current_page) {
    case 'home':
        $page_title = 'الرئيسية';
        break;
    case 'services':
        $page_title = 'خدماتنا';
        break;
    case 'service':
        $page_title = 'تفاصيل الخدمة';
        break;
    case 'projects':
        $page_title = 'مشاريعنا';
        break;
    case 'project':
        $page_title = 'تفاصيل المشروع';
        break;
    case 'about':
        $page_title = 'من نحن';
        break;
    case 'testimonials':
        $page_title = 'آراء العملاء';
        break;
    case 'contact':
        $page_title = 'اتصل بنا';
        break;
    default:
        $page_title = 'الرئيسية';
        break;
}

// الصفحة الكاملة مع العنوان
$full_page_title = $page_title . ' | ' . $site_settings['site_name'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $full_page_title; ?></title>
    
    <!-- وصف الموقع للـ SEO -->
    <meta name="description" content="<?php echo $site_settings['site_description']; ?>">
    <meta name="keywords" content="<?php echo $site_settings['meta_keywords']; ?>">
    
    <!-- الأيقونة المفضلة -->
    <?php if(!empty($site_settings['site_favicon'])): ?>
        <link rel="shortcut icon" href="<?php echo upload_url($site_settings['site_favicon']); ?>" type="image/x-icon">
    <?php endif; ?>
    
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    <!-- Lightbox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Custom CSS من الإعدادات -->
    <?php if(!empty($site_settings['site_custom_css'])): ?>
        <style>
            <?php echo $site_settings['site_custom_css']; ?>
        </style>
    <?php endif; ?>
    
    <!-- كود تحليلات جوجل -->
    <?php if(!empty($site_settings['site_analytics_code'])): ?>
        <?php echo $site_settings['site_analytics_code']; ?>
    <?php endif; ?>
</head>
<body>
    <!-- شريط التواصل العلوي -->
    <div class="top-bar bg-dark text-white py-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="contact-info">
                        <?php if(!empty($site_settings['site_phone'])): ?>
                            <span class="ms-3"><i class="fas fa-phone-alt ms-1"></i> <?php echo $site_settings['site_phone']; ?></span>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_email'])): ?>
                            <span><i class="fas fa-envelope ms-1"></i> <?php echo $site_settings['site_email']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="social-links text-md-start">
                        <?php if(!empty($site_settings['site_facebook'])): ?>
                            <a href="<?php echo $site_settings['site_facebook']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_twitter'])): ?>
                            <a href="<?php echo $site_settings['site_twitter']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_instagram'])): ?>
                            <a href="<?php echo $site_settings['site_instagram']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_youtube'])): ?>
                            <a href="<?php echo $site_settings['site_youtube']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_whatsapp'])): ?>
                            <a href="https://wa.me/<?php echo $site_settings['site_whatsapp']; ?>" class="text-white" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- القائمة الرئيسية -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <?php if(!empty($site_settings['site_logo'])): ?>
                    <img src="<?php echo upload_url($site_settings['site_logo']); ?>" alt="<?php echo $site_settings['site_name']; ?>" class="img-fluid" style="max-height: 60px;">
                <?php else: ?>
                    <?php echo $site_settings['site_name']; ?>
                <?php endif; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>" href="index.php">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'services' || $current_page == 'service') ? 'active' : ''; ?>" href="index.php?page=services">خدماتنا</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'projects' || $current_page == 'project') ? 'active' : ''; ?>" href="index.php?page=projects">مشاريعنا</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>" href="index.php?page=about">من نحن</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'testimonials') ? 'active' : ''; ?>" href="index.php?page=testimonials">آراء العملاء</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'contact') ? 'active' : ''; ?>" href="index.php?page=contact">اتصل بنا</a>
                    </li>
                </ul>
                
                <div class="d-flex me-3">
                    <a href="index.php?page=contact" class="btn btn-primary">طلب عرض سعر</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- محتوى الصفحة -->
    <main>
        <?php
        // تحميل محتوى الصفحة المطلوبة
        switch ($current_page) {
            case 'home':
                include 'pages/home.php';
                break;
            case 'services':
                include 'pages/services.php';
                break;
            case 'service':
                include 'pages/service_details.php';
                break;
            case 'projects':
                include 'pages/projects.php';
                break;
            case 'project':
                include 'pages/project_details.php';
                break;
            case 'about':
                include 'pages/about.php';
                break;
            case 'testimonials':
                include 'pages/testimonials.php';
                break;
            case 'contact':
                include 'pages/contact.php';
                break;
            default:
                include 'pages/home.php';
                break;
        }
        ?>
    </main>
    
    <!-- تذييل الصفحة -->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="mb-4">عن الشركة</h4>
                    <?php if(!empty($site_settings['site_logo'])): ?>
                        <img src="<?php echo upload_url($site_settings['site_logo']); ?>" alt="<?php echo $site_settings['site_name']; ?>" class="img-fluid mb-3" style="max-height: 60px; filter: brightness(0) invert(1);">
                    <?php else: ?>
                        <h5 class="text-white mb-3"><?php echo $site_settings['site_name']; ?></h5>
                    <?php endif; ?>
                    
                    <p><?php echo $site_settings['site_description']; ?></p>
                    
                    <div class="social-links mt-3">
                        <?php if(!empty($site_settings['site_facebook'])): ?>
                            <a href="<?php echo $site_settings['site_facebook']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_twitter'])): ?>
                            <a href="<?php echo $site_settings['site_twitter']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_instagram'])): ?>
                            <a href="<?php echo $site_settings['site_instagram']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_youtube'])): ?>
                            <a href="<?php echo $site_settings['site_youtube']; ?>" class="text-white ms-2" target="_blank"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_whatsapp'])): ?>
                            <a href="https://wa.me/<?php echo $site_settings['site_whatsapp']; ?>" class="text-white" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h4 class="mb-4">روابط سريعة</h4>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="index.php" class="text-white text-decoration-none"><i class="fas fa-angle-right ms-2"></i>الرئيسية</a></li>
                        <li class="mb-2"><a href="index.php?page=services" class="text-white text-decoration-none"><i class="fas fa-angle-right ms-2"></i>خدماتنا</a></li>
                        <li class="mb-2"><a href="index.php?page=projects" class="text-white text-decoration-none"><i class="fas fa-angle-right ms-2"></i>مشاريعنا</a></li>
                        <li class="mb-2"><a href="index.php?page=about" class="text-white text-decoration-none"><i class="fas fa-angle-right ms-2"></i>من نحن</a></li>
                        <li class="mb-2"><a href="index.php?page=testimonials" class="text-white text-decoration-none"><i class="fas fa-angle-right ms-2"></i>آراء العملاء</a></li>
                        <li class="mb-2"><a href="index.php?page=contact" class="text-white text-decoration-none"><i class="fas fa-angle-right ms-2"></i>اتصل بنا</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h4 class="mb-4">معلومات التواصل</h4>
                    <ul class="list-unstyled contact-info">
                        <?php if(!empty($site_settings['site_address'])): ?>
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt ms-2"></i>
                                <?php echo nl2br($site_settings['site_address']); ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_phone'])): ?>
                            <li class="mb-3">
                                <i class="fas fa-phone-alt ms-2"></i>
                                <a href="tel:<?php echo $site_settings['site_phone']; ?>" class="text-white text-decoration-none"><?php echo $site_settings['site_phone']; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_email'])): ?>
                            <li class="mb-3">
                                <i class="fas fa-envelope ms-2"></i>
                                <a href="mailto:<?php echo $site_settings['site_email']; ?>" class="text-white text-decoration-none"><?php echo $site_settings['site_email']; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if(!empty($site_settings['site_whatsapp'])): ?>
                            <li class="mb-3">
                                <i class="fab fa-whatsapp ms-2"></i>
                                <a href="https://wa.me/<?php echo $site_settings['site_whatsapp']; ?>" class="text-white text-decoration-none" target="_blank">تواصل عبر واتساب</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <hr class="mt-4 mb-4">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">
                        <?php if(!empty($site_settings['site_footer_text'])): ?>
                            <?php echo $site_settings['site_footer_text']; ?>
                        <?php else: ?>
                            &copy; <?php echo date('Y'); ?> <?php echo $site_settings['site_name']; ?>. جميع الحقوق محفوظة.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-start">
                    <p class="mb-0">تصميم وتطوير بواسطة <a href="#" class="text-white">أبو جسار</a></p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- زر التمرير لأعلى -->
    <a href="#" class="back-to-top"><i class="fas fa-arrow-up"></i></a>
    
    <!-- زر واتساب -->
    <?php if(!empty($site_settings['site_whatsapp'])): ?>
        <a href="https://wa.me/<?php echo $site_settings['site_whatsapp']; ?>" class="whatsapp-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
    <?php endif; ?>
    
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Owl Carousel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <!-- Lightbox -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            // تهيئة Owl Carousel
            $('.testimonials-carousel').owlCarousel({
                rtl: true,
                loop: true,
                margin: 30,
                nav: false,
                dots: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                    992: {
                        items: 3
                    }
                }
            });
            
            $('.partners-carousel').owlCarousel({
                rtl: true,
                loop: true,
                margin: 30,
                nav: false,
                dots: false,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 2
                    },
                    576: {
                        items: 3
                    },
                    768: {
                        items: 4
                    },
                    992: {
                        items: 5
                    }
                }
            });
            
            // زر التمرير لأعلى
            $(window).scroll(function() {
                if ($(this).scrollTop() > 200) {
                    $('.back-to-top').addClass('active');
                } else {
                    $('.back-to-top').removeClass('active');
                }
            });
            
            $('.back-to-top').click(function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
            
            // تهيئة Lightbox
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': "صورة %1 من %2"
            });
            
            // تنشيط التلميحات
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
    
    <!-- Custom JS من الإعدادات -->
    <?php if(!empty($site_settings['site_custom_js'])): ?>
        <script>
            <?php echo $site_settings['site_custom_js']; ?>
        </script>
    <?php endif; ?>
</body>
</html>

