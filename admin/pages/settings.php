<?php
/**
 * صفحة إدارة إعدادات الموقع
 */

// التحقق من تسجيل الدخول
if(!$user->isLoggedIn()) {
    redirect('login.php');
}

// التحقق من الصلاحيات
if(!$user->hasPermission('manage_settings')) {
    set_flash_message('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
    redirect('index.php?page=dashboard');
}

// متغيرات الخطأ والنجاح
$errors = array();
$success = '';

// معالجة حفظ الإعدادات
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من البيانات
    $site_name = clean_input($_POST['site_name']);
    $site_description = clean_input($_POST['site_description']);
    $site_keywords = clean_input($_POST['site_keywords']);
    $site_email = clean_input($_POST['site_email']);
    $site_phone = clean_input($_POST['site_phone']);
    $site_address = clean_input($_POST['site_address']);
    $site_facebook = clean_input($_POST['site_facebook']);
    $site_twitter = clean_input($_POST['site_twitter']);
    $site_instagram = clean_input($_POST['site_instagram']);
    $site_youtube = clean_input($_POST['site_youtube']);
    $site_whatsapp = clean_input($_POST['site_whatsapp']);
    $site_footer_text = clean_input($_POST['site_footer_text']);
    $site_analytics_code = $_POST['site_analytics_code'];
    $site_custom_css = $_POST['site_custom_css'];
    $site_custom_js = $_POST['site_custom_js'];
    
    // التحقق من اسم الموقع
    if(empty($site_name)) {
        $errors['site_name'] = 'يرجى إدخال اسم الموقع';
    }
    
    // التحقق من البريد الإلكتروني
    if(!empty($site_email) && !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
        $errors['site_email'] = 'البريد الإلكتروني غير صالح';
    }
    
    // إذا لم تكن هناك أخطاء
    if(empty($errors)) {
        // إعداد بيانات الإعدادات
        $settings_data = array(
            'site_name' => $site_name,
            'site_description' => $site_description,
            'site_keywords' => $site_keywords,
            'site_email' => $site_email,
            'site_phone' => $site_phone,
            'site_address' => $site_address,
            'site_facebook' => $site_facebook,
            'site_twitter' => $site_twitter,
            'site_instagram' => $site_instagram,
            'site_youtube' => $site_youtube,
            'site_whatsapp' => $site_whatsapp,
            'site_footer_text' => $site_footer_text,
            'site_analytics_code' => $site_analytics_code,
            'site_custom_css' => $site_custom_css,
            'site_custom_js' => $site_custom_js
        );
        
        // معالجة شعار الموقع
        if(isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
            $logo = upload_file($_FILES['site_logo'], '../assets/uploads', array('jpg', 'jpeg', 'png', 'gif', 'svg'), 2097152);
            
            if($logo) {
                $settings_data['site_logo'] = $logo;
                
                // حذف الشعار القديم
                $old_settings = $setting->getSettings();
                if(!empty($old_settings['site_logo'])) {
                    delete_file($old_settings['site_logo'], '../assets/uploads');
                }
            } else {
                $errors['site_logo'] = 'حدث خطأ أثناء رفع شعار الموقع';
            }
        }
        
        // معالجة الأيقونة المفضلة
        if(isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] == 0) {
            $favicon = upload_file($_FILES['site_favicon'], '../assets/uploads', array('ico', 'png'), 1048576);
            
            if($favicon) {
                $settings_data['site_favicon'] = $favicon;
                
                // حذف الأيقونة القديمة
                $old_settings = $setting->getSettings();
                if(!empty($old_settings['site_favicon'])) {
                    delete_file($old_settings['site_favicon'], '../assets/uploads');
                }
            } else {
                $errors['site_favicon'] = 'حدث خطأ أثناء رفع الأيقونة المفضلة';
            }
        }
        
        // حفظ الإعدادات
        $result = $setting->saveSettings($settings_data);
        
        if($result) {
            set_flash_message('تم حفظ الإعدادات بنجاح', 'success');
            redirect('index.php?page=settings');
        } else {
            
            $errors['general'] = 'حدث خطأ أثناء حفظ الإعدادات';
        }
    }
}

// الحصول على الإعدادات الحالية
$settings = $setting->getSettings();

// تغيير عنوان الصفحة
$page_title = 'إعدادات الموقع';
?>

