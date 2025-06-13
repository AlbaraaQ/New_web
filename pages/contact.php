<?php
/**
 * صفحة التواصل
 */

// معالجة نموذج التواصل
$success_message = '';
$error_message = '';

if(isset($_POST['send_contact'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // التحقق من البيانات
    if(empty($name) || empty($email) || empty($phone) || empty($message)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'يرجى إدخال بريد إلكتروني صحيح';
    } else {
        // إضافة رسالة التواصل الجديدة
        $contact_data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'status' => 'new' // جديدة
        ];
        
        // إذا كان الطلب من صفحة الخدمة
        if(isset($_POST['service_request']) && isset($_POST['service_id']) && isset($_POST['service_title'])) {
            $contact_data['service_id'] = intval($_POST['service_id']);
            $contact_data['service_title'] = $_POST['service_title'];
            $contact_data['type'] = 'service_request';
        } else {
            $contact_data['type'] = 'contact';
        }
        
        $result = $contact->addContact($contact_data);
        
        if($result) {
            $success_message = 'تم إرسال رسالتك بنجاح. سنقوم بالرد عليك في أقرب وقت ممكن.';
        } else {
            $error_message = 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.';
        }
    }
}
?>

<!-- عنوان الصفحة -->
<div class="page-header bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>اتصل بنا</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">اتصل بنا</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- قسم معلومات التواصل -->
<section class="contact-info-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="contact-info-item">
                    <div class="icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="content">
                        <h4>العنوان</h4>
                        <p><?php echo nl2br($site_settings['site_address']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="contact-info-item">
                    <div class="icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="content">
                        <h4>رقم الهاتف</h4>
                        <p>
                            <a href="tel:<?php echo $site_settings['site_phone']; ?>"><?php echo $site_settings['site_phone']; ?></a>
                            <?php if(!empty($site_settings['site_phone2'])): ?>
                                <br>
                                <a href="tel:<?php echo $site_settings['site_phone2']; ?>"><?php echo $site_settings['site_phone2']; ?></a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="contact-info-item">
                    <div class="icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="content">
                        <h4>البريد الإلكتروني</h4>
                        <p>
                            <a href="mailto:<?php echo $site_settings['site_email']; ?>"><?php echo $site_settings['site_email']; ?></a>
                            <?php if(!empty($site_settings['site_email2'])): ?>
                                <br>
                                <a href="mailto:<?php echo $site_settings['site_email2']; ?>"><?php echo $site_settings['site_email2']; ?></a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم نموذج التواصل والخريطة -->
<section class="contact-form-section section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="section-title text-start">
                    <h2>أرسل لنا رسالة</h2>
                    <p>يمكنك التواصل معنا من خلال ملء النموذج التالي وسنقوم بالرد عليك في أقرب وقت ممكن</p>
                </div>
                
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
                
                <div class="contact-form">
                    <form action="index.php?page=contact" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label">الموضوع</label>
                                <input type="text" name="subject" id="subject" class="form-control">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">الرسالة <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" name="send_contact" class="btn btn-primary">إرسال الرسالة</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="contact-map">
                    <div class="section-title text-start">
                        <h2>موقعنا</h2>
                        <p>يمكنك زيارتنا في العنوان التالي</p>
                    </div>
                    
                    <?php if(!empty($site_settings['site_map'])): ?>
                        <div class="map-container">
                            <?php echo $site_settings['site_map']; ?>
                        </div>
                    <?php else: ?>
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3610.1785333235397!2d55.272165!3d25.197201!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f43348a67e24b%3A0xff45e502e1ceb7e2!2sBurj%20Khalifa!5e0!3m2!1sen!2sae!4v1622921100000!5m2!1sen!2sae" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم ساعات العمل -->
<section class="working-hours-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="section-title text-start">
                    <h2>ساعات العمل</h2>
                    <p>نحن متواجدون لخدمتكم خلال الأوقات التالية</p>
                </div>
                
                <div class="working-hours">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>السبت - الخميس</td>
                                    <td>9:00 صباحاً - 6:00 مساءً</td>
                                </tr>
                                <tr>
                                    <td>الجمعة</td>
                                    <td>مغلق</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="section-title text-start">
                    <h2>تواصل معنا عبر وسائل التواصل الاجتماعي</h2>
                    <p>يمكنك متابعتنا والتواصل معنا عبر منصات التواصل الاجتماعي</p>
                </div>
                
                <div class="social-links-large">
                    <?php if(!empty($site_settings['site_facebook'])): ?>
                        <a href="<?php echo $site_settings['site_facebook']; ?>" class="btn btn-outline-primary me-2 mb-2" target="_blank"><i class="fab fa-facebook-f me-2"></i> فيسبوك</a>
                    <?php endif; ?>
                    
                    <?php if(!empty($site_settings['site_twitter'])): ?>
                        <a href="<?php echo $site_settings['site_twitter']; ?>" class="btn btn-outline-info me-2 mb-2" target="_blank"><i class="fab fa-twitter me-2"></i> تويتر</a>
                    <?php endif; ?>
                    
                    <?php if(!empty($site_settings['site_instagram'])): ?>
                        <a href="<?php echo $site_settings['site_instagram']; ?>" class="btn btn-outline-danger me-2 mb-2" target="_blank"><i class="fab fa-instagram me-2"></i> انستغرام</a>
                    <?php endif; ?>
                    
                    <?php if(!empty($site_settings['site_youtube'])): ?>
                        <a href="<?php echo $site_settings['site_youtube']; ?>" class="btn btn-outline-danger me-2 mb-2" target="_blank"><i class="fab fa-youtube me-2"></i> يوتيوب</a>
                    <?php endif; ?>
                    
                    <?php if(!empty($site_settings['site_whatsapp'])): ?>
                        <a href="https://wa.me/<?php echo $site_settings['site_whatsapp']; ?>" class="btn btn-outline-success mb-2" target="_blank"><i class="fab fa-whatsapp me-2"></i> واتساب</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم الأسئلة الشائعة -->
<section class="faq-section section-padding bg-light">
    <div class="container">
        <div class="section-title">
            <h2>الأسئلة الشائعة</h2>
            <p>إليك بعض الأسئلة الشائعة التي قد تساعدك</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                ما هي خدمات الحدادة التي تقدمونها؟
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نقدم مجموعة متنوعة من خدمات الحدادة بما في ذلك تصنيع وتركيب الأبواب والنوافذ الحديدية، الدرابزينات، البوابات، الهياكل المعدنية، وغيرها من الأعمال المعدنية المخصصة.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                ما هي خدمات الكلادنج التي تقدمونها؟
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نقدم خدمات تركيب وصيانة الكلادنج بمختلف أنواعه مثل الألومنيوم، الزجاج، الخشب، والحجر، بالإضافة إلى تصميم وتنفيذ واجهات المباني بأحدث التقنيات والتصاميم.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                كيف يمكنني الحصول على عرض سعر لمشروعي؟
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                يمكنك الحصول على عرض سعر لمشروعك من خلال ملء نموذج التواصل في هذه الصفحة، أو الاتصال بنا مباشرة على الأرقام الموضحة. سيقوم فريقنا بالتواصل معك في أقرب وقت ممكن لمناقشة تفاصيل المشروع وتقديم عرض سعر مناسب.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                هل تقدمون ضمان على أعمالكم؟
                            </button>
                        </h2>
                        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نعم، نقدم ضمان على جميع أعمالنا لمدة تتراوح بين سنة إلى خمس سنوات حسب نوع العمل والمواد المستخدمة. نحن نؤمن بجودة خدماتنا ونلتزم بتقديم أفضل النتائج لعملائنا.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeading5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                ما هي مدة تنفيذ المشاريع؟
                            </button>
                        </h2>
                        <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                تختلف مدة تنفيذ المشاريع حسب حجم وتعقيد المشروع. نحن نلتزم بجدول زمني محدد يتم الاتفاق عليه مع العميل قبل بدء العمل، ونحرص على الالتزام بالمواعيد المحددة لتسليم المشاريع.
                            </div>
                        </div>
                    </div>
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
                <a href="tel:<?php echo $site_settings['site_phone']; ?>" class="btn btn-light">اتصل بنا الآن</a>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // تهيئة خريطة جوجل (إذا كانت موجودة)
        $('.map-container iframe').addClass('w-100');
    });
</script>
