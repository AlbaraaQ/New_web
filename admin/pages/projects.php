<?php
/**
 * صفحة إدارة المشاريع
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/projects_errors.log');
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

// معالجة حذف المشروع
if($action == 'delete' && $id > 0) {
    // الحصول على بيانات المشروع
    $project_data = $project->getProjectById($id);
    
    if($project_data) {
        // حذف الصور المرتبطة بالمشروع
        $project_images = $project->getProjectImages($id);
        foreach($project_images as $image) {
            delete_file($image['file_name'], '../assets/uploads');
        }
        
        // حذف الصورة الرئيسية إذا كانت موجودة
        if(!empty($project_data['main_image'])) {
            delete_file($project_data['main_image'], '../assets/uploads');
        }
        
        // حذف المشروع
        if($project->deleteProject($id)) {
            set_flash_message('تم حذف المشروع بنجاح', 'success');
        } else {
            set_flash_message('حدث خطأ أثناء حذف المشروع', 'danger');
        }
    } else {
        set_flash_message('المشروع غير موجود', 'danger');
    }
    
    redirect('index.php?page=projects');
}

// معالجة إضافة أو تعديل المشروع
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من البيانات
    $name = clean_input($_POST['name']);
    $slug = clean_input($_POST['slug']);
    $client_name = clean_input($_POST['client_name']);
    $short_description = clean_input($_POST['short_description']);
    $description = $_POST['description'];
    $completion_date = clean_input($_POST['completion_date']);
    $service_id = (int)$_POST['service_id'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title']);
    $meta_description = clean_input($_POST['meta_description']);
    $meta_keywords = clean_input($_POST['meta_keywords']);
    
    // التحقق من الاسم
    if(empty($name)) {
        $errors['name'] = 'يرجى إدخال اسم المشروع';
    }
    
    // التحقق من الرابط المخصص
    if(empty($slug)) {
        $errors['slug'] = 'يرجى إدخال الرابط المخصص';
    } elseif($action == 'add' && $project->slugExists($slug)) {
        $errors['slug'] = 'الرابط المخصص موجود بالفعل';
    } elseif($action == 'edit' && $project->slugExists($slug, $id)) {
        $errors['slug'] = 'الرابط المخصص موجود بالفعل';
    }
    
    // التحقق من اسم العميل
    if(empty($client_name)) {
        $errors['client_name'] = 'يرجى إدخال اسم العميل';
    }
    
    // التحقق من الوصف المختصر
    if(empty($short_description)) {
        $errors['short_description'] = 'يرجى إدخال الوصف المختصر';
    }
    
    // التحقق من الوصف
    if(empty($description)) {
        $errors['description'] = 'يرجى إدخال وصف المشروع';
    }
    
    // التحقق من تاريخ الإنجاز
    if(empty($completion_date)) {
        $errors['completion_date'] = 'يرجى إدخال تاريخ إنجاز المشروع';
    }
    
    // التحقق من الخدمة
    if($service_id <= 0) {
        $errors['service_id'] = 'يرجى اختيار الخدمة';
    }
    
    // معالجة رفع الصورة الرئيسية
    
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads';
        
        // إنشاء المجلد إذا لم يكن موجوداً
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
    
        $main_image_name = upload_file($_FILES['main_image'], $upload_dir);
    
        if ($main_image_name) {
            $main_image = array(
                'title' => $name,
                'file_name' => $main_image_name,
                'alt_text' => $name
            );
        } else {
            $errors['main_image'] = 'حدث خطأ أثناء رفع الصورة الرئيسية.';
            error_log("فشل رفع الصورة الرئيسية: " . print_r($_FILES['main_image'], true));
        }
    }
    
    
    // إذا لم تكن هناك أخطاء
    if(empty($errors)) {
        // إعداد بيانات المشروع
        $project_data = array(
            'name' => $name,
            'slug' => $slug,
            'client_name' => $client_name,
            'short_description' => $short_description,
            'description' => $description,
            'main_image' => $main_image_name,
            'completion_date' => $completion_date,
            'service_id' => $service_id,
            'is_featured' => $is_featured,
            'is_active' => $is_active,
            'meta_title' => $meta_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords
        );
        
        // إضافة أو تعديل المشروع
        if($action == 'add') {
            // إضافة مشروع جديد
            $result = $project->addProject($project_data);
            
            if($result) {
                // معالجة صور المشروع
                if(isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $images = array();
                    $files = $_FILES['images'];
                    
                    for($i = 0; $i < count($files['name']); $i++) {
                        if($files['error'][$i] == 0) {
                            $file = array(
                                'name' => $files['name'][$i],
                                'type' => $files['type'][$i],
                                'tmp_name' => $files['tmp_name'][$i],
                                'error' => $files['error'][$i],
                                'size' => $files['size'][$i]
                            );
                            
                            $image = upload_file($file, '../assets/uploads');
                            
                            if($image) {
                                $images[] = array(
                                    'title' => $name . ' - ' . ($i + 1),
                                    'file_name' => $image,
                                    'alt_text' => $name
                                );
                            }
                        }
                    }
                    
                    if(!empty($images)) {
                        $project->addProjectImages($result, $images);
                    }
                }
                
                set_flash_message('تم إضافة المشروع بنجاح', 'success');
                redirect('index.php?page=projects');
            } else {
                $errors['general'] = 'حدث خطأ أثناء إضافة المشروع';
            }
        } elseif($action == 'edit') {
            // في حالة التعديل، إذا تم اختيار حذف الصورة الرئيسية
            if(isset($_POST['delete_main_image']) && $_POST['delete_main_image'] == 'on') {
                // حذف الملف القديم إذا كان موجوداً
                if(!empty($project_data['main_image'])) {
                    delete_file($project_data['main_image'], '../assets/uploads');
                    $project_data['main_image'] = null;
                }
            }
            
            // إذا تم رفع صورة جديدة
            if(!empty($mainImageName)) {
                // حذف الملف القديم إذا كان موجوداً
                if(!empty($project_data['main_image'])) {
                    delete_file($project_data['main_image'], '../assets/uploads');
                }
                $project_data['main_image'] = $mainImageName;
            } elseif(empty($mainImageName) && !isset($_POST['delete_main_image'])) {
                // الاحتفاظ بالصورة القديمة إذا لم يتم رفع صورة جديدة ولم يتم اختيار الحذف
                $project_data['main_image'] = $project_data['main_image'] ?? null;
            }
            
            // تعديل المشروع
            $project_data['id'] = $id;
            $result = $project->updateProject($project_data);
            
            if($result) {
                // معالجة صور المشروع
                if(isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $images = array();
                    $files = $_FILES['images'];
                    
                    for($i = 0; $i < count($files['name']); $i++) {
                        if($files['error'][$i] == 0) {
                            $file = array(
                                'name' => $files['name'][$i],
                                'type' => $files['type'][$i],
                                'tmp_name' => $files['tmp_name'][$i],
                                'error' => $files['error'][$i],
                                'size' => $files['size'][$i]
                            );
                            
                            $image = upload_file($file, '../assets/uploads');
                            
                            if($image) {
                                $images[] = array(
                                    'title' => $name . ' - ' . ($i + 1),
                                    'file_name' => $image,
                                    'alt_text' => $name
                                );
                            }
                        }
                    }
                    
                    if(!empty($images)) {
                        $project->addProjectImages($id, $images);
                    }
                }
                
                set_flash_message('تم تعديل المشروع بنجاح', 'success');
                redirect('index.php?page=projects');
            } else {
                $errors['general'] = 'حدث خطأ أثناء تعديل المشروع';
            }
        }
    }
}

// الحصول على بيانات المشروع في حالة التعديل
$project_data = array();
if($action == 'edit' && $id > 0) {
    $project_data = $project->getProjectById($id);
    
    if(!$project_data) {
        set_flash_message('المشروع غير موجود', 'danger');
        redirect('index.php?page=projects');
    }
    
    // الحصول على صور المشروع
    $project_images = $project->getProjectImages($id);
}

// الحصول على قائمة المشاريع
$projects_list = $project->getProjects();

// الحصول على قائمة الخدمات
$services_list = $service->getServices();

// تغيير عنوان الصفحة
$page_title = 'إدارة المشاريع';
if($action == 'add') {
    $page_title = 'إضافة مشروع جديد';
} elseif($action == 'edit') {
    $page_title = 'تعديل المشروع';
}
?>

<?php if($action == 'list'): ?>
    <!-- قائمة المشاريع -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">قائمة المشاريع</h3>
            <div class="card-tools">
                <a href="index.php?page=projects&action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> إضافة مشروع جديد
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المشروع</th>
                            <th>الصورة</th>
                            <th>العميل</th>
                            <th>الخدمة</th>
                            <th>تاريخ الإنجاز</th>
                            <th>مميز</th>
                            <th>نشط</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($projects_list)): ?>
                            <tr>
                                <td colspan="9" class="text-center">لا توجد مشاريع</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($projects_list as $project_item): ?>
                                <tr>
                                    <td><?php echo $project_item['id']; ?></td>
                                    <td><?php echo $project_item['name']; ?></td>
                                    <td>
                                        <?php if(!empty($project_item['main_image'])): ?>
                                            <img src="<?php echo upload_url($project_item['main_image']); ?>" alt="<?php echo $project_item['name']; ?>" style="max-height: 50px;">
                                        <?php else: ?>
                                            <span class="text-muted">بدون صورة</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $project_item['client_name']; ?></td>
                                    <td><?php echo $project_item['service_name']; ?></td>
                                    <td><?php echo format_date($project_item['completion_date']); ?></td>
                                    <td>
                                        <?php if($project_item['is_featured'] == 1): ?>
                                            <span class="badge badge-success">نعم</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">لا</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($project_item['is_active'] == 1): ?>
                                            <span class="badge badge-success">نعم</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">لا</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?page=projects&action=edit&id=<?php echo $project_item['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <a href="index.php?page=projects&action=delete&id=<?php echo $project_item['id']; ?>" class="btn btn-danger btn-sm btn-delete">
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
    <!-- نموذج إضافة أو تعديل المشروع -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?php echo $action == 'add' ? 'إضافة مشروع جديد' : 'تعديل المشروع'; ?></h3>
            <div class="card-tools">
                <a href="index.php?page=projects" class="btn btn-secondary btn-sm">
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
                            <label for="name">اسم المشروع <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($project_data['name']) ? $project_data['name'] : ''; ?>" required>
                            <?php if(isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">الرابط المخصص <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control <?php echo isset($errors['slug']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($project_data['slug']) ? $project_data['slug'] : ''; ?>" required>
                            <?php if(isset($errors['slug'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['slug']; ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">سيتم استخدام هذا الرابط في عنوان URL للمشروع</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="client_name">اسم العميل <span class="text-danger">*</span></label>
                            <input type="text" name="client_name" id="client_name" class="form-control <?php echo isset($errors['client_name']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($project_data['client_name']) ? $project_data['client_name'] : ''; ?>" required>
                            <?php if(isset($errors['client_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['client_name']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="short_description">الوصف المختصر <span class="text-danger">*</span></label>
                            <textarea name="short_description" id="short_description" class="form-control <?php echo isset($errors['short_description']) ? 'is-invalid' : ''; ?>" rows="3" required><?php echo isset($project_data['short_description']) ? $project_data['short_description'] : ''; ?></textarea>
                            <?php if(isset($errors['short_description'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['short_description']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف المشروع <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control summernote <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" required><?php echo isset($project_data['description']) ? $project_data['description'] : ''; ?></textarea>
                            <?php if(isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['description']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_title">عنوان ميتا (SEO)</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control" value="<?php echo isset($project_data['meta_title']) ? $project_data['meta_title'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_description">وصف ميتا (SEO)</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="3"><?php echo isset($project_data['meta_description']) ? $project_data['meta_description'] : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_keywords">كلمات مفتاحية (SEO)</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="<?php echo isset($project_data['meta_keywords']) ? $project_data['meta_keywords'] : ''; ?>">
                            <small class="form-text text-muted">افصل بين الكلمات المفتاحية بفاصلة</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- حقل الصورة الرئيسية -->
                        <div class="form-group">
                            <label for="main_image">الصورة الرئيسية</label>
                            <div class="custom-file">
                                <input type="file" name="main_image" id="main_image" class="custom-file-input <?php echo isset($errors['main_image']) ? 'is-invalid' : ''; ?>" accept="image/*">
                                <label class="custom-file-label" for="main_image">اختر صورة رئيسية</label>
                                <?php if(isset($errors['main_image'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['main_image']; ?></div>
                                <?php endif; ?>
                            </div>
                            <small class="form-text text-muted">الحد الأقصى لحجم الصورة: 5 ميجابايت</small>
                            
                            <?php if(isset($project_data['main_image']) && !empty($project_data['main_image'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo upload_url($project_data['main_image']); ?>" alt="الصورة الرئيسية للمشروع" class="img-thumbnail" style="max-height: 150px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="delete_main_image" id="delete_main_image">
                                        <label class="form-check-label text-danger" for="delete_main_image">
                                            حذف الصورة الرئيسية
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="service_id">الخدمة <span class="text-danger">*</span></label>
                            <select name="service_id" id="service_id" class="form-control select2 <?php echo isset($errors['service_id']) ? 'is-invalid' : ''; ?>" required>
                                <option value="">اختر الخدمة</option>
                                <?php foreach($services_list as $service_item): ?>
                                    <option value="<?php echo $service_item['id']; ?>" <?php echo (isset($project_data['service_id']) && $project_data['service_id'] == $service_item['id']) ? 'selected' : ''; ?>><?php echo $service_item['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(isset($errors['service_id'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['service_id']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="completion_date">تاريخ إنجاز المشروع <span class="text-danger">*</span></label>
                            <input type="date" name="completion_date" id="completion_date" class="form-control <?php echo isset($errors['completion_date']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($project_data['completion_date']) ? $project_data['completion_date'] : ''; ?>" required>
                            <?php if(isset($errors['completion_date'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['completion_date']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="images">صور المشروع الإضافية</label>
                            <div class="custom-file">
                                <input type="file" name="images[]" id="images" class="custom-file-input" accept="image/*" multiple>
                                <label class="custom-file-label" for="images">اختر صور</label>
                            </div>
                            <small class="form-text text-muted">يمكنك اختيار أكثر من صورة. الحد الأقصى لحجم كل صورة: 5 ميجابايت</small>
                        </div>
                        
                        <?php if(isset($project_images) && !empty($project_images)): ?>
                            <div class="form-group">
                                <label>الصور الإضافية الحالية</label>
                                <div class="row">
                                    <?php foreach($project_images as $image): ?>
                                        <div class="col-md-6 mb-2">
                                            <img src="<?php echo upload_url($image['file_name']); ?>" alt="<?php echo $image['alt_text']; ?>" class="img-thumbnail">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="<?php echo $image['id']; ?>" id="delete_image_<?php echo $image['id']; ?>">
                                                <label class="form-check-label text-danger" for="delete_image_<?php echo $image['id']; ?>">
                                                    حذف
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_featured" id="is_featured" class="custom-control-input" <?php echo (isset($project_data['is_featured']) && $project_data['is_featured'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="is_featured">مشروع مميز</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" <?php echo (!isset($project_data['is_active']) || $project_data['is_active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="is_active">نشط</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $action == 'add' ? 'إضافة' : 'حفظ التغييرات'; ?>
                    </button>
                    <a href="index.php?page=projects" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function() {
    // عرض اسم الملف المختار
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
    
    // توليد الرابط المخصص تلقائياً من اسم المشروع
    $('#name').on('blur', function() {
        if($('#slug').val() == '') {
            var slug = slugify($(this).val());
            $('#slug').val(slug);
        }
    });
    
    // دالة لتحويل النص إلى رابط صالح
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // استبدل المسافات بشرطة
            .replace(/[^\w\-]+/g, '')       // احذف كل الرموز غير الكلمات
            .replace(/\-\-+/g, '-')         // استبدل الشرطات المتعددة بشرطة واحدة
            .replace(/^-+/, '')             // احذف الشرطة من البداية
            .replace(/-+$/, '');            // احذف الشرطة من النهاية
    }
});
</script>