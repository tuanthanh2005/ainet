<div id="home-section" style="display: <?php echo ($tab === 'home') ? 'block' : 'none'; ?>;">
    <!-- Hero / Intro Section -->
    <div class="row align-items-center g-5 py-5 mb-5 rounded-4 position-relative overflow-hidden fade-in-element" style="background: rgba(255,255,255,0.45); border: 1px solid var(--border-color); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); margin-left: 0; margin-right: 0;">
        <div class="col-lg-7 text-start ps-4 ps-md-5">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill fw-bold" style="letter-spacing:1px; font-size:0.75rem;"><i class="fa-solid fa-sparkles me-1"></i> HỆ SINH THÁI TÀI KHOẢN PREMIUM</span>
            <h1 class="display-5 fw-bold text-dark mb-3 lh-sm">Sở Hữu Tài Khoản AI <br><span class="text-gradient fw-extrabold" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Chính Hãng, Giá Rẻ</span></h1>
            <p class="lead text-muted mb-4 fs-6" style="line-height:1.7;">
                <?php echo nl2br(htmlspecialchars($settings['heroDesc'] ?? 'Chào mừng bạn đến với AI CỦA TÔI - nền tảng hàng đầu cung cấp các tài khoản Premium (ChatGPT Plus, Claude Pro, Midjourney, YouTube Premium, GitHub Copilot...) tự động 24/7. Uy tín, an toàn, kích hoạt ngay lập tức với chế độ bảo hành 1 đổi 1 trọn gói.')); ?>
            </p>
            <div class="d-flex gap-2 gap-md-3">
                <a href="<?php echo Url::products(); ?>" class="btn btn-buy flex-fill px-2 px-md-4 py-2.5 fs-6 shadow-sm text-center" style="white-space: nowrap;"><i class="fa-solid fa-store me-1 me-md-2"></i>Xem sản phẩm</a>
                <a href="<?php echo Url::about(); ?>" class="btn btn-outline-dark flex-fill px-2 px-md-4 py-2.5 fs-6 text-center" style="border-radius: 8px; white-space: nowrap;"><i class="fa-solid fa-circle-info me-1 me-md-2"></i>Về chúng tôi</a>
            </div>
        </div>
        <div class="col-lg-5 text-center pe-4 pe-md-5 d-none d-lg-block">
            <img src="<?php echo url('assets/images/gemini_share.png'); ?>" class="img-fluid rounded-4 shadow-sm" alt="AI Của Tôi" loading="eager" fetchpriority="high" decoding="async" style="max-height: 280px; object-fit: cover; border: 1px solid var(--border-color);">
        </div>
    </div>

    <style>
        .pulse-green {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green-anim 1.8s infinite;
            vertical-align: middle;
        }
        @keyframes pulse-green-anim {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
        .system-monitor-card {
            background: rgba(255, 255, 255, 0.45);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            border-radius: 16px;
        }
        .system-monitor-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.85);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
        }
        .live-label {
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 700;
            color: rgba(99, 102, 241, 0.8);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>

    <!-- Product Showcase Section -->
    <?php
    $renderProductList = function($productList) {
        if (empty($productList)) {
            echo '<p class="text-center text-muted py-4">Chưa có sản phẩm.</p>';
            return;
        }
        echo '<div class="row g-4">';
        foreach ($productList as $index => $product):
        ?>
        <div class="col-6 col-md-4 col-lg-3 product-item" data-category="<?= htmlspecialchars($product['category_slug'] ?? '') ?>">
            <div class="card product-card position-relative h-100" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>">
                <?php if (!empty($product['badge'])): ?>
                    <span class="badge-hot"><?= htmlspecialchars($product['badge'] ?? '') ?></span>
                <?php endif; ?>
                <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>" class="card-img-top"
                    alt="<?= htmlspecialchars($product['title'] ?? ($product['category'] ?? 'Sản phẩm')) ?>"
                    loading="lazy" decoding="async">
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
                        <p class="text-muted small mb-2"><i class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> me-1"></i><?= htmlspecialchars($shortFeatureText) ?></p>
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
                            <a href="<?= url('index.php?action=checkoutPage&product_id=' . urlencode($product['id']) . '&variant_idx=0') ?>" class="btn btn-buy shadow-sm" data-auth-required="true">Mua ngay</a>
                            <a href="<?= url('index.php?action=addToCart&id=' . urlencode($product['id'])) ?>" class="btn btn-cart-icon shadow-sm" title="Thêm"><i class="fa-solid fa-plus"></i></a>
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
        <div class="text-center mb-4">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill fw-bold" style="letter-spacing:1px; font-size:0.75rem;"><i class="fa-solid fa-crown me-1 text-warning"></i> SẢN PHẨM NỔI BẬT</span>
            <h2 class="display-6 fw-extrabold mb-2" style="background: linear-gradient(135deg, #1e293b, #4338ca); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Khám Phá Sản Phẩm</h2>
        </div>

        <ul class="nav nav-pills justify-content-center mb-4 gap-2 showcase-tabs" id="productShowcaseTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 fw-bold shadow-sm" id="best-selling-tab" data-bs-toggle="pill" data-bs-target="#best-selling" type="button" role="tab" aria-controls="best-selling" aria-selected="true">
                    <i class="fa-solid fa-fire text-danger me-1"></i> Bán chạy nhất
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-bold shadow-sm" id="highest-rated-tab" data-bs-toggle="pill" data-bs-target="#highest-rated" type="button" role="tab" aria-controls="highest-rated" aria-selected="false">
                    <i class="fa-solid fa-star text-warning me-1"></i> Đánh giá cao
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-bold shadow-sm" id="newest-tab" data-bs-toggle="pill" data-bs-target="#newest" type="button" role="tab" aria-controls="newest" aria-selected="false">
                    <i class="fa-solid fa-sparkles text-primary me-1"></i> Mới ra mắt
                </button>
            </li>
        </ul>

        <div class="tab-content" id="productShowcaseTabContent">
            <div class="tab-pane fade show active" id="best-selling" role="tabpanel" aria-labelledby="best-selling-tab">
                <?php $renderProductList($bestSellingProducts ?? []); ?>
            </div>
            <div class="tab-pane fade" id="highest-rated" role="tabpanel" aria-labelledby="highest-rated-tab">
                <?php $renderProductList($highestRatedProducts ?? []); ?>
            </div>
            <div class="tab-pane fade" id="newest" role="tabpanel" aria-labelledby="newest-tab">
                <?php $renderProductList($newestProducts ?? []); ?>
            </div>
        </div>
        
        <div class="text-center mt-4 pt-2">
            <a href="<?php echo Url::products(); ?>" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold shadow-sm" style="transition: all 0.3s ease;">
                Xem tất cả sản phẩm <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
    
    <style>
        .showcase-tabs .nav-link {
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--border-color);
            color: #4b5563;
            transition: all 0.3s ease;
        }
        .showcase-tabs .nav-link:hover {
            background-color: #f3f4f6;
            transform: translateY(-2px);
        }
        .showcase-tabs .nav-link.active {
            background: var(--vip-gradient);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3) !important;
        }
        .showcase-tabs .nav-link.active i {
            color: white !important;
        }
    </style>

    <!-- Live System Operations Dashboard -->
    <div class="mb-5 fade-in-element" style="animation-delay: 0.1s;">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
            <span class="pulse-green"></span>
            <span class="text-uppercase fw-extrabold text-muted small" style="letter-spacing: 1.5px; font-size: 0.7rem;">Hệ thống giám sát trạng thái & vận hành trực tuyến</span>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-4 system-monitor-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="live-label mb-2"><i class="fa-solid fa-server"></i> Máy Chủ</span>
                        <div class="fs-2 fw-extrabold text-success" style="font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                            <span class="pulse-green"></span> ONLINE
                        </div>
                    </div>
                    <div class="small text-muted mt-2 fw-medium">Tự động bàn giao 24/7/365</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="p-4 system-monitor-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="live-label mb-2"><i class="fa-solid fa-bolt"></i> Phản Hồi</span>
                        <div class="fs-2 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">
                            ~5 Giây
                        </div>
                    </div>
                    <div class="small text-muted mt-2 fw-medium">Kích hoạt qua API SePay tức thì</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="p-4 system-monitor-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="live-label mb-2"><i class="fa-solid fa-cart-shopping"></i> Đơn Tự Động</span>
                        <div class="fs-2 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">
                            <?= number_format($systemStats['completed_orders'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div class="small text-muted mt-2 fw-medium">Tổng giao dịch được xử lý thành công</div>
                </div>
            </div>
            
            <div class="col-6 col-md-3">
                <div class="p-4 system-monitor-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="live-label mb-2"><i class="fa-solid fa-star"></i> Đánh Giá Hài Lòng</span>
                        <div class="fs-2 fw-extrabold text-primary" style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800;">
                            <?= number_format($systemStats['average_rating'], 1) ?>/5 ★
                        </div>
                    </div>
                    <div class="small text-muted mt-2 fw-medium">Chỉ số phản hồi thực tế từ người dùng</div>
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
                    <?php if ($cat['icon']): ?>
                        <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> <?= htmlspecialchars($cat['icon_color'] ?: 'text-primary') ?> fs-5"></i>
                    <?php endif; ?>
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
                                <span class="small text-muted" style="min-width: 60px;">5 ★</span>
                                <div class="progress flex-grow-1 mx-3" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $systemStats['pct_5'] ?>%;" aria-valuenow="<?= $systemStats['pct_5'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted font-monospace"><?= $systemStats['pct_5'] ?>%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small text-muted" style="min-width: 60px;">4 ★</span>
                                <div class="progress flex-grow-1 mx-3" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $systemStats['pct_4'] ?>%;" aria-valuenow="<?= $systemStats['pct_4'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted font-monospace"><?= $systemStats['pct_4'] ?>%</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-muted" style="min-width: 60px;">1-3 ★</span>
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
        $qParam = !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '';
        $sortParam = !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
        ?>
        <a href="<?php echo Url::withQuery(Url::products(), array_filter(['q' => $searchQuery ?? null, 'sort' => ($sort ?? '') !== 'newest' ? ($sort ?? '') : null])); ?>"
           class="cat-pill text-decoration-none <?php echo (empty($currentCat) || $currentCat === 'all') ? 'active' : ''; ?>">Tất Cả</a>
        <?php foreach ($categories as $cat): ?>
            <?php $activeSlug = $cat['seo_slug'] ?: $cat['slug']; ?>
            <a href="<?php echo Url::category($activeSlug) . ($qParam || $sortParam ? '?' . ltrim($qParam . $sortParam, '&') : ''); ?>"
               class="cat-pill text-decoration-none <?= $cat['is_pro'] ? 'pro-glow' : '' ?> <?php echo ($currentCat === $cat['slug'] || $currentCat === $cat['seo_slug']) ? 'active' : ''; ?>">
                <?php if ($cat['icon']): ?>
                    <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> <?= htmlspecialchars($cat['icon_color'] ?: 'text-primary') ?>"></i>
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
                    <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($product['title'] ?? ($product['category'] ?? 'Sản phẩm')) ?>"
                        loading="<?= $index < 4 ? 'eager' : 'lazy' ?>" <?= $index < 4 ? 'fetchpriority="high"' : '' ?> decoding="async">
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
                                    class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> me-1"></i>
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
                                    class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> me-1"></i>
                                <?= htmlspecialchars($product['feature_text'] ?? '') ?></p>
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
                                    <span class="text-muted text-decoration-line-through small" style="line-height:1;">
                                        <?= number_format($cardOrig, 0, ',', '.') ?>đ
                                    </span>
                                </div>
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
                ?>
                <div class="col-12 col-md-6 col-lg-4 fade-in-element" style="animation-delay: <?= $index * 0.1 ?>s;">
                    <a href="<?= htmlspecialchars(Url::blog($blog)) ?>" class="text-decoration-none text-reset">
                        <div class="blog-card h-100 bg-white shadow-sm border" style="border-radius:12px; overflow:hidden;">
                            <img src="<?= htmlspecialchars(image_url($blogImage)) ?>" class="blog-img" loading="lazy" decoding="async" alt="<?= htmlspecialchars($blogTitle) ?>">
                            <div class="blog-content p-3">
                                <?php if ($blogDate): ?>
                                    <div class="blog-date small text-muted mb-1"><?= htmlspecialchars($blogDate) ?></div>
                                <?php endif; ?>
                                <h3 class="blog-title mb-0 fw-bold fs-6 text-dark"><?= htmlspecialchars($blogTitle) ?></h3>
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
