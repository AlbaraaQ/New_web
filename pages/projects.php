<?php
/**
 * صفحة المشاريع
 */

// الحصول على جميع المشاريع
$all_projects = $project->getAllProjects();

// الحصول على تصنيفات المشاريع (يمكن إضافتها لاحقاً)
$project_categories = $project->getProjectCategories();
?>

<!-- عنوان الصفحة -->
<div class="page-header bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>مشاريعنا</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">مشاريعنا</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- قسم المشاريع -->
<section class="projects-section section-padding">
    <div class="container">
        <div class="section-title">
            <h2>مشاريعنا المميزة</h2>
            <p>نفتخر بتنفيذ العديد من المشاريع المميزة في مجال الحدادة والكلادنج بأعلى معايير الجودة</p>
        </div>
        
        <?php if(!empty($project_categories)): ?>
            <!-- تصنيفات المشاريع -->
            <div class="projects-filter mb-5">
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-filter="*">جميع المشاريع</a>
                    </li>
                    <?php foreach($project_categories as $category): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-filter=".<?php echo $category['slug']; ?>"><?php echo $category['name']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <?php if(empty($all_projects)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        لا توجد مشاريع متاحة حالياً
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($all_projects as $project_item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="project-card">
                            <div class="project-img">
                                <?php if(!empty($project_item['main_image'])): ?>
                                    <img src="<?php echo upload_url($project_item['main_image']); ?>" alt="<?php echo $project_item['title']; ?>">
                                <?php else: ?>
                                    <img src="assets/img/project-placeholder.jpg" alt="<?php echo $project_item['title']; ?>">
                                <?php endif; ?>
                            </div>
                            <div class="project-overlay">
                                <h3><?php echo $project_item['title']; ?></h3>
                                <a href="index.php?page=project&id=<?php echo $project_item['id']; ?>" class="btn btn-primary btn-sm">عرض التفاصيل</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- قسم إحصائيات المشاريع -->
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

<!-- قسم طلب مشروع جديد -->
<section class="cta-section section-padding bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9 mb-4 mb-lg-0">
                <h2>هل لديك مشروع جديد؟</h2>
                <p class="mb-0">تواصل معنا الآن للحصول على استشارة مجانية وعرض سعر لمشروعك</p>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a href="index.php?page=contact" class="btn btn-light">طلب عرض سعر</a>
            </div>
        </div>
    </div>
</section>

<?php if(!empty($project_categories)): ?>
<script>
    $(document).ready(function() {
        // تهيئة فلتر المشاريع
        $('.projects-filter .nav-link').on('click', function(e) {
            e.preventDefault();
            
            // تغيير الفلتر النشط
            $('.projects-filter .nav-link').removeClass('active');
            $(this).addClass('active');
            
            // الحصول على فئة الفلتر
            var filterValue = $(this).attr('data-filter');
            
            // تطبيق الفلتر
            if(filterValue === '*') {
                $('.project-card').parent().show();
            } else {
                $('.project-card').parent().hide();
                $(filterValue).parent().show();
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
<?php endif; ?>
