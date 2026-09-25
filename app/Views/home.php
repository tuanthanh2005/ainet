<div id="home-section" style="display: <?php echo ($tab === 'home') ? 'block' : 'none'; ?>;">
    <!-- Hero / Intro Section (Chỉ hiển thị trên laptop / PC, ẩn trên mobile) -->
    <div class="hero-banner-card d-none d-lg-block position-relative overflow-hidden mb-5 rounded-4 p-4 p-md-5 border shadow-sm fade-in-element"
         style="background: radial-gradient(circle at 90% 15%, rgba(99, 102, 241, 0.08) 0%, transparent 50%), radial-gradient(circle at 10% 85%, rgba(168, 85, 247, 0.05) 0%, transparent 50%), linear-gradient(135deg, #ffffff 0%, #fcfdfe 100%); border-color: rgba(226, 232, 240, 0.9) !important; box-shadow: 0 16px 36px -12px rgba(15, 23, 42, 0.05) !important;">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-7 text-start">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3 fw-bold"
                     style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.18); color: #6366f1; font-size: 0.75rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary"></i>
                    <span>HỆ SINH THÁI TÀI KHOẢN PREMIUM</span>
                </div>
                <h1 class="display-6 display-md-5 fw-extrabold text-dark mb-3" style="letter-spacing: -0.02em; line-height: 1.25;">
                    Sở Hữu Tài Khoản AI <br>
                    <span class="text-gradient" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Chính Hãng, Giá Rẻ</span>
                </h1>
                <p class="text-muted mb-4 fs-6" style="line-height: 1.7; max-width: 540px;">
                    <?php echo nl2br(htmlspecialchars($settings['heroDesc'] ?? 'Chào mừng bạn đến với AI CỦA TÔI - nền tảng hàng đầu cung cấp các tài khoản Premium (ChatGPT Plus, Claude Pro, Midjourney, YouTube Premium, GitHub Copilot...) tự động 24/7. Uy tín, an toàn, kích hoạt ngay lập tức với chế độ bảo hành 1 đổi 1 trọn gói.')); ?>
                </p>

                <!-- Value Props / Feature Highlights -->
                <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 mb-4 pt-1">
                    <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill shadow-xs" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2); font-size: 0.82rem; font-weight: 600; color: #b45309;">
                        <i class="fa-solid fa-bolt text-warning me-2 fs-6"></i>
                        <span>Kích hoạt 24/7</span>
                    </div>
                    <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill shadow-xs" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.82rem; font-weight: 600; color: #047857;">
                        <i class="fa-solid fa-shield-halved text-success me-2 fs-6"></i>
                        <span>Bảo hành 1 đổi 1</span>
                    </div>
                    <div class="d-inline-flex align-items-center px-3 py-1.5 rounded-pill shadow-xs" style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.2); font-size: 0.82rem; font-weight: 600; color: #4338ca;">
                        <i class="fa-solid fa-circle-check text-primary me-2 fs-6"></i>
                        <span>Chính hãng 100%</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="<?php echo Url::products(); ?>" class="btn btn-buy px-4 py-2.5 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.95rem; min-height: 44px;">
                        <i class="fa-solid fa-store"></i>
                        <span>Xem sản phẩm</span>
                    </a>
                    <a href="<?php echo Url::about(); ?>" class="btn btn-outline-dark px-4 py-2.5 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 0.95rem; min-height: 44px; border-color: #cbd5e1; background: #ffffff; color: #1e293b;">
                        <i class="fa-solid fa-circle-info text-secondary"></i>
                        <span>Về chúng tôi</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center">
                <div class="position-relative w-100" style="max-width: 420px;">
                    <!-- Ambient Backlight -->
                    <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 rounded-4" style="background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, rgba(168, 85, 247, 0.12) 50%, transparent 75%); filter: blur(28px); z-index: 0; pointer-events: none; transform: scale(1.08);"></div>
                    <!-- Image Showcase Card -->
                    <div class="position-relative rounded-4 overflow-hidden shadow-sm border" style="border-color: rgba(226, 232, 240, 0.8) !important; z-index: 1; aspect-ratio: 16 / 10; background: #0f172a;">
                        <img src="<?php echo url('assets/images/gemini_share.webp'); ?>" width="1024" height="1024" class="w-100 h-100" alt="AI Của Tôi" loading="eager" fetchpriority="high" decoding="async" style="object-fit: cover; transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                        <!-- Top-Right Chip -->
                        <div class="position-absolute top-0 end-0 m-3 px-2.5 py-1 rounded-pill d-flex align-items-center gap-1.5 shadow-sm" style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.15); font-size: 0.72rem; color: #38bdf8; font-weight: 600;">
                            <span class="spinner-grow spinner-grow-sm text-success" style="width: 7px; height: 7px;" role="status"></span>
                            <span>Auto 24/7</span>
                        </div>
                        <!-- Bottom Gradient Floating Caption -->
                        <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(to top, rgba(15, 23, 42, 0.88) 0%, rgba(15, 23, 42, 0.4) 60%, transparent 100%);">
                            <div class="d-flex align-items-center justify-content-between text-white" style="font-size: 0.78rem;">
                                <span class="fw-semibold"><i class="fa-solid fa-award text-warning me-1"></i>Hệ thống tự động uy tín</span>
                                <span class="badge bg-primary bg-opacity-75 text-white fw-bold px-2 py-1" style="font-size: 0.7rem;">Chính hãng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Showcase Section -->
    <?php
    $cleanFaIcon = function($icon, $default = 'fa-circle-check') {
        $icon = trim((string)$icon) ?: $default;
        if ($icon === 'fa-sparkles') return 'fa-wand-magic-sparkles';
        if ($icon === 'fa-shield-check') return 'fa-shield-halved';
        return !str_contains($icon, 'fa-') ? 'fa-' . $icon : $icon;
    };
    $renderProductList = function($productList) use ($cleanFaIcon) {
        if (empty($productList)) {
            echo '<p class="text-center text-muted py-4">Chưa có sản phẩm.</p>';
            return;
        }
        echo '<div class="row g-4">';
        foreach ($productList as $index => $product):
            $cardVariantIdx = Product::firstAvailableVariantIndex($product);
            $cardAvailable = $cardVariantIdx !== null;
            $cardVariantIdx = $cardVariantIdx ?? 0;
            $cardOption = $product['options'][$cardVariantIdx] ?? [];
        ?>
        <div class="col-6 col-md-4 col-lg-3 product-item" data-category="<?= htmlspecialchars($product['category_slug'] ?? '') ?>">
            <div class="card product-card position-relative h-100" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>">
                <?php if (!empty($product['badge'])): ?>
                    <span class="badge-hot"><?= htmlspecialchars($product['badge'] ?? '') ?></span>
                <?php endif; ?>
                <div class="product-image-wrapper position-relative w-100 overflow-hidden" style="aspect-ratio: 5 / 4; background-color: var(--light-gray, #f3f4f6);">
                    <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>" class="card-img-top position-absolute top-0 start-0 w-100 h-100"
                        alt="<?= htmlspecialchars($product['title'] ?? ($product['category'] ?? 'Sản phẩm')) ?>"
                        loading="lazy" decoding="async" style="object-fit: cover;">
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
                                $rating = (float)($product['rating'] ?? 0);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($rating > 0 && $i <= floor($rating)) echo '<i class="fa-solid fa-star"></i>';
                                    elseif ($rating > 0 && $i - 0.5 == $rating) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                    else echo '<i class="fa-regular fa-star text-secondary opacity-50"></i>';
                                }
                            ?>
                        </div>
                        <span class="text-muted sold-text">Đã bán <?= number_format($product['sold_count'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <?php
                        $shortFeatureText = trim((string) ($product['feature_text'] ?? ''));
                        $cardFeatures = array_values(array_filter((array) ($product['card_features'] ?? []), 'strlen'));
                    ?>
                    <?php if ($shortFeatureText !== ''): ?>
                        <p class="text-muted small mb-2"><i class="fa-solid <?= htmlspecialchars($cleanFaIcon($product['feature_icon'] ?? '')) ?> me-1"></i><?= htmlspecialchars($shortFeatureText) ?></p>
                    <?php endif; ?>
                    <div class="mt-auto">
                        <?php
                            $cardPrice = (float) ($cardOption['price'] ?? $product['price'] ?? 0);
                            $variantOrig = (float) ($cardOption['original_price'] ?? 0);
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
                            <?php if ($cardAvailable): ?>
                                <a href="<?= url('index.php?action=checkoutPage&product_id=' . urlencode($product['id']) . '&variant_idx=' . $cardVariantIdx) ?>" class="btn btn-buy shadow-sm" data-auth-required="true">Mua ngay</a>
                                <a href="<?= url('index.php?action=addToCart&id=' . urlencode($product['id']) . '&variant_idx=' . $cardVariantIdx) ?>" class="btn btn-cart-icon shadow-sm" title="Thêm"><i class="fa-solid fa-plus"></i></a>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary shadow-sm flex-grow-1" disabled>Hết hàng</button>
                                <button type="button" class="btn btn-cart-icon shadow-sm" disabled aria-label="Hết hàng"><i class="fa-solid fa-ban"></i></button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        endforeach;
        echo '</div>';
    };
    ?>

    <div class="mb-5 fade-in-element" style="animation-delay: 0.2s;">
        <div class="text-center mb-3 mb-md-4 pb-md-2">
            <h2 class="fw-extrabold mb-0 d-inline-flex align-items-center justify-content-center gap-2" style="font-size: clamp(1.25rem, 4.5vw, 2.15rem); letter-spacing: -0.02em; color: #0f172a;">
                <i class="fa-solid fa-crown text-warning" style="font-size: 0.9em;"></i>
                <span>Sản Phẩm <span class="text-gradient" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Nổi Bật</span></span>
            </h2>
        </div>

        <?php
            $featuredList = !empty($products) ? $products : (!empty($bestSellingProducts) ? $bestSellingProducts : []);
            $featuredList = array_slice($featuredList, 0, 8);
            $renderProductList($featuredList);
        ?>
        
        <div class="text-center mt-4 pt-3">
            <a href="<?php echo Url::products(); ?>" class="btn btn-outline-dark rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="transition: all 0.3s ease; font-size: 0.95rem; border-width: 1.5px;">
                <span>Xem tất cả sản phẩm</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Why Choose Us & Service Guarantees Section -->
    <div class="mb-5 fade-in-element" style="animation-delay: 0.1s;">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-2 fw-bold"
                 style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); color: #059669; font-size: 0.75rem; letter-spacing: 0.8px;">
                <i class="fa-solid fa-shield-halved"></i>
                <span>CAM KẾT CHẤT LƯỢNG DỊCH VỤ</span>
            </div>
            <h2 class="fw-extrabold mb-2" style="font-size: clamp(1.25rem, 4.5vw, 2.15rem); letter-spacing: -0.02em; color: #0f172a;">
                Tại Sao Khách Hàng <span class="text-gradient" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Tin Tưởng Lựa Chọn?</span>
            </h2>
            <p class="text-muted mx-auto mb-0" style="max-width: 520px; font-size: 0.92rem; line-height: 1.6;">
                Quy trình vận hành minh bạch, bàn giao tự động và bảo vệ tối đa quyền lợi khách hàng
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="commitment-card h-100 d-flex flex-column text-start">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="commitment-icon rounded-3 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                            <i class="fa-solid fa-bolt-lightning fs-4"></i>
                        </div>
                        <span class="badge rounded-pill fw-bold" style="background: rgba(99, 102, 241, 0.08); color: #6366f1; font-size: 0.7rem;">TỰ ĐỘNG</span>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Bàn Giao Tức Thì</h5>
                    <p class="text-muted small mb-0 lh-base flex-grow-1">
                        Kết nối trực tiếp cổng SePay ngân hàng. Hệ thống nhận diện thanh toán và gửi thông tin tài khoản ngay lập tức.
                    </p>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="commitment-card h-100 d-flex flex-column text-start">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="commitment-icon rounded-3 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fa-solid fa-shield-heart fs-4"></i>
                        </div>
                        <span class="badge rounded-pill fw-bold" style="background: rgba(16, 185, 129, 0.08); color: #059669; font-size: 0.7rem;">AN TÂM</span>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Bảo Hành 1 Đổi 1</h5>
                    <p class="text-muted small mb-0 lh-base flex-grow-1">
                        Bảo hành trọn vẹn toàn bộ thời hạn gói mua. Sẵn sàng đổi tài khoản mới hoặc xử lý nhanh gọn nếu có sự cố kỹ thuật.
                    </p>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="commitment-card h-100 d-flex flex-column text-start">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="commitment-icon rounded-3 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fa-solid fa-award fs-4"></i>
                        </div>
                        <span class="badge rounded-pill fw-bold" style="background: rgba(245, 158, 11, 0.08); color: #b45309; font-size: 0.7rem;">CHÍNH HÃNG</span>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Tài Khoản Ổn Định</h5>
                    <p class="text-muted small mb-0 lh-base flex-grow-1">
                        Cung cấp tài khoản chính chủ, tạo lập an toàn với email chuẩn. Không dùng thẻ lậu/thẻ ảo đảm bảo sử dụng bền lâu.
                    </p>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-3">
                <div class="commitment-card h-100 d-flex flex-column text-start">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="commitment-icon rounded-3 d-inline-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                            <i class="fa-solid fa-headset fs-4"></i>
                        </div>
                        <span class="badge rounded-pill fw-bold" style="background: rgba(14, 165, 233, 0.08); color: #0284c7; font-size: 0.7rem;">HỖ TRỢ</span>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">Hỗ Trợ Kỹ Thuật 24/7</h5>
                    <p class="text-muted small mb-0 lh-base flex-grow-1">
                        Kênh hỗ trợ trực tiếp qua Zalo và Telegram có nhân viên trực thường xuyên, hướng dẫn đăng nhập và sử dụng chi tiết.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Highlights Brands Section -->
    <div class="mb-5 text-center fade-in-element" style="animation-delay: 0.3s;">
        <h2 class="fw-bold mb-2">Đối Tác & Dịch Vụ Phổ Biến</h2>
        <p class="text-muted small mb-4">Các bộ công cụ AI hàng đầu thế giới được nhiều doanh nghiệp sử dụng</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo Url::category($cat['seo_slug'] ?: $cat['slug']); ?>" class="text-decoration-none d-flex align-items-center gap-2 px-4 py-3 rounded-4 shadow-sm border border-light bg-white hover-up" style="transition: all 0.3s ease;">
                    <i class="fa-solid <?= htmlspecialchars($cat['icon'] ?: 'fa-layer-group') ?> <?= htmlspecialchars($cat['icon_color'] ?: 'text-primary') ?> fs-5"></i>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Dynamic Reviews & Testimonials Section -->
    <?php if (!empty($recentReviews)): ?>
    <div class="mb-5 fade-in-element" style="animation-delay: 0.4s;">
        <div class="row g-4 align-items-stretch">
            <!-- Left Side: Summary Card -->
            <div class="col-lg-4">
                <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between system-monitor-card" style="background: rgba(255,255,255,0.6);">
                    <div>
                        <span class="live-label mb-2"><i class="fa-solid fa-shield-heart"></i> Đánh Giá Xác Thực</span>
                        <h2 class="fw-bold text-dark mb-3">Ý Kiến Khách Hàng</h2>
                        
                        <div class="d-flex align-items-center gap-3 my-4">
                            <span class="display-4 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">
                                <?= number_format($systemStats['average_rating'], 1) ?>
                            </span>
                            <div>
                                <div class="text-warning fs-5">
                                    <?php
                                    $stars = round($systemStats['average_rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $stars ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                <div class="small text-muted mt-1">Trung bình cộng đánh giá</div>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small text-muted" style="min-width: 60px;">5 <i class="fa-solid fa-star text-warning"></i></span>
                                <div class="progress flex-grow-1 mx-3" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $systemStats['pct_5'] ?>%;" aria-valuenow="<?= $systemStats['pct_5'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted font-monospace"><?= $systemStats['pct_5'] ?>%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small text-muted" style="min-width: 60px;">4 <i class="fa-solid fa-star text-warning"></i></span>
                                <div class="progress flex-grow-1 mx-3" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $systemStats['pct_4'] ?>%;" aria-valuenow="<?= $systemStats['pct_4'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted font-monospace"><?= $systemStats['pct_4'] ?>%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-muted" style="min-width: 60px;">1-3 <i class="fa-solid fa-star text-warning"></i></span>
                                <div class="progress flex-grow-1 mx-3" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $systemStats['pct_1_3'] ?>%;" aria-valuenow="<?= $systemStats['pct_1_3'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted font-monospace"><?= $systemStats['pct_1_3'] ?>%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-muted small mb-3">Mọi ý kiến đóng góp được hệ thống tự động ghi nhận từ tài khoản người dùng thực tế sau khi mua sản phẩm thành công.</p>
                        <a href="<?php echo Url::products(); ?>" class="btn btn-sm btn-outline-dark w-100 rounded-pill py-2.5 fw-bold" style="border-radius: 8px;"><i class="fa-solid fa-pen-nib me-2"></i>Xem sản phẩm & Trải nghiệm</a>
                    </div>
                </div>
            </div>
            
            <!-- Right Side: Reviews Feed -->
            <div class="col-lg-8">
                <div class="row g-3">
                    <?php if (!empty($recentReviews)): ?>
                        <?php foreach (array_slice($recentReviews, 0, 4) as $rev): ?>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between system-monitor-card" style="background: rgba(255,255,255,0.75);">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="text-warning">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    echo $i <= (int)$rev['rating'] ? '<i class="fa-solid fa-star fs-6"></i>' : '<i class="fa-regular fa-star fs-6"></i>';
                                                }
                                                ?>
                                            </div>
                                            <span class="text-muted smaller"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y', strtotime($rev['created_at'])) ?></span>
                                        </div>
                                        <p class="text-dark small italic mb-3" style="line-height: 1.6;">"<?= htmlspecialchars($rev['content']) ?>"</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-3 border-top pt-3">
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            $reviewerAvatar = !empty($rev['user_avatar']) ? htmlspecialchars($rev['user_avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($rev['user_name'] ?? 'Khách') . '&background=6366f1&color=fff';
                                            ?>
                                            <img src="<?= $reviewerAvatar ?>" class="rounded-circle me-3 border border-2 border-white shadow-sm" width="38" height="38" alt="Avatar">
                                            <div>
                                                <div class="fw-bold text-dark small" style="font-size: 0.85rem;"><?= htmlspecialchars($rev['user_name'] ?? 'Khách Hàng') ?></div>
                                                <div class="text-muted smaller" style="font-size: 0.75rem;">Người mua hàng</div>
                                            </div>
                                        </div>
                                        <?php if (!empty($rev['product_title'])): ?>
                                            <span class="badge bg-light text-dark border small fw-normal py-1.5 px-2.5 rounded-pill" style="font-size: 0.7rem; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($rev['product_title']) ?>">
                                                <i class="fa-solid fa-shopping-bag text-primary me-1"></i><?= htmlspecialchars($rev['product_title']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fa-regular fa-comments fs-2 opacity-50 mb-3 d-block"></i>
                            <p class="mb-0">Chưa có đánh giá nào từ khách hàng.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Blog posts for home tab credibility -->
    <?php if (!empty($blogs)): ?>
        <div class="mb-5 fade-in-element" style="animation-delay: 0.5s;">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Cập Nhật Tin Tức & Hướng Dẫn</h2>
                    <p class="text-muted small mb-0">Các kiến thức hữu ích và mẹo sử dụng công cụ AI hiệu quả</p>
                </div>
                <a href="<?php echo Url::blogs(); ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach (array_slice($blogs, 0, 3) as $blog): ?>
                    <?php
                        $blogDate  = !empty($blog['created_at']) ? date('d/m/Y', strtotime($blog['created_at'])) : '';
                        $blogTitle = $blog['title'] ?? '';
                        $blogImage = $blog['image'] ?? '';
                    ?>
                    <div class="col-12 col-md-4">
                        <a href="<?= htmlspecialchars(Url::blog($blog)) ?>" class="text-decoration-none text-reset">
                            <div class="blog-card h-100 shadow-sm border bg-white" style="border-radius:12px; overflow:hidden; transition:all 0.3s ease;">
                                <div style="height: 160px; overflow:hidden;">
                                    <img src="<?= htmlspecialchars(image_url($blogImage)) ?>" class="w-100 h-100" loading="lazy" decoding="async" style="object-fit:cover; transition:all 0.5s ease;" alt="<?= htmlspecialchars($blogTitle) ?>">
                                </div>
                                <div class="p-3">
                                    <?php if ($blogDate): ?>
                                        <div class="text-muted small mb-1"><i class="fa-regular fa-clock me-1"></i><?= htmlspecialchars($blogDate) ?></div>
                                    <?php endif; ?>
                                    <h3 class="fw-bold mb-0 text-dark" style="font-size:1rem; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; height:2.8rem;"><?= htmlspecialchars($blogTitle) ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="products-section" style="display: <?php echo ($tab === 'products') ? 'block' : 'none'; ?>;">
    <!-- Category Pill Menu -->
    <div class="category-menu-wrapper fade-in-element" style="animation-delay: 0.1s;">
        <?php
        $currentCat = $categorySlug ?? ($_GET['category'] ?? '');
        $sortParam = !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
        ?>
        <a href="<?php echo Url::withQuery(Url::products(), array_filter(['sort' => ($sort ?? '') !== 'newest' ? ($sort ?? '') : null])); ?>"
           class="cat-pill text-decoration-none <?php echo (empty($currentCat) || $currentCat === 'all') ? 'active' : ''; ?>">Tất Cả</a>
        <?php foreach ($categories as $cat): ?>
            <?php $activeSlug = $cat['seo_slug'] ?: $cat['slug']; ?>
            <a href="<?php echo Url::category($activeSlug) . ($sortParam ? '?' . ltrim($sortParam, '&') : ''); ?>"
               class="cat-pill text-decoration-none <?= $cat['is_pro'] ? 'pro-glow' : '' ?> <?php echo ($currentCat === $cat['slug'] || $currentCat === $cat['seo_slug']) ? 'active' : ''; ?>">
                <i class="fa-solid <?= htmlspecialchars($cat['icon'] ?: 'fa-layer-group') ?> <?= htmlspecialchars($cat['icon_color'] ?: 'text-primary') ?>"></i>
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row g-4" id="product-list" data-page-size="12">
        <?php foreach ($products as $index => $product):
            $cardVariantIdx = Product::firstAvailableVariantIndex($product);
            $cardAvailable = $cardVariantIdx !== null;
            $cardVariantIdx = $cardVariantIdx ?? 0;
            $cardOption = $product['options'][$cardVariantIdx] ?? [];
        ?>
            <div class="col-6 col-md-4 col-lg-3 product-item"
                 data-category="<?= htmlspecialchars($product['category_slug'] ?? '') ?>">
                <div class="card product-card position-relative h-100" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>">
                    <?php if (!empty($product['badge'])): ?>
                        <span class="badge-hot"><?= htmlspecialchars($product['badge'] ?? '') ?></span>
                    <?php endif; ?>
                    <div class="product-image-wrapper position-relative w-100 overflow-hidden" style="aspect-ratio: 5 / 4; background-color: var(--light-gray, #f3f4f6);">
                        <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>" class="card-img-top position-absolute top-0 start-0 w-100 h-100"
                            alt="<?= htmlspecialchars($product['title'] ?? ($product['category'] ?? 'Sản phẩm')) ?>"
                            loading="<?= $index < 4 ? 'eager' : 'lazy' ?>" <?= $index < 4 ? 'fetchpriority="high"' : '' ?> decoding="async" style="object-fit: cover;">
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
                                    $rating = (float)($product['rating'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($rating > 0 && $i <= floor($rating)) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        } elseif ($rating > 0 && $i - 0.5 == $rating) {
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star text-secondary opacity-50"></i>';
                                        }
                                    }
                                ?>
                            </div>
                            <span class="text-muted sold-text">Đã bán <?= number_format($product['sold_count'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <?php
                            $shortFeatureText = trim((string) ($product['feature_text'] ?? ''));
                            $cardFeatures = array_values(array_filter((array) ($product['card_features'] ?? []), 'strlen'));
                        ?>
                        <?php if ($shortFeatureText !== ''): ?>
                            <p class="text-muted small mb-2"><i
                                    class="fa-solid <?= htmlspecialchars($cleanFaIcon($product['feature_icon'] ?? '')) ?> me-1"></i>
                                <?= htmlspecialchars($shortFeatureText) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($cardFeatures)): ?>
                            <ul class="product-card-features list-unstyled text-muted small mb-3">
                                <?php foreach (array_slice($cardFeatures, 0, 4) as $feature): ?>
                                    <li><i class="fa-solid fa-check me-2"></i><?= htmlspecialchars($feature) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php elseif ($shortFeatureText === ''): ?>
                            <p class="text-muted small mb-3"><i
                                    class="fa-solid <?= htmlspecialchars($cleanFaIcon($product['feature_icon'] ?? '')) ?> me-1"></i>
                                <?= htmlspecialchars($product['feature_text'] ?? '') ?></p>
                        <?php endif; ?>
                        <div class="mt-auto">
                            <?php
                                $cardPrice = (float) ($cardOption['price'] ?? $product['price'] ?? 0);
                                $variantOrig = (float) ($cardOption['original_price'] ?? 0);
                                $productOrig = (float) ($product['original_price'] ?? 0);
                                $cardOrig = $variantOrig > $cardPrice ? $variantOrig : $productOrig;
                                $cardHasDiscount = $cardOrig > $cardPrice && $cardPrice > 0;
                                $cardOff = $cardHasDiscount ? round((1 - $cardPrice / $cardOrig) * 100) : 0;
                            ?>
                            <?php if ($cardHasDiscount): ?>
                                <div class="d-flex align-items-baseline flex-wrap gap-2 mb-3">
                                    <p class="product-price mb-0"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                                    <span class="badge bg-danger"><?= '-' . $cardOff . '%' ?></span>
                                    <span class="text-muted text-decoration-line-through small" style="line-height:1;">
                                        <?= number_format($cardOrig, 0, ',', '.') ?>đ
                                    </span>
                                </div>
                            <?php else: ?>
                                <p class="product-price mb-3"><?= number_format($cardPrice, 0, ',', '.') ?>đ</p>
                            <?php endif; ?>
                            <div class="product-actions position-relative" style="z-index: 2;">
                                <?php if ($cardAvailable): ?>
                                    <a href="<?= url('index.php?action=checkoutPage&product_id=' . urlencode($product['id']) . '&variant_idx=' . $cardVariantIdx) ?>"
                                       class="btn btn-buy shadow-sm" data-auth-required="true">Mua ngay</a>
                                    <a href="<?= url('index.php?action=addToCart&id=' . urlencode($product['id']) . '&variant_idx=' . $cardVariantIdx) ?>"
                                       class="btn btn-cart-icon shadow-sm" title="Thêm"><i class="fa-solid fa-plus"></i></a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary shadow-sm flex-grow-1" disabled>Hết hàng</button>
                                    <button type="button" class="btn btn-cart-icon shadow-sm" disabled aria-label="Hết hàng"><i class="fa-solid fa-ban"></i></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <?php
        $pageUrl = function($p) use ($currentCat, $searchQuery, $sort) {
            $base = !empty($currentCat) && $currentCat !== 'all' ? Url::category($currentCat) : Url::products();
            $query = array_filter([
                'q' => $searchQuery ?: null,
                'sort' => $sort !== 'newest' ? $sort : null,
                'page' => $p > 1 ? $p : null,
            ]);
            return Url::withQuery($base, $query) . '#products-section';
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

    <!-- Tìm nhanh theo sản phẩm (Internal Linking Automation - Item 3) -->
    <div class="mt-5 pt-4 border-top fade-in-element">
        <h4 class="fw-bold mb-3 fs-5 text-dark"><i class="fa-solid fa-tags text-primary me-2"></i>Tìm nhanh theo sản phẩm</h4>
        <div class="position-relative">
            <div id="keyword-grid" class="d-flex flex-wrap gap-2 keyword-search-grid" style="max-height: 76px; overflow: hidden; transition: max-height 0.3s ease-in-out;">
                <?php
                $seoKeywordsData = [];
                $keywordPath = APP_ROOT . '/config/seo_keywords.json';
                if (file_exists($keywordPath)) {
                    $seoData = json_decode(file_get_contents($keywordPath), true);
                    $seoKeywordsData = $seoData['keywords'] ?? [];
                }
                if (empty($seoKeywordsData)) {
                    $seoKeywordsData = [
                        'gpt' => ['display_name' => 'ChatGPT'],
                        'gemini' => ['display_name' => 'Gemini'],
                        'copilot' => ['display_name' => 'GitHub Copilot'],
                        'canva' => ['display_name' => 'Canva Pro'],
                        'netflix' => ['display_name' => 'Netflix Premium'],
                        'youtube' => ['display_name' => 'YouTube Premium'],
                        'claude' => ['display_name' => 'Claude Pro'],
                        'midjourney' => ['display_name' => 'Midjourney'],
                        'suno' => ['display_name' => 'Suno AI'],
                        'runway' => ['display_name' => 'Runway Gen-3'],
                        'luma' => ['display_name' => 'Luma Dream Machine'],
                        'elevenlabs' => ['display_name' => 'ElevenLabs'],
                        'perplexity' => ['display_name' => 'Perplexity Pro'],
                        'poe' => ['display_name' => 'Poe AI'],
                        'capcut' => ['display_name' => 'CapCut Pro'],
                        'freepik' => ['display_name' => 'Freepik Premium'],
                        'adobe' => ['display_name' => 'Adobe CC'],
                        'cursor' => ['display_name' => 'Cursor AI'],
                        'gamma' => ['display_name' => 'Gamma App'],
                        'ai' => ['display_name' => 'Tài khoản AI']
                    ];
                }
                foreach ($seoKeywordsData as $slug => $info):
                ?>
                    <a href="<?= Url::search(htmlspecialchars($slug)) ?>" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 small hover-up">
                        <?= htmlspecialchars($info['display_name'] ?? $slug) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div id="keyword-toggle-btn-wrap" class="text-center mt-3 d-none">
                <button class="btn btn-sm btn-light border rounded-pill px-4 py-1.5 text-muted hover-up shadow-sm small" onclick="toggleKeywords()">
                    <span id="keyword-toggle-text">Xem thêm</span>
                    <i id="keyword-toggle-icon" class="fa-solid fa-chevron-down ms-1.5"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const grid = document.getElementById('keyword-grid');
            const wrap = document.getElementById('keyword-toggle-btn-wrap');
            if (grid && wrap) {
                // If scrollHeight is greater than max-height threshold
                if (grid.scrollHeight > 82) {
                    wrap.classList.remove('d-none');
                }
            }
        });

        function toggleKeywords() {
            const grid = document.getElementById('keyword-grid');
            const txt = document.getElementById('keyword-toggle-text');
            const icon = document.getElementById('keyword-toggle-icon');
            if (!grid || !txt || !icon) return;

            if (grid.style.maxHeight === '76px' || grid.style.maxHeight === '') {
                grid.style.maxHeight = grid.scrollHeight + 'px';
                txt.textContent = 'Thu gọn';
                icon.className = 'fa-solid fa-chevron-up ms-1.5';
                
                setTimeout(() => {
                    if (grid.style.maxHeight !== '76px') {
                        grid.style.maxHeight = 'none';
                    }
                }, 300);
            } else {
                grid.style.maxHeight = grid.offsetHeight + 'px';
                grid.offsetHeight; // reflow
                grid.style.maxHeight = '76px';
                txt.textContent = 'Xem thêm';
                icon.className = 'fa-solid fa-chevron-down ms-1.5';
            }
        }
    </script>
</div>

<div id="blog-section" style="display: <?php echo ($tab === 'blog') ? 'block' : 'none'; ?>;">
    <!-- Blog Section Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2 fw-semibold text-primary bg-primary bg-opacity-10 small"
                 style="font-size: 0.78rem; letter-spacing: 0.5px;">
                <i class="fa-solid fa-newspaper"></i>
                <span>TẠP CHÍ & CẨM NANG AI</span>
            </div>
            <h1 class="h3 fw-extrabold mb-1 text-dark" style="letter-spacing: -0.02em;">Kiến Thức & Tin Tức Công Nghệ AI</h1>
            <p class="text-muted small mb-0">Tổng hợp các bài viết hướng dẫn, thủ thuật Prompt và cẩm nang tài khoản bản quyền hữu ích</p>
        </div>
        <?php if (!empty($totalBlogs) && $totalBlogs > 0): ?>
            <div class="d-flex align-items-center gap-2 text-muted small bg-light px-3 py-1.5 rounded-pill border">
                <i class="fa-regular fa-file-lines text-primary"></i>
                <span>Trang <strong><?= $blogPage ?? 1 ?></strong> / <strong><?= $totalBlogPages ?? 1 ?></strong> (Tổng <strong><?= $totalBlogs ?></strong> bài)</span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Blog Grid: 3 articles per row, 6 articles per page (2 rows) -->
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
                ?>
                <div class="col-12 col-md-6 col-lg-4 fade-in-element" style="animation-delay: <?= $index * 0.08 ?>s;">
                    <a href="<?= htmlspecialchars(Url::blog($blog)) ?>" class="text-decoration-none text-reset">
                        <div class="blog-card h-100 bg-white shadow-sm border rounded-4 overflow-hidden">
                            <div class="blog-img-wrap position-relative">
                                <img src="<?= htmlspecialchars(image_url($blogImage)) ?>" class="blog-img" loading="lazy" decoding="async" alt="<?= htmlspecialchars($blogTitle) ?>">
                                <span class="position-absolute bottom-0 start-0 m-3 px-2.5 py-1 rounded-pill small fw-semibold text-white bg-dark bg-opacity-75 shadow-xs" style="font-size: 0.72rem; backdrop-filter: blur(4px);">
                                    <i class="fa-regular fa-clock me-1"></i> 3 phút đọc
                                </span>
                            </div>
                            <div class="blog-content p-3.5">
                                <?php if ($blogDate): ?>
                                    <div class="blog-date small text-muted mb-2 d-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                        <i class="fa-regular fa-calendar-days text-primary opacity-75"></i>
                                        <span><?= htmlspecialchars($blogDate) ?></span>
                                    </div>
                                <?php endif; ?>
                                <h3 class="blog-title mb-0 fw-bold fs-6 text-dark"><?= htmlspecialchars($blogTitle) ?></h3>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Blog Pagination (6 articles per page = 2 rows of 3) -->
    <?php if (($totalBlogPages ?? 1) > 1): ?>
        <?php
        $blogPageUrl = function($p) {
            return Url::withQuery(Url::blogs(), ['page' => $p > 1 ? $p : null]);
        };
        ?>
        <div class="d-flex justify-content-center mt-5 mb-4">
            <nav aria-label="Blog pagination">
                <ul class="pagination pagination-md shadow-sm border rounded-pill overflow-hidden bg-white px-2 py-1 mb-0" style="gap:4px; list-style: none;">
                    <?php if (($blogPage ?? 1) > 1): ?>
                        <li class="page-item">
                            <a class="page-link border-0 rounded-circle text-dark d-flex align-items-center justify-content-center" 
                               style="width:38px;height:38px;" href="<?= $blogPageUrl(($blogPage ?? 1) - 1) ?>" aria-label="Trang trước">
                                <i class="fa-solid fa-chevron-left small"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= ($totalBlogPages ?? 1); $i++): ?>
                        <li class="page-item <?= $i === ($blogPage ?? 1) ? 'active' : '' ?>">
                            <a class="page-link border-0 rounded-circle d-flex align-items-center justify-content-center <?= $i === ($blogPage ?? 1) ? 'bg-dark text-white fw-bold shadow-sm' : 'text-dark' ?>" 
                               style="width:38px;height:38px;" href="<?= $blogPageUrl($i) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if (($blogPage ?? 1) < ($totalBlogPages ?? 1)): ?>
                        <li class="page-item">
                            <a class="page-link border-0 rounded-circle text-dark d-flex align-items-center justify-content-center" 
                               style="width:38px;height:38px;" href="<?= $blogPageUrl(($blogPage ?? 1) + 1) ?>" aria-label="Trang sau">
                                <i class="fa-solid fa-chevron-right small"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
<script>
    const fakeOrders = <?php echo json_encode($recentOrders); ?>;
</script>
