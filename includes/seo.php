<?php
/**
 * ملف تحسين محركات البحث (SEO)
 * 
 * يحتوي على الدوال الخاصة بتوليد بيانات meta ديناميكياً وتحسين محركات البحث
 */

// دالة توليد عنوان الصفحة
function generate_page_title($page, $data = null) {
    global $site_settings;
    
    $site_name = isset($site_settings['site_name']) ? $site_settings['site_name'] : 'أبو جسار للحدادة والكلادنج';
    $title = '';
    
    switch ($page) {
        case 'home':
            $title = $site_name . ' - ' . (isset($site_settings['site_slogan']) ? $site_settings['site_slogan'] : 'خدمات الحدادة والكلادنج بأعلى جودة');
            break;
        case 'services':
            $title = 'خدماتنا - ' . $site_name;
            break;
        case 'service':
            if ($data && isset($data['title'])) {
                $title = $data['title'] . ' - خدمات ' . $site_name;
            } else {
                $title = 'تفاصيل الخدمة - ' . $site_name;
            }
            break;
        case 'projects':
            $title = 'مشاريعنا - ' . $site_name;
            break;
        case 'project':
            if ($data && isset($data['title'])) {
                $title = $data['title'] . ' - مشاريع ' . $site_name;
            } else {
                $title = 'تفاصيل المشروع - ' . $site_name;
            }
            break;
        case 'about':
            $title = 'من نحن - ' . $site_name;
            break;
        case 'testimonials':
            $title = 'آراء العملاء - ' . $site_name;
            break;
        case 'contact':
            $title = 'اتصل بنا - ' . $site_name;
            break;
        default:
            $title = $site_name;
    }
    
    return $title;
}

// دالة توليد وصف الصفحة
function generate_page_description($page, $data = null) {
    global $site_settings;
    
    $default_description = isset($site_settings['site_description']) ? $site_settings['site_description'] : 'شركة أبو جسار للحدادة والكلادنج - نقدم خدمات الحدادة والكلادنج بأعلى جودة وأفضل الأسعار';
    $description = '';
    
    switch ($page) {
        case 'home':
            $description = $default_description;
            break;
        case 'services':
            $description = 'تعرف على خدمات شركة أبو جسار للحدادة والكلادنج المتنوعة والمتميزة بأعلى جودة وأفضل الأسعار';
            break;
        case 'service':
            if ($data && isset($data['description'])) {
                $description = truncate_text(strip_tags($data['description']), 160);
            } else {
                $description = 'تفاصيل خدمات شركة أبو جسار للحدادة والكلادنج المتميزة بالجودة العالية والأسعار المنافسة';
            }
            break;
        case 'projects':
            $description = 'استعرض مشاريع شركة أبو جسار للحدادة والكلادنج المنفذة بأعلى معايير الجودة والاحترافية';
            break;
        case 'project':
            if ($data && isset($data['description'])) {
                $description = truncate_text(strip_tags($data['description']), 160);
            } else {
                $description = 'تفاصيل مشاريع شركة أبو جسار للحدادة والكلادنج المنفذة بأعلى معايير الجودة والاحترافية';
            }
            break;
        case 'about':
            $description = 'تعرف على شركة أبو جسار للحدادة والكلادنج، خبرتنا، رؤيتنا، ورسالتنا في تقديم أفضل خدمات الحدادة والكلادنج';
            break;
        case 'testimonials':
            $description = 'آراء وتقييمات عملاء شركة أبو جسار للحدادة والكلادنج حول خدماتنا ومشاريعنا المتميزة';
            break;
        case 'contact':
            $description = 'تواصل مع شركة أبو جسار للحدادة والكلادنج للحصول على خدماتنا المتميزة وعروض الأسعار';
            break;
        default:
            $description = $default_description;
    }
    
    return $description;
}

