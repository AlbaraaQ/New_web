<?php
/**
 * فئة الخدمات
 * 
 * تتعامل مع إدارة الخدمات
 */
class Service {
    private $db;
    
    /**
     * إنشاء كائن الخدمات
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * الحصول على جميع الخدمات
     * 
     * @param boolean $active_only عرض الخدمات النشطة فقط
     * @return array قائمة الخدمات
     */
    public function getServices($active_only = false) {
        if($active_only) {
            $this->db->query('SELECT * FROM services WHERE is_active = 1 ORDER BY `order` ASC');
        } else {
            $this->db->query('SELECT * FROM services ORDER BY `order` ASC');
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على الخدمات المميزة
     * 
     * @param int $limit عدد الخدمات المراد عرضها
     * @return array قائمة الخدمات المميزة
     */
    public function getFeaturedServices($limit = 3) {
        $this->db->query('SELECT * FROM services WHERE is_active = 1 AND is_featured = 1 ORDER BY `order` ASC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    public function getTotalServices() {
        $this->db->query('SELECT COUNT(*) as total FROM services WHERE is_active = 1');
        $result = $this->db->single();
        return $result['total'];
    }
    /**
     * الحصول على خدمة بواسطة المعرف
     * 
     * @param int $id معرف الخدمة
     * @return array بيانات الخدمة
     */
    public function getServiceById($id) {
        $this->db->query('SELECT * FROM services WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * الحصول على خدمة بواسطة الرابط المخصص
     * 
     * @param string $slug الرابط المخصص
     * @return array بيانات الخدمة
     */
    public function getServiceBySlug($slug) {
        $this->db->query('SELECT * FROM services WHERE slug = :slug AND is_active = 1');
        $this->db->bind(':slug', $slug);
        
        return $this->db->single();
    }
    
    /**
     * إضافة خدمة جديدة
     * 
     * @param array $data بيانات الخدمة
     * @return boolean نجاح أو فشل الإضافة
     */
    public function addService($data) {
        // التحقق من وجود الرابط المخصص
        if($this->slugExists($data['slug'])) {
            return false;
        }
        
        $this->db->query('INSERT INTO services (name, slug, short_description, description, image, is_featured, `order`, is_active, meta_title, meta_description, meta_keywords) VALUES (:name, :slug, :short_description, :description, :image, :is_featured, :order, :is_active, :meta_title, :meta_description, :meta_keywords)');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':short_description', $data['short_description']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':is_featured', $data['is_featured']);
        $this->db->bind(':order', $data['order']);
        $this->db->bind(':is_active', $data['is_active']);
        $this->db->bind(':meta_title', $data['meta_title']);
        $this->db->bind(':meta_description', $data['meta_description']);
        $this->db->bind(':meta_keywords', $data['meta_keywords']);
        
        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * تعديل خدمة
     * 
     * @param array $data بيانات الخدمة
     * @return boolean نجاح أو فشل التعديل
     */
    public function updateService($data) {
        // التحقق من وجود الرابط المخصص لخدمة أخرى
        if($this->slugExists($data['slug'], $data['id'])) {
            return false;
        }
        
        $this->db->query('UPDATE services SET name = :name, slug = :slug, short_description = :short_description, description = :description, is_featured = :is_featured, `order` = :order, is_active = :is_active, meta_title = :meta_title, meta_description = :meta_description, meta_keywords = :meta_keywords WHERE id = :id');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':short_description', $data['short_description']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':is_featured', $data['is_featured']);
        $this->db->bind(':order', $data['order']);
        $this->db->bind(':is_active', $data['is_active']);
        $this->db->bind(':meta_title', $data['meta_title']);
        $this->db->bind(':meta_description', $data['meta_description']);
        $this->db->bind(':meta_keywords', $data['meta_keywords']);
        $this->db->bind(':id', $data['id']);
        
        return $this->db->execute();
    }
    
    /**
     * تحديث صورة الخدمة
     * 
     * @param int $id معرف الخدمة
     * @param string $image مسار الصورة
     * @return boolean نجاح أو فشل التحديث
     */
    public function updateServiceImage($id, $image) {
        $this->db->query('UPDATE services SET image = :image WHERE id = :id');
        
        $this->db->bind(':image', $image);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * حذف خدمة
     * 
     * @param int $id معرف الخدمة
     * @return boolean نجاح أو فشل الحذف
     */
    public function deleteService($id) {
        // حذف الصور المرتبطة بالخدمة
        $this->db->query('DELETE FROM images WHERE service_id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();
        
        // حذف الخدمة
        $this->db->query('DELETE FROM services WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * التحقق من وجود الرابط المخصص
     * 
     * @param string $slug الرابط المخصص
     * @param int $exclude_id معرف الخدمة المستثناة (اختياري)
     * @return boolean وجود الرابط المخصص
     */
    public function slugExists($slug, $exclude_id = null) {
        if($exclude_id) {
            $this->db->query('SELECT * FROM services WHERE slug = :slug AND id != :exclude_id');
            $this->db->bind(':exclude_id', $exclude_id);
        } else {
            $this->db->query('SELECT * FROM services WHERE slug = :slug');
        }
        
        $this->db->bind(':slug', $slug);
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    /**
     * الحصول على عدد الخدمات
     * 
     * @param boolean $active_only عرض الخدمات النشطة فقط
     * @return int عدد الخدمات
     */
    public function getServicesCount($active_only = false) {
        if($active_only) {
            $this->db->query('SELECT COUNT(*) as count FROM services WHERE is_active = 1');
        } else {
            $this->db->query('SELECT COUNT(*) as count FROM services');
        }
        
        $result = $this->db->single();
        return $result['count'];
    }
    
    /**
     * إضافة صور للخدمة
     * 
     * @param int $service_id معرف الخدمة
     * @param array $images بيانات الصور
     * @return boolean نجاح أو فشل الإضافة
     */
    public function addServiceImages($service_id, $images) {
        $success = true;
        
        foreach($images as $image) {
            $this->db->query('INSERT INTO images (title, file_name, alt_text, service_id) VALUES (:title, :file_name, :alt_text, :service_id)');
            
            $this->db->bind(':title', $image['title']);
            $this->db->bind(':file_name', $image['file_name']);
            $this->db->bind(':alt_text', $image['alt_text']);
            $this->db->bind(':service_id', $service_id);
            
            if(!$this->db->execute()) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * الحصول على صور الخدمة
     * 
     * @param int $service_id معرف الخدمة
     * @return array قائمة الصور
     */
    public function getServiceImages($service_id) {
        $this->db->query('SELECT * FROM images WHERE service_id = :service_id');
        $this->db->bind(':service_id', $service_id);
        
        return $this->db->resultSet();
    }
    
    /**
     * حذف صورة
     * 
     * @param int $image_id معرف الصورة
     * @return boolean نجاح أو فشل الحذف
     */
    public function deleteImage($image_id) {
        $this->db->query('DELETE FROM images WHERE id = :id');
        $this->db->bind(':id', $image_id);
        
        return $this->db->execute();
    }
}
