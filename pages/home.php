<?php
/**
 * صفحة الرئيسية للموقع
 */

// الحصول على الخدمات المميزة (أحدث 6 خدمات)
$featured_services = $service->getFeaturedServices(6);

// الحصول على المشاريع المميزة (أحدث 6 مشاريع)
$featured_projects = $project->getFeaturedProjects(6);

// الحصول على آراء العملاء المعتمدة (أحدث 6 تقييمات)
$featured_testimonials = $testimonial->getFeaturedTestimonials(6);

// الحصول على إحصائيات الموقع
$total_projects = $project->getTotalProjects();
$total_clients = $testimonial->getTotalClients();
$total_services = $service->getTotalServices();
$years_experience = 15; // يمكن تغييرها من الإعدادات لاحقاً
?>

<!-- السلايدر الرئيسي -->
<div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    
    <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('assets/img/slider/slide1.jpg');">
            <div class="carousel-caption text-center">
                <h2 class="animate__animated animate__fadeInDown">أفضل خدمات الحدادة والكلادنج</h2>
                <p class="animate__animated animate__fadeInUp">نقدم أفضل خدمات الحدادة والكلادنج بأعلى جودة وأفضل الأسعار مع ضمان الجودة والإتقان في العمل</p>
                <a href="index.php?page=services" class="btn btn-primary animate__animated animate__fadeInUp">خدماتنا</a>
                <a href="index.php?page=contact" class="btn btn-outline-light animate__animated animate__fadeInUp">تواصل معنا</a>
            </div>
        </div>
        
        <div class="carousel-item" style="background-image: url('assets/img/slider/slide2.jpg');">
            <div class="carousel-caption text-center">
                <h2 class="animate__animated animate__fadeInDown">مشاريع مميزة ومتنوعة</h2>
                <p class="animate__animated animate__fadeInUp">نفتخر بتنفيذ العديد من المشاريع المميزة في مجال الحدادة والكلادنج بأعلى معايير الجودة</p>
                <a href="index.php?page=projects" class="btn btn-primary animate__animated animate__fadeInUp">مشاريعنا</a>
                <a href="index.php?page=contact" class="btn btn-outline-light animate__animated animate__fadeInUp">طلب عرض سعر</a>
            </div>
        </div>
        
        <div class="carousel-item" style="background-image: url('assets/img/slider/slide3.jpg');">
            <div class="carousel-caption text-center">
                <h2 class="animate__animated animate__fadeInDown">فريق محترف وخبرة طويلة</h2>
                <p class="animate__animated animate__fadeInUp">نمتلك فريق عمل محترف وخبرة تزيد عن <?php echo $years_experience; ?> عاماً في مجال الحدادة والكلادنج</p>
                <a href="index.php?page=about" class="btn btn-primary animate__animated animate__fadeInUp">من نحن</a>
                <a href="index.php?page=contact" class="btn btn-outline-light animate__animated animate__fadeInUp">تواصل معنا</a>
            </div>
        </div>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">السابق</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">التالي</span>
    </button>
</div>

<!-- قسم من نحن -->
<section class="about-section section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-img">
                    <img src="assets/img/about.jpg" alt="من نحن" class="img-fluid">
                    <div class="experience">
                        <h3><?php echo $years_experience; ?>+</h3>
                        <p>سنوات من الخبرة</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="about-content">
                    <h2>نحن شركة رائدة في مجال الحدادة والكلادنج</h2>
                    <p>نحن شركة متخصصة في مجال الحدادة والكلادنج، نقدم خدمات متميزة بأعلى جودة وأفضل الأسعار. نمتلك فريق عمل محترف وخبرة طويلة في المجال تمكننا من تنفيذ جميع المشاريع بدقة وإتقان.</p>
                    <p>نسعى دائماً لتقديم أفضل الخدمات لعملائنا والحفاظ على ثقتهم من خلال الالتزام بالمواعيد والجودة العالية في التنفيذ.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>جودة عالية</h5>
                                    <p class="mb-0">نستخدم أفضل الخامات والمواد</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>فريق محترف</h5>
                                    <p class="mb-0">فنيين ومهندسين ذوي خبرة</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>أسعار تنافسية</h5>
                                    <p class="mb-0">أفضل الأسعار في السوق</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>ضمان الجودة</h5>
                                    <p class="mb-0">نقدم ضمان على جميع أعمالنا</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php?page=about" class="btn btn-primary me-3">المزيد عنا</a>
                        <a href="index.php?page=contact" class="btn btn-outline-primary">تواصل معنا</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم الخدمات -->
