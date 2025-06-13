<?php
/**
 * فئة الإعدادات
 * 
 * تتعامل مع إدارة إعدادات الموقع
 */
class Setting {
    private $db;
    
    /**
     * إنشاء كائن الإعدادات
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * الحصول على جميع الإعدادات
     * 
     * @return array إعدادات الموقع
     */
    public function getSettings() {
        $this->db->query('SELECT * FROM settings WHERE id = 1');
        return $this->db->single();
    }
    
    /**
     * تحديث الإعدادات
     * 
     * @param array $data بيانات الإعدادات
     * @return boolean نجاح أو فشل التحديث
     */
    public function updateSettings($data) {
        $this->db->query('UPDATE settings SET 
            site_name = :site_name, 
            site_description = :site_description, 
            site_email = :site_email, 
            site_phone = :site_phone, 
            site_address = :site_address, 
            site_facebook = :site_facebook, 
            site_twitter = :site_twitter, 
            site_instagram = :site_instagram, 
            site_youtube = :site_youtube,
            site_whatsapp = :site_whatsapp, 
            site_keywords = :site_keywords,
            site_footer_text = :site_footer_text,
            site_analytics_code = :site_analytics_code,
            site_custom_css = :site_custom_css,
            site_custom_js = :site_custom_js
            WHERE id = 1');
        
        $this->db->bind(':site_name', $data['site_name']);
        $this->db->bind(':site_description', $data['site_description']);
        $this->db->bind(':site_email', $data['site_email']);
        $this->db->bind(':site_phone', $data['site_phone']);
        $this->db->bind(':site_address', $data['site_address']);
        $this->db->bind(':site_facebook', $data['site_facebook']);
        $this->db->bind(':site_twitter', $data['site_twitter']);
        $this->db->bind(':site_instagram', $data['site_instagram']);
        $this->db->bind(':site_youtube', $data['site_youtube']);
        $this->db->bind(':site_whatsapp', $data['site_whatsapp']);
        $this->db->bind(':site_keywords', $data['site_keywords']);
        $this->db->bind(':site_footer_text', $data['site_footer_text']);
        $this->db->bind(':site_analytics_code', $data['site_analytics_code']);
        $this->db->bind(':site_custom_css', $data['site_custom_css']);
        $this->db->bind(':site_custom_js', $data['site_custom_js']);
        
        return $this->db->execute();
    }
    
    /**
     * تحديث شعار الموقع
     * 
     * @param string $logo مسار الشعار
     * @return boolean نجاح أو فشل التحديث
     */
    public function updateLogo($logo) {
        $this->db->query('UPDATE settings SET site_logo = :site_logo WHERE id = 1');
        $this->db->bind(':site_logo', $logo);
        return $this->db->execute();
    }

    /**
     * تحديث الأيقونة المفضلة
     * 
     * @param string $favicon مسار الأيقونة
     * @return boolean نجاح أو فشل التحديث
     */
    public function updateFavicon($favicon) {
        $this->db->query('UPDATE settings SET site_favicon = :site_favicon WHERE id = 1');
        $this->db->bind(':site_favicon', $favicon);
        return $this->db->execute();
    }

    /**
     * حفظ إعدادات الموقع في قاعدة البيانات
     * 
     * @param array $settings_data مصفوفة تحتوي على بيانات الإعدادات
     * @return bool يعيد true إذا تم الحفظ بنجاح، false إذا فشل
     */
    public function saveSettings($settings_data) {
        if (!isset($this->db)) {
            throw new Exception('اتصال قاعدة البيانات غير مهيئ');
        }
    
        try {
            // تعيين القيم الافتراضية
            $defaults = [
                'site_logo' => null,
                'site_favicon' => null,
                'site_description' => null,
                'site_email' => null,
                'site_phone' => null,
                'site_address' => null,
                'site_facebook' => null,
                'site_twitter' => null,
                'site_instagram' => null,
                'site_youtube' => null,
                'site_whatsapp' => null,
                'site_keywords' => null,
                'site_footer_text' => null,
                'site_analytics_code' => null,
                'site_custom_css' => null,
                'site_custom_js' => null
            ];
    
            $data = array_merge($defaults, $settings_data);
    
            // بدء المعاملة
            $this->db->beginTransaction();
    
            // محاولة التحديث أولاً
            $update_sql = "UPDATE settings SET 
                site_name = ?, 
                site_logo = ?, 
                site_favicon = ?, 
                site_description = ?, 
                site_email = ?, 
                site_phone = ?, 
                site_address = ?, 
                site_facebook = ?, 
                site_twitter = ?, 
                site_instagram = ?, 
                site_youtube = ?, 
                site_whatsapp = ?, 
                site_keywords = ?, 
                site_footer_text = ?, 
                site_analytics_code = ?, 
                site_custom_css = ?, 
                site_custom_js = ? 
                WHERE id = 1";
    
            $this->db->query($update_sql);
            $this->db->bind(1, $data['site_name']);
            $this->db->bind(2, $data['site_logo']);
            $this->db->bind(3, $data['site_favicon']);
            $this->db->bind(4, $data['site_description']);
            $this->db->bind(5, $data['site_email']);
            $this->db->bind(6, $data['site_phone']);
            $this->db->bind(7, $data['site_address']);
            $this->db->bind(8, $data['site_facebook']);
            $this->db->bind(9, $data['site_twitter']);
            $this->db->bind(10, $data['site_instagram']);
            $this->db->bind(11, $data['site_youtube']);
            $this->db->bind(12, $data['site_whatsapp']);
            $this->db->bind(13, $data['site_keywords']);
            $this->db->bind(14, $data['site_footer_text']);
            $this->db->bind(15, $data['site_analytics_code']);
            $this->db->bind(16, $data['site_custom_css']);
            $this->db->bind(17, $data['site_custom_js']);
            
            $this->db->execute();
    
            // إذا لم يتم تحديث أي سجلات (لا يوجد سجل أساساً)
            if ($this->db->rowCount() === 0) {
                // استعلام INSERT باستخدام ? بدلاً من :named_parameters
                $insert_sql = "UPDATE INTO settings (
                    site_name, site_logo, site_favicon, site_description,
                    site_email, site_phone, site_address, site_facebook,
                    site_twitter, site_instagram, site_youtube, site_whatsapp,
                    site_keywords, site_footer_text, site_analytics_code,
                    site_custom_css, site_custom_js
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";
    
                $this->db->query($insert_sql);
                $this->db->bind(1, $data['site_name']);
                $this->db->bind(2, $data['site_logo']);
                $this->db->bind(3, $data['site_favicon']);
                $this->db->bind(4, $data['site_description']);
                $this->db->bind(5, $data['site_email']);
                $this->db->bind(6, $data['site_phone']);
                $this->db->bind(7, $data['site_address']);
                $this->db->bind(8, $data['site_facebook']);
                $this->db->bind(9, $data['site_twitter']);
                $this->db->bind(10, $data['site_instagram']);
                $this->db->bind(11, $data['site_youtube']);
                $this->db->bind(12, $data['site_whatsapp']);
                $this->db->bind(13, $data['site_keywords']);
                $this->db->bind(14, $data['site_footer_text']);
                $this->db->bind(15, $data['site_analytics_code']);
                $this->db->bind(16, $data['site_custom_css']);
                $this->db->bind(17, $data['site_custom_js']);
                
                $this->db->execute();
            }
    
            $this->db->commit();
            return true;
    
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('PDO Error: ' . $e->getMessage());
            throw new Exception('حدث خطأ في قاعدة البيانات: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('General Error: ' . $e->getMessage());
            throw new Exception('حدث خطأ عام: ' . $e->getMessage());
        }
    }
}