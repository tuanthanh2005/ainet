<?php
/**
 * Render the SEO placeholder landing page for pre-orders.
 * Item 2: SEO Placeholder & Pre-order Link Hijacking
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center mb-5">
            <!-- Glassmorphism Hero Card -->
            <div class="placeholder-hero-card p-5 mb-5 rounded-4 position-relative overflow-hidden fade-in-element" 
                 style="background: rgba(255, 255, 255, 0.45); border: 1px solid var(--border-color); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);">
                
                <span class="badge bg-danger bg-opacity-10 text-danger mb-3 px-3 py-2 rounded-pill fw-bold" style="letter-spacing:1px; font-size:0.75rem;">
                    <i class="fa-solid fa-hourglass-start me-1 animate-spin-slow"></i> SẮP RA MẮT / PRE-ORDER
                </span>
                
                <h1 class="display-5 fw-extrabold text-dark mb-3 lh-sm">
                    Tài Khoản <span class="text-gradient fw-extrabold" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        <?= htmlspecialchars($productTitle) ?>
                    </span> Giá Rẻ
                </h1>
                
                <p class="lead text-muted mb-4 fs-6 mx-auto" style="max-width: 600px; line-height:1.7;">
                    Dịch vụ đang được hệ thống kỹ thuật cấu hình và kiểm nghiệm chất lượng. 
                    Đăng ký email ngay để nhận <strong>mã giảm giá 15%</strong> và thông báo tự động ngay khi sản phẩm chính thức mở bán!
                </p>

                <!-- Pre-order capture form -->
                <form action="<?= url('index.php?action=submitPreOrder') ?>" method="POST" class="preorder-form mx-auto" style="max-width: 500px;">
                    <?= Csrf::field(); ?>
                    <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
                    
                    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden bg-white p-1 border">
                        <span class="input-group-text border-0 bg-transparent text-muted ps-3"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control border-0 shadow-none py-2.5" placeholder="Nhập email của bạn..." required>
                        <button class="btn btn-buy px-4 py-2 fw-bold rounded-pill" type="submit">
                            <i class="fa-solid fa-bell me-1.5"></i> Đăng Ký Sớm
                        </button>
                    </div>
                </form>

                <div class="d-flex justify-content-center align-items-center gap-3 mt-4 text-muted small">
                    <span><i class="fa-solid fa-shield-halved text-success me-1"></i> Bảo mật thông tin</span>
                    <span class="opacity-50">|</span>
                    <span><i class="fa-solid fa-tag text-warning me-1"></i> Nhận code giảm 15%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Alternative Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5 fade-in-element" style="animation-delay: 0.2s;">
            <div class="text-center mb-4">
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-1.5 rounded-pill fw-bold" style="letter-spacing:1px; font-size:0.7rem;">
                    <i class="fa-solid fa-cubes me-1"></i> GIẢI PHÁP THAY THẾ HOÀN HẢO
                </span>
                <h2 class="fw-extrabold text-dark">Sản Phẩm Đang Sẵn Hàng</h2>
                <p class="text-muted small">Trong lúc chờ đợi, bạn có thể tham khảo các công cụ chất lượng cao có sẵn bên dưới</p>
            </div>

            <div class="row g-4">
                <?php foreach ($relatedProducts as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3 product-item">
                        <div class="card product-card position-relative h-100" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>">
                            <?php if (!empty($product['badge'])): ?>
                                <span class="badge-hot"><?= htmlspecialchars($product['badge'] ?? '') ?></span>
                            <?php endif; ?>
                            
                            <div class="product-image-wrapper position-relative w-100 overflow-hidden">
                                <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>" class="card-img-top position-absolute top-0 start-0 w-100 h-100"
                                     alt="<?= htmlspecialchars($product['title'] ?? '') ?>"
                                     loading="lazy" decoding="async">
                            </div>
                            
                            <div class="card-body d-flex flex-column p-4">
                                <h3 class="product-title mb-1">
                                    <a href="<?= htmlspecialchars(Url::product($product)) ?>" class="stretched-link text-decoration-none text-dark">
                                        <?= htmlspecialchars($product['title'] ?? '') ?>
                                    </a>
                                </h3>
                                
                                <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.75rem;">
                                    <div class="text-warning">
                                        <?php 
                                            $rating = (float)($product['rating'] ?? 5);
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star text-secondary opacity-50"></i>';
                                            }
                                        ?>
                                    </div>
                                    <span class="text-muted sold-text">Đã bán <?= number_format($product['sold_count'] ?? 0, 0, ',', '.') ?></span>
                                </div>
                                
                                <?php if (!empty($product['feature_text'])): ?>
                                    <p class="text-muted small mb-3"><i class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> me-1"></i><?= htmlspecialchars($product['feature_text']) ?></p>
                                <?php endif; ?>
                                
                                <div class="mt-auto">
                                    <?php
                                        $cardPrice = (float) ($product['options'][0]['price'] ?? $product['price'] ?? 0);
                                        $variantOrig = (float) ($product['options'][0]['original_price'] ?? 0);
                                        $productOrig = (float) ($product['original_price'] ?? 0);
                                        $cardOrig = $variantOrig > $cardPrice ? $variantOrig : $productOrig;
                                        $cardHasDiscount = $cardOrig > $cardPrice && $cardPrice > 0;
                                        $cardOff = $cardHasDiscount ? round((1 - $cardPrice / $cardOrig) * 100) : 0;
                                    ?>
                                    <?php if ($cardHasDiscount): ?>
                                        <div class="d-flex align-items-baseline flex-wrap gap-2 mb-3">
                                            <p class="product-price mb-0"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                                            <span class="badge bg-danger"><?= '-' . $cardOff . '%' ?></span>
                                            <span class="text-muted text-decoration-line-through small" style="line-height:1;"><?= number_format($cardOrig, 0, ',', '.') ?>đ</span>
                                        </div>
                                    <?php else: ?>
                                        <p class="product-price mb-3"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                                    <?php endif; ?>
                                    
                                    <div class="product-actions position-relative" style="z-index: 2;">
                                        <a href="<?= Url::product($product) ?>" class="btn btn-buy shadow-sm w-100 text-center">Chi tiết</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-slow {
    animation: spin-slow 8s linear infinite;
    display: inline-block;
}
.preorder-form .input-group {
    background-color: #ffffff;
    transition: all 0.3s ease;
}
.preorder-form .input-group:focus-within {
    border-color: var(--vip-primary) !important;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.15) !important;
}
</style>