// دالة توليد الكلمات المفتاحية
function generate_page_keywords($page, $data = null) {
    global $site_settings;
    
    $default_keywords = isset($site_settings['site_keywords']) ? $site_settings['site_keywords'] : 'حدادة، كلادنج، أبو جسار، أعمال معدنية، واجهات، أبواب حديد، نوافذ، درابزين، بوابات، هياكل معدنية';
    $keywords = '';
    
    switch ($page) {
        case 'home':
            $keywords = $default_keywords;
            break;
        case 'services':
            $keywords = 'خدمات الحدادة، خدمات الكلادنج، أعمال معدنية، واجهات، أبواب حديد، نوافذ، درابزين، بوابات، هياكل معدنية، أبو جسار';
            break;
        case 'service':
            if ($data && isset($data['title']) && isset($data['keywords'])) {
                $keywords = $data['title'] . '، ' . $data['keywords'] . '، أبو جسار للحدادة والكلادنج';
            } else {
                $keywords = 'خدمات الحدادة، خدمات الكلادنج، أعمال معدنية، واجهات، أبو جسار';
            }
            break;
        case 'projects':
            $keywords = 'مشاريع الحدادة، مشاريع الكلادنج، أعمال معدنية منفذة، واجهات، أبواب حديد، نوافذ، درابزين، بوابات، هياكل معدنية، أبو جسار';
            break;
        case 'project':
            if ($data && isset($data['title']) && isset($data['keywords'])) {
                $keywords = $data['title'] . '، ' . $data['keywords'] . '، مشاريع أبو جسار للحدادة والكلادنج';
            } else {
                $keywords = 'مشاريع الحدادة، مشاريع الكلادنج، أعمال معدنية منفذة، واجهات، أبو جسار';
            }
            break;
        case 'about':
            $keywords = 'من نحن، شركة أبو جسار، الحدادة والكلادنج، خبرة، جودة، احترافية، أعمال معدنية';
            break;
        case 'testimonials':
            $keywords = 'آراء العملاء، تقييمات، شهادات، رضا العملاء، أبو جسار للحدادة والكلادنج';
            break;
        case 'contact':
            $keywords = 'اتصل بنا، تواصل معنا، عنوان شركة أبو جسار، رقم هاتف، بريد إلكتروني، موقع، خريطة';
            break;
        default:
            $keywords = $default_keywords;
    }
    
    return $keywords;
}

// دالة توليد الروابط القياسية (Canonical URL)
function generate_canonical_url($page, $data = null) {
    global $site_settings;
    
    $base_url = isset($site_settings['site_url']) ? rtrim($site_settings['site_url'], '/') : 'https://abujassar.com';
    $url = $base_url;
    
    switch ($page) {
        case 'home':
            $url .= '/';
            break;
        case 'services':
            $url .= '/index.php?page=services';
            break;
        case 'service':
            if ($data && isset($data['id'])) {
                $url .= '/index.php?page=service&id=' . $data['id'];
            } else {
                $url .= '/index.php?page=services';
            }
            break;
        case 'projects':
            $url .= '/index.php?page=projects';
            break;
        case 'project':
            if ($data && isset($data['id'])) {
                $url .= '/index.php?page=project&id=' . $data['id'];
            } else {
                $url .= '/index.php?page=projects';
            }
            break;
        case 'about':
            $url .= '/index.php?page=about';
            break;
        case 'testimonials':
            $url .= '/index.php?page=testimonials';
            break;
        case 'contact':
            $url .= '/index.php?page=contact';
            break;
        default:
            $url .= '/';
    }
    
    return $url;
}

// دالة توليد بيانات Open Graph
function generate_open_graph_tags($page, $data = null) {
    global $site_settings;
    
    $site_name = isset($site_settings['site_name']) ? $site_settings['site_name'] : 'أبو جسار للحدادة والكلادنج';
    $base_url = isset($site_settings['site_url']) ? rtrim($site_settings['site_url'], '/') : 'https://abujassar.com';
    $default_image = $base_url . '/assets/img/logo.png';
    
    $title = generate_page_title($page, $data);
    $description = generate_page_description($page, $data);
    $url = generate_canonical_url($page, $data);
    $image = $default_image;
    
    // تحديد الصورة المناسبة للصفحة
    if ($data) {
        if (isset($data['image'])) {
            $image = $base_url . '/' . $data['image'];
        } elseif (isset($data['main_image'])) {
            $image = $base_url . '/' . $data['main_image'];
        }
    }
    
    $og_tags = [
        'og:title' => $title,
        'og:description' => $description,
        'og:url' => $url,
        'og:image' => $image,
        'og:type' => ($page == 'home') ? 'website' : 'article',
        'og:site_name' => $site_name
    ];
    
    return $og_tags;
}

