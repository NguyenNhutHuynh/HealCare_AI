<?php
$profile = $_SESSION['patient_profile'] ?? [];
$selectedConditions = $profile['conditions'] ?? [];
?>
<section class="page-intro"><span class="eyebrow">CÁ NHÂN HÓA GỢI Ý</span><h1>Hồ sơ sức khỏe</h1><p>Điền những thông tin bạn thấy thoải mái chia sẻ để Healcare lọc món phù hợp hơn.</p></section>
<form class="profile-form" method="post" action="<?= url('profile') ?>">
    <div class="privacy-note">🔒 Hồ sơ chỉ lưu tạm trong phiên trình duyệt hiện tại, không thay thế bệnh án chính thức. Không nhập số căn cước, địa chỉ hoặc thông tin nhận dạng nhạy cảm.</div>
    <div class="form-grid">
        <label>Tên gọi (không bắt buộc)<input name="name" value="<?= e((string) ($profile['name'] ?? '')) ?>" placeholder="Ví dụ: cô Lan"></label>
        <label>Tuổi<input type="number" name="age" min="0" max="120" value="<?= e((string) ($profile['age'] ?? '')) ?>" placeholder="Ví dụ: 65"></label>
        <label>Giới tính<select name="gender"><option value="">Không muốn nêu</option><option value="Nữ" <?= ($profile['gender'] ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option><option value="Nam" <?= ($profile['gender'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option></select></label>
        <label>Chế độ ăn hiện tại<input name="diet" value="<?= e((string) ($profile['diet'] ?? '')) ?>" placeholder="Ví dụ: ăn nhạt, ít đường"></label>
    </div>
    <fieldset><legend>Bệnh đang được theo dõi</legend><div class="check-grid"><?php foreach ($knowledge->diseases() as $slug => $disease): ?><label class="check-item"><input type="checkbox" name="conditions[]" value="<?= e($slug) ?>" <?= in_array($slug, $selectedConditions, true) ? 'checked' : '' ?>><span><?= e($disease['name']) ?></span></label><?php endforeach; ?></div></fieldset>
    <div class="form-grid">
        <label>Dị ứng hoặc không ăn được<textarea name="allergies" rows="3" placeholder="Ví dụ: cá, đậu phộng, sữa..."><?= e((string) ($profile['allergies'] ?? '')) ?></textarea></label>
        <label>Thuốc/thực phẩm bổ sung đang dùng<textarea name="medication" rows="3" placeholder="Không bắt buộc; chỉ ghi tên chung nếu muốn"><?= e((string) ($profile['medication'] ?? '')) ?></textarea></label>
        <label class="wide">Điều cần lưu ý thêm<textarea name="notes" rows="3" placeholder="Ví dụ: khó nhai, ăn kém, cần món mềm..."><?= e((string) ($profile['notes'] ?? '')) ?></textarea></label>
    </div>
    <div class="form-actions"><a class="text-link" href="<?= url('home') ?>">Để sau</a><button class="button" type="submit">Lưu hồ sơ và xem gợi ý <span>→</span></button></div>
</form>
