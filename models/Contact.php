<?php
/**
 * فئة التواصل
 * 
 * تتعامل مع إدارة طلبات التواصل
 */
class Contact {
    private $db;
    
    /**
     * إنشاء كائن التواصل
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * الحصول على جميع طلبات التواصل
     * 
     * @return array قائمة طلبات التواصل
     */
    public function getContacts() {
        $this->db->query('SELECT * FROM contacts ORDER BY created_at DESC');
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على طلب تواصل بواسطة المعرف
     * 
     * @param int $id معرف طلب التواصل
     * @return array بيانات طلب التواصل
     */
    public function getContactById($id) {
        $this->db->query('SELECT * FROM contacts WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * إضافة طلب تواصل جديد
     * 
     * @param array $data بيانات طلب التواصل
     * @return boolean نجاح أو فشل الإضافة
     */
    public function addContact($data) {
        $this->db->query('INSERT INTO contacts (name, phone, email, message) VALUES (:name, :phone, :email, :message)');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':message', $data['message']);
        
        return $this->db->execute();
    }
    
    /**
     * تحديث حالة القراءة لطلب التواصل
     * 
     * @param int $id معرف طلب التواصل
     * @param boolean $status حالة القراءة
     * @return boolean نجاح أو فشل التحديث
     */
    public function markAsRead($id, $status = true) {
        $this->db->query('UPDATE contacts SET is_read = :is_read WHERE id = :id');
        
        $this->db->bind(':is_read', $status);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * حذف طلب تواصل
     * 
     * @param int $id معرف طلب التواصل
     * @return boolean نجاح أو فشل الحذف
     */
    public function deleteContact($id) {
        $this->db->query('DELETE FROM contacts WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * الحصول على عدد طلبات التواصل
     * 
     * @param boolean $unread_only عرض الطلبات غير المقروءة فقط
     * @return int عدد طلبات التواصل
     */
    public function getContactsCount($unread_only = false) {
        if($unread_only) {
            $this->db->query('SELECT COUNT(*) as count FROM contacts WHERE is_read = 0');
        } else {
            $this->db->query('SELECT COUNT(*) as count FROM contacts');
        }
        
        $result = $this->db->single();
        return $result['count'];
    }
    
    /**
     * الحصول على أحدث طلبات التواصل
     * 
     * @param int $limit عدد الطلبات المراد عرضها
     * @return array قائمة أحدث طلبات التواصل
     */
    public function getLatestContacts($limit = 5) {
        $this->db->query('SELECT * FROM contacts ORDER BY created_at DESC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
}
