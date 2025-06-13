<?php
/**
 * فئة الزوار
 * 
 * تتعامل مع تسجيل وإحصاء زوار الموقع
 */
class Visitor {
    private $db;
    
    /**
     * إنشاء كائن الزوار
     * 
     * @param Database $db كائن قاعدة البيانات
     */
    public function __construct($db = null) {
        if ($db === null) {
            $this->db = new Database();
        } else {
            $this->db = $db;
        }
    }
    
    /**
     * تسجيل زيارة جديدة
     * 
     * @param array $data بيانات الزيارة (اختياري)
     * @return boolean نجاح أو فشل التسجيل
     */
    public function recordVisit($data = null) {
        // إذا لم يتم تمرير بيانات، قم بإنشاء البيانات تلقائيًا
        if ($data === null) {
            // التحقق من وجود جلسة
            if(!isset($_SESSION['visitor_id'])) {
                $_SESSION['visitor_id'] = session_id();
            }
            
            $page_visited = $_SERVER['REQUEST_URI'];
            $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $ip_address = get_client_ip();
            $session_id = $_SESSION['visitor_id'];
            
            // التحقق من وجود زيارة سابقة لهذه الصفحة في نفس الجلسة
            $is_unique = !$this->hasVisitedBefore($session_id, $page_visited);
            
            // الحصول على معلومات المتصفح والجهاز
            $browser_info = get_browser_info($user_agent);
            
            // تسجيل الزيارة
            $data = array(
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'page_visited' => $page_visited,
                'referrer' => $referrer,
                'country' => '', // يمكن استخدام خدمة خارجية للحصول على البلد
                'city' => '', // يمكن استخدام خدمة خارجية للحصول على المدينة
                'device_type' => $browser_info['device_type'],
                'browser' => $browser_info['browser'],
                'os' => $browser_info['os'],
                'session_id' => $session_id,
                'is_unique' => $is_unique
            );
        }
        
        $this->db->query('INSERT INTO visitors (ip_address, user_agent, page_visited, referrer, visit_date, country, city, device_type, browser, os, session_id, is_unique) VALUES (:ip_address, :user_agent, :page_visited, :referrer, CURDATE(), :country, :city, :device_type, :browser, :os, :session_id, :is_unique)');
        
        $this->db->bind(':ip_address', $data['ip_address']);
        $this->db->bind(':user_agent', $data['user_agent']);
        $this->db->bind(':page_visited', $data['page_visited']);
        $this->db->bind(':referrer', $data['referrer']);
        $this->db->bind(':country', $data['country']);
        $this->db->bind(':city', $data['city']);
        $this->db->bind(':device_type', $data['device_type']);
        $this->db->bind(':browser', $data['browser']);
        $this->db->bind(':os', $data['os']);
        $this->db->bind(':session_id', $data['session_id']);
        $this->db->bind(':is_unique', $data['is_unique']);
        
        if($this->db->execute()) {
            // تحديث الإحصائيات اليومية
            $this->updateDailyStats($data);
            return true;
        }
        
        return false;
    }
    
