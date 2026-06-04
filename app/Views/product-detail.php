<?php
function render_product_detail_description(string $raw): string {
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }

    $tags = 'p|h[1-6]|ul|ol|li|blockquote|strong|em|br|a|img|span|b|i|u|table|thead|tbody|tr|th|td';
    if (preg_match('/&lt;\s*(' . $tags . ')\b/i', $raw)) {
        $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    if (preg_match('/<\s*(' . $tags . ')\b/i', $raw)) {
        $allowed = '<p><br><strong><b><em><i><u><s><h1><h2><h3><h4><ul><ol><li><blockquote><a><img><span><table><thead><tbody><tr><th><td>';
        return strip_tags($raw, $allowed);
    }

    return nl2br(htmlspecialchars($raw));
}

require_once APP_ROOT . '/app/Models/Review.php';
$reviews = Review::getByProductId($product['id'] ?? '');
$reviewCount = count($reviews);
?>
<div class="py-lg-4 pb-5">
    <a href="<?php echo url(); ?>" class="back-link mb-4 d-inline-block text-decoration-none text-dark fw-bold">
        <i class="fa-solid fa-arrow-left me-2"></i> Trở về danh sách
    </a>

    <?php if (empty($product)): ?>
        <div class="product-detail-container text-center py-5">
            <h1 class="fw-bold text-dark mb-3" style="font-size: 1.7rem;">Sản phẩm không tồn tại</h1>
            <p class="text-muted mb-4">Sản phẩm này có thể đã bị xóa, đổi đường dẫn hoặc đang bị ẩn.</p>
            <a href="<?php echo url('index.php?tab=products'); ?>" class="btn btn-buy px-4 py-3 rounded-pill">
                Xem danh sách sản phẩm
            </a>
        </div>
    <?php return; endif; ?>

    <div class="product-detail-container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0 text-center">
                <img src="<?= htmlspecialchars(image_url($product['image'])) ?>" class="detail-image shadow-sm w-100 rounded-4"
                    alt="<?= htmlspecialchars($product['title'] ?? 'Sản phẩm') ?>" loading="eager" fetchpriority="high" decoding="async" style="height: auto; object-fit: cover; border: 1px solid var(--border-color);">

                <!-- Trust Badges Under Image -->
                <div class="trust-badges-container">
                    <div class="trust-badge-card">
                        <div class="trust-icon-circle">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="trust-badge-title">Bảo Hành</div>
                        <div class="trust-badge-desc">1 đổi 1 trọn đời</div>
                    </div>
                    <div class="trust-badge-card">
                        <div class="trust-icon-circle">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div class="trust-badge-title">Giao Hàng</div>
                        <div class="trust-badge-desc">Tự động 5 phút</div>
                    </div>
                    <div class="trust-badge-card">
                        <div class="trust-icon-circle">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="trust-badge-title">Hỗ Trợ</div>
                        <div class="trust-badge-desc">24/7 mọi lúc</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5">
                <div class="detail-category mb-2 text-muted fw-bold text-uppercase"
                    style="font-size: 0.85rem; letter-spacing: 1px;"><?= htmlspecialchars($product['category'] ?? '') ?>
                </div>
                <h1 class="detail-title mb-3 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">
                    <?= htmlspecialchars($product['title'] ?? '') ?>
                </h1>

                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-light">
                    <div style="color: var(--pure-black);">
                        <?php
                            $avgRating = round($product['rating'] ?? 5);
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $avgRating ? '<i class="fa-solid fa-star text-warning"></i>' : '<i class="fa-regular fa-star text-warning"></i>';
                            }
                        ?>
                    </div>
                    <small class="text-muted"><span id="sold-count">Đã bán
                            <?= number_format($product['sold_count'] ?? 0, 0, ',', '.') ?></span> | <span
                            id="detail-stock"><?= $product['options'][0]['stock'] ?? 0 ?></span> Sản Phẩm </small>
                </div>

                <div class="mb-4">
                    <?php
                        $firstOpt = $product['options'][0] ?? null;
                        $basePrice = (float) ($firstOpt['price'] ?? $product['price'] ?? 0);
                        $baseOriginal = (float) ($firstOpt['original_price'] ?? 0);
                        $hasDiscount = $baseOriginal > $basePrice && $basePrice > 0;
                        $percentOff  = $hasDiscount ? round((1 - $basePrice / $baseOriginal) * 100) : 0;
                    ?>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="detail-price fw-bold text-dark" id="detail-current-price"
                            style="font-size: 1.8rem;"><?= number_format($basePrice, 0, ',', '.') ?>đ</span>
                        <span class="detail-price-original text-muted text-decoration-line-through" id="detail-original-price"
                              style="<?= $hasDiscount ? '' : 'display:none;' ?>">
                            <?= $hasDiscount ? number_format($baseOriginal, 0, ',', '.') . 'đ' : '' ?>
                        </span>
                        <span class="badge bg-danger fs-6" id="detail-discount-badge"
                              style="<?= $hasDiscount ? '' : 'display:none;' ?>">
                            -<span id="detail-discount-pct"><?= $percentOff ?></span>%
                        </span>
                    </div>
                </div>

                <?php $shortDescription = trim((string) ($product['feature_text'] ?? '')) ?: trim((string) ($product['description'] ?? '')); ?>
                <?php if ($shortDescription !== ''): ?>
                    <p class="text-muted mb-4 lh-lg" style="font-size: 0.95rem;">
                        <?= htmlspecialchars($shortDescription) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="<?php echo url('index.php?action=productAction'); ?>" data-requires-login="buy">
                    <?php echo Csrf::field(); ?>
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                    <input type="hidden" name="action_type" id="detail-action-type" value="buy">

                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Chọn gói
                            thời gian</h6>
                        <div id="product-options">
                            <?php if (isset($product['options']) && is_array($product['options'])): ?>
                                <?php foreach ($product['options'] as $index => $option):
                                    $isOutOfStock = ($option['stock'] ?? 0) <= 0;
                                    $optPrice     = (float) ($option['price'] ?? 0);
                                    $optOriginal  = (float) ($option['original_price'] ?? 0);
                                    $optDiscount  = $optOriginal > $optPrice && $optPrice > 0;
                                    $optPctOff    = $optDiscount ? round((1 - $optPrice / $optOriginal) * 100) : 0;
                                    ?>
                                    <label class="option-item <?= $index === 0 && !$isOutOfStock ? 'selected' : '' ?> <?= $isOutOfStock ? 'disabled-option' : '' ?>"
                                        for="variant_<?= $index ?>"
                                        onclick="<?= $isOutOfStock ? 'event.preventDefault();' : 'selectOption(this)' ?>"
                                        data-price="<?= $optPrice ?>"
                                        data-original-price="<?= $optOriginal ?>"
                                        data-stock="<?= $option['stock'] ?? 0 ?>"
                                        data-index="<?= $index ?>">
                                        <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                                            <div class="d-flex align-items-center">
                                                <div class="custom-radio">
                                                    <input type="radio" id="variant_<?= $index ?>" name="variant_idx" value="<?= $index ?>"
                                                           <?= $index === 0 && !$isOutOfStock ? 'checked' : '' ?>
                                                           <?= $isOutOfStock ? 'disabled' : '' ?>
                                                           style="position: absolute; opacity: 0; pointer-events: none;">
                                                    <div class="radio-dot"></div>
                                                </div>
                                                <span style="<?= $isOutOfStock ? 'text-decoration: line-through; color: #aaa;' : '' ?>">
                                                    <?= htmlspecialchars($option['name'] ?? '') ?>
                                                </span>
                                                <?php if ($optDiscount && !$isOutOfStock): ?>
                                                    <span class="badge bg-danger ms-2">-<?= $optPctOff ?>%</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end">
                                                <?php if ($isOutOfStock): ?>
                                                    <span class="fw-bold text-muted" style="font-size: 1.05rem;">Hết hàng</span>
                                                <?php else: ?>
                                                    <?php if ($optDiscount): ?>
                                                        <div class="text-muted small text-decoration-line-through" style="line-height:1;">
                                                            <?= number_format($optOriginal, 0, ',', '.') ?>đ
                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="fw-bold" style="font-size: 1.1rem;">
                                                        <?= number_format($optPrice, 0, ',', '.') ?>đ
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-buy flex-grow-1 py-3 fs-6 rounded-pill"
                            onclick="document.getElementById('detail-action-type').value='buy'">Mua Ngay</button>
                        <button type="submit" class="btn btn-outline-dark px-4 py-3 rounded-pill"
                            onclick="document.getElementById('detail-action-type').value='cart'" title="Thêm vào giỏ">
                            <i class="fa-solid fa-cart-plus"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Info Tabs -->
        <div class="product-info-tabs mt-4 bg-white rounded-4 shadow-sm p-3 p-md-4 mb-5">
            <nav class="nav nav-tabs border-0 justify-content-center justify-content-md-start mb-4 gap-2"
                id="productTabs" role="tablist">
                <div class="d-flex justify-content-center gap-1 gap-md-2 flex-nowrap">
                    <button class="nav-link active custom-tab-btn" id="desc-tab" data-bs-toggle="tab"
                        data-bs-target="#desc" type="button" role="tab" aria-controls="desc" aria-selected="true">
                        <i class="fa-solid fa-file-lines me-1"></i>Mô tả chi tiết
                    </button>
                    <button class="nav-link custom-tab-btn" id="reviews-tab" data-bs-toggle="tab"
                        data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews"
                        aria-selected="false">
                        <i class="fa-solid fa-star me-1"></i>Đánh giá (<?= $reviewCount ?>)
                    </button>
                </div>

                <!-- Break line on mobile only -->
                <div class="w-100 d-md-none m-0 p-0" style="height: 0;"></div>

                <div class="d-flex justify-content-center">
                    <button class="nav-link custom-tab-btn" id="warranty-tab" data-bs-toggle="tab"
                        data-bs-target="#warranty" type="button" role="tab" aria-controls="warranty"
                        aria-selected="false">
                        Chính sách bảo hành
                    </button>
                </div>
            </nav>

            <div class="tab-content border-top pt-4" id="productTabsContent">
                <div class="tab-pane fade show active" id="desc" role="tabpanel" aria-labelledby="desc-tab">
                    <div class="text-muted lh-lg" style="font-size: 0.95rem;">
                        <?php $detailDescription = trim((string) ($product['description'] ?? '')) ?: $shortDescription; ?>
                        <?php if ($detailDescription !== ''): ?>
                            <div class="product-detail-description">
                                <?= render_product_detail_description($detailDescription) ?>
                            </div>
                        <?php else: ?>
                            <p>Thông tin chi tiết đang được cập nhật.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa-regular fa-comment-dots fs-1 mb-3 text-light-gray"></i>
                            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                        </div>
                    <?php else: ?>
                        <div class="reviews-list mt-3">
                            <?php foreach ($reviews as $rev): ?>
                                <div class="review-item d-flex gap-3 mb-4 pb-4 border-bottom">
                                    <div class="review-avatar">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                            <?= strtoupper(substr($rev['user_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div class="review-content flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <strong class="text-dark"><?= htmlspecialchars($rev['user_name'] ?? 'Khách hàng') ?></strong>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></small>
                                        </div>
                                        <div class="mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?= $i <= (int)$rev['rating'] ? '<i class="fa-solid fa-star text-warning" style="font-size:0.85rem;"></i>' : '<i class="fa-regular fa-star text-warning" style="font-size:0.85rem;"></i>' ?>
                                            <?php endfor; ?>
                                        </div>
                                        <?php if (!empty($rev['content'])): ?>
                                            <p class="mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.6;"><?= nl2br(htmlspecialchars($rev['content'])) ?></p>
                                        <?php endif; ?>

                                        <!-- Review Replies -->
                                        <?php $replies = Review::getRepliesByReviewId((int)$rev['id']); ?>
                                        <?php if (!empty($replies)): ?>
                                            <div class="review-replies-list mt-3 ps-3 border-start border-2 border-light-subtle">
                                                <?php foreach ($replies as $reply): ?>
                                                    <div class="reply-item mb-2 p-2 rounded-3 <?php echo ($reply['user_role'] ?? '') === 'admin' ? 'reply-admin' : 'reply-user'; ?>">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <div>
                                                                <strong class="text-dark small"><?= htmlspecialchars($reply['user_name'] ?? 'Người dùng') ?></strong>
                                                                <?php if (($reply['user_role'] ?? '') === 'admin'): ?>
                                                                    <span class="badge bg-primary ms-1" style="font-size: 0.7rem; background: linear-gradient(135deg, #6366f1, #a855f7) !important;">QTV</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <small class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?></small>
                                                        </div>
                                                        <p class="mb-0 text-secondary small" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Reply Form (if allowed) -->
                                        <?php if (Review::canReply((int)$rev['id'], $currentUser)): ?>
                                            <div class="reply-form-wrapper mt-2">
                                                <button class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-semibold toggle-reply-btn" onclick="toggleReplyForm(<?= $rev['id'] ?>)">
                                                    <i class="fa-regular fa-comment me-1"></i> Phản hồi
                                                </button>
                                                <form id="reply-form-<?= $rev['id'] ?>" action="<?= url('index.php?action=submitReviewReply') ?>" method="POST" class="mt-2 d-none reply-submit-form">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                                    <div class="input-group">
                                                        <input type="text" name="content" class="form-control form-control-sm rounded-start-3" placeholder="Nhập câu trả lời của bạn..." required>
                                                        <button class="btn btn-sm btn-primary rounded-end-3" type="submit" style="background: linear-gradient(135deg, #6366f1, #a855f7); border: none;">Gửi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="tab-pane fade" id="warranty" role="tabpanel" aria-labelledby="warranty-tab">
                    <div class="warranty-content" style="font-size: 0.95rem;">
                        <h6 class="fw-bold mb-3 text-dark" style="font-size: 1rem;">Quy định chung:</h6>
                        <ul class="list-unstyled text-muted mb-4 lh-lg">
                            <li class="mb-2 d-flex"><i class="fa-solid fa-check text-primary mt-1 me-3"></i> <span>Bảo
                                    hành 1 đổi 1 nếu sản phẩm lỗi kỹ thuật.</span></li>
                            <li class="mb-2 d-flex"><i class="fa-solid fa-check text-primary mt-1 me-3"></i> <span>Thời
                                    gian xử lý khiếu nại trong vòng 24h.</span></li>
                            <li class="mb-2 d-flex"><i class="fa-solid fa-check text-primary mt-1 me-3"></i> <span>Hỗ
                                    trợ kỹ thuật trọn đời sau khi mua.</span></li>
                        </ul>

                        <h6 class="fw-bold mb-3 text-dark" style="font-size: 1rem;">Trường hợp từ chối bảo hành:</h6>
                        <ul class="list-unstyled text-muted lh-lg mb-0">
                            <li class="mb-2 d-flex"><i class="fa-solid fa-xmark text-danger mt-1 me-3"></i> <span>Sản
                                    phẩm đã quá hạn bảo hành quy định.</span></li>
                            <li class="mb-2 d-flex"><i class="fa-solid fa-xmark text-danger mt-1 me-3"></i> <span>Do lỗi
                                    của người dùng trong quá trình sử dụng.</span></li>
                            <li class="mb-2 d-flex"><i class="fa-solid fa-xmark text-danger mt-1 me-3"></i> <span>Đã can
                                    thiệp hoặc thay đổi cấu trúc sản phẩm.</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php
            $faqPrice = isset($basePrice) ? number_format((float) $basePrice, 0, ',', '.') . 'đ' : 'theo gói đang hiển thị';
            $faqTitle = $product['title'] ?? 'sản phẩm';
        ?>
        <section class="product-faq mt-4 bg-white rounded-4 shadow-sm p-3 p-md-4 mb-5">
            <h2 class="fw-bold text-dark mb-3" style="font-size: 1.2rem;">Câu hỏi thường gặp</h2>
            <div class="accordion" id="productFaq">
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header">
                        <button class="accordion-button bg-white px-0 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqWarranty" aria-expanded="true" aria-controls="faqWarranty">
                            Mua <?= htmlspecialchars($faqTitle) ?> có được bảo hành không?
                        </button>
                    </h3>
                    <div id="faqWarranty" class="accordion-collapse collapse show" data-bs-parent="#productFaq">
                        <div class="accordion-body px-0 text-muted">
                            Có. Sản phẩm được hỗ trợ bảo hành theo mô tả sản phẩm và chính sách hiển thị trên trang chi tiết.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed bg-white px-0 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqPrice" aria-expanded="false" aria-controls="faqPrice">
                            Giá <?= htmlspecialchars($faqTitle) ?> là bao nhiêu?
                        </button>
                    </h3>
                    <div id="faqPrice" class="accordion-collapse collapse" data-bs-parent="#productFaq">
                        <div class="accordion-body px-0 text-muted">
                            Giá hiện tại bắt đầu từ <?= htmlspecialchars($faqPrice) ?>. Giá có thể thay đổi theo từng gói và thời hạn sử dụng.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed bg-white px-0 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqDelivery" aria-expanded="false" aria-controls="faqDelivery">
                            Sau khi thanh toán bao lâu thì nhận được sản phẩm?
                        </button>
                    </h3>
                    <div id="faqDelivery" class="accordion-collapse collapse" data-bs-parent="#productFaq">
                        <div class="accordion-body px-0 text-muted">
                            Hệ thống xử lý đơn hàng tự động. Sau khi thanh toán thành công, bạn sẽ nhận thông tin sản phẩm theo hướng dẫn trên website.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<style>
.product-detail-description h1,
.product-detail-description h2,
.product-detail-description h3 {
    color: #111;
    font-weight: 750;
    line-height: 1.35;
    margin: 1.15rem 0 0.55rem;
}
.product-detail-description {
    color: #444;
    font-size: 1rem;
    line-height: 1.75;
}
.product-detail-description p {
    margin: 0 0 0.85rem;
    font-size: 1rem;
}
.product-detail-description h1 { font-size: 1.25rem; }
.product-detail-description h2 { font-size: 1.13rem; }
.product-detail-description h3 { font-size: 1.03rem; }
.product-detail-description strong,
.product-detail-description b {
    font-weight: 700;
}
.product-detail-description ul,
.product-detail-description ol {
    padding-left: 1.35rem;
    margin-bottom: 1rem;
}
.product-detail-description li { margin-bottom: 0.35rem; }
.product-detail-description table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    background: #fff;
}
.product-detail-description th,
.product-detail-description td {
    border: 1px solid #e5e7eb;
    padding: 0.65rem 0.75rem;
    vertical-align: top;
}
.product-detail-description th {
    color: #111;
    background: #f8fafc;
    font-weight: 700;
}

/* Review Replies Custom Styling */
.review-replies-list {
    margin-left: 10px;
}
.reply-item {
    background-color: #f8fafc;
    border: 1px solid #f1f5f9;
    transition: all 0.2s ease-in-out;
}
.reply-item.reply-admin {
    background-color: #faf5ff;
    border-left: 3px solid #a855f7;
    border-top: 1px solid #f3e8ff;
    border-right: 1px solid #f3e8ff;
    border-bottom: 1px solid #f3e8ff;
}
.reply-item:hover {
    background-color: #f1f5f9;
}
.reply-item.reply-admin:hover {
    background-color: #f3e8ff;
}
</style>

<script>
function toggleReplyForm(reviewId) {
    const form = document.getElementById('reply-form-' + reviewId);
    if (form) {
        form.classList.toggle('d-none');
        const input = form.querySelector('input[name="content"]');
        if (input && !form.classList.contains('d-none')) {
            input.focus();
        }
    }
}
</script>
