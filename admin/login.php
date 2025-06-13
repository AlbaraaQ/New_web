<?php
/**
 * صفحة تسجيل الدخول للوحة التحكم
 */

// تضمين ملفات الإعدادات والدوال المساعدة
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/Database.php';

// بدء الجلسة
session_start();

// تضمين نموذج المستخدم
require_once '../models/User.php';
require_once '../models/Setting.php';

// إنشاء كائن المستخدم
$user = new User();
$setting = new Setting();

// التحقق من تسجيل الدخول
if($user->isLoggedIn()) {
    redirect('index.php');
}

// الحصول على إعدادات الموقع
$site_settings = $setting->getSettings();

// متغيرات الخطأ
$errors = array();
$username = '';

// معالجة نموذج تسجيل الدخول
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من البيانات
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    // التحقق من اسم المستخدم
    if(empty($username)) {
        $errors['username'] = 'يرجى إدخال اسم المستخدم';
    }
    
    // التحقق من كلمة المرور
    if(empty($password)) {
        $errors['password'] = 'يرجى إدخال كلمة المرور';
    }
    
    // إذا لم تكن هناك أخطاء
    if(empty($errors)) {
        // محاولة تسجيل الدخول
        if($user->login($username, $password)) {
            // تسجيل الدخول بنجاح
            redirect('index.php');
        } else {
            // فشل تسجيل الدخول
            $errors['login'] = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    
    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts - Cairo -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-box {
            width: 360px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .login-logo img {
            max-width: 150px;
            height: auto;
        }
        
        .login-box-msg {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }
        
        .form-control {
            height: 45px;
        }
        
        .btn-primary {
            background-color: #1E3A8A;
            border-color: #1E3A8A;
            height: 45px;
        }
        
        .btn-primary:hover {
            background-color: #152c69;
            border-color: #152c69;
        }
        
        .input-group-text {
            width: 45px;
            justify-content: center;
            background-color: #f4f6f9;
            border-left: 0;
        }
        
        .form-control {
            border-right: 0;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <?php if(!empty($site_settings['site_logo'])): ?>
                <img src="<?php echo upload_url($site_settings['site_logo']); ?>" alt="<?php echo $site_settings['site_name']; ?>">
            <?php else: ?>
                <h1><?php echo $site_settings['site_name']; ?></h1>
            <?php endif; ?>
        </div>
        
        <div class="login-box-msg">
            <h4>تسجيل الدخول إلى لوحة التحكم</h4>
            <p>أدخل اسم المستخدم وكلمة المرور للدخول</p>
        </div>
        
        <?php if(isset($errors['login'])): ?>
            <div class="alert alert-danger"><?php echo $errors['login']; ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <div class="mb-3">
                <div class="input-group">
                    <input type="text" name="username" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" placeholder="اسم المستخدم" value="<?php echo $username; ?>">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <?php if(isset($errors['username'])): ?>
                    <div class="invalid-feedback d-block"><?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <div class="input-group">
                    <input type="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" placeholder="كلمة المرور">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <?php if(isset($errors['password'])): ?>
                    <div class="invalid-feedback d-block"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block w-100">تسجيل الدخول</button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