    /**
     * تحديث الإحصائيات اليومية
     * 
     * @param array $data بيانات الزيارة
     */
    private function updateDailyStats($data) {
        // التحقق من وجود إحصائيات لهذا اليوم
        $this->db->query('SELECT * FROM visitor_stats WHERE date = CURDATE()');
        $stats = $this->db->single();
        
        if($stats) {
            // تحديث الإحصائيات الموجودة
            $page_views = json_decode($stats['page_views'], true);
            $referrers = json_decode($stats['referrers'], true);
            $browsers = json_decode($stats['browsers'], true);
            $devices = json_decode($stats['devices'], true);
            $os = json_decode($stats['os'], true);
            $countries = json_decode($stats['countries'], true);
            
            // تحديث عدد الزيارات
            $total_visits = $stats['total_visits'] + 1;
            $unique_visits = $stats['unique_visits'];
            
            if($data['is_unique']) {
                $unique_visits++;
            }
            
            // تحديث الصفحات المزارة
            if(isset($page_views[$data['page_visited']])) {
                $page_views[$data['page_visited']]++;
            } else {
                $page_views[$data['page_visited']] = 1;
            }
            
            // تحديث المصادر
            if(!empty($data['referrer'])) {
                if(isset($referrers[$data['referrer']])) {
                    $referrers[$data['referrer']]++;
                } else {
                    $referrers[$data['referrer']] = 1;
                }
            }
            
            // تحديث المتصفحات
            if(isset($browsers[$data['browser']])) {
                $browsers[$data['browser']]++;
            } else {
                $browsers[$data['browser']] = 1;
            }
            
            // تحديث الأجهزة
            if(isset($devices[$data['device_type']])) {
                $devices[$data['device_type']]++;
            } else {
                $devices[$data['device_type']] = 1;
            }
            
            // تحديث أنظمة التشغيل
            if(isset($os[$data['os']])) {
                $os[$data['os']]++;
            } else {
                $os[$data['os']] = 1;
            }
            
            // تحديث البلدان
            if(!empty($data['country'])) {
                if(isset($countries[$data['country']])) {
                    $countries[$data['country']]++;
                } else {
                    $countries[$data['country']] = 1;
                }
            }
            
            // تحديث الإحصائيات في قاعدة البيانات
            $this->db->query('UPDATE visitor_stats SET total_visits = :total_visits, unique_visits = :unique_visits, page_views = :page_views, referrers = :referrers, browsers = :browsers, devices = :devices, os = :os, countries = :countries WHERE date = CURDATE()');
            
            $this->db->bind(':total_visits', $total_visits);
            $this->db->bind(':unique_visits', $unique_visits);
            $this->db->bind(':page_views', json_encode($page_views));
            $this->db->bind(':referrers', json_encode($referrers));
            $this->db->bind(':browsers', json_encode($browsers));
            $this->db->bind(':devices', json_encode($devices));
            $this->db->bind(':os', json_encode($os));
            $this->db->bind(':countries', json_encode($countries));
            
            $this->db->execute();
        } else {
            // إنشاء إحصائيات جديدة لهذا اليوم
            $page_views = array($data['page_visited'] => 1);
            $referrers = array();
            $browsers = array($data['browser'] => 1);
            $devices = array($data['device_type'] => 1);
            $os = array($data['os'] => 1);
            $countries = array();
            
            if(!empty($data['referrer'])) {
                $referrers[$data['referrer']] = 1;
            }
            
            if(!empty($data['country'])) {
                $countries[$data['country']] = 1;
            }
            
            $this->db->query('INSERT INTO visitor_stats (date, total_visits, unique_visits, page_views, referrers, browsers, devices, os, countries) VALUES (CURDATE(), 1, :unique_visits, :page_views, :referrers, :browsers, :devices, :os, :countries)');
            
            $this->db->bind(':unique_visits', $data['is_unique'] ? 1 : 0);
            $this->db->bind(':page_views', json_encode($page_views));
            $this->db->bind(':referrers', json_encode($referrers));
            $this->db->bind(':browsers', json_encode($browsers));
            $this->db->bind(':devices', json_encode($devices));
            $this->db->bind(':os', json_encode($os));
            $this->db->bind(':countries', json_encode($countries));
            
            $this->db->execute();
        }
    }
    