<section class="services-section section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>خدماتنا</h2>
            <p>نقدم مجموعة متنوعة من الخدمات في مجال الحدادة والكلادنج بأعلى جودة وأفضل الأسعار</p>
        </div>
        
        <div class="row">
            <?php if(empty($featured_services)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        لا توجد خدمات متاحة حالياً
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($featured_services as $service_item): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="service-card">
                            <div class="service-img">
                                <?php if(!empty($service_item['image'])): ?>
                                    <img src="<?php echo upload_url($service_item['image']); ?>" alt="<?php echo $service_item['name']; ?>">
                                <?php else: ?>
                                    <img src="assets/img/service-placeholder.jpg" alt="<?php echo $service_item['name']; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="service-content">
                                <h3><?php echo $service_item['name']; ?></h3>
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
        
        <div class="text-center mt-5">
            <a href="index.php?page=services" class="btn btn-primary">عرض جميع الخدمات</a>
        </div>
    </div>
</section>

<!-- قسم الإحصائيات -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="stat-number" data-count="<?php echo $total_projects; ?>">0</div>
                    <div class="stat-text">مشاريع منجزة</div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-count="<?php echo $total_clients; ?>">0</div>
                    <div class="stat-text">عملاء سعداء</div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-number" data-count="<?php echo $total_services; ?>">0</div>
                    <div class="stat-text">خدمات متنوعة</div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number" data-count="<?php echo $years_experience; ?>">0</div>
                    <div class="stat-text">سنوات الخبرة</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم المشاريع -->
<section class="projects-section section-padding">
    <div class="container">
        <div class="section-title">
            <h2>مشاريعنا</h2>
            <p>نفتخر بتنفيذ العديد من المشاريع المميزة في مجال الحدادة والكلادنج بأعلى معايير الجودة</p>
        </div>
        
        <div class="row">
            <?php if(empty($featured_projects)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        لا توجد مشاريع متاحة حالياً
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($featured_projects as $project_item): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="project-card">
                            <div class="project-img">
                                <?php if(!empty($project_item['main_image'])): ?>
                                    <img src="<?php echo upload_url($project_item['main_image']); ?>" alt="<?php echo $project_item['name']; ?>">
                                <?php else: ?>
                                    <img src="assets/img/project-placeholder.jpg" alt="<?php echo $project_item['name']; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="project-overlay">
                                <h3><?php echo $project_item['name']; ?></h3>
                                <a href="index.php?page=project&id=<?php echo $project_item['id']; ?>" class="btn btn-primary btn-sm">عرض التفاصيل</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="index.php?page=projects" class="btn btn-primary">عرض جميع المشاريع</a>
        </div>
    </div>
</section>

<!-- قسم آراء العملاء -->
<section class="testimonials-section section-padding">
    <div class="container">
        <div class="section-title">
            <h2>آراء العملاء</h2>
            <p>نفخر بثقة عملائنا وآرائهم الإيجابية حول خدماتنا ومشاريعنا</p>
        </div>
        
        <?php if(empty($featured_testimonials)): ?>
            <div class="alert alert-info text-center">
                لا توجد تقييمات متاحة حالياً
            </div>
        <?php else: ?>
            <div class="owl-carousel testimonials-carousel">
                <?php foreach($featured_testimonials as $testimonial_item): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <?php echo $testimonial_item['content']; ?>
                        </div>
                        <div class="testimonial-rating">
                            <?php echo rating_stars($testimonial_item['rating']); ?>
                        </div>
                        <div class="testimonial-author">
                            <?php if(!empty($testimonial_item['client_image'])): ?>
                                <img src="<?php echo upload_url($testimonial_item['client_image']); ?>" alt="<?php echo $testimonial_item['client_name']; ?>">
                            <?php else: ?>
                                <img src="assets/img/user-placeholder.jpg" alt="<?php echo $testimonial_item['client_name']; ?>">
                            <?php endif; ?>
                            <div class="testimonial-author-info">
                                <h4><?php echo $testimonial_item['client_name']; ?></h4>
                                <p><?php echo $testimonial_item['client_position']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <a href="index.php?page=testimonials" class="btn btn-primary me-3">جميع التقييمات</a>
            <a href="index.php?page=testimonials#add-testimonial" class="btn btn-outline-primary">أضف تقييمك</a>
        </div>
    </div>
</section>

<!-- قسم الشركاء -->
<section class="partners-section">
    <div class="container">
        <div class="owl-carousel partners-carousel">
            <div class="partner-item">
                <img src="assets/img/partners/partner1.png" alt="شريك 1">
            </div>
            <div class="partner-item">
                <img src="assets/img/partners/partner2.png" alt="شريك 2">
            </div>
            <div class="partner-item">
                <img src="assets/img/partners/partner3.png" alt="شريك 3">
            </div>
            <div class="partner-item">
                <img src="assets/img/partners/partner4.png" alt="شريك 4">
            </div>
            <div class="partner-item">
                <img src="assets/img/partners/partner5.png" alt="شريك 5">
            </div>
            <div class="partner-item">
                <img src="assets/img/partners/partner6.png" alt="شريك 6">
            </div>
        </div>
    </div>
</section>

<!-- قسم التواصل المختصر -->
<section class="cta-section section-padding bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9 mb-4 mb-lg-0">
                <h2>هل تحتاج إلى خدماتنا؟</h2>
                <p class="mb-0">تواصل معنا الآن للحصول على استشارة مجانية وعرض سعر لمشروعك</p>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a href="index.php?page=contact" class="btn btn-light">تواصل معنا</a>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // تهيئة سلايدر آراء العملاء
        $('.testimonials-carousel').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            navText: ['<i class="fas fa-chevron-right"></i>', '<i class="fas fa-chevron-left"></i>'],
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
        
        // تهيئة سلايدر الشركاء
        $('.partners-carousel').owlCarousel({
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
                    items: 6
                }
            }
        });
        
        // تهيئة عداد الإحصائيات
        $('.stat-number').each(function() {
            var $this = $(this);
            var countTo = $this.attr('data-count');
            
            $({ countNum: $this.text() }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });
    });
</script>
