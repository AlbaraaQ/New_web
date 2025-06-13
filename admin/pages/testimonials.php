<?php
/**
 * صفحة إدارة تقييمات العملاء
 */

// التحقق من تسجيل الدخول
if(!$user->isLoggedIn()) {
    redirect('login.php');
}

// تحديد العملية المطلوبة
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// متغيرات الخطأ والنجاح
$errors = array();
$success = '';

// معالجة حذف التقييم
if($action == 'delete' && $id > 0) {
    // الحصول على بيانات التقييم
    $testimonial_data = $testimonial->getTestimonialById($id);
    
    if($testimonial_data) {
        // حذف صورة العميل
        if(!empty($testimonial_data['client_image'])) {
            delete_file($testimonial_data['client_image'], '../assets/uploads');
        }
        
        // حذف التقييم
        if($testimonial->deleteTestimonial($id)) {
            set_flash_message('تم حذف التقييم بنجاح', 'success');
        } else {
            set_flash_message('حدث خطأ أثناء حذف التقييم', 'danger');
        }
    } else {
        set_flash_message('التقييم غير موجود', 'danger');
    }
    
    redirect('index.php?page=testimonials');
}

// معالجة تغيير حالة التقييم
if($action == 'approve' && $id > 0) {
    if($testimonial->approveTestimonial1($id)) {
        set_flash_message('تم الموافقة على التقييم بنجاح', 'success');
    } else {
        set_flash_message('حدث خطأ أثناء تغيير حالة التقييم', 'danger');
    }
    
    redirect('index.php?page=testimonials');
}

// معالجة إضافة أو تعديل التقييم
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من البيانات
    $client_name = clean_input($_POST['client_name']);
    $client_position = clean_input($_POST['client_position']);
    $content = clean_input($_POST['content']);
    $rating = (int)$_POST['rating'];
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;
    
    // التحقق من اسم العميل
    if(empty($client_name)) {
        $errors['client_name'] = 'يرجى إدخال اسم العميل';
    }
    
    // التحقق من المحتوى
    if(empty($content)) {
        $errors['content'] = 'يرجى إدخال نص التقييم';
    }
    
    // التحقق من التقييم
    if($rating < 1 || $rating > 5) {
        $errors['rating'] = 'يرجى اختيار تقييم صحيح (1-5)';
    }
    
    // إذا لم تكن هناك أخطاء
    if(empty($errors)) {
        // إعداد بيانات التقييم
        $testimonial_data = array(
            'client_name' => $client_name,
            'client_position' => $client_position,
            'content' => $content,
            'rating' => $rating,
            'is_approved' => $is_approved
        );
        
        // معالجة الصورة
        if(isset($_FILES['client_image']) && $_FILES['client_image']['error'] == 0) {
            $image = upload_file($_FILES['client_image'], '../assets/uploads', array('jpg', 'jpeg', 'png', 'gif'), 2097152);
            
            if($image) {
                $testimonial_data['client_image'] = $image;
                
                // حذف الصورة القديمة في حالة التعديل
                if($action == 'edit' && !empty($testimonial_data['client_image'])) {
                    $old_testimonial = $testimonial->getTestimonialById($id);
                    if(!empty($old_testimonial['client_image'])) {
                        delete_file($old_testimonial['client_image'], '../assets/uploads');
                    }
                }
            } else {
                $errors['client_image'] = 'حدث خطأ أثناء رفع الصورة';
            }
        }
        
        // إضافة أو تعديل التقييم
        if($action == 'add') {
            // إضافة تقييم جديد
            $result = $testimonial->addTestimonial($testimonial_data);
            
            if($result) {
                set_flash_message('تم إضافة التقييم بنجاح', 'success');
                redirect('index.php?page=testimonials');
            } else {
                $errors['general'] = 'حدث خطأ أثناء إضافة التقييم';
            }
        } elseif($action == 'edit') {
            // تعديل التقييم
            $testimonial_data['id'] = $id;
            $result = $testimonial->updateTestimonial($testimonial_data);
            
            if($result) {
                set_flash_message('تم تعديل التقييم بنجاح', 'success');
                redirect('index.php?page=testimonials');
            } else {
                $errors['general'] = 'حدث خطأ أثناء تعديل التقييم';
            }
        }
    }
}

// الحصول على بيانات التقييم في حالة التعديل
$testimonial_data = array();
if($action == 'edit' && $id > 0) {
    $testimonial_data = $testimonial->getTestimonialById($id);
    
    if(!$testimonial_data) {
        set_flash_message('التقييم غير موجود', 'danger');
        redirect('index.php?page=testimonials');
    }
}

// الحصول على قائمة التقييمات
$testimonials_list = $testimonial->getTestimonials();

// تغيير عنوان الصفحة
$page_title = 'إدارة تقييمات العملاء';
if($action == 'add') {
    $page_title = 'إضافة تقييم جديد';
} elseif($action == 'edit') {
    $page_title = 'تعديل التقييم';
}
?>

