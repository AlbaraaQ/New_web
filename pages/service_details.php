<?php
/**
 * صفحة تفاصيل الخدمة
 */

// التحقق من وجود معرف الخدمة
if(!isset($_GET['id']) || empty($_GET['id'])) {
    // إعادة التوجيه إلى صفحة الخدمات إذا لم يتم تحديد معرف الخدمة
    header('Location: index.php?page=services');
    exit;
}

// الحصول على معرف الخدمة
$service_id = intval($_GET['id']);

// الحصول على تفاصيل الخدمة
$service_details = $service->getServiceById($service_id);

// التحقق من وجود الخدمة
if(empty($service_details)) {
    // إعادة التوجيه إلى صفحة الخدمات إذا لم يتم العثور على الخدمة
    header('Location: index.php?page=services');
    exit;
}

// الحصول على الخدمات ذات الصلة
$related_services = $service->getRelatedServices($service_id, 3);
?>

<!-- عنوان الصفحة -->
<div class="page-header bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1><?php echo $service_details['title']; ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="index.php?page=services" class="text-white">خدماتنا</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $service_details['title']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- قسم تفاصيل الخدمة -->
<section class="service-details-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="service-details-content">
                    <!-- صورة الخدمة -->
                    <div class="service-details-img">
                        <?php if(!empty($service_details['image'])): ?>
                            <img src="<?php echo upload_url($service_details['image']); ?>" alt="<?php echo $service_details['title']; ?>" class="img-fluid">
                        <?php else: ?>
                            <img src="assets/img/service-placeholder.jpg" alt="<?php echo $service_details['title']; ?>" class="img-fluid">
                        <?php endif; ?>
                    </div>
                    
                    <!-- وصف الخدمة -->
                    <h2><?php echo $service_details['title']; ?></h2>
                    <div class="service-description">
                        <?php echo $service_details['description']; ?>
                    </div>
                    
                    <!-- مميزات الخدمة -->
                    <?php if(!empty($service_details['features'])): ?>
                        <div class="service-features">
                            <h3>مميزات الخدمة</h3>
                            <ul>
                                <?php 
                                $features = explode("\n", $service_details['features']);
                                foreach($features as $feature): 
                                    if(!empty(trim($feature))):
                                ?>
                                    <li><i class="fas fa-check-circle"></i> <?php echo trim($feature); ?></li>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- معرض صور الخدمة -->
                    <?php if(!empty($service_details['gallery'])): ?>
                        <div class="service-gallery mt-5">
                            <h3>معرض الصور</h3>
                            <div class="row mt-4">
                                <?php 
                                $gallery = json_decode($service_details['gallery'], true);
                                foreach($gallery as $image): 
                                ?>
                                    <div class="col-md-4 mb-4">
                                        <a href="<?php echo upload_url($image); ?>" data-lightbox="service-gallery" data-title="<?php echo $service_details['title']; ?>">
                                            <img src="<?php echo upload_url($image); ?>" alt="<?php echo $service_details['title']; ?>" class="img-fluid rounded">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- زر طلب الخدمة -->
                    <div class="mt-5">
                        <a href="index.php?page=contact" class="btn btn-primary">طلب هذه الخدمة</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="service-sidebar">
                    <!-- معلومات الخدمة -->
                    <div class="widget service-info bg-light p-4 rounded mb-4">
                        <h4 class="widget-title">معلومات الخدمة</h4>
                        <ul class="list-unstyled">
                            <?php if(!empty($service_details['price'])): ?>
                                <li><strong>السعر:</strong> <?php echo $service_details['price']; ?></li>
                            <?php endif; ?>
                            <?php if(!empty($service_details['duration'])): ?>
                                <li><strong>مدة التنفيذ:</strong> <?php echo $service_details['duration']; ?></li>
                            <?php endif; ?>
                            <?php if(!empty($service_details['category'])): ?>
                                <li><strong>التصنيف:</strong> <?php echo $service_details['category']; ?></li>
                            <?php endif; ?>
                            <li><strong>تاريخ الإضافة:</strong> <?php echo format_date($service_details['created_at']); ?></li>
                        </ul>
                    </div>
                    
                    <!-- نموذج طلب الخدمة -->
                    <div class="widget service-request bg-light p-4 rounded mb-4">
                        <h4 class="widget-title">طلب الخدمة</h4>
                        <form action="index.php?page=contact" method="post" class="service-request-form">
                            <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                            <input type="hidden" name="service_title" value="<?php echo $service_details['title']; ?>">
                            
                            <div class="mb-3">
                                <input type="text" name="name" class="form-control" placeholder="الاسم الكامل" required>
                            </div>
                            
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required>
                            </div>
                            
                            <div class="mb-3">
                                <input type="tel" name="phone" class="form-control" placeholder="رقم الهاتف" required>
                            </div>
                            
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="4" placeholder="تفاصيل الطلب" required></textarea>
                            </div>
                            
                            <button type="submit" name="service_request" class="btn btn-primary w-100">إرسال الطلب</button>
                        </form>
                    </div>
                    
                    <!-- خدمات ذات صلة -->
                    <?php if(!empty($related_services)): ?>
                        <div class="widget related-services bg-light p-4 rounded">
                            <h4 class="widget-title">خدمات ذات صلة</h4>
                            <ul class="list-unstyled">
                                <?php foreach($related_services as $related): ?>
                                    <li class="d-flex mb-3">
                                        <div class="flex-shrink-0">
                                            <?php if(!empty($related['image'])): ?>
                                                <img src="<?php echo upload_url($related['image']); ?>" alt="<?php echo $related['title']; ?>" class="img-fluid rounded" style="width: 70px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="assets/img/service-placeholder.jpg" alt="<?php echo $related['title']; ?>" class="img-fluid rounded" style="width: 70px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0"><a href="index.php?page=service&id=<?php echo $related['id']; ?>"><?php echo $related['title']; ?></a></h6>
                                            <small class="text-muted"><?php echo truncate_text($related['description'], 50); ?></small>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
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
                <h2>هل تحتاج إلى هذه الخدمة؟</h2>
                <p class="mb-0">تواصل معنا الآن للحصول على استشارة مجانية وعرض سعر لمشروعك</p>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a href="index.php?page=contact" class="btn btn-light">طلب عرض سعر</a>
            </div>
        </div>
    </div>
</section>
