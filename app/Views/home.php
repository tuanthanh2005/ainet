<div id="products-section" style="display: <?php echo ($tab === 'products') ? 'block' : 'none'; ?>;">
    <!-- Category Pill Menu -->
    <div class="category-menu-wrapper fade-in-element" style="animation-delay: 0.1s;">
        <?php
        $currentCat = $categorySlug ?? ($_GET['category'] ?? '');
        $qParam = !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '';
        $sortParam = !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
        ?>
        <a href="<?php echo url('index.php?tab=products&category=all' . $qParam . $sortParam); ?>"
           class="cat-pill text-decoration-none <?php echo (empty($currentCat) || $currentCat === 'all') ? 'active' : ''; ?>">Tất Cả</a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?php echo url('index.php?tab=products&category=' . urlencode($cat['slug']) . $qParam . $sortParam); ?>"
               class="cat-pill text-decoration-none <?= $cat['is_pro'] ? 'pro-glow' : '' ?> <?php echo ($currentCat === $cat['slug']) ? 'active' : ''; ?>">
                <?php if ($cat['icon']): ?>
                    <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> <?= htmlspecialchars($cat['icon_color']) ?>"></i>
                <?php endif; ?>
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4" id="product-list" data-page-size="12">
        <?php foreach ($products as $index => $product): ?>
            <div class="col-6 col-md-4 col-lg-3 product-item"
                data-category="<?= htmlspecialchars($product['category_slug'] ?? '') ?>">
                <div class="card product-card position-relative h-100" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>">
                    <?php if (!empty($product['badge'])): ?>
                        <span class="badge-hot"><?= htmlspecialchars($product['badge'] ?? '') ?></span>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars($product['image'] ?? '') ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($product['category'] ?? '') ?>">
                    <div class="card-body d-flex flex-column p-4">
                        <h3 class="product-title mb-1">
                            <a href="<?= htmlspecialchars(Url::product($product)) ?>" class="stretched-link text-decoration-none text-dark">
                                <?= htmlspecialchars($product['title'] ?? '') ?>
                            </a>
                        </h3>
                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.75rem;">
                            <div class="text-warning">
                                <?php 
                                    $rating = $product['rating'] ?? 5;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= floor($rating)) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        } elseif ($i - 0.5 == $rating) {
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
                                ?>
                            </div>
                            <span class="text-muted sold-text">Đã bán <?= number_format($product['sold_count'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <p class="text-muted small mb-3"><i
                                class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> me-1"></i>
                            <?= htmlspecialchars($product['feature_text'] ?? '') ?></p>
                        <div class="mt-auto">
                            <?php
                                $cardOrig = (float) ($product['options'][0]['original_price'] ?? 0);
                                $cardPrice = (float) ($product['options'][0]['price'] ?? $product['price'] ?? 0);
                                $cardHasDiscount = $cardOrig > $cardPrice && $cardPrice > 0;
                                $cardOff = $cardHasDiscount ? round((1 - $cardPrice / $cardOrig) * 100) : 0;
                            ?>
                            <?php if ($cardHasDiscount): ?>
                                <div class="d-flex align-items-baseline gap-2 mb-1">
                                    <p class="product-price mb-0"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                                    <span class="badge bg-danger"><?= '-' . $cardOff . '%' ?></span>
                                </div>
                                <p class="text-muted text-decoration-line-through small mb-3" style="line-height:1;">
                                    <?= number_format($cardOrig, 0, ',', '.') ?>đ
                                </p>
                            <?php else: ?>
                                <p class="product-price mb-3"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                            <?php endif; ?>
                            <div class="product-actions position-relative" style="z-index: 2;">
                                <a href="<?= url('index.php?action=checkoutPage&product_id=' . urlencode($product['id']) . '&variant_idx=0') ?>" 
                                   class="btn btn-buy shadow-sm" data-auth-required="true">Mua ngay</a>
                                <a href="<?= url('index.php?action=addToCart&id=' . urlencode($product['id'])) ?>" 
                                   class="btn btn-cart-icon shadow-sm" title="Thêm">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <?php
        $catParam = !empty($currentCat) ? '&category=' . urlencode($currentCat) : '';
        $sortParam = !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
        $qParam = !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '';
        $pageUrl = function($p) use ($catParam, $qParam, $sortParam) {
            return url('index.php?tab=products' . $catParam . $qParam . $sortParam . '&page=' . $p . '#products-section');
        };
        ?>
        <div class="d-flex justify-content-center mt-5 mb-3">
            <nav aria-label="Product pagination">
                <ul class="pagination pagination-md shadow-sm border rounded-pill overflow-hidden bg-white px-2 py-1 mb-0" style="gap:4px; list-style: none;">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link border-0 rounded-circle text-dark d-flex align-items-center justify-content-center" 
                               style="width:36px;height:36px;" href="<?= $pageUrl($page - 1) ?>">
                                <i class="fa-solid fa-chevron-left small"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link border-0 rounded-circle d-flex align-items-center justify-content-center <?= $i === $page ? 'bg-dark text-white fw-bold' : 'text-dark' ?>" 
                               style="width:36px;height:36px;" href="<?= $pageUrl($i) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link border-0 rounded-circle text-dark d-flex align-items-center justify-content-center" 
                               style="width:36px;height:36px;" href="<?= $pageUrl($page + 1) ?>">
                                <i class="fa-solid fa-chevron-right small"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div id="product-empty" class="text-center py-5 text-muted">
            <i class="fa-regular fa-folder-open fs-1 opacity-25 mb-3 d-block"></i>
            <p class="mb-0">Không có sản phẩm nào trong danh mục này.</p>
        </div>
    <?php endif; ?>
</div>

<div id="blog-section" style="display: <?php echo ($tab === 'blog') ? 'block' : 'none'; ?>;">
    <div class="row g-4">
        <?php if (empty($blogs)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="fa-regular fa-newspaper fs-1 opacity-25 mb-3 d-block"></i>
                <p class="mb-0">Chưa có bài viết nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($blogs as $index => $blog): ?>
                <?php
                    $blogDate  = !empty($blog['created_at']) ? date('d/m/Y', strtotime($blog['created_at'])) : '';
                    $blogTitle = $blog['title'] ?? '';
                    $blogImage = $blog['image'] ?? '';
                    $blogId    = $blog['id'] ?? '';
                ?>
                <div class="col-12 col-md-6 col-lg-4 fade-in-element" style="animation-delay: <?= $index * 0.1 ?>s;">
                    <a href="<?= htmlspecialchars(Url::blog($blog)) ?>" class="text-decoration-none text-reset">
                        <div class="blog-card h-100">
                            <img src="<?= htmlspecialchars($blogImage) ?>" class="blog-img" alt="<?= htmlspecialchars($blogTitle) ?>">
                            <div class="blog-content">
                                <?php if ($blogDate): ?>
                                    <div class="blog-date"><?= htmlspecialchars($blogDate) ?></div>
                                <?php endif; ?>
                                <h3 class="blog-title mb-0"><?= htmlspecialchars($blogTitle) ?></h3>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
    const fakeOrders = <?php echo json_encode($recentOrders); ?>;
</script>