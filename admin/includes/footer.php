<?php
/**
 * ملف التذييل للوحة التحكم
 */
?>
<!-- Main Footer -->
<footer class="main-footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <p class="mb-0">
                    &copy; <?php echo date('Y'); ?> 
                    <a href="<?php echo BASE_URL; ?>"><?php echo htmlspecialchars($site_settings['site_name']); ?></a>. 
                    جميع الحقوق محفوظة.
                </p>
            </div>
            <div class="col-sm-6 text-left">
                <p class="mb-0 float-sm-right">
                    <b>الإصدار</b> <?php echo htmlspecialchars($site_settings['version'] ?? '1.0.0'); ?>
                    | وقت التحميل: <?php echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3); ?> ثانية
                </p>
            </div>
        </div>
    </div>
</footer>