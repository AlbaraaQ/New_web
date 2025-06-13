<?php
/**
 * صفحة الخدمات
 */

// الحصول على جميع الخدمات
$all_services = $service->getAllServices();

// الحصول على تصنيفات الخدمات (يمكن إضافتها لاحقاً)
$service_categories = $service->getServiceCategories();
?>

<!-- عنوان الصفحة -->
<div class="page-header bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>خدماتنا</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">خدماتنا</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- قسم الخدمات -->
<section class="services-section section-padding">
    <div class="container">
        <div class="section-title">
            <h2>خدماتنا المتميزة</h2>
            <p>نقدم مجموعة متنوعة من الخدمات في مجال الحدادة والكلادنج بأعلى جودة وأفضل الأسعار</p>
        </div>
        
        <?php if(!empty($service_categories)): ?>
            <!-- تصنيفات الخدمات -->
            <div class="services-filter mb-5">
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-filter="*">جميع الخدمات</a>
                    </li>
                    <?php foreach($service_categories as $category): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-filter=".<?php echo $category['slug']; ?>"><?php echo $category['name']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <?php if(empty($all_services)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        لا توجد خدمات متاحة حالياً
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($all_services as $service_item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="service-card">
                            <div class="service-img">
                                <?php if(!empty($service_item['image'])): ?>
                                    <img src="<?php echo upload_url($service_item['image']); ?>" alt="<?php echo $service_item['title']; ?>">
                                <?php else: ?>
                                    <img src="assets/img/service-placeholder.jpg" alt="<?php echo $service_item['title']; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="service-content">
                                <h3><?php echo $service_item['title']; ?></h3>
                                <p><?php echo truncate_text($service_item['description'], 100); ?></p>
                            </div>
                            <div class="service-footer">
                                <a href="index.php?page=service&id=<?php echo $service_item['id']; ?>" class="btn btn-sm btn-primary">تفاصيل الخدمة</a>
                                <a href="index.php?page=contact" class="btn btn-sm btn-outline-primary">طلب الخدمة</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- قسم مميزات خدماتنا -->
<section class="features-section section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>لماذا تختار خدماتنا؟</h2>
            <p>نحرص على تقديم أفضل الخدمات لعملائنا بأعلى جودة وأفضل الأسعار</p>
        </div>
        
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-medal fa-3x text-primary"></i>
                    </div>
                    <h4>جودة عالية</h4>
                    <p>نستخدم أفضل الخامات والمواد لضمان جودة عالية في جميع أعمالنا</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-hand-holding-usd fa-3x text-primary"></i>
                    </div>
                    <h4>أسعار تنافسية</h4>
                    <p>نقدم أفضل الأسعار التنافسية في السوق مع ضمان الجودة</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-users-cog fa-3x text-primary"></i>
                    </div>
                    <h4>فريق محترف</h4>
                    <p>فريق عمل محترف من الفنيين والمهندسين ذوي الخبرة الطويلة</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset fa-3x text-primary"></i>
                    </div>
                    <h4>دعم فني</h4>
                    <p>نقدم دعم فني على مدار الساعة لجميع عملائنا</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم طلب الخدمة -->
<section class="cta-section section-padding bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9 mb-4 mb-lg-0">
                <h2>هل تحتاج إلى خدماتنا؟</h2>
                <p class="mb-0">تواصل معنا الآن للحصول على استشارة مجانية وعرض سعر لمشروعك</p>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a href="index.php?page=contact" class="btn btn-light">طلب عرض سعر</a>
            </div>
        </div>
    </div>
</section>

<?php if(!empty($service_categories)): ?>
<script>
    $(document).ready(function() {
        // تهيئة فلتر الخدمات
        $('.services-filter .nav-link').on('click', function(e) {
            e.preventDefault();
            
            // تغيير الفلتر النشط
            $('.services-filter .nav-link').removeClass('active');
            $(this).addClass('active');
            
            // الحصول على فئة الفلتر
            var filterValue = $(this).attr('data-filter');
            
            // تطبيق الفلتر
            if(filterValue === '*') {
                $('.service-card').parent().show();
            } else {
                $('.service-card').parent().hide();
                $(filterValue).parent().show();
            }
        });
    });
</script>
<?php endif; ?>
