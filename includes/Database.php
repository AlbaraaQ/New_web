<?php
/**
 * فئة قاعدة البيانات
 * 
 * تتعامل مع الاتصال بقاعدة البيانات وتنفيذ الاستعلامات
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    private $conn;
    private $error;
    private $stmt;
    
    /**
     * إنشاء اتصال بقاعدة البيانات
     */
    public function __construct() {
        // إعداد DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        
        // إعداد خيارات PDO
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
        
        // إنشاء كائن PDO
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            echo 'خطأ في الاتصال بقاعدة البيانات: ' . $this->error;
        }
    }
    
    /**
     * إعداد استعلام
     * 
     * @param string $query استعلام SQL
     */
    public function query($query) {
        $this->stmt = $this->conn->prepare($query);
    }
    
    /**
     * ربط القيم بالاستعلام
     * 
     * @param string $param اسم المعامل
     * @param mixed $value قيمة المعامل
     * @param mixed $type نوع المعامل (اختياري)
     */
    public function bind($param, $value, $type = null) {
        if(is_null($type)) {
            switch(true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        $this->stmt->bindValue($param, $value, $type);
    }
    
    /**
     * تنفيذ الاستعلام
     * 
     * @return boolean نجاح أو فشل التنفيذ
     */
    public function execute() {
        return $this->stmt->execute();
    }
    
    /**
     * الحصول على نتائج متعددة
     * 
     * @return array مصفوفة من النتائج
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * الحصول على نتيجة واحدة
     * 
     * @return array سجل واحد
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * الحصول على عدد الصفوف المتأثرة
     * 
     * @return int عدد الصفوف
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * الحصول على آخر معرف تم إدراجه
     * 
     * @return int المعرف
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * بدء المعاملة
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * تأكيد المعاملة
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * التراجع عن المعاملة
     */
    public function rollBack() {
        return $this->conn->rollBack();
    }
    
    /**
     * تهروب النص لمنع حقن SQL
     * 
     * @param string $string النص المراد تهريبه
     * @return string النص المهرب
     */
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
