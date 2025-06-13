<?php
/**
 * صفحة من نحن
 */

// الحصول على معلومات الشركة من الإعدادات
$company_info = $setting->getSettings();

// الحصول على فريق العمل (يمكن إضافته لاحقاً)
$team_members = []; // يمكن إضافة نموذج خاص بفريق العمل لاحقاً
?>

<!-- عنوان الصفحة -->
<div class="page-header bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>من نحن</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">من نحن</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- قسم من نحن -->
<section class="about-section section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="about-img">
                    <img src="assets/img/about.jpg" alt="من نحن" class="img-fluid rounded shadow">
                    <div class="experience">
                        <h3>15+</h3>
                        <p>سنوات من الخبرة</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="about-content">
                    <h2>من نحن</h2>
                    <p>شركة أبو جسار للحدادة والكلادنج هي شركة رائدة في مجال الحدادة والكلادنج، تأسست منذ أكثر من 15 عاماً على يد مجموعة من الخبراء والمهندسين المتخصصين في هذا المجال.</p>
                    <p>نحن نفتخر بتقديم خدمات متميزة وحلول مبتكرة لعملائنا في مختلف المشاريع، سواء كانت مشاريع سكنية أو تجارية أو صناعية. نعمل دائماً على تطوير أساليب العمل واستخدام أحدث التقنيات والمعدات لضمان تقديم أفضل النتائج.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>رؤيتنا</h5>
                                    <p class="mb-0">أن نكون الشركة الرائدة في مجال الحدادة والكلادنج على مستوى المنطقة.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>رسالتنا</h5>
                                    <p class="mb-0">تقديم خدمات متميزة وحلول مبتكرة تلبي احتياجات عملائنا وتفوق توقعاتهم.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>قيمنا</h5>
                                    <p class="mb-0">الجودة، الالتزام، الاحترافية، الابتكار، رضا العملاء.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-primary fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5>أهدافنا</h5>
                                    <p class="mb-0">التطوير المستمر، توسيع نطاق الخدمات، تحقيق رضا العملاء.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم لماذا تختارنا -->
<section class="why-choose-us-section section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>لماذا تختارنا؟</h2>
            <p>نحن نتميز بالعديد من المميزات التي تجعلنا الخيار الأفضل لعملائنا</p>
        </div>
        
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-medal fa-3x text-primary"></i>
                    </div>
                    <h4>جودة عالية</h4>
                    <p>نستخدم أفضل الخامات والمواد لضمان جودة عالية في جميع أعمالنا</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-hand-holding-usd fa-3x text-primary"></i>
                    </div>
                    <h4>أسعار تنافسية</h4>
                    <p>نقدم أفضل الأسعار التنافسية في السوق مع ضمان الجودة</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-users-cog fa-3x text-primary"></i>
                    </div>
                    <h4>فريق محترف</h4>
                    <p>فريق عمل محترف من الفنيين والمهندسين ذوي الخبرة الطويلة</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset fa-3x text-primary"></i>
                    </div>
                    <h4>دعم فني</h4>
                    <p>نقدم دعم فني على مدار الساعة لجميع عملائنا</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-clock fa-3x text-primary"></i>
                    </div>
                    <h4>الالتزام بالمواعيد</h4>
                    <p>نلتزم بتسليم المشاريع في المواعيد المحددة دون تأخير</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card text-center p-4 bg-white rounded shadow-sm">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <h4>ضمان الجودة</h4>
                    <p>نقدم ضمان على جميع أعمالنا لضمان رضا العملاء</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم فريق العمل -->
<?php if(!empty($team_members)): ?>
<section class="team-section section-padding">
    <div class="container">
        <div class="section-title">
            <h2>فريق العمل</h2>
            <p>نفتخر بفريق عمل محترف من الخبراء والمهندسين والفنيين</p>
        </div>
        
        <div class="row">
            <?php foreach($team_members as $member): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="team-card">
                        <div class="team-img">
                            <?php if(!empty($member['image'])): ?>
                                <img src="<?php echo upload_url($member['image']); ?>" alt="<?php echo $member['name']; ?>" class="img-fluid">
                            <?php else: ?>
                                <img src="assets/img/team-placeholder.jpg" alt="<?php echo $member['name']; ?>" class="img-fluid">
                            <?php endif; ?>
                            
                            <div class="social-links">
                                <?php if(!empty($member['facebook'])): ?>
                                    <a href="<?php echo $member['facebook']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <?php endif; ?>
                                
                                <?php if(!empty($member['twitter'])): ?>
                                    <a href="<?php echo $member['twitter']; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                                <?php endif; ?>
                                
                                <?php if(!empty($member['linkedin'])): ?>
                                    <a href="<?php echo $member['linkedin']; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                                
                                <?php if(!empty($member['instagram'])): ?>
                                    <a href="<?php echo $member['instagram']; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="team-content">
                            <h3><?php echo $member['name']; ?></h3>
                            <p><?php echo $member['position']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- قسم الإحصائيات -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="stat-number" data-count="<?php echo $project->getTotalProjects(); ?>">0</div>
                    <div class="stat-text">مشاريع منجزة</div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-count="<?php echo $testimonial->getTotalClients(); ?>">0</div>
                    <div class="stat-text">عملاء سعداء</div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stat-number" data-count="<?php echo $service->getTotalServices(); ?>">0</div>
                    <div class="stat-text">خدمات متنوعة</div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number" data-count="15">0</div>
                    <div class="stat-text">سنوات الخبرة</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم الشركاء -->
<section class="partners-section section-padding">
    <div class="container">
        <div class="section-title">
            <h2>شركاؤنا</h2>
            <p>نفتخر بالتعاون مع أفضل الشركات والمؤسسات في المجال</p>
        </div>
        
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
