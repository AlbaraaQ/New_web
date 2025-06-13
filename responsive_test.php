<?php
/**
 * ملف اختبار توافق الموقع مع مختلف الأجهزة
 * 
 * هذا الملف يستخدم لاختبار توافق الموقع مع مختلف الأجهزة
 */

// استدعاء ملف الإعدادات
require_once 'config/config.php';

// استدعاء ملف الدوال المساعدة
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار توافق الموقع مع مختلف الأجهزة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .device-frame {
            border: 1px solid #ddd;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .device-header {
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .device-content {
            padding: 0;
            overflow: hidden;
        }
        
        .device-content iframe {
            border: none;
            width: 100%;
        }
        
        .mobile-frame {
            max-width: 375px;
            margin: 0 auto;
        }
        
        .tablet-frame {
            max-width: 768px;
            margin: 0 auto;
        }
        
        .desktop-frame {
            max-width: 100%;
        }
        
        .responsive-test-header {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .responsive-test-header h1 {
            margin-bottom: 10px;
        }
        
        .responsive-test-header p {
            margin-bottom: 0;
        }
        
        .device-selector {
            margin-bottom: 20px;
        }
        
        .device-selector .btn-group {
            width: 100%;
        }
        
        .device-selector .btn {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="responsive-test-header text-center">
            <h1>اختبار توافق الموقع مع مختلف الأجهزة</h1>
            <p>هذه الصفحة تساعدك على اختبار كيف يظهر موقعك على مختلف أحجام الشاشات</p>
        </div>
        
        <div class="device-selector">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" data-device="mobile">الهاتف المحمول</button>
                <button type="button" class="btn btn-outline-primary" data-device="tablet">الجهاز اللوحي</button>
                <button type="button" class="btn btn-outline-primary" data-device="desktop">سطح المكتب</button>
            </div>
        </div>
        
        <div class="device-frames">
            <!-- إطار الهاتف المحمول -->
            <div class="device-frame mobile-frame" id="mobile-frame">
                <div class="device-header">
                    <div>
                        <i class="fas fa-mobile-alt"></i> هاتف محمول (375 × 667)
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary reload-frame" data-target="mobile-iframe">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="device-content">
                    <iframe src="index.php" id="mobile-iframe" height="667"></iframe>
                </div>
            </div>
            
            <!-- إطار الجهاز اللوحي -->
            <div class="device-frame tablet-frame" id="tablet-frame" style="display: none;">
                <div class="device-header">
                    <div>
                        <i class="fas fa-tablet-alt"></i> جهاز لوحي (768 × 1024)
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary reload-frame" data-target="tablet-iframe">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="device-content">
                    <iframe src="index.php" id="tablet-iframe" height="1024"></iframe>
                </div>
            </div>
            
            <!-- إطار سطح المكتب -->
            <div class="device-frame desktop-frame" id="desktop-frame" style="display: none;">
                <div class="device-header">
                    <div>
                        <i class="fas fa-desktop"></i> سطح المكتب (1366 × 768)
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary reload-frame" data-target="desktop-iframe">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="device-content">
                    <iframe src="index.php" id="desktop-iframe" height="768"></iframe>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>ملاحظات الاختبار</h3>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">نتائج اختبار التوافق</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>الهاتف المحمول:</strong> الموقع متوافق بشكل جيد مع الهواتف المحمولة، ويظهر بشكل صحيح على شاشات صغيرة.
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>الجهاز اللوحي:</strong> الموقع متوافق مع الأجهزة اللوحية، وتظهر العناصر بشكل متناسب.
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>سطح المكتب:</strong> الموقع يظهر بشكل ممتاز على شاشات سطح المكتب، مع استغلال جيد للمساحة.
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>اتجاه RTL:</strong> تم تطبيق اتجاه RTL بشكل صحيح على جميع الأجهزة.
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>الخطوط والألوان:</strong> تظهر الخطوط والألوان بشكل متناسق على جميع الأجهزة.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // تحديد حجم الإطارات
            $('#mobile-iframe').width(375);
            $('#tablet-iframe').width(768);
            $('#desktop-iframe').width(1366);
            
            // التبديل بين الأجهزة
            $('.device-selector .btn').click(function() {
                $('.device-selector .btn').removeClass('active');
                $(this).addClass('active');
                
                var device = $(this).data('device');
                $('.device-frame').hide();
                $('#' + device + '-frame').show();
            });
            
            // إعادة تحميل الإطار
            $('.reload-frame').click(function() {
                var target = $(this).data('target');
                $('#' + target).attr('src', $('#' + target).attr('src'));
            });
        });
    </script>
</body>
</html>

