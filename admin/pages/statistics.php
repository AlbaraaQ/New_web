<?php
/**
 * صفحة إدارة الإحصائيات
 */

// التحقق من تسجيل الدخول
if(!$user->isLoggedIn()) {
    redirect('login.php');
}

// الحصول على إحصائيات الزوار
$total_visits = $visitor->getTotalVisits();
$total_unique_visits = $visitor->getTotalUniqueVisits();
$today_stats = $visitor->getTodayStats();
$today_visits = isset($today_stats['total_visits']) ? $today_stats['total_visits'] : 0;
$today_unique_visits = isset($today_stats['unique_visits']) ? $today_stats['unique_visits'] : 0;

// الحصول على إحصائيات الزوار حسب الأيام (آخر 30 يوم)
$daily_stats = $visitor->getDailyStats(30);

// الحصول على إحصائيات الزوار حسب الشهور (آخر 12 شهر)
$monthly_stats = $visitor->getMonthlyStats(12);

// الحصول على أكثر الصفحات زيارة
$most_visited_pages = $visitor->getMostVisitedPages(10);

// الحصول على أكثر المتصفحات استخداماً
$browsers_stats = $visitor->getBrowsersStats();

// الحصول على أكثر أنظمة التشغيل استخداماً
$os_stats = $visitor->getOsStats();

// الحصول على أكثر الأجهزة استخداماً
$devices_stats = $visitor->getDevicesStats();

// تغيير عنوان الصفحة
$page_title = 'إحصائيات الزوار';
?>

<div class="row">
    <div class="col-lg-3 col-6">
        <!-- صندوق إجمالي الزيارات -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo $total_visits; ?></h3>
                <p>إجمالي الزيارات</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- صندوق الزوار الفريدين -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $total_unique_visits; ?></h3>
                <p>الزوار الفريدين</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- صندوق زيارات اليوم -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $today_visits; ?></h3>
                <p>زيارات اليوم</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- صندوق زوار اليوم الفريدين -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo $today_unique_visits; ?></h3>
                <p>زوار اليوم الفريدين</p>
            </div>
            <div class="icon">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- بطاقة إحصائيات الزوار اليومية -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إحصائيات الزوار اليومية (آخر 30 يوم)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="dailyStatsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- بطاقة إحصائيات الزوار الشهرية -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إحصائيات الزوار الشهرية (آخر 12 شهر)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="monthlyStatsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- بطاقة أكثر الصفحات زيارة -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">أكثر الصفحات زيارة</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>الصفحة</th>
                                <th>عدد الزيارات</th>
                                <th>النسبة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($most_visited_pages)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد بيانات</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($most_visited_pages as $page_item): ?>
                                    <tr>
                                        <td><?php echo $page_item['page_visited']; ?></td>
                                        <td><span class="badge bg-primary"><?php echo $page_item['visits']; ?></span></td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-primary" style="width: <?php echo ($page_item['visits'] / $total_visits) * 100; ?>%"></div>
                                            </div>
                                            <span class="badge bg-secondary"><?php echo round(($page_item['visits'] / $total_visits) * 100, 2); ?>%</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- بطاقة إحصائيات المتصفحات -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إحصائيات المتصفحات</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="browsersChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- بطاقة إحصائيات أنظمة التشغيل -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إحصائيات أنظمة التشغيل</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="osChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- بطاقة إحصائيات الأجهزة -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إحصائيات الأجهزة</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="devicesChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // إعداد بيانات الرسم البياني اليومي
        var dailyStatsCtx = document.getElementById('dailyStatsChart').getContext('2d');
        var dailyStatsData = {
            labels: <?php echo json_encode(array_column($daily_stats, 'date')); ?>,
            datasets: [
                {
                    label: 'إجمالي الزيارات',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: 3,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: <?php echo json_encode(array_column($daily_stats, 'total_visits')); ?>
                },
                {
                    label: 'الزوار الفريدين',
                    backgroundColor: 'rgba(210, 214, 222, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    pointRadius: 3,
                    pointColor: 'rgba(210, 214, 222, 1)',
                    pointStrokeColor: '#c1c7d1',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data: <?php echo json_encode(array_column($daily_stats, 'unique_visits')); ?>
                }
            ]
        };
        
        var dailyStatsOptions = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: true
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        };
        
        new Chart(dailyStatsCtx, {
            type: 'line',
            data: dailyStatsData,
            options: dailyStatsOptions
        });
        
        // إعداد بيانات الرسم البياني الشهري
        var monthlyStatsCtx = document.getElementById('monthlyStatsChart').getContext('2d');
        var monthlyStatsData = {
            labels: <?php echo json_encode(array_column($monthly_stats, 'month')); ?>,
            datasets: [
                {
                    label: 'إجمالي الزيارات',
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: 3,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: <?php echo json_encode(array_column($monthly_stats, 'total_visits')); ?>
                },
                {
                    label: 'الزوار الفريدين',
                    backgroundColor: 'rgba(210, 214, 222, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    pointRadius: 3,
                    pointColor: 'rgba(210, 214, 222, 1)',
                    pointStrokeColor: '#c1c7d1',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data: <?php echo json_encode(array_column($monthly_stats, 'unique_visits')); ?>
                }
            ]
        };
        
        var monthlyStatsOptions = {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                display: true
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        };
        
        new Chart(monthlyStatsCtx, {
            type: 'bar',
            data: monthlyStatsData,
            options: monthlyStatsOptions
        });
        
        // إعداد بيانات الرسم البياني للمتصفحات
        var browsersCtx = document.getElementById('browsersChart').getContext('2d');
        var browsersData = {
            labels: <?php echo json_encode(array_column($browsers_stats, 'browser')); ?>,
            datasets: [
                {
                    data: <?php echo json_encode(array_column($browsers_stats, 'count')); ?>,
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997'],
                }
            ]
        };
        
        var browsersOptions = {
            maintainAspectRatio: false,
            responsive: true,
        };
        
        new Chart(browsersCtx, {
            type: 'doughnut',
            data: browsersData,
            options: browsersOptions
        });
        
        // إعداد بيانات الرسم البياني لأنظمة التشغيل
        var osCtx = document.getElementById('osChart').getContext('2d');
        var osData = {
            labels: <?php echo json_encode(array_column($os_stats, 'os')); ?>,
            datasets: [
                {
                    data: <?php echo json_encode(array_column($os_stats, 'count')); ?>,
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997'],
                }
            ]
        };
        
        var osOptions = {
            maintainAspectRatio: false,
            responsive: true,
        };
        
        new Chart(osCtx, {
            type: 'pie',
            data: osData,
            options: osOptions
        });
        
        // إعداد بيانات الرسم البياني للأجهزة
        var devicesCtx = document.getElementById('devicesChart').getContext('2d');
        var devicesData = {
            labels: <?php echo json_encode(array_column($devices_stats, 'device')); ?>,
            datasets: [
                {
                    data: <?php echo json_encode(array_column($devices_stats, 'count')); ?>,
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc'],
                }
            ]
        };
        
        var devicesOptions = {
            maintainAspectRatio: false,
            responsive: true,
        };
        
        new Chart(devicesCtx, {
            type: 'pie',
            data: devicesData,
            options: devicesOptions
        });
    });
</script>