// دالة توليد بيانات Twitter Card
function generate_twitter_card_tags($page, $data = null) {
    global $site_settings;
    
    $site_name = isset($site_settings['site_name']) ? $site_settings['site_name'] : 'أبو جسار للحدادة والكلادنج';
    $base_url = isset($site_settings['site_url']) ? rtrim($site_settings['site_url'], '/') : 'https://abujassar.com';
    $default_image = $base_url . '/assets/img/logo.png';
    
    $title = generate_page_title($page, $data);
    $description = generate_page_description($page, $data);
    $image = $default_image;
    
    // تحديد الصورة المناسبة للصفحة
    if ($data) {
        if (isset($data['image'])) {
            $image = $base_url . '/' . $data['image'];
        } elseif (isset($data['main_image'])) {
            $image = $base_url . '/' . $data['main_image'];
        }
    }
    
    $twitter_tags = [
        'twitter:card' => 'summary_large_image',
        'twitter:title' => $title,
        'twitter:description' => $description,
        'twitter:image' => $image,
        'twitter:site' => isset($site_settings['site_twitter_username']) ? $site_settings['site_twitter_username'] : '@abujassar'
    ];
    
    return $twitter_tags;
}

// دالة إنشاء خريطة الموقع (Sitemap)
function generate_sitemap() {
    global $db, $site_settings;
    
    $base_url = isset($site_settings['site_url']) ? rtrim($site_settings['site_url'], '/') : 'https://abujassar.com';
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // الصفحة الرئيسية
    $sitemap .= "\t<url>\n";
    $sitemap .= "\t\t<loc>" . $base_url . "/</loc>\n";
    $sitemap .= "\t\t<changefreq>daily</changefreq>\n";
    $sitemap .= "\t\t<priority>1.0</priority>\n";
    $sitemap .= "\t</url>\n";
    
    // الصفحات الثابتة
    $static_pages = [
        'services' => 'weekly',
        'projects' => 'weekly',
        'about' => 'monthly',
        'testimonials' => 'weekly',
        'contact' => 'monthly'
    ];
    
    foreach ($static_pages as $page => $changefreq) {
        $sitemap .= "\t<url>\n";
        $sitemap .= "\t\t<loc>" . $base_url . "/index.php?page=" . $page . "</loc>\n";
        $sitemap .= "\t\t<changefreq>" . $changefreq . "</changefreq>\n";
        $sitemap .= "\t\t<priority>0.8</priority>\n";
        $sitemap .= "\t</url>\n";
    }
    
    // صفحات الخدمات
    $query = "SELECT id, updated_at FROM services WHERE status = 'active'";
    $result = $db->query($query);
    
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetch_assoc($result)) {
            $sitemap .= "\t<url>\n";
            $sitemap .= "\t\t<loc>" . $base_url . "/index.php?page=service&id=" . $row['id'] . "</loc>\n";
            $sitemap .= "\t\t<lastmod>" . date('Y-m-d', strtotime($row['updated_at'])) . "</lastmod>\n";
            $sitemap .= "\t\t<changefreq>monthly</changefreq>\n";
            $sitemap .= "\t\t<priority>0.7</priority>\n";
            $sitemap .= "\t</url>\n";
        }
    }
    
    // صفحات المشاريع
    $query = "SELECT id, updated_at FROM projects WHERE status = 'active'";
    $result = $db->query($query);
    
    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetch_assoc($result)) {
            $sitemap .= "\t<url>\n";
            $sitemap .= "\t\t<loc>" . $base_url . "/index.php?page=project&id=" . $row['id'] . "</loc>\n";
            $sitemap .= "\t\t<lastmod>" . date('Y-m-d', strtotime($row['updated_at'])) . "</lastmod>\n";
            $sitemap .= "\t\t<changefreq>monthly</changefreq>\n";
            $sitemap .= "\t\t<priority>0.7</priority>\n";
            $sitemap .= "\t</url>\n";
        }
    }
    
    $sitemap .= '</urlset>';
    
    return $sitemap;
}

// دالة إنشاء ملف robots.txt
function generate_robots_txt() {
    global $site_settings;
    
    $base_url = isset($site_settings['site_url']) ? rtrim($site_settings['site_url'], '/') : 'https://abujassar.com';
    $robots = "User-agent: *\n";
    $robots .= "Disallow: /admin/\n";
    $robots .= "Disallow: /includes/\n";
    $robots .= "Disallow: /config/\n";
    $robots .= "Allow: /\n\n";
    $robots .= "Sitemap: " . $base_url . "/sitemap.xml";
    
    return $robots;
}

// دالة اختصار النص للوصف
function truncate_text($text, $length = 160) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    return $text . '...';
}

