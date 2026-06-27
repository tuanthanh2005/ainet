<?php
$contactMethods = json_decode($settings['contact_methods'] ?? '[]', true);
if (empty($contactMethods)) {
    $contactMethods = [
        ['icon' => 'fa-solid fa-envelope', 'text' => 'tetuongmmovn@gmail.com'],
        ['icon' => 'fa-brands fa-telegram', 'text' => '@specademy'],
        ['icon' => 'fa-solid fa-phone', 'text' => 'Zalo: 0967037906']
    ];
}
?>
<div class="container py-5">
    <!-- Thông báo chính -->
    <div class="text-center mb-5 fade-in-element">
        <span class="badge bg-danger bg-opacity-10 text-danger mb-3 px-3 py-2 rounded-pill fw-bold" style="letter-spacing:1px; font-size:0.75rem;">
            <i class="fa-solid fa-circle-exclamation me-1"></i> KHÔNG TÌM THẤY SẢN PHẨM
        </span>
        <h1 class="display-5 fw-extrabold mb-3" style="background: linear-gradient(135deg, #ef4444, #f43f5e); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Sản phẩm không tồn tại hoặc đã dừng bán
        </h1>
        <p class="text-muted fs-5 max-w-2xl mx-auto">
            Sản phẩm bạn đang tìm kiếm hiện tại không có trên hệ thống hoặc đường dẫn đã thay đổi.
        </p>
    </div>

    <!-- Hộp liên hệ đặt hàng từ Admin -->
    <div class="row justify-content-center mb-5">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg text-center p-4 p-md-5 rounded-4 bg-gradient-premium position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
                <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 10rem; transform: translate(20%, -20%);">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h3 class="fw-bold mb-3 text-white">Bạn muốn đặt mua sản phẩm này?</h3>
                <p class="text-light opacity-75 mb-4">
                    Đừng lo lắng, hãy liên hệ trực tiếp với Admin để đặt hàng nhanh chóng và nhận hỗ trợ tốt nhất!
                </p>
                <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 p-3 mb-4 text-start">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fa-solid fa-bell text-warning"></i>
                        <span class="fw-bold text-white small">Thông báo đặt hàng:</span>
                    </div>
                    <p class="mb-0 text-light opacity-80 small">
                        Bạn hãy liên hệ admin đặt hàng nhé! Admin hỗ trợ kích hoạt tài khoản, gói dịch vụ nhanh chóng 24/7.
                    </p>
                </div>
                
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <?php foreach ($contactMethods as $method): 
                        $link = '#';
                        $btnClass = 'btn-outline-light';
                        if (strpos($method['text'], '@') !== false) {
                            $tgUser = str_replace('@', '', $method['text']);
                            $link = 'https://t.me/' . $tgUser;
                            $btnClass = 'btn-primary';
                        } elseif (strpos($method['text'], '0') !== false || strpos($method['text'], 'Zalo') !== false) {
                            $phone = preg_replace('/[^0-9]/', '', $method['text']);
                            $link = 'https://zalo.me/' . ($settings['zalo'] ?? $phone);
                            $btnClass = 'btn-success';
                        } elseif (filter_var($method['text'], FILTER_VALIDATE_EMAIL)) {
                            $link = 'mailto:' . $method['text'];
                        }
                    ?>
                        <a href="<?= htmlspecialchars($link) ?>" target="_blank" class="btn <?= $btnClass ?> py-3 px-4 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="<?= htmlspecialchars($method['icon']) ?> fs-5"></i>
                            <span><?= htmlspecialchars($method['text']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm liên quan (nếu có) -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="fw-extrabold fs-3 m-0" style="background: linear-gradient(135deg, #1e293b, #4338ca); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <i class="fa-solid fa-bag-shopping text-primary me-2"></i>Các sản phẩm liên quan
                </h2>
                <a href="<?= Url::products() ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $product): ?>
                <div class="col-6 col-md-4 col-lg-3 product-item">
                    <div class="card product-card position-relative h-100" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>">
                        <?php if (!empty($product['badge'])): ?>
                            <span class="badge-hot"><?= htmlspecialchars($product['badge'] ?? '') ?></span>
                        <?php endif; ?>
                        <div class="product-image-wrapper position-relative w-100 overflow-hidden" style="aspect-ratio: 5 / 4; background-color: var(--light-gray, #f3f4f6);">
                            <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>" class="card-img-top position-absolute top-0 start-0 w-100 h-100"
                                 alt="<?= htmlspecialchars($product['title'] ?? '') ?>"
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
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Danh sách bài viết liên quan (nếu có) -->
    <?php if (!empty($relatedBlogs)): ?>
        <div class="mb-5">
            <h2 class="fw-extrabold fs-3 mb-4" style="background: linear-gradient(135deg, #1e293b, #4338ca); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                <i class="fa-solid fa-newspaper text-primary me-2"></i>Các bài viết chia sẻ liên quan
            </h2>
            <div class="row g-4">
                <?php foreach ($relatedBlogs as $blog): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 16px; transition: all 0.3s; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);">
                        <img src="<?= htmlspecialchars(image_url($blog['image'] ?? '')) ?>" class="card-img-top" alt="<?= htmlspecialchars($blog['title'] ?? '') ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-4 d-flex flex-column">
                            <h4 class="fw-bold mb-2">
                                <a href="<?= htmlspecialchars(Url::blog($blog)) ?>" class="text-decoration-none text-dark stretched-link">
                                    <?= htmlspecialchars($blog['title'] ?? '') ?>
                                </a>
                            </h4>
                            <p class="text-muted small mb-3">
                                <?= htmlspecialchars(Seo::truncate(strip_tags($blog['description'] ?? ''), 120)) ?>
                            </p>
                            <span class="text-primary fw-bold mt-auto small">Xem chi tiết <i class="fa-solid fa-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
