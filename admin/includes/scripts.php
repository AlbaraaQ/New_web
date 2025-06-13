<?php
/**
 * ملف النصوص البرمجية للوحة التحكم
 */
?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

<!-- Summernote -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<!-- Custom Scripts -->
<script>
    $(document).ready(function() {
        // تفعيل DataTables
        $('.datatable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/ar.json"
            },
            "order": [[0, "desc"]]
        });
        
        // تفعيل Summernote
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            lang: 'ar-AR'
        });
        
        // تفعيل Select2
        $('.select2').select2({
            dir: "rtl",
            language: "ar"
        });
        
        // معاينة الصورة قبل الرفع
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
            
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-image').attr('src', e.target.result);
                    $('#preview-container').show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // تأكيد الحذف
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            var deleteUrl = $(this).attr('href');
            
            if (confirm('هل أنت متأكد من رغبتك في حذف هذا العنصر؟')) {
                window.location.href = deleteUrl;
            }
        });
        
        // إنشاء الرابط المخصص تلقائياً
        $('#name').on('keyup', function() {
            var name = $(this).val();
            var slug = slugify(name);
            $('#slug').val(slug);
        });
        
        // دالة تحويل النص إلى رابط مخصص
        function slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }
    });
</script>
