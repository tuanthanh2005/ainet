<div class="py-lg-4 pb-5">
    <a href="<?php echo url(); ?>" class="back-link mb-4 d-inline-block text-decoration-none text-dark fw-bold">
        <i class="fa-solid fa-arrow-left me-2"></i> Trở về danh sách
    </a>

    <div class="product-detail-container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0 text-center">
                <img src="<?= htmlspecialchars($product['image']) ?>" class="detail-image shadow-sm w-100 rounded-4"
                    alt="Product" style="height: auto; object-fit: cover; border: 1px solid var(--border-color);">

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
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                            class="fa-solid fa-star"></i>
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
                        <i class="fa-solid fa-star me-1"></i>Đánh giá (0)
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
                        <p class="mb-4">Lưu ý: Bảo Hành</p>
                        <p class="mb-4">Tài khoản <?= htmlspecialchars($product['title']) ?> bao gồm:</p>
                        <?php if ($detailDescription !== ''): ?>
                            <div class="product-detail-description"><?= nl2br(htmlspecialchars($detailDescription)) ?></div>
                        <?php else: ?>
                            <p>Thông tin chi tiết đang được cập nhật.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div class="text-center py-5 text-muted">
                        <i class="fa-regular fa-comment-dots fs-1 mb-3 text-light-gray"></i>
                        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                    </div>
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
    </div>
</div>