// دالة إنشاء وسوم meta
function generate_meta_tags($page, $data = null) {
    $title = generate_page_title($page, $data);
    $description = generate_page_description($page, $data);
    $keywords = generate_page_keywords($page, $data);
    $canonical = generate_canonical_url($page, $data);
    $og_tags = generate_open_graph_tags($page, $data);
    $twitter_tags = generate_twitter_card_tags($page, $data);
    
    $meta_tags = '';
    
    // وسوم meta الأساسية
    $meta_tags .= '<meta charset="UTF-8">' . "\n";
    $meta_tags .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
    $meta_tags .= '<title>' . $title . '</title>' . "\n";
    $meta_tags .= '<meta name="description" content="' . $description . '">' . "\n";
    $meta_tags .= '<meta name="keywords" content="' . $keywords . '">' . "\n";
    $meta_tags .= '<link rel="canonical" href="' . $canonical . '">' . "\n";
    
    // وسوم Open Graph
    foreach ($og_tags as $property => $content) {
        $meta_tags .= '<meta property="' . $property . '" content="' . $content . '">' . "\n";
    }
    
    // وسوم Twitter Card
    foreach ($twitter_tags as $name => $content) {
        $meta_tags .= '<meta name="' . $name . '" content="' . $content . '">' . "\n";
    }
    
    return $meta_tags;
}

// دالة إنشاء هيكل البيانات المنظمة (Schema.org)
function generate_schema_markup($page, $data = null) {
    global $site_settings;
    
    $site_name = isset($site_settings['site_name']) ? $site_settings['site_name'] : 'أبو جسار للحدادة والكلادنج';
    $base_url = isset($site_settings['site_url']) ? rtrim($site_settings['site_url'], '/') : 'https://abujassar.com';
    $logo = $base_url . '/assets/img/logo.png';
    
    $schema = [];
    
    // معلومات المنظمة
    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $site_name,
        'url' => $base_url,
        'logo' => $logo,
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => isset($site_settings['site_phone']) ? $site_settings['site_phone'] : '',
            'contactType' => 'customer service'
        ]
    ];
    
    // إضافة روابط التواصل الاجتماعي
    $social_links = [];
    if (isset($site_settings['site_facebook'])) $social_links[] = $site_settings['site_facebook'];
    if (isset($site_settings['site_twitter'])) $social_links[] = $site_settings['site_twitter'];
    if (isset($site_settings['site_instagram'])) $social_links[] = $site_settings['site_instagram'];
    if (isset($site_settings['site_youtube'])) $social_links[] = $site_settings['site_youtube'];
    
    if (!empty($social_links)) {
        $organization['sameAs'] = $social_links;
    }
    
    $schema[] = $organization;
    
    // إضافة معلومات إضافية حسب نوع الصفحة
    switch ($page) {
        case 'home':
            $website = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $site_name,
                'url' => $base_url,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $base_url . '/index.php?s={search_term_string}',
                    'query-input' => 'required name=search_term_string'
                ]
            ];
            $schema[] = $website;
            break;
            
        case 'service':
            if ($data) {
                $service = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Service',
                    'name' => $data['title'],
                    'description' => strip_tags($data['description']),
                    'provider' => [
                        '@type' => 'Organization',
                        'name' => $site_name
                    ]
                ];
                
                if (isset($data['image'])) {
                    $service['image'] = $base_url . '/' . $data['image'];
                }
                
                $schema[] = $service;
            }
            break;
            
        case 'project':
            if ($data) {
                $project = [
                    '@context' => 'https://schema.org',
                    '@type' => 'CreativeWork',
                    'name' => $data['title'],
                    'description' => strip_tags($data['description']),
                    'creator' => [
                        '@type' => 'Organization',
                        'name' => $site_name
                    ]
                ];
                
                if (isset($data['main_image'])) {
                    $project['image'] = $base_url . '/' . $data['main_image'];
                }
                
                if (isset($data['created_at'])) {
                    $project['dateCreated'] = date('Y-m-d', strtotime($data['created_at']));
                }
                
                $schema[] = $project;
            }
            break;
            
        case 'about':
            $about = [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'من نحن - ' . $site_name,
                'description' => 'تعرف على شركة أبو جسار للحدادة والكلادنج، خبرتنا، رؤيتنا، ورسالتنا في تقديم أفضل خدمات الحدادة والكلادنج'
            ];
            $schema[] = $about;
            break;
            
        case 'contact':
            $contact = [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'اتصل بنا - ' . $site_name,
                'description' => 'تواصل مع شركة أبو جسار للحدادة والكلادنج للحصول على خدماتنا المتميزة وعروض الأسعار'
            ];
            $schema[] = $contact;
            break;
    }
    
    $schema_markup = '';
    foreach ($schema as $item) {
        $schema_markup .= '<script type="application/ld+json">' . json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
    
    return $schema_markup;
}