<?php if($action == 'list'): ?>
    <!-- قائمة التقييمات -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">قائمة تقييمات العملاء</h3>
            <div class="card-tools">
                <a href="index.php?page=testimonials&action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> إضافة تقييم جديد
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>اسم العميل</th>
                            <th>المسمى الوظيفي</th>
                            <th>التقييم</th>
                            <th>التقييم</th>
                            <th>الحالة</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($testimonials_list)): ?>
                            <tr>
                                <td colspan="9" class="text-center">لا توجد تقييمات</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($testimonials_list as $testimonial_item): ?>
                                <tr>
                                    <td><?php echo $testimonial_item['id']; ?></td>
                                    <td>
                                        <?php if(!empty($testimonial_item['client_image'])): ?>
                                            <img src="<?php echo upload_url($testimonial_item['client_image']); ?>" alt="<?php echo $testimonial_item['client_name']; ?>" class="img-thumbnail" width="50">
                                        <?php else: ?>
                                            <img src="assets/img/user.png" alt="لا توجد صورة" class="img-thumbnail" width="50">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $testimonial_item['client_name']; ?></td>
                                    <td><?php echo $testimonial_item['client_position']; ?></td>
                                    <td><?php echo truncate_text($testimonial_item['content'], 50); ?></td>
                                    <td><?php echo rating_stars($testimonial_item['rating']); ?></td>
                                    <td>
                                        <?php if($testimonial_item['is_approved'] == 1): ?>
                                            <span class="badge badge-success">معتمد</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">بانتظار الموافقة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo format_date($testimonial_item['created_at']); ?></td>
                                    <td>
                                        <?php if($testimonial_item['is_approved'] == 0): ?>
                                            <a href="index.php?page=testimonials&action=approve&id=<?php echo $testimonial_item['id']; ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> موافقة
                                            </a>
                                        <?php endif; ?>
                                        <a href="index.php?page=testimonials&action=edit&id=<?php echo $testimonial_item['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <a href="index.php?page=testimonials&action=delete&id=<?php echo $testimonial_item['id']; ?>" class="btn btn-danger btn-sm btn-delete">
                                            <i class="fas fa-trash"></i> حذف
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php elseif($action == 'add' || $action == 'edit'): ?>
    <!-- نموذج إضافة أو تعديل التقييم -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?php echo $action == 'add' ? 'إضافة تقييم جديد' : 'تعديل التقييم'; ?></h3>
            <div class="card-tools">
                <a href="index.php?page=testimonials" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> العودة إلى القائمة
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if(isset($errors['general'])): ?>
                <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="client_name">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" id="client_name" class="form-control <?php echo isset($errors['client_name']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($testimonial_data['client_name']) ? $testimonial_data['client_name'] : ''; ?>" required>
                            <?php if(isset($errors['client_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['client_name']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="client_position">المسمى الوظيفي</label>
                            <input type="text" name="client_position" id="client_position" class="form-control" value="<?php echo isset($testimonial_data['client_position']) ? $testimonial_data['client_position'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="content">نص التقييم <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control <?php echo isset($errors['content']) ? 'is-invalid' : ''; ?>" rows="5" required><?php echo isset($testimonial_data['content']) ? $testimonial_data['content'] : ''; ?></textarea>
                            <?php if(isset($errors['content'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['content']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rating">التقييم <span class="text-danger">*</span></label>
                            <select name="rating" id="rating" class="form-control <?php echo isset($errors['rating']) ? 'is-invalid' : ''; ?>" required>
                                <option value="">اختر التقييم</option>
                                <option value="5" <?php echo (isset($testimonial_data['rating']) && $testimonial_data['rating'] == 5) ? 'selected' : ''; ?>>5 نجوم - ممتاز</option>
                                <option value="4" <?php echo (isset($testimonial_data['rating']) && $testimonial_data['rating'] == 4) ? 'selected' : ''; ?>>4 نجوم - جيد جداً</option>
                                <option value="3" <?php echo (isset($testimonial_data['rating']) && $testimonial_data['rating'] == 3) ? 'selected' : ''; ?>>3 نجوم - جيد</option>
                                <option value="2" <?php echo (isset($testimonial_data['rating']) && $testimonial_data['rating'] == 2) ? 'selected' : ''; ?>>2 نجوم - مقبول</option>
                                <option value="1" <?php echo (isset($testimonial_data['rating']) && $testimonial_data['rating'] == 1) ? 'selected' : ''; ?>>1 نجمة - ضعيف</option>
                            </select>
                            <?php if(isset($errors['rating'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['rating']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="client_image">صورة العميل</label>
                            <div class="custom-file">
                                <input type="file" name="client_image" id="client_image" class="custom-file-input <?php echo isset($errors['client_image']) ? 'is-invalid' : ''; ?>" accept="image/*">
                                <label class="custom-file-label" for="client_image">اختر صورة</label>
                                <?php if(isset($errors['client_image'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['client_image']; ?></div>
                                <?php endif; ?>
                            </div>
                            <small class="form-text text-muted">الحد الأقصى لحجم الصورة: 2 ميجابايت</small>
                            
                            <?php if(isset($testimonial_data['client_image']) && !empty($testimonial_data['client_image'])): ?>
                                <div class="mt-2" id="preview-container">
                                    <img src="<?php echo upload_url($testimonial_data['client_image']); ?>" alt="<?php echo $testimonial_data['client_name']; ?>" class="img-thumbnail" id="preview-image">
                                </div>
                            <?php else: ?>
                                <div class="mt-2" id="preview-container" style="display: none;">
                                    <img src="" alt="معاينة الصورة" class="img-thumbnail" id="preview-image">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_approved" id="is_approved" class="custom-control-input" <?php echo (isset($testimonial_data['is_approved']) && $testimonial_data['is_approved'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="is_approved">معتمد</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $action == 'add' ? 'إضافة' : 'حفظ التغييرات'; ?>
                    </button>
                    <a href="index.php?page=testimonials" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
