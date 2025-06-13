<?php
/**
 * ملف القائمة الجانبية للوحة التحكم
 */

// تحديد الصفحات الفرعية النشطة
$active_subpages = [
    'services' => ['services', 'add_service', 'edit_service'],
    'projects' => ['projects', 'add_project', 'edit_project'],
    'users' => ['users', 'add_user', 'edit_user']
];
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link text-center">
        <?php if(!empty($site_settings['site_logo'])): ?>
            <img src="<?php echo upload_url($site_settings['site_logo']); ?>" alt="<?php echo htmlspecialchars($site_settings['site_name']); ?>" class="brand-image img-circle elevation-3">
        <?php endif; ?>
        <span class="brand-text font-weight-light"><?php echo htmlspecialchars($site_settings['site_name']); ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="assets/img/user.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['full_name']); ?></a>
                
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- لوحة التحكم -->
                <li class="nav-item">
                    <a href="index.php?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>لوحة التحكم</p>
                    </a>
                </li>
                
                <!-- الخدمات -->
                <li class="nav-item <?php echo in_array($page, $active_subpages['services']) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, $active_subpages['services']) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>
                            الخدمات
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?page=services" class="nav-link <?php echo $page == 'services' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>عرض الخدمات</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=services&action=add" class="nav-link <?php echo $page == 'add_service' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>إضافة خدمة جديدة</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- المشاريع -->
                <li class="nav-item <?php echo in_array($page, $active_subpages['projects']) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, $active_subpages['projects']) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>
                            المشاريع
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?page=projects" class="nav-link <?php echo $page == 'projects' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>عرض المشاريع</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=projects&action=add" class="nav-link <?php echo $page == 'add_project' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>إضافة مشروع جديد</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- التقييمات -->
                <li class="nav-item">
                    <a href="index.php?page=testimonials" class="nav-link <?php echo $page == 'testimonials' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-star"></i>
                        <p>
                            تقييمات العملاء
                            <?php if(($count = $testimonial->getNewTestimonialsCount()) > 0): ?>
                                <span class="badge badge-warning right"><?php echo $count; ?></span>
                            <?php endif; ?>
                        </p>
                    </a>
                </li>
                
                <!-- طلبات التواصل -->
                <li class="nav-item">
                    <a href="index.php?page=contacts" class="nav-link <?php echo $page == 'contacts' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            طلبات التواصل
                            <?php if(($count = $contact->getContactsCount(true)) > 0): ?>
                                <span class="badge badge-danger right"><?php echo $count; ?></span>
                            <?php endif; ?>
                        </p>
                    </a>
                </li>
                
                <!-- الإحصائيات -->
                <li class="nav-item">
                    <a href="index.php?page=statistics" class="nav-link <?php echo $page == 'statistics' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>إحصائيات الزوار</p>
                    </a>
                </li>
                
                <!-- المستخدمين (للأدمن فقط) -->
                <?php if($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item <?php echo in_array($page, $active_subpages['users']) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo in_array($page, $active_subpages['users']) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            المستخدمين
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?page=users" class="nav-link <?php echo $page == 'users' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>عرض المستخدمين</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=users&action=add" class="nav-link <?php echo $page == 'add_user' ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>إضافة مستخدم جديد</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- الإعدادات -->
                <li class="nav-item">
                    <a href="index.php?page=settings" class="nav-link <?php echo $page == 'settings' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>إعدادات الموقع</p>
                    </a>
                </li>
                
                <!-- تسجيل الخروج -->
                <li class="nav-item">
                    <a href="index.php?logout=1" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>تسجيل الخروج</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>