    /**
     * التحقق من وجود زيارة سابقة للجلسة
     * 
     * @param string $session_id معرف الجلسة
     * @param string $page_visited الصفحة المزارة
     * @return boolean وجود زيارة سابقة
     */
    public function hasVisitedBefore($session_id, $page_visited) {
        $this->db->query('SELECT * FROM visitors WHERE session_id = :session_id AND page_visited = :page_visited AND visit_date = CURDATE()');
        
        $this->db->bind(':session_id', $session_id);
        $this->db->bind(':page_visited', $page_visited);
        
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
    
    /**
     * الحصول على إحصائيات الزوار
     * 
     * @param string $start_date تاريخ البداية
     * @param string $end_date تاريخ النهاية
     * @return array إحصائيات الزوار
     */
    public function getVisitorStats($start_date, $end_date) {
        $this->db->query('SELECT * FROM visitor_stats WHERE date BETWEEN :start_date AND :end_date ORDER BY date ASC');
        
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على إحصائيات اليوم
     * 
     * @return array إحصائيات اليوم
     */
    public function getTodayStats() {
        $this->db->query('SELECT * FROM visitor_stats WHERE date = CURDATE()');
        
        $result = $this->db->single();
        
        if(!$result) {
            // إنشاء إحصائيات فارغة
            $result = array(
                'total_visits' => 0,
                'unique_visits' => 0,
                'page_views' => '{}',
                'referrers' => '{}',
                'browsers' => '{}',
                'devices' => '{}',
                'os' => '{}',
                'countries' => '{}'
            );
        }
        
        return $result;
    }
    
    /**
     * الحصول على إجمالي الزيارات
     * 
     * @return int إجمالي الزيارات
     */
    public function getTotalVisits() {
        $this->db->query('SELECT SUM(total_visits) as total FROM visitor_stats');
        
        $result = $this->db->single();
        return $result['total'] ? $result['total'] : 0;
    }
    
    /**
     * الحصول على إجمالي الزيارات الفريدة
     * 
     * @return int إجمالي الزيارات الفريدة
     */
    public function getTotalUniqueVisits() {
        $this->db->query('SELECT SUM(unique_visits) as total FROM visitor_stats');
        
        $result = $this->db->single();
        return $result['total'] ? $result['total'] : 0;
    }
    
    /**
     * الحصول على أكثر الصفحات زيارة
     * 
     * @param int $limit عدد الصفحات المراد عرضها
     * @return array أكثر الصفحات زيارة
     */
    public function getMostVisitedPages($limit = 5) {
        $this->db->query('SELECT page_visited, COUNT(*) as visits FROM visitors GROUP BY page_visited ORDER BY visits DESC LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب البلد
     * 
     * @return array توزيع الزوار حسب البلد
     */
    public function getVisitorsByCountry() {
        $this->db->query('SELECT country, COUNT(*) as visits FROM visitors WHERE country != "" GROUP BY country ORDER BY visits DESC');
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب المتصفح
     * 
     * @return array توزيع الزوار حسب المتصفح
     */
    public function getVisitorsByBrowser() {
        $this->db->query('SELECT browser, COUNT(*) as visits FROM visitors GROUP BY browser ORDER BY visits DESC');
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب نوع الجهاز
     * 
     * @return array توزيع الزوار حسب نوع الجهاز
     */
    public function getVisitorsByDevice() {
        $this->db->query('SELECT device_type, COUNT(*) as visits FROM visitors GROUP BY device_type ORDER BY visits DESC');
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب نظام التشغيل
     * 
     * @return array توزيع الزوار حسب نظام التشغيل
     */
    public function getVisitorsByOS() {
        $this->db->query('SELECT os, COUNT(*) as visits FROM visitors GROUP BY os ORDER BY visits DESC');
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على إحصائيات الزوار حسب الفترة الزمنية
     * 
     * @param string $period الفترة الزمنية (day, week, month, year)
     * @return array إحصائيات الزوار
     */
    public function getVisitorStatsByPeriod($period = 'day') {
        switch($period) {
            case 'day':
                $this->db->query('SELECT DATE_FORMAT(visit_time, "%H:00") as time_period, COUNT(*) as visits FROM visitors WHERE visit_date = CURDATE() GROUP BY time_period ORDER BY time_period ASC');
                break;
            case 'week':
                $this->db->query('SELECT DATE_FORMAT(visit_date, "%a") as time_period, COUNT(*) as visits FROM visitors WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY time_period ORDER BY FIELD(time_period, "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat")');
                break;
            case 'month':
                $this->db->query('SELECT DATE_FORMAT(visit_date, "%d") as time_period, COUNT(*) as visits FROM visitors WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY time_period ORDER BY time_period ASC');
                break;
            case 'year':
                $this->db->query('SELECT DATE_FORMAT(visit_date, "%b") as time_period, COUNT(*) as visits FROM visitors WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY time_period ORDER BY FIELD(time_period, "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec")');
                break;
            default:
                $this->db->query('SELECT DATE_FORMAT(visit_time, "%H:00") as time_period, COUNT(*) as visits FROM visitors WHERE visit_date = CURDATE() GROUP BY time_period ORDER BY time_period ASC');
        }
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على مصادر الزيارات
     * 
     * @param int $limit عدد النتائج
     * @return array مصادر الزيارات
     */
    public function getVisitSources($limit = 10) {
        $this->db->query('SELECT referrer, COUNT(*) as visits FROM visitors WHERE referrer != "" GROUP BY referrer ORDER BY visits DESC LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على إحصائيات الزوار للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @return array إحصائيات الزوار
     */
    public function getStatsByPeriod($period = 'today') {
        $stats = array();
        
        // إجمالي الزيارات
        $stats['total_visits'] = $this->getTotalVisitsByPeriod($period);
        
        // إجمالي الزيارات الفريدة
        $stats['unique_visits'] = $this->getTotalUniqueVisitsByPeriod($period);
        
        // أكثر الصفحات زيارة
        $stats['most_visited_pages'] = $this->getMostVisitedPagesByPeriod($period);
        
        // توزيع الزوار حسب البلد
        $stats['visitors_by_country'] = $this->getVisitorsByCountryAndPeriod($period);
        
        // توزيع الزوار حسب المتصفح
        $stats['visitors_by_browser'] = $this->getVisitorsByBrowserAndPeriod($period);
        
        // توزيع الزوار حسب نوع الجهاز
        $stats['visitors_by_device'] = $this->getVisitorsByDeviceAndPeriod($period);
        
        // توزيع الزوار حسب نظام التشغيل
        $stats['visitors_by_os'] = $this->getVisitorsByOSAndPeriod($period);
        
        // مصادر الزيارات
        $stats['visit_sources'] = $this->getVisitSourcesByPeriod($period);
        
        return $stats;
    }
    
    /**
     * الحصول على إجمالي الزيارات للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @return int إجمالي الزيارات
     */
    private function getTotalVisitsByPeriod($period = 'today') {
        $query = 'SELECT COUNT(*) as total FROM visitors';
        
        switch($period) {
            case 'today':
                $query .= ' WHERE visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' WHERE visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $this->db->query($query);
        $result = $this->db->single();
        
        return $result['total'] ? $result['total'] : 0;
    }
    
    /**
     * الحصول على إجمالي الزيارات الفريدة للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @return int إجمالي الزيارات الفريدة
     */
    private function getTotalUniqueVisitsByPeriod($period = 'today') {
        $query = 'SELECT COUNT(DISTINCT ip_address) as total FROM visitors';
        
        switch($period) {
            case 'today':
                $query .= ' WHERE visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' WHERE visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $this->db->query($query);
        $result = $this->db->single();
        
        return $result['total'] ? $result['total'] : 0;
    }
    
    /**
     * الحصول على أكثر الصفحات زيارة للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @param int $limit عدد النتائج
     * @return array أكثر الصفحات زيارة
     */
    private function getMostVisitedPagesByPeriod($period = 'today', $limit = 5) {
        $query = 'SELECT page_visited, COUNT(*) as visits FROM visitors';
        
        switch($period) {
            case 'today':
                $query .= ' WHERE visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' WHERE visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $query .= ' GROUP BY page_visited ORDER BY visits DESC LIMIT :limit';
        
        $this->db->query($query);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب البلد للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @param int $limit عدد النتائج
     * @return array توزيع الزوار حسب البلد
     */
    private function getVisitorsByCountryAndPeriod($period = 'today', $limit = 10) {
        $query = 'SELECT country, COUNT(*) as visits FROM visitors WHERE country != ""';
        
        switch($period) {
            case 'today':
                $query .= ' AND visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' AND visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $query .= ' GROUP BY country ORDER BY visits DESC LIMIT :limit';
        
        $this->db->query($query);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب المتصفح للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @return array توزيع الزوار حسب المتصفح
     */
    private function getVisitorsByBrowserAndPeriod($period = 'today') {
        $query = 'SELECT browser, COUNT(*) as visits FROM visitors';
        
        switch($period) {
            case 'today':
                $query .= ' WHERE visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' WHERE visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $query .= ' GROUP BY browser ORDER BY visits DESC';
        
        $this->db->query($query);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب نوع الجهاز للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @return array توزيع الزوار حسب نوع الجهاز
     */
    private function getVisitorsByDeviceAndPeriod($period = 'today') {
        $query = 'SELECT device_type, COUNT(*) as visits FROM visitors';
        
        switch($period) {
            case 'today':
                $query .= ' WHERE visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' WHERE visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $query .= ' GROUP BY device_type ORDER BY visits DESC';
        
        $this->db->query($query);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على توزيع الزوار حسب نظام التشغيل للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @return array توزيع الزوار حسب نظام التشغيل
     */
    private function getVisitorsByOSAndPeriod($period = 'today') {
        $query = 'SELECT os, COUNT(*) as visits FROM visitors';
        
        switch($period) {
            case 'today':
                $query .= ' WHERE visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' WHERE visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $query .= ' GROUP BY os ORDER BY visits DESC';
        
        $this->db->query($query);
        
        return $this->db->resultSet();
    }
    
    /**
     * الحصول على مصادر الزيارات للفترة المحددة
     * 
     * @param string $period الفترة (today, yesterday, week, month, year, all)
     * @param int $limit عدد النتائج
     * @return array مصادر الزيارات
     */
    private function getVisitSourcesByPeriod($period = 'today', $limit = 10) {
        $query = 'SELECT referrer, COUNT(*) as visits FROM visitors WHERE referrer != ""';
        
        switch($period) {
            case 'today':
                $query .= ' AND visit_date = CURDATE()';
                break;
            case 'yesterday':
                $query .= ' AND visit_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $query .= ' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $query .= ' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'year':
                $query .= ' AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)';
                break;
        }
        
        $query .= ' GROUP BY referrer ORDER BY visits DESC LIMIT :limit';
        
        $this->db->query($query);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
    /**
 * الحصول على إحصائيات الزوار اليومية
 * 
 * @param int $days عدد الأيام المطلوبة
 * @return array إحصائيات الزوار اليومية
 */
public function getDailyStats($days = 30) {
    $this->db->query('SELECT 
        date, 
        COALESCE(SUM(total_visits), 0) as total_visits, 
        COALESCE(SUM(unique_visits), 0) as unique_visits 
        FROM visitor_stats 
        WHERE date >= DATE_SUB(CURDATE(), INTERVAL :days DAY) 
        GROUP BY date 
        ORDER BY date ASC');
    
    $this->db->bind(':days', $days);
    
    $results = $this->db->resultSet();
    
    // إذا لم تكن هناك نتائج، نرجع مصفوفة فارغة
    return $results ? $results : [];
}

/**
 * الحصول على إحصائيات الزوار الشهرية
 * 
 * @param int $months عدد الأشهر المطلوبة
 * @return array إحصائيات الزوار الشهرية
 */
public function getMonthlyStats($months = 12) {
    $this->db->query('SELECT 
        DATE_FORMAT(date, "%Y-%m") as month, 
        COALESCE(SUM(total_visits), 0) as total_visits, 
        COALESCE(SUM(unique_visits), 0) as unique_visits 
        FROM visitor_stats 
        WHERE date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH) 
        GROUP BY month 
        ORDER BY month ASC');
    
    $this->db->bind(':months', $months);
    
    $results = $this->db->resultSet();
    
    // إذا لم تكن هناك نتائج، نرجع مصفوفة فارغة
    return $results ? $results : [];
}

/**
 * الحصول على إحصائيات المتصفحات
 * 
 * @param int $limit عدد النتائج
 * @return array إحصائيات المتصفحات
 */
public function getBrowsersStats($limit = 10) {
    $this->db->query('SELECT browser, COUNT(*) as count FROM visitors GROUP BY browser ORDER BY count DESC LIMIT :limit');
    
    $this->db->bind(':limit', $limit);
    
    return $this->db->resultSet();
}

/**
 * الحصول على إحصائيات أنظمة التشغيل
 * 
 * @param int $limit عدد النتائج
 * @return array إحصائيات أنظمة التشغيل
 */
public function getOsStats($limit = 10) {
    $this->db->query('SELECT os, COUNT(*) as count FROM visitors GROUP BY os ORDER BY count DESC LIMIT :limit');
    
    $this->db->bind(':limit', $limit);
    
    return $this->db->resultSet();
}

/**
 * الحصول على إحصائيات الأجهزة
 * 
 * @param int $limit عدد النتائج
 * @return array إحصائيات الأجهزة
 */
public function getDevicesStats($limit = 10) {
    $this->db->query('SELECT device_type as device, COUNT(*) as count FROM visitors GROUP BY device_type ORDER BY count DESC LIMIT :limit');
    
    $this->db->bind(':limit', $limit);
    
    return $this->db->resultSet();
}

/**
 * اختصار النص وإضافة نقاط (...) إذا تجاوز الطول المحدد
 * 
 * @param string $str النص الأصلي
 * @param int $max_length الطول الأقصى المطلوب
 * @param string $position مكان وضع النقاط (front, middle, end)
 * @param string $ellipsis النص المستخدم للإختصار (عادة ...)
 * @return string النص المختصر
 */
function ellipsize($str, $max_length, $position = 'end', $ellipsis = '...') {
    if (strlen($str) <= $max_length) {
        return $str;
    }

    switch ($position) {
        case 'front':
            return $ellipsis . substr($str, -( $max_length - strlen($ellipsis) ));
        case 'middle':
            return substr($str, 0, floor(($max_length - strlen($ellipsis)) / 2)) . $ellipsis . substr($str, -( $max_length - strlen($ellipsis) - floor(($max_length - strlen($ellipsis)) / 2) ));
        default: // end
            return substr($str, 0, $max_length - strlen($ellipsis)) . $ellipsis;
    }
}

/**
 * الحصول على لون المتصفح الأساسي
 * 
 * @param string $browser اسم المتصفح
 * @return string كود اللون
 */
function getBrowserColor($browser) {
    $colors = [
        'Chrome' => '#4285F4',
        'Firefox' => '#FF9500',
        'Safari' => '#1CD1A1',
        'Edge' => '#0078D7',
        'IE' => '#00A2ED',
        'Opera' => '#FF1B2D',
        'Samsung Browser' => '#1428A0',
        'UC Browser' => '#FF9900',
        'Brave' => '#FB542B',
        'Vivaldi' => '#EF3939',
        'Default' => '#6c757d'
    ];

    return $colors[$browser] ?? $colors['Default'];
}

/**
 * الحصول على لون المتصفح عند التحويم
 * 
 * @param string $browser اسم المتصفح
 * @return string كود اللون
 */
function getBrowserHoverColor($browser) {
    $colors = [
        'Chrome' => '#3367D6',
        'Firefox' => '#E68600',
        'Safari' => '#17B38A',
        'Edge' => '#0066B4',
        'IE' => '#0089CB',
        'Opera' => '#E6172D',
        'Samsung Browser' => '#0D1E7A',
        'UC Browser' => '#E68600',
        'Brave' => '#E4462B',
        'Vivaldi' => '#D63131',
        'Default' => '#5a6268'
    ];

    return $colors[$browser] ?? $colors['Default'];
}

/**
 * الحصول on class لون المتصفح لاستخدامه في CSS
 * 
 * @param string $browser اسم المتصفح
 * @return string اسم الكلاس
 */
function getBrowserColorClass($browser) {
    $classes = [
        'Chrome' => 'text-primary',
        'Firefox' => 'text-warning',
        'Safari' => 'text-success',
        'Edge' => 'text-info',
        'IE' => 'text-info',
        'Opera' => 'text-danger',
        'Samsung Browser' => 'text-indigo',
        'UC Browser' => 'text-orange',
        'Brave' => 'text-red',
        'Vivaldi' => 'text-red',
        'Default' => 'text-secondary'
    ];

    return $classes[$browser] ?? $classes['Default'];
}

/**
 * الحصول على لون نظام التشغيل الأساسي
 * 
 * @param string $os اسم نظام التشغيل
 * @return string كود اللون
 */
function getOsColor($os) {
    $colors = [
        'Windows' => '#0078D7',
        'macOS' => '#A2AAAD',
        'iOS' => '#000000',
        'Android' => '#3DDC84',
        'Linux' => '#FCC624',
        'Chrome OS' => '#4285F4',
        'Default' => '#6c757d'
    ];

    return $colors[$os] ?? $colors['Default'];
}

/**
 * الحصول على لون نظام التشغيل عند التحويم
 * 
 * @param string $os اسم نظام التشغيل
 * @return string كود اللون
 */
function getOsHoverColor($os) {
    $colors = [
        'Windows' => '#0066B4',
        'macOS' => '#8A9295',
        'iOS' => '#000000',
        'Android' => '#2BC370',
        'Linux' => '#E6B400',
        'Chrome OS' => '#3367D6',
        'Default' => '#5a6268'
    ];

    return $colors[$os] ?? $colors['Default'];
}

/**
 * الحصول على class لون نظام التشغيل لاستخدامه في CSS
 * 
 * @param string $os اسم نظام التشغيل
 * @return string اسم الكلاس
 */
function getOsColorClass($os) {
    $classes = [
        'Windows' => 'text-info',
        'macOS' => 'text-secondary',
        'iOS' => 'text-dark',
        'Android' => 'text-success',
        'Linux' => 'text-warning',
        'Chrome OS' => 'text-primary',
        'Default' => 'text-secondary'
    ];

    return $classes[$os] ?? $classes['Default'];
}

/**
 * الحصول على لون الجهاز الأساسي
 * 
 * @param string $device نوع الجهاز
 * @return string كود اللون
 */
function getDeviceColor($device) {
    $colors = [
        'Desktop' => '#4285F4',
        'Mobile' => '#34A853',
        'Tablet' => '#FBBC05',
        'Bot' => '#EA4335',
        'Default' => '#6c757d'
    ];

    return $colors[$device] ?? $colors['Default'];
}

/**
 * الحصول على لون الجهاز عند التحويم
 * 
 * @param string $device نوع الجهاز
 * @return string كود اللون
 */
function getDeviceHoverColor($device) {
    $colors = [
        'Desktop' => '#3367D6',
        'Mobile' => '#2D9246',
        'Tablet' => '#E6B400',
        'Bot' => '#D33426',
        'Default' => '#5a6268'
    ];

    return $colors[$device] ?? $colors['Default'];
}

/**
 * الحصول على class لون الجهاز لاستخدامه في CSS
 * 
 * @param string $device نوع الجهاز
 * @return string اسم الكلاس
 */
function getDeviceColorClass($device) {
    $classes = [
        'Desktop' => 'text-primary',
        'Mobile' => 'text-success',
        'Tablet' => 'text-warning',
        'Bot' => 'text-danger',
        'Default' => 'text-secondary'
    ];

    return $classes[$device] ?? $classes['Default'];
}

}