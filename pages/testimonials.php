<?php
/**
 * صفحة آراء العملاء
 */

// الحصول على جميع التقييمات المعتمدة
$all_testimonials = $testimonial->getAllApprovedTestimonials();

// معالجة نموذج إضافة تقييم جديد
$success_message = '';
$error_message = '';

if(isset($_POST['add_testimonial'])) {
    $client_name = filter_input(INPUT_POST, 'client_name', FILTER_SANITIZE_STRING);
    $client_position = filter_input(INPUT_POST, 'client_position', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    
    // التحقق من البيانات
    if(empty($client_name) || empty($content) || empty($rating)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        // إضافة التقييم الجديد
        $testimonial_data = [
            'client_name' => $client_name,
            'client_position' => $client_position,
            'content' => $content,
            'rating' => $rating,
            'status' => 'pending' // بانتظار الموافقة
        ];
        
        // معالجة الصورة إذا تم رفعها
        if(isset($_FILES['client_image']) && $_FILES['client_image']['error'] === UPLOAD_ERR_OK) {
            $client_image = upload_image($_FILES['client_image'], 'testimonials');
            if($client_image) {
                $testimonial_data['client_image'] = $client_image;
            }
        }
        
        $result = $testimonial->addTestimonial($testimonial_data);
        
        if($result) {
            $success_message = 'تم إرسال تقييمك بنجاح. سيتم مراجعته والموافقة عليه قريباً.';
        } else {
            $error_message = 'حدث خطأ أثناء إرسال التقييم. يرجى المحاولة مرة أخرى.';
        }
    }
}
?>

<!-- عنوان الصفحة -->
<div class="page-header bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>آراء العملاء</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">آراء العملاء</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- قسم آراء العملاء -->
<section class="testimonials-section section-padding">
    <div class="container">
        <div class="section-title">
            <h2>آراء عملائنا</h2>
            <p>نفخر بثقة عملائنا وآرائهم الإيجابية حول خدماتنا ومشاريعنا</p>
        </div>
        
        <div class="row testimonials-page">
            <?php if(empty($all_testimonials)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        لا توجد تقييمات متاحة حالياً
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($all_testimonials as $testimonial_item): ?>
                    <div class="col-md-6 col-lg-4">
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
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- قسم إضافة تقييم جديد -->
<section class="add-testimonial-section section-padding bg-light" id="add-testimonial">
    <div class="container">
        <div class="section-title">
            <h2>أضف تقييمك</h2>
            <p>نحن نقدر آراء عملائنا ونسعى دائماً لتحسين خدماتنا</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if(!empty($success_message)): ?>
                    <div class="alert alert-success mb-4">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($error_message)): ?>
                    <div class="alert alert-danger mb-4">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="add-testimonial-form">
                    <form action="index.php?page=testimonials#add-testimonial" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" name="client_name" id="client_name" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="client_position" class="form-label">المسمى الوظيفي / الشركة</label>
                                <input type="text" name="client_position" id="client_position" class="form-control">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">التقييم <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">التقييم <span class="text-danger">*</span></label>
                            <div class="rating-stars">
                                <input type="radio" name="rating" id="rating-5" value="5">
                                <label for="rating-5"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="rating-4" value="4">
                                <label for="rating-4"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="rating-3" value="3" checked>
                                <label for="rating-3"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="rating-2" value="2">
                                <label for="rating-2"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="rating-1" value="1">
                                <label for="rating-1"><i class="fas fa-star"></i></label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="client_image" class="form-label">صورة شخصية (اختياري)</label>
                            <input type="file" name="client_image" id="client_image" class="form-control" accept="image/*">
                            <div class="form-text">الحد الأقصى لحجم الصورة: 2 ميجابايت</div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" name="add_testimonial" class="btn btn-primary">إرسال التقييم</button>
                        </div>
                    </form>
                </div>
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
        // تهيئة نظام التقييم بالنجوم
        $('.rating-stars input').on('change', function() {
            var $this = $(this);
            $('.rating-stars label').removeClass('active');
            $this.next('label').addClass('active');
            $this.prevAll('input').next('label').addClass('active');
        });
    });
</script>
