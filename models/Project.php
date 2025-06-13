<?php
/**
 * فئة المشاريع
 * 
 * تتعامل مع إدارة المشاريع
 */
class Project {
    private $db;
    
    /**
     * إنشاء كائن المشاريع
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * الحصول على جميع المشاريع
     * 
     * @param boolean $active_only عرض المشاريع النشطة فقط
     * @return array قائمة المشاريع
     */
    public function getProjects($active_only = false) {
        if($active_only) {
            $this->db->query('SELECT p.*, s.name as service_name FROM projects p 
                              LEFT JOIN services s ON p.service_id = s.id 
                              WHERE p.is_active = 1 
                              ORDER BY p.completion_date DESC');
        } else {
            $this->db->query('SELECT p.*, s.name as service_name FROM projects p 
                              LEFT JOIN services s ON p.service_id = s.id 
                              ORDER BY p.completion_date DESC');
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على المشاريع المميزة
     * 
     * @param int $limit عدد المشاريع المراد عرضها
     * @return array قائمة المشاريع المميزة
     */
    public function getFeaturedProjects($limit = 3) {
        $this->db->query('SELECT p.*, s.name as service_name FROM projects p 
                          LEFT JOIN services s ON p.service_id = s.id 
                          WHERE p.is_active = 1 AND p.is_featured = 1 
                          ORDER BY p.completion_date DESC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    public function getTotalProjects() {
        $this->db->query('SELECT COUNT(*) as total FROM projects WHERE is_active = 1');
        $result = $this->db->single();
        return $result['total'];
    }
    
    /**
     * الحصول على مشروع بواسطة المعرف
     * 
     * @param int $id معرف المشروع
     * @return array بيانات المشروع
     */
    public function getProjectById($id) {
        $this->db->query('SELECT p.*, s.name as service_name FROM projects p 
                          LEFT JOIN services s ON p.service_id = s.id 
                          WHERE p.id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    /**
     * الحصول على مشروع بواسطة الرابط المخصص
     * 
     * @param string $slug الرابط المخصص
     * @return array بيانات المشروع
     */
    public function getProjectBySlug($slug) {
        $this->db->query('SELECT p.*, s.name as service_name FROM projects p 
                          LEFT JOIN services s ON p.service_id = s.id 
                          WHERE p.slug = :slug AND p.is_active = 1');
        $this->db->bind(':slug', $slug);
        
        return $this->db->single();
    }
    
    /**
     * الحصول على مشاريع بواسطة الخدمة
     * 
     * @param int $service_id معرف الخدمة
     * @param boolean $active_only عرض المشاريع النشطة فقط
     * @return array قائمة المشاريع
     */
    public function getProjectsByService($service_id, $active_only = true) {
        if($active_only) {
            $this->db->query('SELECT * FROM projects WHERE service_id = :service_id AND is_active = 1 ORDER BY completion_date DESC');
        } else {
            $this->db->query('SELECT * FROM projects WHERE service_id = :service_id ORDER BY completion_date DESC');
        }
        
        $this->db->bind(':service_id', $service_id);
        
        return $this->db->resultSet();
    }
    
    /**
     * إضافة مشروع جديد
     * 
     * @param array $data بيانات المشروع
     * @return boolean نجاح أو فشل الإضافة
     */
    public function addProject($data) {
        // التحقق من وجود الرابط المخصص
        if($this->slugExists($data['slug'])) {
            return false;
        }
        
        $this->db->query('INSERT INTO projects 
            (name, slug, client_name, short_description, description, main_image, completion_date, service_id, is_featured, is_active, meta_title, meta_description, meta_keywords) 
            VALUES 
            (:name, :slug, :client_name, :short_description, :description, :main_image, :completion_date, :service_id, :is_featured, :is_active, :meta_title, :meta_description, :meta_keywords)');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':client_name', $data['client_name']);
        $this->db->bind(':short_description', $data['short_description']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':main_image', $data['main_image'] ?? null);
        $this->db->bind(':completion_date', $data['completion_date']);
        $this->db->bind(':service_id', $data['service_id']);
        $this->db->bind(':is_featured', $data['is_featured']);
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
     * تعديل مشروع
     * 
     * @param array $data بيانات المشروع
     * @return boolean نجاح أو فشل التعديل
     */
    public function updateProject($data) {
        // التحقق من وجود الرابط المخصص
        if($this->slugExists($data['slug'], $data['id'])) {
            return false;
        }
        
        $this->db->query('UPDATE projects SET 
            name = :name,
            slug = :slug,
            client_name = :client_name,
            short_description = :short_description,
            description = :description,
            main_image = :main_image,
            completion_date = :completion_date,
            service_id = :service_id,
            is_featured = :is_featured,
            is_active = :is_active,
            meta_title = :meta_title,
            meta_description = :meta_description,
            meta_keywords = :meta_keywords
            WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':client_name', $data['client_name']);
        $this->db->bind(':short_description', $data['short_description']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':main_image', $data['main_image'] ?? null);
        $this->db->bind(':completion_date', $data['completion_date']);
        $this->db->bind(':service_id', $data['service_id']);
        $this->db->bind(':is_featured', $data['is_featured']);
        $this->db->bind(':is_active', $data['is_active']);
        $this->db->bind(':meta_title', $data['meta_title']);
        $this->db->bind(':meta_description', $data['meta_description']);
        $this->db->bind(':meta_keywords', $data['meta_keywords']);
        
        return $this->db->execute();
    }
    
    /**
     * حذف مشروع
     * 
     * @param int $id معرف المشروع
     * @return boolean نجاح أو فشل الحذف
     */
    public function deleteProject($id) {
        // حذف الصور المرتبطة بالمشروع
        $this->db->query('DELETE FROM images WHERE project_id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();
        
        // حذف المشروع
        $this->db->query('DELETE FROM projects WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    /**
     * التحقق من وجود الرابط المخصص
     * 
     * @param string $slug الرابط المخصص
     * @param int $exclude_id معرف المشروع المستثنى (اختياري)
     * @return boolean وجود الرابط المخصص
     */
    public function slugExists($slug, $exclude_id = null) {
        if($exclude_id) {
            $this->db->query('SELECT * FROM projects WHERE slug = :slug AND id != :exclude_id');
            $this->db->bind(':exclude_id', $exclude_id);
        } else {
            $this->db->query('SELECT * FROM projects WHERE slug = :slug');
        }
        
        $this->db->bind(':slug', $slug);
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    /**
     * الحصول على عدد المشاريع
     * 
     * @param boolean $active_only عرض المشاريع النشطة فقط
     * @return int عدد المشاريع
     */
    public function getProjectsCount($active_only = false) {
        if($active_only) {
            $this->db->query('SELECT COUNT(*) as count FROM projects WHERE is_active = 1');
        } else {
            $this->db->query('SELECT COUNT(*) as count FROM projects');
        }
        
        $result = $this->db->single();
        return $result['count'];
    }
    
    /**
     * إضافة صور للمشروع
     * 
     * @param int $project_id معرف المشروع
     * @param array $images بيانات الصور
     * @return boolean نجاح أو فشل الإضافة
     */
    public function addProjectImages($project_id, $images) {
        $success = true;
        
        foreach($images as $image) {
            $this->db->query('INSERT INTO images (title, file_name, alt_text, project_id) VALUES (:title, :file_name, :alt_text, :project_id)');
            
            $this->db->bind(':title', $image['title']);
            $this->db->bind(':file_name', $image['file_name']);
            $this->db->bind(':alt_text', $image['alt_text']);
            $this->db->bind(':project_id', $project_id);
            
            if(!$this->db->execute()) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * الحصول على صور المشروع
     * 
     * @param int $project_id معرف المشروع
     * @return array قائمة الصور
     */
    public function getProjectImages($project_id) {
        $this->db->query('SELECT * FROM images WHERE project_id = :project_id');
        $this->db->bind(':project_id', $project_id);
        
        return $this->db->resultSet();
    }
    /**
 * الحصول على تفاصيل المشروع مع المشاريع ذات الصلة
 * 
 * @param int|string $identifier معرف المشروع أو الرابط المخصص
 * @param bool $bySlug تحديد ما إذا كان البحث بالرابط المخصص
 * @param int $relatedLimit عدد المشاريع ذات الصلة المطلوبة
 * @return array بيانات المشروع مع المشاريع ذات الصلة
 */
/**
 * الحصول على تفاصيل المشروع مع الصور والمشاريع ذات الصلة (مُحسنة)
 * 
 * @param int|string $identifier معرف المشروع أو الرابط المخصص
 * @param bool $bySlug تحديد ما إذا كان البحث بالرابط المخصص
 * @param array $options خيارات إضافية:
 *                      - 'related_limit' => عدد المشاريع ذات الصلة (افتراضي: 3)
 *                      - 'images_order' => ترتيب الصور (ASC أو DESC، افتراضي: DESC)
 *                      - 'include_inactive' => تضمين المشاريع غير النشطة (افتراضي: false)
 * @return array|null بيانات المشروع مع الصور والمشاريع ذات الصلة
 */
public function getProjectDetailsWithRelated($identifier, $bySlug = false, $options = []) {
    // دمج الخيارات مع القيم الافتراضية
    $defaults = [
        'related_limit' => 3,
        'images_order' => 'DESC',
        'include_inactive' => false
    ];
    $options = array_merge($defaults, $options);

    // الحصول على بيانات المشروع الأساسية
    $project = $bySlug ? $this->getProjectBySlug($identifier) : $this->getProjectById($identifier);
    
    if(!$project || (!$project['is_active'] && !$options['include_inactive'])) {
        return null;
    }
    
    // الحصول على صور المشروع مع الخيارات المحددة
    $project['images'] = $this->getProjectImages($project['id'], $options['images_order']);
    
    // الحصول على المشاريع ذات الصلة إذا كان المشروع نشطاً
    
    
    // إضافة معلومات إضافية
    $project['total_images'] = count($project['images']);
    $project['main_image_url'] = !empty($project['main_image']) ? 
        'uploads/projects/' . $project['main_image'] : 
        'assets/images/default-project.jpg';
    
    return $project;
}
}
