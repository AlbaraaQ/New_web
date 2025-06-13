<?php
/**
 * صفحة إدارة الخدمات
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

// معالجة حذف الخدمة
if($action == 'delete' && $id > 0) {
    // الحصول على بيانات الخدمة
    $service_data = $service->getServiceById($id);
    
    if($service_data) {
        // حذف صورة الخدمة
        if(!empty($service_data['image'])) {
            delete_file($service_data['image'], '../assets/uploads');
        }
        
        // حذف الخدمة
        if($service->deleteService($id)) {
            set_flash_message('تم حذف الخدمة بنجاح', 'success');
        } else {
            set_flash_message('حدث خطأ أثناء حذف الخدمة', 'danger');
        }
    } else {
        set_flash_message('الخدمة غير موجودة', 'danger');
    }
    
    redirect('index.php?page=services');
}

// معالجة إضافة أو تعديل الخدمة
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من البيانات
    $name = clean_input($_POST['name']);
    $slug = clean_input($_POST['slug']);
    $short_description = clean_input($_POST['short_description']);
    $description = $_POST['description'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $order = (int)$_POST['order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title']);
    $meta_description = clean_input($_POST['meta_description']);
    $meta_keywords = clean_input($_POST['meta_keywords']);
    
    // التحقق من الاسم
    if(empty($name)) {
        $errors['name'] = 'يرجى إدخال اسم الخدمة';
    }
    
    // التحقق من الرابط المخصص
    if(empty($slug)) {
        $errors['slug'] = 'يرجى إدخال الرابط المخصص';
    } elseif($action == 'add' && $service->slugExists($slug)) {
        $errors['slug'] = 'الرابط المخصص موجود بالفعل';
    } elseif($action == 'edit' && $service->slugExists($slug, $id)) {
        $errors['slug'] = 'الرابط المخصص موجود بالفعل';
    }
    
    // التحقق من الوصف المختصر
    if(empty($short_description)) {
        $errors['short_description'] = 'يرجى إدخال الوصف المختصر';
    }
    
    // التحقق من الوصف
    if(empty($description)) {
        $errors['description'] = 'يرجى إدخال وصف الخدمة';
    }
    
    // إذا لم تكن هناك أخطاء
    if(empty($errors)) {
        // إعداد بيانات الخدمة
        $service_data = array(
            'name' => $name,
            'slug' => $slug,
            'short_description' => $short_description,
            'description' => $description,
            'is_featured' => $is_featured,
            'order' => $order,
            'is_active' => $is_active,
            'meta_title' => $meta_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords
        );
        
        // معالجة الصورة
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = upload_file($_FILES['image'], '../assets/uploads', array('jpg', 'jpeg', 'png', 'gif'), 5242880);
            
            if($image) {
                $service_data['image'] = $image;
                
                // حذف الصورة القديمة في حالة التعديل
                if($action == 'edit' && !empty($service_data['image'])) {
                    $old_service = $service->getServiceById($id);
                    if(!empty($old_service['image'])) {
                        delete_file($old_service['image'], '../assets/uploads');
                    }
                }
            } else {
                $errors['image'] = 'حدث خطأ أثناء رفع الصورة';
            }
        }
        
        // إضافة أو تعديل الخدمة
        if($action == 'add') {
            // إضافة خدمة جديدة
            $result = $service->addService($service_data);
            
            if($result) {
                set_flash_message('تم إضافة الخدمة بنجاح', 'success');
                redirect('index.php?page=services');
            } else {
                $errors['general'] = 'حدث خطأ أثناء إضافة الخدمة';
            }
        } elseif($action == 'edit') {
            // تعديل الخدمة
            $service_data['id'] = $id;
            $result = $service->updateService($service_data);
            
            if($result) {
                set_flash_message('تم تعديل الخدمة بنجاح', 'success');
                redirect('index.php?page=services');
            } else {
                $errors['general'] = 'حدث خطأ أثناء تعديل الخدمة';
            }
        }
    }
}

// الحصول على بيانات الخدمة في حالة التعديل
$service_data = array();
if($action == 'edit' && $id > 0) {
    $service_data = $service->getServiceById($id);
    
    if(!$service_data) {
        set_flash_message('الخدمة غير موجودة', 'danger');
        redirect('index.php?page=services');
    }
}

// الحصول على قائمة الخدمات
$services_list = $service->getServices();

// تغيير عنوان الصفحة
$page_title = 'إدارة الخدمات';
if($action == 'add') {
    $page_title = 'إضافة خدمة جديدة';
} elseif($action == 'edit') {
    $page_title = 'تعديل الخدمة';
}
?>

<?php if($action == 'list'): ?>
    <!-- قائمة الخدمات -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">قائمة الخدمات</h3>
            <div class="card-tools">
                <a href="index.php?page=services&action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> إضافة خدمة جديدة
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
                            <th>اسم الخدمة</th>
                            <th>الوصف المختصر</th>
                            <th>الترتيب</th>
                            <th>مميزة</th>
                            <th>نشطة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($services_list)): ?>
                            <tr>
                                <td colspan="8" class="text-center">لا توجد خدمات</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($services_list as $service_item): ?>
                                <tr>
                                    <td><?php echo $service_item['id']; ?></td>
                                    <td>
                                        <?php if(!empty($service_item['image'])): ?>
                                            <img src="<?php echo upload_url($service_item['image']); ?>" alt="<?php echo $service_item['name']; ?>" class="img-thumbnail" width="50">
                                        <?php else: ?>
                                            <img src="assets/img/no-image.png" alt="لا توجد صورة" class="img-thumbnail" width="50">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $service_item['name']; ?></td>
                                    <td><?php echo truncate_text($service_item['short_description'], 50); ?></td>
                                    <td><?php echo $service_item['order']; ?></td>
                                    <td>
                                        <?php if($service_item['is_featured'] == 1): ?>
                                            <span class="badge badge-success">نعم</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">لا</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($service_item['is_active'] == 1): ?>
                                            <span class="badge badge-success">نعم</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">لا</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?page=services&action=edit&id=<?php echo $service_item['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <a href="index.php?page=services&action=delete&id=<?php echo $service_item['id']; ?>" class="btn btn-danger btn-sm btn-delete">
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
    <!-- نموذج إضافة أو تعديل الخدمة -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?php echo $action == 'add' ? 'إضافة خدمة جديدة' : 'تعديل الخدمة'; ?></h3>
            <div class="card-tools">
                <a href="index.php?page=services" class="btn btn-secondary btn-sm">
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
                            <label for="name">اسم الخدمة <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($service_data['name']) ? $service_data['name'] : ''; ?>" required>
                            <?php if(isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">الرابط المخصص <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control <?php echo isset($errors['slug']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($service_data['slug']) ? $service_data['slug'] : ''; ?>" required>
                            <?php if(isset($errors['slug'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['slug']; ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">سيتم استخدام هذا الرابط في عنوان URL للخدمة</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="short_description">الوصف المختصر <span class="text-danger">*</span></label>
                            <textarea name="short_description" id="short_description" class="form-control <?php echo isset($errors['short_description']) ? 'is-invalid' : ''; ?>" rows="3" required><?php echo isset($service_data['short_description']) ? $service_data['short_description'] : ''; ?></textarea>
                            <?php if(isset($errors['short_description'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['short_description']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف الخدمة <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control summernote <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" required><?php echo isset($service_data['description']) ? $service_data['description'] : ''; ?></textarea>
                            <?php if(isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_title">عنوان ميتا (SEO)</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control" value="<?php echo isset($service_data['meta_title']) ? $service_data['meta_title'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_description">وصف ميتا (SEO)</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="3"><?php echo isset($service_data['meta_description']) ? $service_data['meta_description'] : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_keywords">كلمات مفتاحية (SEO)</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="<?php echo isset($service_data['meta_keywords']) ? $service_data['meta_keywords'] : ''; ?>">
                            <small class="form-text text-muted">افصل بين الكلمات المفتاحية بفاصلة</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="image">صورة الخدمة</label>
                            <div class="custom-file">
                                <input type="file" name="image" id="image" class="custom-file-input <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" accept="image/*">
                                <label class="custom-file-label" for="image">اختر صورة</label>
                                <?php if(isset($errors['image'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['image']; ?></div>
                                <?php endif; ?>
                            </div>
                            <small class="form-text text-muted">الحد الأقصى لحجم الصورة: 5 ميجابايت</small>
                            
                            <?php if(isset($service_data['image']) && !empty($service_data['image'])): ?>
                                <div class="mt-2" id="preview-container">
                                    <img src="<?php echo upload_url($service_data['image']); ?>" alt="<?php echo $service_data['name']; ?>" class="img-thumbnail" id="preview-image">
                                </div>
                            <?php else: ?>
                                <div class="mt-2" id="preview-container" style="display: none;">
                                    <img src="" alt="معاينة الصورة" class="img-thumbnail" id="preview-image">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="order">الترتيب</label>
                            <input type="number" name="order" id="order" class="form-control" value="<?php echo isset($service_data['order']) ? $service_data['order'] : 0; ?>" min="0">
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_featured" id="is_featured" class="custom-control-input" <?php echo (isset($service_data['is_featured']) && $service_data['is_featured'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="is_featured">خدمة مميزة</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" <?php echo (!isset($service_data['is_active']) || $service_data['is_active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="is_active">نشط</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $action == 'add' ? 'إضافة' : 'حفظ التغييرات'; ?>
                    </button>
                    <a href="index.php?page=services" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
