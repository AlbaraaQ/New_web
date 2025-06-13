<?php
/**
 * فئة التقييمات
 * 
 * تتعامل مع إدارة تقييمات العملاء
 */
class Testimonial {
    private $db;
    
    /**
     * إنشاء كائن التقييمات
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * الحصول على جميع التقييمات
     * 
     * @param boolean $approved_only عرض التقييمات المعتمدة فقط
     * @return array قائمة التقييمات
     */
    public function getTestimonials($approved_only = false) {
        if($approved_only) {
            $this->db->query('SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC');
        } else {
            $this->db->query('SELECT * FROM testimonials ORDER BY created_at DESC');
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على تقييمات مميزة
     * 
     * @param int $limit عدد التقييمات المراد عرضها
     * @return array قائمة التقييمات
     */
    public function getFeaturedTestimonials($limit = 3) {
        $this->db->query('SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY rating DESC, created_at DESC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    public function getTotalClients() {
        $this->db->query('SELECT COUNT(DISTINCT client_name) as total FROM testimonials WHERE is_approved = 1');
        $result = $this->db->single();
        return $result['total'];
    }
    /**
     * الحصول على تقييم بواسطة المعرف
     * 
     * @param int $id معرف التقييم
     * @return array بيانات التقييم
     */
    public function getTestimonialById($id) {
        $this->db->query('SELECT * FROM testimonials WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * إضافة تقييم جديد
     * 
     * @param array $data بيانات التقييم
     * @return boolean نجاح أو فشل الإضافة
     */
    public function addTestimonial($data) {
        $this->db->query('INSERT INTO testimonials (client_name, client_position, client_image, rating, content, is_approved) VALUES (:client_name, :client_position, :client_image, :rating, :content, :is_approved)');
        
        $this->db->bind(':client_name', $data['client_name']);
        $this->db->bind(':client_position', $data['client_position']);
        $this->db->bind(':client_image', $data['client_image']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':is_approved', $data['is_approved']);
        
        return $this->db->execute();
    }
    
    /**
     * تعديل تقييم
     * 
     * @param array $data بيانات التقييم
     * @return boolean نجاح أو فشل التعديل
     */
    public function updateTestimonial($data) {
        $this->db->query('UPDATE testimonials SET client_name = :client_name, client_position = :client_position, rating = :rating, content = :content, is_approved = :is_approved WHERE id = :id');
        
        $this->db->bind(':client_name', $data['client_name']);
        $this->db->bind(':client_position', $data['client_position']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':is_approved', $data['is_approved']);
        $this->db->bind(':id', $data['id']);
        
        return $this->db->execute();
    }
    
    /**
     * تحديث صورة العميل
     * 
     * @param int $id معرف التقييم
     * @param string $image مسار الصورة
     * @return boolean نجاح أو فشل التحديث
     */
    public function updateClientImage($id, $image) {
        $this->db->query('UPDATE testimonials SET client_image = :client_image WHERE id = :id');
        
        $this->db->bind(':client_image', $image);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * تغيير حالة الموافقة على التقييم
     * 
     * @param int $id معرف التقييم
     * @param boolean $status حالة الموافقة
     * @return boolean نجاح أو فشل التغيير
     */
    public function approveTestimonial($id, $status) {
        $this->db->query('UPDATE testimonials SET is_approved = :is_approved WHERE id = :id');
        
        $this->db->bind(':is_approved', $status);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    public function approveTestimonial1($id, $status = 1) {
        $this->db->query('UPDATE testimonials SET is_approved = :is_approved WHERE id = :id');
        
        $this->db->bind(':is_approved', $status);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    /**
     * حذف تقييم
     * 
     * @param int $id معرف التقييم
     * @return boolean نجاح أو فشل الحذف
     */
    public function deleteTestimonial($id) {
        $this->db->query('DELETE FROM testimonials WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * الحصول على عدد التقييمات
     * 
     * @param boolean $approved_only عرض التقييمات المعتمدة فقط
     * @return int عدد التقييمات
     */
    public function getTestimonialsCount($approved_only = false) {
        if($approved_only) {
            $this->db->query('SELECT COUNT(*) as count FROM testimonials WHERE is_approved = 1');
        } else {
            $this->db->query('SELECT COUNT(*) as count FROM testimonials');
        }
        
        $result = $this->db->single();
        return $result['count'];
    }
    
    /**
     * الحصول على عدد التقييمات الجديدة (غير المعتمدة)
     * 
     * @return int عدد التقييمات الجديدة
     */
    public function getNewTestimonialsCount() {
        $this->db->query('SELECT COUNT(*) as count FROM testimonials WHERE is_approved = 0');
        
        $result = $this->db->single();
        return $result['count'];
    }
    
    /**
     * الحصول على متوسط التقييمات
     * 
     * @return float متوسط التقييمات
     */
    public function getAverageRating() {
        $this->db->query('SELECT AVG(rating) as avg_rating FROM testimonials WHERE is_approved = 1');
        
        $result = $this->db->single();
        return round($result['avg_rating'], 1);
    }
}
