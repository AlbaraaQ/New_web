<?php
/**
 * صفحة إدارة المستخدمين
 */

// التحقق من تسجيل الدخول
if(!$user->isLoggedIn()) {
    redirect('login.php');
}

// التحقق من الصلاحيات
if(!$user->hasPermission('manage_users')) {
    set_flash_message('ليس لديك صلاحية للوصول إلى هذه الصفحة', 'danger');
    redirect('index.php?page=dashboard');
}

// تحديد العملية المطلوبة
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// متغيرات الخطأ والنجاح
$errors = array();
$success = '';

// معالجة حذف المستخدم
if($action == 'delete' && $id > 0) {
    // لا يمكن حذف المستخدم الحالي
    if($id == $_SESSION['user_id']) {
        set_flash_message('لا يمكنك حذف حسابك الحالي', 'danger');
        redirect('index.php?page=users');
    }
    
    // الحصول على بيانات المستخدم
    $user_data = $user->getUserById($id);
    
    if($user_data) {
        // حذف المستخدم
        if($user->deleteUser($id)) {
            set_flash_message('تم حذف المستخدم بنجاح', 'success');
        } else {
            set_flash_message('حدث خطأ أثناء حذف المستخدم', 'danger');
        }
    } else {
        set_flash_message('المستخدم غير موجود', 'danger');
    }
    
    redirect('index.php?page=users');
}

