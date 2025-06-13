<?php
/**
 * صفحة تفاصيل المشروع
 */

// التحقق من وجود معرف المشروع
if(!isset($_GET['id']) || empty($_GET['id'])) {
    // إعادة التوجيه إلى صفحة المشاريع إذا لم يتم تحديد معرف المشروع
    header('Location: index.php?page=projects');
    exit;
}

// الحصول على معرف المشروع
$project_id = intval($_GET['id']);

// الحصول على تفاصيل المشروع
$project_details = $project->getProjectById($project_id);

// التحقق من وجود المشروع
if(empty($project_details)) {
    // إعادة التوجيه إلى صفحة المشاريع إذا لم يتم العثور على المشروع
    header('Location: index.php?page=projects');
    exit;
}

// الحصول على المشاريع ذات الصلة
$related_projects = $project->getRelatedProjects($project_id, 3);
?>

<!-- عنوان الصفحة -->
<div class="page-header bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1><?php echo $project_details['title']; ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="index.php?page=projects" class="text-white">مشاريعنا</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $project_details['title']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- قسم تفاصيل المشروع -->
<section class="project-details-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- معرض صور المشروع -->
                <?php if(!empty($project_details['gallery'])): ?>
                    <div class="project-details-slider mb-4">
                        <div id="projectCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php 
                                $gallery = json_decode($project_details['gallery'], true);
                                foreach($gallery as $index => $image): 
                                ?>
                                    <button type="button" data-bs-target="#projectCarousel" data-bs-slide-to="<?php echo $index; ?>" <?php echo ($index === 0) ? 'class="active" aria-current="true"' : ''; ?> aria-label="Slide <?php echo $index + 1; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="carousel-inner rounded">
                                <?php foreach($gallery as $index => $image): ?>
                                    <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                                        <img src="<?php echo upload_url($image); ?>" class="d-block w-100" alt="<?php echo $project_details['title']; ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <button class="carousel-control-prev" type="button" data-bs-target="#projectCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">السابق</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#projectCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">التالي</span>
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- صورة المشروع الرئيسية -->
                    <div class="project-details-img mb-4">
                        <?php if(!empty($project_details['main_image'])): ?>
                            <img src="<?php echo upload_url($project_details['main_image']); ?>" alt="<?php echo $project_details['title']; ?>" class="img-fluid rounded">
                        <?php else: ?>
                            <img src="assets/img/project-placeholder.jpg" alt="<?php echo $project_details['title']; ?>" class="img-fluid rounded">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- وصف المشروع -->
                <div class="project-details-content">
                    <h2><?php echo $project_details['title']; ?></h2>
                    <div class="project-description">
                        <?php echo $project_details['description']; ?>
                    </div>
                    
                    <!-- تحديات المشروع -->
                    <?php if(!empty($project_details['challenges'])): ?>
                        <div class="project-challenges mt-5">
                            <h3>تحديات المشروع</h3>
                            <div class="mt-3">
                                <?php echo $project_details['challenges']; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- حلول المشروع -->
                    <?php if(!empty($project_details['solutions'])): ?>
                        <div class="project-solutions mt-5">
                            <h3>الحلول المقدمة</h3>
                            <div class="mt-3">
                                <?php echo $project_details['solutions']; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- نتائج المشروع -->
                    <?php if(!empty($project_details['results'])): ?>
                        <div class="project-results mt-5">
                            <h3>نتائج المشروع</h3>
                            <div class="mt-3">
                                <?php echo $project_details['results']; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="project-sidebar">
                    <!-- معلومات المشروع -->
                    <div class="widget project-details-info bg-light p-4 rounded mb-4">
                        <h4 class="widget-title">معلومات المشروع</h4>
                        <ul class="list-unstyled">
                            <?php if(!empty($project_details['client'])): ?>
                                <li><strong>العميل:</strong> <?php echo $project_details['client']; ?></li>
                            <?php endif; ?>
                            <?php if(!empty($project_details['location'])): ?>
                                <li><strong>الموقع:</strong> <?php echo $project_details['location']; ?></li>
                            <?php endif; ?>
                            <?php if(!empty($project_details['category'])): ?>
                                <li><strong>التصنيف:</strong> <?php echo $project_details['category']; ?></li>
                            <?php endif; ?>
                            <?php if(!empty($project_details['start_date'])): ?>
                                <li><strong>تاريخ البدء:</strong> <?php echo format_date($project_details['start_date']); ?></li>
                            <?php endif; ?>
                            <?php if(!empty($project_details['end_date'])): ?>
                                <li><strong>تاريخ الانتهاء:</strong> <?php echo format_date($project_details['end_date']); ?></li>
                            <?php endif; ?>
                            <?php if(!empty($project_details['budget'])): ?>
                                <li><strong>الميزانية:</strong> <?php echo $project_details['budget']; ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- مشاركة المشروع -->
                    <div class="widget project-share bg-light p-4 rounded mb-4">
                        <h4 class="widget-title">مشاركة المشروع</h4>
                        <div class="social-share mt-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(current_url()); ?>" target="_blank" class="btn btn-sm btn-primary me-2"><i class="fab fa-facebook-f"></i> فيسبوك</a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(current_url()); ?>&text=<?php echo urlencode($project_details['title']); ?>" target="_blank" class="btn btn-sm btn-info me-2"><i class="fab fa-twitter"></i> تويتر</a>
                            <a href="https://wa.me/?text=<?php echo urlencode($project_details['title'] . ' - ' . current_url()); ?>" target="_blank" class="btn btn-sm btn-success"><i class="fab fa-whatsapp"></i> واتساب</a>
                        </div>
                    </div>
                    
                    <!-- طلب مشروع مشابه -->
                    <div class="widget project-request bg-light p-4 rounded mb-4">
                        <h4 class="widget-title">طلب مشروع مشابه</h4>
                        <p>هل ترغب في تنفيذ مشروع مشابه؟ تواصل معنا الآن للحصول على عرض سعر.</p>
                        <a href="index.php?page=contact" class="btn btn-primary w-100">طلب عرض سعر</a>
                    </div>
                    
                    <!-- مشاريع ذات صلة -->
                    <?php if(!empty($related_projects)): ?>
                        <div class="widget related-projects bg-light p-4 rounded">
                            <h4 class="widget-title">مشاريع ذات صلة</h4>
                            <ul class="list-unstyled">
                                <?php foreach($related_projects as $related): ?>
                                    <li class="d-flex mb-3">
                                        <div class="flex-shrink-0">
                                            <?php if(!empty($related['main_image'])): ?>
                                                <img src="<?php echo upload_url($related['main_image']); ?>" alt="<?php echo $related['title']; ?>" class="img-fluid rounded" style="width: 70px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="assets/img/project-placeholder.jpg" alt="<?php echo $related['title']; ?>" class="img-fluid rounded" style="width: 70px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0"><a href="index.php?page=project&id=<?php echo $related['id']; ?>"><?php echo $related['title']; ?></a></h6>
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

<!-- قسم طلب مشروع جديد -->
<section class="cta-section section-padding bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9 mb-4 mb-lg-0">
                <h2>هل لديك مشروع مشابه؟</h2>
                <p class="mb-0">تواصل معنا الآن للحصول على استشارة مجانية وعرض سعر لمشروعك</p>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a href="index.php?page=contact" class="btn btn-light">طلب عرض سعر</a>
            </div>
        </div>
    </div>
</section>
