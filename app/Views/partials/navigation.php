<?php
$currentAction = $_GET['action'] ?? 'index';
$activeTab = $tab ?? ($_GET['tab'] ?? 'products');
if (!in_array($activeTab, ['products', 'blog'])) {
    $activeTab = 'products';
}
?>
<div class="navigation-wrapper mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-light">
        <div class="tab-nav">
            <a href="<?php echo url('index.php?tab=products'); ?>"
               class="tab-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'products') ? 'active' : ''; ?>">Sản Phẩm</a>
            <a href="<?php echo url('index.php?tab=blog'); ?>"
               class="tab-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'blog') ? 'active' : ''; ?>">Tạp Chí</a>

            <a href="<?php echo Url::about(); ?>"
                class="tab-btn text-decoration-none <?php echo $currentAction === 'about' ? 'active' : ''; ?>">Giới
                Thiệu</a>

            <a href="<?php echo Url::contact(); ?>"
                class="tab-btn text-decoration-none <?php echo $currentAction === 'contact' ? 'active' : ''; ?>">Liên
                Hệ</a>
        </div>

        <?php if ($currentAction === 'index'): ?>
            <select
                class="form-select w-auto border-0 bg-transparent fw-semibold shadow-none cursor-pointer d-none d-md-block">
                <option>Mới cập nhật</option>
                <option>Giá: Thấp đến Cao</option>
                <option>Bán chạy nhất</option>
            </select>
        <?php endif; ?>
    </div>
</div>