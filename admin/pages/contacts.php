<?php
/**
 * صفحة إدارة طلبات التواصل - إصدار محسن
 */

// التحقق من تسجيل الدخول
if(!$user->isLoggedIn()) {
    redirect('login.php');
}

// تحديد العملية المطلوبة
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// معالجة الحذف
if($action == 'delete' && $id > 0) {
    if($contact->deleteContact($id)) {
        set_flash_message('تم حذف طلب التواصل بنجاح', 'success');
    } else {
        set_flash_message('حدث خطأ أثناء حذف طلب التواصل', 'danger');
    }
    redirect('index.php?page=contacts');
}

// معالجة تعيين كمقروء
if($action == 'mark_read' && $id > 0) {
    if($contact->markContactAsRead($id)) {
        set_flash_message('تم تغيير حالة طلب التواصل بنجاح', 'success');
    } else {
        set_flash_message('حدث خطأ أثناء تغيير حالة طلب التواصل', 'danger');
    }
    redirect('index.php?page=contacts');
}

// بيانات طلب التواصل
$contact_data = [];
if($action == 'view' && $id > 0) {
    $contact_data = $contact->getContactById($id);
    if(!$contact_data) {
        set_flash_message('طلب التواصل غير موجود', 'danger');
        redirect('index.php?page=contacts');
    }
    $contact->markAsRead($id);
}

// الحصول على القائمة
$contacts_list = $contact->getContacts();
$page_title = $action == 'view' ? 'عرض طلب التواصل' : 'إدارة طلبات التواصل';
?>

<?php if($action == 'list'): ?>
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-envelope mr-1"></i> قائمة طلبات التواصل
        </h3>
        <div class="card-tools">
            <div class="btn-group">
                <a href="index.php?page=contacts&filter=unread" class="btn btn-warning btn-sm">
                    <i class="fas fa-envelope"></i> غير مقروءة
                </a>
                <a href="index.php?page=contacts" class="btn btn-info btn-sm">
                    <i class="fas fa-list"></i> الكل
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped datatable">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">الاسم</th>
                        <th width="15%">البريد الإلكتروني</th>
                        <th width="10%">الهاتف</th>
                        <th width="25%">الرسالة</th>
                        <th width="10%">التاريخ</th>
                        <th width="10%">الحالة</th>
                        <th width="10%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($contacts_list)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">لا توجد طلبات تواصل</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($contacts_list as $item): ?>
                    <tr class="<?= $item['is_read'] ? '' : 'font-weight-bold' ?>">
                        <td><?= $item['id'] ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><a href="mailto:<?= $item['email'] ?>"><?= $item['email'] ?></a></td>
                        <td><?= $item['phone'] ?: '--' ?></td>
                        <td><?= truncate_text($item['message'], 50) ?></td>
                        <td><?= format_date($item['created_at'], true) ?></td>
                        <td>
                            <span class="badge badge-<?= $item['is_read'] ? 'success' : 'danger' ?>">
                                <?= $item['is_read'] ? 'مقروء' : 'غير مقروء' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="index.php?page=contacts&action=view&id=<?= $item['id'] ?>" 
                                   class="btn btn-info" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if(!$item['is_read']): ?>
                                <a href="index.php?page=contacts&action=mark_read&id=<?= $item['id'] ?>" 
                                   class="btn btn-success" title="تعيين كمقروء">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                <a href="index.php?page=contacts&action=delete&id=<?= $item['id'] ?>" 
                                   class="btn btn-danger btn-delete" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php elseif($action == 'view'): ?>
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-envelope-open-text mr-1"></i> عرض طلب التواصل
        </h3>
        <div class="card-tools">
            <a href="index.php?page=contacts" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right mr-1"></i> العودة
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">الاسم:</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($contact_data['name']) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">البريد الإلكتروني:</label>
                    <p class="form-control-plaintext">
                        <a href="mailto:<?= $contact_data['email'] ?>"><?= $contact_data['email'] ?></a>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">رقم الهاتف:</label>
                    <p class="form-control-plaintext">
                        <?= $contact_data['phone'] ? 
                            '<a href="tel:'.$contact_data['phone'].'">'.$contact_data['phone'].'</a>' : 
                            '<span class="text-muted">غير متوفر</span>' ?>
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">التاريخ:</label>
                    <p class="form-control-plaintext"><?= format_date($contact_data['created_at'], true) ?></p>
                </div>
            </div>
        </div>
        
        <?php if(!empty($contact_data['subject'])): ?>
        <div class="form-group">
            <label class="font-weight-bold">الموضوع:</label>
            <p class="form-control-plaintext"><?= htmlspecialchars($contact_data['subject']) ?></p>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label class="font-weight-bold">الرسالة:</label>
            <div class="border rounded p-3 bg-light">
                <?= nl2br(htmlspecialchars($contact_data['message'])) ?>
            </div>
        </div>
        
        <div class="form-group text-left mt-4">
            <a href="mailto:<?= $contact_data['email'] ?>" class="btn btn-primary">
                <i class="fas fa-reply mr-1"></i> الرد
            </a>
            <?php if($contact_data['phone']): ?>
            <a href="tel:<?= $contact_data['phone'] ?>" class="btn btn-success">
                <i class="fas fa-phone mr-1"></i> الاتصال
            </a>
            <?php endif; ?>
            <a href="index.php?page=contacts&action=delete&id=<?= $contact_data['id'] ?>" 
               class="btn btn-danger btn-delete">
                <i class="fas fa-trash mr-1"></i> حذف
            </a>
        </div>
    </div>
</div>
<?php endif; ?>