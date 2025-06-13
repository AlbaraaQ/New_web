-- ملف إنشاء قاعدة البيانات والجداول الرئيسية لموقع أبو جسار للحدادة والكلادنج
-- تاريخ الإنشاء: 3 يونيو 2025
-- المطور: Manus AI

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS abujassar_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- استخدام قاعدة البيانات
USE abujassar_db;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الخدمات
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    short_description VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    `order` INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    meta_title VARCHAR(100) NULL,
    meta_description VARCHAR(255) NULL,
    meta_keywords VARCHAR(255) NULL,
    INDEX idx_slug (slug),
    INDEX idx_is_featured (is_featured),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المشاريع
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    client_name VARCHAR(100) NOT NULL,
    short_description VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    completion_date DATE NULL,
    service_id INT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    meta_title VARCHAR(100) NULL,
    meta_description VARCHAR(255) NULL,
    meta_keywords VARCHAR(255) NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_service_id (service_id),
    INDEX idx_is_featured (is_featured),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الصور
CREATE TABLE IF NOT EXISTS images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NULL,
    service_id INT NULL,
    project_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_service_id (service_id),
    INDEX idx_project_id (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول التقييمات
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    client_image VARCHAR(255) NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    content TEXT NOT NULL,
    is_approved TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_approved (is_approved),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الزوار
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    page_visited VARCHAR(255) NOT NULL,
    referrer VARCHAR(255) NULL,
    visit_date DATE NOT NULL,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    country VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    device_type VARCHAR(50) NULL,
    browser VARCHAR(100) NULL,
    os VARCHAR(100) NULL,
    session_id VARCHAR(100) NULL,
    is_unique TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_visit_date (visit_date),
    INDEX idx_page_visited (page_visited),
    INDEX idx_country (country),
    INDEX idx_device_type (device_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول إحصائيات الزوار
CREATE TABLE IF NOT EXISTS visitor_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL UNIQUE,
    total_visits INT NOT NULL DEFAULT 0,
    unique_visits INT NOT NULL DEFAULT 0,
    page_views TEXT NOT NULL DEFAULT '{}',
    referrers TEXT NOT NULL DEFAULT '{}',
    browsers TEXT NOT NULL DEFAULT '{}',
    devices TEXT NOT NULL DEFAULT '{}',
    os TEXT NOT NULL DEFAULT '{}',
    countries TEXT NOT NULL DEFAULT '{}',
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول طلبات التواصل
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الإعدادات
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(100) NOT NULL,
    site_logo VARCHAR(255) NULL,
    site_description TEXT NULL,
    contact_email VARCHAR(100) NULL,
    contact_phone VARCHAR(20) NULL,
    contact_address TEXT NULL,
    facebook_url VARCHAR(255) NULL,
    twitter_url VARCHAR(255) NULL,
    instagram_url VARCHAR(255) NULL,
    whatsapp_number VARCHAR(20) NULL,
    meta_keywords VARCHAR(255) NULL,
    meta_description VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدخال بيانات افتراضية للمستخدم الأول (المدير)
INSERT INTO users (username, password, email, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'مدير النظام', 'admin');

-- إدخال بيانات افتراضية للإعدادات
INSERT INTO settings (site_name, site_description, contact_email, contact_phone, meta_keywords, meta_description) 
VALUES ('أبو جسار للحدادة والكلادنج', 'شركة متخصصة في أعمال الحدادة والكلادنج بجودة عالية وأسعار منافسة', 'info@abujassar.com', '+966123456789', 'حدادة، كلادنج، أعمال معدنية، أبو جسار', 'أبو جسار للحدادة والكلادنج - شركة متخصصة في أعمال الحدادة والكلادنج بجودة عالية وأسعار منافسة');
