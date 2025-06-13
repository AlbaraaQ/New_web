<?php
/**
 * فئة المستخدم
 * 
 * تتعامل مع إدارة المستخدمين والصلاحيات
 */
class User {
    private $db;
    
    /**
     * إنشاء كائن المستخدم
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * تسجيل الدخول
     * 
     * @param string $username اسم المستخدم
     * @param string $password كلمة المرور
     * @return boolean نجاح أو فشل تسجيل الدخول
     */
    public function login($username, $password) {
        $this->db->query('SELECT * FROM users WHERE username = :username AND is_active = 1');
        $this->db->bind(':username', $username);
        
        $row = $this->db->single();
        
        if($row) {
            $hashed_password = $row['password'];
            
            if(password_verify($password, $hashed_password)) {
                // تحديث آخر تسجيل دخول
                $this->updateLastLogin($row['id']);
                
                // تخزين بيانات المستخدم في الجلسة
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * تحديث آخر تسجيل دخول
     * 
     * @param int $user_id معرف المستخدم
     */
    private function updateLastLogin($user_id) {
        $this->db->query('UPDATE users SET last_login = NOW() WHERE id = :user_id');
        $this->db->bind(':user_id', $user_id);
        $this->db->execute();
    }
    
    /**
     * التحقق من تسجيل الدخول
     * 
     * @return boolean حالة تسجيل الدخول
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * تسجيل الخروج
     */
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['full_name']);
        unset($_SESSION['email']);
        unset($_SESSION['role']);
        
        session_destroy();
    }
    
    /**
     * الحصول على جميع المستخدمين
     * 
     * @return array قائمة المستخدمين
     */
    public function getUsers() {
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على مستخدم بواسطة المعرف
     * 
     * @param int $id معرف المستخدم
     * @return array بيانات المستخدم
     */
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * إضافة مستخدم جديد
     * 
     * @param array $data بيانات المستخدم
     * @return boolean نجاح أو فشل الإضافة
     */
    public function addUser($data) {
        // التحقق من وجود اسم المستخدم أو البريد الإلكتروني
        if($this->usernameExists($data['username']) || $this->emailExists($data['email'])) {
            return false;
        }
        
        // تشفير كلمة المرور
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $this->db->query('INSERT INTO users (username, password, email, full_name, role) VALUES (:username, :password, :email, :full_name, :role)');
        
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':role', $data['role']);
        
        return $this->db->execute();
    }
    
    /**
     * تعديل مستخدم
     * 
     * @param array $data بيانات المستخدم
     * @return boolean نجاح أو فشل التعديل
     */
    public function updateUser($data) {
        // التحقق من وجود اسم المستخدم أو البريد الإلكتروني لمستخدم آخر
        if($this->usernameExists($data['username'], $data['id']) || $this->emailExists($data['email'], $data['id'])) {
            return false;
        }
        
        // إذا تم تغيير كلمة المرور
        if(!empty($data['password'])) {
            // تشفير كلمة المرور الجديدة
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $this->db->query('UPDATE users SET username = :username, password = :password, email = :email, full_name = :full_name, role = :role, is_active = :is_active WHERE id = :id');
            $this->db->bind(':password', $data['password']);
        } else {
            $this->db->query('UPDATE users SET username = :username, email = :email, full_name = :full_name, role = :role, is_active = :is_active WHERE id = :id');
        }
        
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':is_active', $data['is_active']);
        $this->db->bind(':id', $data['id']);
        
        return $this->db->execute();
    }
    
    /**
     * حذف مستخدم
     * 
     * @param int $id معرف المستخدم
     * @return boolean نجاح أو فشل الحذف
     */
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * التحقق من وجود اسم المستخدم
     * 
     * @param string $username اسم المستخدم
     * @param int $exclude_id معرف المستخدم المستثنى (اختياري)
     * @return boolean وجود اسم المستخدم
     */
    public function usernameExists($username, $exclude_id = null) {
        if($exclude_id) {
            $this->db->query('SELECT * FROM users WHERE username = :username AND id != :exclude_id');
            $this->db->bind(':exclude_id', $exclude_id);
        } else {
            $this->db->query('SELECT * FROM users WHERE username = :username');
        }
        
        $this->db->bind(':username', $username);
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    /**
     * التحقق من وجود البريد الإلكتروني
     * 
     * @param string $email البريد الإلكتروني
     * @param int $exclude_id معرف المستخدم المستثنى (اختياري)
     * @return boolean وجود البريد الإلكتروني
     */
    public function emailExists($email, $exclude_id = null) {
        if($exclude_id) {
            $this->db->query('SELECT * FROM users WHERE email = :email AND id != :exclude_id');
            $this->db->bind(':exclude_id', $exclude_id);
        } else {
            $this->db->query('SELECT * FROM users WHERE email = :email');
        }
        
        $this->db->bind(':email', $email);
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    /**
     * تغيير كلمة المرور
     * 
     * @param int $user_id معرف المستخدم
     * @param string $current_password كلمة المرور الحالية
     * @param string $new_password كلمة المرور الجديدة
     * @return boolean نجاح أو فشل التغيير
     */
    public function changePassword($user_id, $current_password, $new_password) {
        $this->db->query('SELECT password FROM users WHERE id = :user_id');
        $this->db->bind(':user_id', $user_id);
        
        $row = $this->db->single();
        
        if($row && password_verify($current_password, $row['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $this->db->query('UPDATE users SET password = :password WHERE id = :user_id');
            $this->db->bind(':password', $hashed_password);
            $this->db->bind(':user_id', $user_id);
            
            return $this->db->execute();
        }
        
        return false;
    }
    
    /**
     * التحقق من صلاحية المستخدم
     * 
     * @param string $required_role الصلاحية المطلوبة
     * @return boolean هل المستخدم يملك الصلاحية
     */
    public function hasPermission($required_role) {
        if(!$this->isLoggedIn()) {
            return false;
        }
        
        $user_role = $_SESSION['role'];
        
        // المدير لديه جميع الصلاحيات
        if($user_role == 'admin') {
            return true;
        }
        
        // التحقق من الصلاحية المطلوبة
        return $user_role == $required_role;
    }
}