// معالجة إضافة أو تعديل المستخدم
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من البيانات
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = clean_input($_POST['role']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // التحقق من الاسم
    if(empty($name)) {
        $errors['name'] = 'يرجى إدخال اسم المستخدم';
    }
    
    // التحقق من البريد الإلكتروني
    if(empty($email)) {
        $errors['email'] = 'يرجى إدخال البريد الإلكتروني';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'البريد الإلكتروني غير صالح';
    } elseif($action == 'add' && $user->emailExists($email)) {
        $errors['email'] = 'البريد الإلكتروني موجود بالفعل';
    } elseif($action == 'edit' && $user->emailExists($email, $id)) {
        $errors['email'] = 'البريد الإلكتروني موجود بالفعل';
    }
    
    // التحقق من اسم المستخدم
    if(empty($username)) {
        $errors['username'] = 'يرجى إدخال اسم المستخدم';
    } elseif($action == 'add' && $user->usernameExists($username)) {
        $errors['username'] = 'اسم المستخدم موجود بالفعل';
    } elseif($action == 'edit' && $user->usernameExists($username, $id)) {
        $errors['username'] = 'اسم المستخدم موجود بالفعل';
    }
    
    // التحقق من كلمة المرور
    if($action == 'add' || !empty($password)) {
        if(empty($password)) {
            $errors['password'] = 'يرجى إدخال كلمة المرور';
        } elseif(strlen($password) < 6) {
            $errors['password'] = 'يجب أن تكون كلمة المرور 6 أحرف على الأقل';
        } elseif($password != $confirm_password) {
            $errors['confirm_password'] = 'كلمة المرور غير متطابقة';
        }
    }
    
    // التحقق من الدور
    if(empty($role)) {
        $errors['role'] = 'يرجى اختيار دور المستخدم';
    }
    
    // إذا لم تكن هناك أخطاء
    if(empty($errors)) {
        // إعداد بيانات المستخدم
        $user_data = array(
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'role' => $role,
            'is_active' => $is_active
        );
        
        // إضافة كلمة المرور إذا تم إدخالها
        if(!empty($password)) {
            $user_data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // إضافة أو تعديل المستخدم
        if($action == 'add') {
            // إضافة مستخدم جديد
            $result = $user->addUser($user_data);
            
            if($result) {
                set_flash_message('تم إضافة المستخدم بنجاح', 'success');
                redirect('index.php?page=users');
            } else {
                $errors['general'] = 'حدث خطأ أثناء إضافة المستخدم';
            }
        } elseif($action == 'edit') {
            // تعديل المستخدم
            $user_data['id'] = $id;
            $result = $user->updateUser($user_data);
            
            if($result) {
                set_flash_message('تم تعديل المستخدم بنجاح', 'success');
                redirect('index.php?page=users');
            } else {
                $errors['general'] = 'حدث خطأ أثناء تعديل المستخدم';
            }
        }
    }
}

// الحصول على بيانات المستخدم في حالة التعديل
$user_data = array();
if($action == 'edit' && $id > 0) {
    $user_data = $user->getUserById($id);
    
    if(!$user_data) {
        set_flash_message('المستخدم غير موجود', 'danger');
        redirect('index.php?page=users');
    }
}

// الحصول على قائمة المستخدمين
$users_list = $user->getUsers();

// الحصول على قائمة الأدوار
$roles = array(
    'admin' => 'مدير النظام',
    'editor' => 'محرر',
    'viewer' => 'مشاهد'
);

// تغيير عنوان الصفحة
$page_title = 'إدارة المستخدمين';
if($action == 'add') {
    $page_title = 'إضافة مستخدم جديد';
} elseif($action == 'edit') {
    $page_title = 'تعديل المستخدم';
}
?>

<?php if($action == 'list'): ?>
    <!-- قائمة المستخدمين -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">قائمة المستخدمين</h3>
            <div class="card-tools">
                <a href="index.php?page=users&action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> إضافة مستخدم جديد
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>اسم المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($users_list)): ?>
                            <tr>
                                <td colspan="8" class="text-center">لا يوجد مستخدمين</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($users_list as $user_item): ?>
                                <tr>
                                    <td><?php echo $user_item['id']; ?></td>
                                    <td><?php echo $user_item['full_name']; ?></td>
                                    <td><?php echo $user_item['username']; ?></td>
                                    <td><?php echo $user_item['email']; ?></td>
                                    <td>
                                        <?php if($user_item['role'] == 'admin'): ?>
                                            <span class="badge badge-danger">مدير النظام</span>
                                        <?php elseif($user_item['role'] == 'editor'): ?>
                                            <span class="badge badge-warning">محرر</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">مشاهد</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($user_item['is_active'] == 1): ?>
                                            <span class="badge badge-success">نشط</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">غير نشط</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo format_date($user_item['created_at']); ?></td>
                                    <td>
                                        <a href="index.php?page=users&action=edit&id=<?php echo $user_item['id']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <?php if($user_item['id'] != $_SESSION['user_id']): ?>
                                            <a href="index.php?page=users&action=delete&id=<?php echo $user_item['id']; ?>" class="btn btn-danger btn-sm btn-delete">
                                                <i class="fas fa-trash"></i> حذف
                                            </a>
                                        <?php endif; ?>
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
    <!-- نموذج إضافة أو تعديل المستخدم -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?php echo $action == 'add' ? 'إضافة مستخدم جديد' : 'تعديل المستخدم'; ?></h3>
            <div class="card-tools">
                <a href="index.php?page=users" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> العودة إلى القائمة
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if(isset($errors['general'])): ?>
                <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($user_data['name']) ? $user_data['name'] : ''; ?>" required>
                            <?php if(isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($user_data['email']) ? $user_data['email'] : ''; ?>" required>
                            <?php if(isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">اسم المستخدم <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="username" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" value="<?php echo isset($user_data['username']) ? $user_data['username'] : ''; ?>" required>
                            <?php if(isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">كلمة المرور <?php echo $action == 'add' ? '<span class="text-danger">*</span>' : ''; ?></label>
                            <input type="password" name="password" id="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" <?php echo $action == 'add' ? 'required' : ''; ?>>
                            <?php if(isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                            <?php if($action == 'edit'): ?>
                                <small class="form-text text-muted">اتركها فارغة إذا كنت لا ترغب في تغييرها</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">تأكيد كلمة المرور <?php echo $action == 'add' ? '<span class="text-danger">*</span>' : ''; ?></label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" <?php echo $action == 'add' ? 'required' : ''; ?>>
                            <?php if(isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">الدور <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" required>
                                <option value="">اختر الدور</option>
                                <?php foreach($roles as $role_key => $role_name): ?>
                                    <option value="<?php echo $role_key; ?>" <?php echo (isset($user_data['role']) && $user_data['role'] == $role_key) ? 'selected' : ''; ?>><?php echo $role_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(isset($errors['role'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['role']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" <?php echo (!isset($user_data['is_active']) || $user_data['is_active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="is_active">نشط</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $action == 'add' ? 'إضافة' : 'حفظ التغييرات'; ?>
                    </button>
                    <a href="index.php?page=users" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
