<?php
$currentAction = $_GET['action'] ?? 'index';
$activeTab = $tab ?? ($_GET['tab'] ?? 'home');
if (!in_array($activeTab, ['home', 'products', 'blog'])) {
    $activeTab = 'home';
}
$isProductPage = $currentAction === 'index' && $activeTab === 'products';
?>
<div class="navigation-wrapper <?php echo $isProductPage ? 'mb-4 mb-lg-2' : 'mb-0 d-none'; ?>">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 border-bottom border-light pb-2 pb-md-0 <?php echo $isProductPage ? 'justify-content-lg-end border-lg-0 pb-lg-0 mb-lg-0' : 'd-none'; ?>">
        <div class="tab-nav flex-grow-1 flex-md-grow-0 d-none">
            <a href="<?php echo url('index.php?tab=home'); ?>"
               class="tab-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'home') ? 'active' : ''; ?>">Trang Chủ</a>
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

        <?php if ($currentAction === 'index' && $activeTab === 'products'): ?>
            <?php $currentSort = $sort ?? $_GET['sort'] ?? 'newest'; ?>
            <select id="product-sort-select"
                class="form-select w-auto border-0 bg-transparent fw-semibold shadow-none cursor-pointer ms-auto"
                style="font-size: 0.9rem;"
                onchange="handleSortChange(this.value)">
                <option value="newest" <?php echo $currentSort === 'newest' ? 'selected' : ''; ?>>Mới cập nhật</option>
                <option value="price_asc" <?php echo $currentSort === 'price_asc' ? 'selected' : ''; ?>>Giá: Thấp đến Cao</option>
                <option value="best_seller" <?php echo $currentSort === 'best_seller' ? 'selected' : ''; ?>>Bán chạy nhất</option>
            </select>
        <?php endif; ?>
    </div>
</div>

<script>
function handleSortChange(val) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', val);
    window.location.search = urlParams.toString();
}
</script>