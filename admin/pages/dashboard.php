<?php
/**
 * صفحة لوحة التحكم الرئيسية
 */

// التحقق من تسجيل الدخول
if(!$user->isLoggedIn()) {
    redirect('login.php');
}

// إحصائيات عامة
$total_services = $service->getServicesCount();
$total_projects = $project->getProjectsCount();
$total_testimonials = $testimonial->getTestimonialsCount();
$total_contacts = $contact->getContactsCount();
$total_visits = $visitor->getTotalVisits();
$total_unique_visits = $visitor->getTotalUniqueVisits();
$new_testimonials = $testimonial->getNewTestimonialsCount();
$unread_contacts = $contact->getContactsCount(true);

// إحصائيات اليوم
$today_stats = $visitor->getTodayStats();
$today_visits = isset($today_stats['total_visits']) ? $today_stats['total_visits'] : 0;
$today_unique_visits = isset($today_stats['unique_visits']) ? $today_stats['unique_visits'] : 0;

// أحدث التقييمات
$latest_testimonials = $testimonial->getTestimonials(true);
$latest_testimonials = array_slice($latest_testimonials, 0, 5);

// أحدث طلبات التواصل
$latest_contacts = $contact->getLatestContacts(5);

// أكثر الصفحات زيارة
$most_visited_pages = $visitor->getMostVisitedPages(5);

// تغيير عنوان الصفحة
$page_title = 'لوحة التحكم';
?>

<div class="row">
    <div class="col-lg-3 col-6">
        <!-- صندوق الخدمات -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo $total_services; ?></h3>
                <p>الخدمات</p>
            </div>
            <div class="icon">
                <i class="fas fa-tools"></i>
            </div>
            <a href="index.php?page=services" class="small-box-footer">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- صندوق المشاريع -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $total_projects; ?></h3>
                <p>المشاريع</p>
            </div>
            <div class="icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <a href="index.php?page=projects" class="small-box-footer">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- صندوق التقييمات -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $total_testimonials; ?></h3>
                <p>تقييمات العملاء</p>
            </div>
            <div class="icon">
                <i class="fas fa-star"></i>
            </div>
            <a href="index.php?page=testimonials" class="small-box-footer">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- صندوق الزوار -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo $total_visits; ?></h3>
                <p>إجمالي الزيارات</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <a href="index.php?page=statistics" class="small-box-footer">عرض التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- بطاقة أحدث طلبات التواصل -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">أحدث طلبات التواصل</h3>
                <div class="card-tools">
                    <?php if($unread_contacts > 0): ?>
                        <span class="badge badge-danger"><?php echo $unread_contacts; ?> غير مقروء</span>
                    <?php endif; ?>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الرسالة</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($latest_contacts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد طلبات تواصل</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($latest_contacts as $contact_item): ?>
                                    <tr>
                                        <td><a href="index.php?page=contacts&id=<?php echo $contact_item['id']; ?>"><?php echo $contact_item['name']; ?></a></td>
                                        <td><?php echo $contact_item['email']; ?></td>
                                        <td><?php echo truncate_text($contact_item['message'], 50); ?></td>
                                        <td><?php echo format_date($contact_item['created_at'], true); ?></td>
                                        <td>
                                            <?php if($contact_item['is_read'] == 0): ?>
                                                <span class="badge badge-danger">غير مقروء</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">مقروء</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                <a href="index.php?page=contacts" class="btn btn-sm btn-primary float-start">عرض جميع طلبات التواصل</a>
            </div>
        </div>
        
        <!-- بطاقة أكثر الصفحات زيارة -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">أكثر الصفحات زيارة</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>الصفحة</th>
                                <th>عدد الزيارات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($most_visited_pages)): ?>
                                <tr>
                                    <td colspan="2" class="text-center">لا توجد بيانات</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($most_visited_pages as $page_item): ?>
                                    <tr>
                                        <td><?php echo $page_item['page_visited']; ?></td>
                                        <td><span class="badge bg-primary"><?php echo $page_item['visits']; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                <a href="index.php?page=statistics" class="btn btn-sm btn-info float-start">عرض جميع الإحصائيات</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- بطاقة إحصائيات اليوم -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إحصائيات اليوم</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                    <p class="text-success text-xl">
                        <i class="fas fa-users"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                            <?php echo $today_visits; ?>
                        </span>
                        <span class="text-muted">زيارات اليوم</span>
                    </p>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                    <p class="text-primary text-xl">
                        <i class="fas fa-user"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                            <?php echo $today_unique_visits; ?>
                        </span>
                        <span class="text-muted">زوار فريدين</span>
                    </p>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="text-danger text-xl">
                        <i class="fas fa-chart-pie"></i>
                    </p>
                    <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                            <?php echo $total_unique_visits; ?>
                        </span>
                        <span class="text-muted">إجمالي الزوار الفريدين</span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- بطاقة أحدث التقييمات -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">أحدث تقييمات العملاء</h3>
                <div class="card-tools">
                    <?php if($new_testimonials > 0): ?>
                        <span class="badge badge-warning"><?php echo $new_testimonials; ?> جديد</span>
                    <?php endif; ?>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if(empty($latest_testimonials)): ?>
                        <li class="item">
                            <div class="product-info">
                                <div class="product-title">
                                    لا توجد تقييمات
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <?php foreach($latest_testimonials as $testimonial_item): ?>
                            <li class="item">
                                <div class="product-img">
                                    <?php if(!empty($testimonial_item['client_image'])): ?>
                                        <img src="<?php echo upload_url($testimonial_item['client_image']); ?>" alt="<?php echo $testimonial_item['client_name']; ?>" class="img-size-50">
                                    <?php else: ?>
                                        <img src="assets/img/user.png" alt="<?php echo $testimonial_item['client_name']; ?>" class="img-size-50">
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <a href="index.php?page=testimonials&id=<?php echo $testimonial_item['id']; ?>" class="product-title">
                                        <?php echo $testimonial_item['client_name']; ?>
                                        <?php if($testimonial_item['is_approved'] == 0): ?>
                                            <span class="badge badge-warning float-left">جديد</span>
                                        <?php endif; ?>
                                    </a>
                                    <span class="product-description">
                                        <?php echo rating_stars($testimonial_item['rating']); ?>
                                    </span>
                                    <span class="product-description">
                                        <?php echo truncate_text($testimonial_item['content'], 50); ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="index.php?page=testimonials" class="uppercase">عرض جميع التقييمات</a>
            </div>
        </div>
    </div>
</div>
