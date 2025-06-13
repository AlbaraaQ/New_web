<?php
/**
 * ملف الرأس للوحة التحكم
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars($page_title); ?> - لوحة التحكم</title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo !empty($site_settings['site_favicon']) ? upload_url($site_settings['site_favicon']) : 'assets/img/favicon.ico'; ?>">
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/admin.css?v=<?php echo time(); ?>">
    
    <style>
        :root {
            --primary-color: #1E3A8A;
            --secondary-color: #2c5282;
            --accent-color: #4299e1;
            --light-color: #f8fafc;
            --dark-color: #1a202c;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        
        .content-wrapper {
            background-color: #f8fafc;
        }
        
        .sidebar-dark-primary {
            background-color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
        }
        
        .small-box {
            border-radius: 0.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .small-box .icon {
            font-size: 70px;
            position: absolute;
            right: 15px;
            top: 15px;
            transition: all 0.3s;
            opacity: 0.3;
        }
        
        .small-box:hover .icon {
            transform: scale(1.1);
            opacity: 0.5;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index.php" class="nav-link">الرئيسية</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="<?php echo BASE_URL; ?>" target="_blank" class="nav-link">
                        <i class="fas fa-external-link-alt mr-1"></i> عرض الموقع
                    </a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav mr-auto">
                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-comments"></i>
                        <?php $unread_contacts = $contact->getContactsCount(true); ?>
                        <?php if($unread_contacts > 0): ?>
                            <span class="badge badge-danger navbar-badge"><?php echo $unread_contacts; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <?php foreach($contact->getLatestContacts(3) as $msg): ?>
                            <a href="index.php?page=contacts&id=<?php echo $msg['id']; ?>" class="dropdown-item">
                                <div class="media">
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title">
                                            <?php echo htmlspecialchars($msg['name']); ?>
                                            <?php if(!$msg['is_read']): ?>
                                                <span class="float-left text-danger"><i class="fas fa-star"></i></span>
                                            <?php endif; ?>
                                        </h3>
                                        <p class="text-sm"><?php echo truncate_text($msg['message'], 50); ?></p>
                                        <p class="text-sm text-muted">
                                            <i class="far fa-clock mr-1"></i>
                                            <?php echo format_date($msg['created_at'], true); ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                        <?php endforeach; ?>
                        <a href="index.php?page=contacts" class="dropdown-item dropdown-footer">عرض جميع الرسائل</a>
                    </div>
                </li>
                
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <?php $new_testimonials = $testimonial->getNewTestimonialsCount(); ?>
                        <?php if($new_testimonials > 0): ?>
                            <span class="badge badge-warning navbar-badge"><?php echo $new_testimonials; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header"><?php echo $new_testimonials; ?> تقييم جديد</span>
                        <div class="dropdown-divider"></div>
                        <a href="index.php?page=testimonials" class="dropdown-item">
                            <i class="fas fa-star mr-2"></i> عرض التقييمات الجديدة
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="index.php?page=statistics" class="dropdown-item">
                            <i class="fas fa-chart-line mr-2"></i> عرض الإحصائيات
                        </a>
                    </div>
                </li>
                
                <!-- User Menu -->
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <img src="assets/img/user.png" class="user-image img-circle elevation-2" alt="User Image">
                        <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> الملف الشخصي
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="index.php?logout=1" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> تسجيل الخروج
                        </a>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>