<!-- إضافة مكتبات Bootstrap وjQuery المطلوبة -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">إعدادات الموقع</h3>
    </div>
    <div class="card-body">
        <?php if(isset($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs" id="settings-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">إعدادات عامة</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">معلومات التواصل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false">وسائل التواصل الاجتماعي</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false">تحسين محركات البحث</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="advanced-tab" data-toggle="tab" href="#advanced" role="tab" aria-controls="advanced" aria-selected="false">إعدادات متقدمة</a>
                    </li>
                </ul>
                
                <div class="tab-content" id="settings-tabs-content">
                    <!-- إعدادات عامة -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_name">اسم الموقع <span class="text-danger">*</span></label>
                                    <input type="text" name="site_name" id="site_name" class="form-control <?php echo isset($errors['site_name']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($settings['site_name']) ? $settings['site_name'] : ''; ?>" required>
                                    <?php if(isset($errors['site_name'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['site_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_description">وصف الموقع</label>
                                    <textarea name="site_description" id="site_description" class="form-control" rows="3"><?php echo isset($settings['site_description']) ? $settings['site_description'] : ''; ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_footer_text">نص التذييل</label>
                                    <textarea name="site_footer_text" id="site_footer_text" class="form-control" rows="3"><?php echo isset($settings['site_footer_text']) ? $settings['site_footer_text'] : ''; ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_logo">شعار الموقع</label>
                                    <div class="custom-file">
                                        <input type="file" name="site_logo" id="site_logo" class="custom-file-input <?php echo isset($errors['site_logo']) ? 'is-invalid' : ''; ?>" accept="image/*">
                                        <label class="custom-file-label" for="site_logo">اختر شعار الموقع</label>
                                        <?php if(isset($errors['site_logo'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['site_logo']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted">الحد الأقصى لحجم الشعار: 2 ميجابايت</small>
                                    
                                    <?php if(isset($settings['site_logo']) && !empty($settings['site_logo'])): ?>
                                        <div class="mt-2" id="logo-preview-container">
                                            <img src="<?php echo upload_url($settings['site_logo']); ?>" alt="شعار الموقع" class="img-thumbnail" id="logo-preview-image" style="max-height: 100px;">
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2" id="logo-preview-container" style="display: none;">
                                            <img src="" alt="شعار الموقع" class="img-thumbnail" id="logo-preview-image" style="max-height: 100px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_favicon">الأيقونة المفضلة</label>
                                    <div class="custom-file">
                                        <input type="file" name="site_favicon" id="site_favicon" class="custom-file-input <?php echo isset($errors['site_favicon']) ? 'is-invalid' : ''; ?>" accept=".ico,.png">
                                        <label class="custom-file-label" for="site_favicon">اختر الأيقونة المفضلة</label>
                                        <?php if(isset($errors['site_favicon'])): ?>
                                            <div class="invalid-feedback"><?php echo $errors['site_favicon']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted">الحد الأقصى لحجم الأيقونة: 1 ميجابايت</small>
                                    
                                    <?php if(isset($settings['site_favicon']) && !empty($settings['site_favicon'])): ?>
                                        <div class="mt-2" id="favicon-preview-container">
                                            <img src="<?php echo upload_url($settings['site_favicon']); ?>" alt="الأيقونة المفضلة" class="img-thumbnail" id="favicon-preview-image" style="max-height: 32px;">
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2" id="favicon-preview-container" style="display: none;">
                                            <img src="" alt="الأيقونة المفضلة" class="img-thumbnail" id="favicon-preview-image" style="max-height: 32px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- معلومات التواصل -->
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_email">البريد الإلكتروني</label>
                                    <input type="email" name="site_email" id="site_email" class="form-control <?php echo isset($errors['site_email']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($settings['site_email']) ? $settings['site_email'] : ''; ?>">
                                    <?php if(isset($errors['site_email'])): ?>
                                        <div class="invalid-feedback"><?php echo $errors['site_email']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_phone">رقم الهاتف</label>
                                    <input type="text" name="site_phone" id="site_phone" class="form-control" value="<?php echo isset($settings['site_phone']) ? $settings['site_phone'] : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_address">العنوان</label>
                                    <textarea name="site_address" id="site_address" class="form-control" rows="4"><?php echo isset($settings['site_address']) ? $settings['site_address'] : ''; ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_whatsapp">رقم واتساب</label>
                                    <input type="text" name="site_whatsapp" id="site_whatsapp" class="form-control" value="<?php echo isset($settings['site_whatsapp']) ? $settings['site_whatsapp'] : ''; ?>">
                                    <small class="form-text text-muted">أدخل رقم الهاتف مع رمز الدولة، مثال: 966512345678</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- وسائل التواصل الاجتماعي -->
                    <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_facebook">فيسبوك</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                        </div>
                                        <input type="text" name="site_facebook" id="site_facebook" class="form-control" value="<?php echo isset($settings['site_facebook']) ? $settings['site_facebook'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_twitter">تويتر</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                        </div>
                                        <input type="text" name="site_twitter" id="site_twitter" class="form-control" value="<?php echo isset($settings['site_twitter']) ? $settings['site_twitter'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_instagram">انستغرام</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                        </div>
                                        <input type="text" name="site_instagram" id="site_instagram" class="form-control" value="<?php echo isset($settings['site_instagram']) ? $settings['site_instagram'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_youtube">يوتيوب</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                                        </div>
                                        <input type="text" name="site_youtube" id="site_youtube" class="form-control" value="<?php echo isset($settings['site_youtube']) ? $settings['site_youtube'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- تحسين محركات البحث -->
                    <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="site_keywords">الكلمات المفتاحية</label>
                                    <textarea name="site_keywords" id="site_keywords" class="form-control" rows="3"><?php echo isset($settings['site_keywords']) ? $settings['site_keywords'] : ''; ?></textarea>
                                    <small class="form-text text-muted">افصل بين الكلمات المفتاحية بفاصلة</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_analytics_code">كود تحليلات جوجل</label>
                                    <textarea name="site_analytics_code" id="site_analytics_code" class="form-control" rows="5"><?php echo isset($settings['site_analytics_code']) ? $settings['site_analytics_code'] : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- إعدادات متقدمة -->
                    <div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_custom_css">CSS مخصص</label>
                                    <textarea name="site_custom_css" id="site_custom_css" class="form-control" rows="10"><?php echo isset($settings['site_custom_css']) ? $settings['site_custom_css'] : ''; ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="site_custom_js">JavaScript مخصص</label>
                                    <textarea name="site_custom_js" id="site_custom_js" class="form-control" rows="10"><?php echo isset($settings['site_custom_js']) ? $settings['site_custom_js'] : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // تهيئة نظام التبويبات
        $('#settings-tabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // معاينة شعار الموقع
        $('#site_logo').on('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#logo-preview-image').attr('src', e.target.result);
                    $('#logo-preview-container').show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // معاينة الأيقونة المفضلة
        $('#site_favicon').on('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#favicon-preview-image').attr('src', e.target.result);
                    $('#favicon-preview-container').show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // عرض اسم الملف المختار
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>