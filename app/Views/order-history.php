<?php
/**
 * Lịch sử đơn hàng cho user đã đăng nhập (action=orderHistory).
 * Controller truyền $orders (array), mỗi đơn đã decode delivered_items thành array.
 */
?>
<div class="oh-page py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h3 class="fw-bold mb-1">Lịch sử mua hàng</h3>
                    <p class="text-muted small mb-0">Bấm vào đơn để lấy tài khoản đã mua hoặc tiếp tục thanh toán.</p>
                </div>
                <a href="<?= url() ?>" class="btn btn-light border rounded-pill px-4">
                    <i class="fa-solid fa-cart-shopping me-2"></i>Tiếp tục mua sắm
                </a>
            </div>

            <?php if ($totalAll > 0): ?>
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="oh-stat">
                        <div class="oh-stat-label">Tổng đơn</div>
                        <div class="oh-stat-value"><?= $totalAll ?></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="oh-stat">
                        <div class="oh-stat-label">Đã hoàn tất</div>
                        <div class="oh-stat-value text-success"><?= $totalCompleted ?></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="oh-stat">
                        <div class="oh-stat-label">Chờ thanh toán</div>
                        <div class="oh-stat-value text-warning"><?= $totalPending ?></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="oh-stat">
                        <div class="oh-stat-label">Đã chi</div>
                        <div class="oh-stat-value"><?= number_format($totalSpent, 0, ',', '.') ?>đ</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-5 text-center">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;">
                            <i class="fa-solid fa-receipt fs-3 text-muted"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Chưa có đơn hàng</h5>
                        <p class="text-muted mb-4">Khi bạn mua sản phẩm, đơn hàng sẽ hiện ở đây.</p>
                        <a href="<?= url() ?>" class="btn btn-buy rounded-3 px-4 py-2">Xem sản phẩm</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($orders as $idx => $order): ?>
                        <?php
                            require_once APP_ROOT . '/app/Models/Review.php';
                            $items     = $order['delivered_items'] ?? [];
                            $hasItems  = is_array($items) && count($items) > 0;
                            $rowId     = 'oh-' . $idx;
                            $status    = $order['status'] ?? '';
                            $statusCls = $status === 'completed' ? 'oh-completed'
                                        : ($status === 'processing' ? 'oh-processing'
                                        : ($status === 'pending' ? 'oh-pending' : 'oh-cancelled'));
                            $statusLabel = $status === 'completed' ? 'Thành công'
                                         : ($status === 'processing' ? 'Đang xử lý'
                                         : ($status === 'pending' ? 'Chờ thanh toán' : 'Đã hủy'));
                            $statusIcon = $status === 'completed' ? 'fa-check-circle'
                                        : ($status === 'processing' ? 'fa-spinner fa-spin'
                                        : ($status === 'pending' ? 'fa-clock' : 'fa-times-circle'));
                            $imageUrl   = $order['product_image'] ?? null; // optional, may not be set
                            
                            $hasReviewed = false;
                            if ($status === 'completed') {
                                $hasReviewed = Review::hasReviewed($order['id'], $order['product_id']);
                            }
                        ?>
                        <div class="oh-order <?= $statusCls ?>">
                            <div class="oh-row">
                                <div class="oh-thumb">
                                    <?php if ($imageUrl): ?>
                                        <img src="<?= htmlspecialchars(image_url($imageUrl)) ?>" alt="">
                                    <?php else: ?>
                                        <i class="fa-solid <?= htmlspecialchars($statusIcon) ?>"></i>
                                    <?php endif; ?>
                                </div>

                                <div class="oh-meta">
                                    <div class="oh-title">
                                        <?= htmlspecialchars($order['product_name']) ?>
                                    </div>
                                    <div class="oh-sub">
                                        <?= htmlspecialchars($order['variant_name']) ?>
                                        <?php if (!empty($order['quantity']) && (int) $order['quantity'] > 1): ?>
                                            <span class="oh-dot">·</span> Số lượng <strong><?= (int) $order['quantity'] ?></strong>
                                        <?php endif; ?>
                                    </div>
                                    <div class="oh-id-row">
                                        <code class="oh-id">#<?= htmlspecialchars($order['id']) ?></code>
                                        <button type="button" class="oh-copy-mini" onclick="copyOrderText(this, '#<?= htmlspecialchars($order['id']) ?>')" title="Coppy">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                        <span class="oh-date">
                                            <i class="fa-regular fa-clock"></i>
                                            <?= !empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : '' ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="oh-side">
                                    <div class="oh-price"><?= number_format($order['amount'], 0, ',', '.') ?>đ</div>
                                    <div class="oh-status">
                                        <i class="fa-solid <?= $statusIcon ?> me-1"></i><?= $statusLabel ?>
                                    </div>

                                    <?php if ($status === 'completed' && $hasItems): ?>
                                        <button type="button" class="oh-cta oh-cta-dark"
                                                data-bs-toggle="collapse" data-bs-target="#<?= $rowId ?>"
                                                aria-expanded="false">
                                            <i class="fa-solid fa-key"></i>
                                            <span>Xem tài khoản</span>
                                            <span class="oh-cta-badge"><?= count($items) ?></span>
                                            <i class="fa-solid fa-chevron-down chev"></i>
                                        </button>
                                        <?php if (!$hasReviewed): ?>
                                            <button type="button" class="oh-cta oh-cta-warning mt-1 btn-review" data-order="<?= htmlspecialchars($order['id']) ?>" data-product="<?= htmlspecialchars($order['product_id']) ?>" data-name="<?= htmlspecialchars($order['product_name']) ?>">
                                                <i class="fa-solid fa-star"></i>
                                                <span>Đánh giá</span>
                                            </button>
                                        <?php endif; ?>
                                    <?php elseif ($status === 'completed'): ?>
                                        <button type="button" class="oh-cta oh-cta-warning"
                                                data-bs-toggle="collapse" data-bs-target="#<?= $rowId ?>"
                                                aria-expanded="false">
                                            <i class="fa-solid fa-circle-info"></i>
                                            <span>Chi tiết</span>
                                            <i class="fa-solid fa-chevron-down chev"></i>
                                        </button>
                                        <?php if (!$hasReviewed): ?>
                                            <button type="button" class="oh-cta oh-cta-warning mt-1 btn-review" data-order="<?= htmlspecialchars($order['id']) ?>" data-product="<?= htmlspecialchars($order['product_id']) ?>" data-name="<?= htmlspecialchars($order['product_name']) ?>">
                                                <i class="fa-solid fa-star"></i>
                                                <span>Đánh giá</span>
                                            </button>
                                        <?php endif; ?>
                                    <?php elseif ($status === 'processing'): ?>
                                        <button type="button" class="oh-cta oh-cta-primary text-white"
                                                data-bs-toggle="collapse" data-bs-target="#<?= $rowId ?>"
                                                aria-expanded="false" style="background:#0d6efd;">
                                            <i class="fa-solid fa-spinner fa-spin"></i>
                                            <span>Chi tiết</span>
                                            <i class="fa-solid fa-chevron-down chev"></i>
                                        </button>
                                    <?php elseif ($status === 'pending'): ?>
                                        <a href="<?= url('index.php?action=payment&id=' . urlencode($order['id'])) ?>"
                                           class="oh-cta oh-cta-warning">
                                            <i class="fa-solid fa-arrow-right"></i>
                                            <span>Thanh toán</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($status !== 'cancelled'): ?>
                            <div class="collapse" id="<?= $rowId ?>">
                                <div class="oh-detail">
                                    <?php if ($status === 'pending'): ?>
                                        <div class="alert alert-warning border-0 rounded-3 mb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div>
                                                <i class="fa-solid fa-circle-info me-2"></i>
                                                Đơn này chưa được thanh toán.
                                            </div>
                                            <a href="<?= url('index.php?action=payment&id=' . urlencode($order['id'])) ?>" class="btn btn-warning btn-sm fw-bold">
                                                <i class="fa-solid fa-arrow-right me-1"></i>Tiếp tục thanh toán
                                            </a>
                                        </div>
                                    <?php elseif ($status === 'processing'): ?>
                                        <div class="oh-empty-stock">
                                            <div class="d-flex align-items-start gap-3 mb-3">
                                                <div class="oh-empty-icon" style="background:#e7f1ff;color:#0d6efd;">
                                                    <i class="fa-solid fa-hourglass-half"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Đơn hàng đang xử lý</h6>
                                                    <p class="text-muted small mb-0">Đơn hàng của bạn đã thanh toán thành công và đang được Admin xử lý. Quá trình xử lý thường mất 5 - 15 phút. Bạn hãy copy mã đơn hàng bên dưới để liên hệ Admin qua Telegram hoặc Zalo nhận tài khoản nhanh nhất.</p>
                                                </div>
                                            </div>
                                            <div class="oh-code-box">
                                                <code class="flex-grow-1">#<?= htmlspecialchars($order['id']) ?></code>
                                                <button type="button" class="btn btn-dark px-3"
                                                        onclick="copyOrderText(this, '#<?= htmlspecialchars($order['id']) ?>')">
                                                    <i class="fa-regular fa-copy me-1"></i>Copy
                                                </button>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 mt-3">
                                                <a href="https://t.me/specademy" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-brands fa-telegram me-1"></i>Liên hệ Telegram
                                                </a>
                                            </div>
                                        </div>
                                    <?php elseif ($status === 'completed' && !$hasItems): ?>
                                        <div class="oh-empty-stock">
                                            <div class="d-flex align-items-start gap-3 mb-3">
                                                <div class="oh-empty-icon">
                                                    <i class="fa-solid fa-circle-exclamation"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Đơn của bạn</h6>
                                                    <p class="text-muted small mb-0">Đơn của bạn đã thanh toán thành công. Hãy <strong>Coppy</strong> bên dưới và gửi cho admin để được giao thủ công nhanh nhất.</p>
                                                </div>
                                            </div>
                                            <div class="oh-code-box">
                                                <code class="flex-grow-1">#<?= htmlspecialchars($order['id']) ?></code>
                                                <button type="button" class="btn btn-dark px-3"
                                                        onclick="copyOrderText(this, '#<?= htmlspecialchars($order['id']) ?>')">
                                                    <i class="fa-regular fa-copy me-1"></i>Copy
                                                </button>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 mt-3">
                                                <a href="https://t.me/specademy" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-brands fa-telegram me-1"></i>Liên hệ Telegram
                                                </a>
                                            </div>
                                        </div>
                                    <?php elseif ($hasItems): ?>
                                        <?php
                                            $expectedQty = max(1, (int) ($order['quantity'] ?? 1));
                                            $deliveredCount = count($items);
                                            $missing = max(0, $expectedQty - $deliveredCount);
                                        ?>
                                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                            <h6 class="fw-bold text-success mb-0">
                                                <i class="fa-solid fa-box-check me-2"></i>
                                                <?php if ($missing > 0): ?>
                                                    Đã giao <?= $deliveredCount ?>/<?= $expectedQty ?> sản phẩm
                                                <?php else: ?>
                                                    Sản phẩm đã giao (<?= $deliveredCount ?>)
                                                <?php endif; ?>
                                            </h6>
                                            <button class="btn btn-sm btn-outline-dark" type="button" onclick="copyAllItems(this)" data-items="<?= htmlspecialchars(rawurlencode(implode("\n\n", $items)), ENT_QUOTES) ?>">
                                                <i class="fa-regular fa-copy me-1"></i>Sao chép tất cả
                                            </button>
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            <?php foreach ($items as $i => $content): ?>
                                                <div class="oh-item">
                                                    <span class="oh-item-num">#<?= $i + 1 ?></span>
                                                    <pre class="oh-item-content"><?= htmlspecialchars($content) ?></pre>
                                                    <button class="oh-item-copy" type="button"
                                                            data-clip="<?= htmlspecialchars(rawurlencode($content), ENT_QUOTES) ?>"
                                                            onclick="copyOrderText(this, decodeURIComponent(this.dataset.clip))"
                                                            title="Sao chép">
                                                        <i class="fa-regular fa-copy"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if ($missing > 0): ?>
                                            <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 small d-flex align-items-center gap-2 flex-wrap">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>Còn <strong><?= $missing ?></strong> sản phẩm chưa giao do hết kho. Hãy <strong>copy</strong> mã đơn</span>
                                                <code class="px-2 py-1 bg-white border rounded">#<?= htmlspecialchars($order['id']) ?></code>
                                                <button type="button" class="btn btn-sm btn-dark"
                                                        onclick="copyOrderText(this, '#<?= htmlspecialchars($order['id']) ?>')">
                                                    <i class="fa-regular fa-copy me-1"></i>Copy
                                                </button>
                                                <span>và gửi cho admin để được giao bổ sung.</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <?php
                    $pageUrl = function($p) {
                        return url('index.php?action=orderHistory&page=' . $p);
                    };
                    ?>
                    <div class="d-flex justify-content-center mt-4 mb-3">
                        <nav aria-label="Order pagination">
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
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Generic Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= url('index.php?action=submitReview') ?>" method="POST">
                <?php echo Csrf::field(); ?>
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="reviewModalLabel">Đánh giá sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-2">
                    <p class="text-muted small mb-4">Bạn cảm thấy sản phẩm <strong id="reviewProductName">...</strong> như thế nào?</p>
                    
                    <input type="hidden" name="order_id" id="reviewOrderId" value="">
                    <input type="hidden" name="product_id" id="reviewProductId" value="">
                    
                    <div class="rating-stars mb-4" dir="rtl">
                        <input type="radio" id="oh_star5" name="rating" value="5" checked><label for="oh_star5" class="fs-1 text-warning mx-1" style="cursor:pointer;"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="oh_star4" name="rating" value="4"><label for="oh_star4" class="fs-1 text-warning mx-1" style="cursor:pointer;"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="oh_star3" name="rating" value="3"><label for="oh_star3" class="fs-1 text-warning mx-1" style="cursor:pointer;"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="oh_star2" name="rating" value="2"><label for="oh_star2" class="fs-1 text-warning mx-1" style="cursor:pointer;"><i class="fa-solid fa-star"></i></label>
                        <input type="radio" id="oh_star1" name="rating" value="1"><label for="oh_star1" class="fs-1 text-warning mx-1" style="cursor:pointer;"><i class="fa-solid fa-star"></i></label>
                    </div>
                    
                    <textarea name="content" class="form-control rounded-3" rows="3" placeholder="Chia sẻ thêm cảm nhận của bạn (không bắt buộc)..."></textarea>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Để sau</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.oh-page { background: #fafafa; min-height: 70vh; }

/* Stat strip */
.oh-stat {
    background: #fff;
    border: 1px solid #efefef;
    border-radius: 14px;
    padding: 16px 18px;
    height: 100%;
}
.oh-stat-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #999;
    font-weight: 600;
    margin-bottom: 6px;
}
.oh-stat-value {
    font-size: 1.4rem;
    font-weight: 700;
    color: #111;
    line-height: 1.1;
}

/* Order card */
.oh-order {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 16px;
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.2s;
}
.oh-order:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.05); transform: translateY(-1px); }
.oh-order.oh-pending { border-left: 4px solid #f4b400; }
.oh-order.oh-processing { border-left: 4px solid #0d6efd; }
.oh-order.oh-completed { border-left: 4px solid #1aa260; }
.oh-order.oh-cancelled { border-left: 4px solid #d93025; }

.oh-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
}
.oh-thumb {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    background: #f5f5f5;
    display: flex; align-items: center; justify-content: center;
    color: #aaa;
    flex: 0 0 auto;
    overflow: hidden;
}
.oh-thumb img { width: 100%; height: 100%; object-fit: cover; }
.oh-completed .oh-thumb { background: #e8f6ee; color: #1aa260; }
.oh-processing .oh-thumb { background: #e7f1ff; color: #0d6efd; }
.oh-pending .oh-thumb   { background: #fff5db; color: #d39200; }
.oh-cancelled .oh-thumb { background: #fdecea; color: #d93025; }
.oh-thumb i { font-size: 1.4rem; }

.oh-meta { flex: 1 1 auto; min-width: 0; }
.oh-title {
    font-size: 1.02rem;
    font-weight: 700;
    color: #111;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.oh-sub {
    color: #666;
    font-size: 0.86rem;
    margin-top: 2px;
}
.oh-dot { margin: 0 4px; opacity: 0.5; }
.oh-id-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    flex-wrap: wrap;
}
.oh-id {
    background: #f3f3f3;
    color: #555;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
}
.oh-copy-mini {
    border: 0; background: transparent; color: #999;
    padding: 0; font-size: 0.85rem; cursor: pointer;
}
.oh-copy-mini:hover { color: #111; }
.oh-date {
    color: #888;
    font-size: 0.78rem;
    display: inline-flex; align-items: center; gap: 4px;
    margin-left: auto;
}

/* Right side: price + status + CTA stacked vertically */
.oh-side {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex: 0 0 auto;
}
.oh-price { font-weight: 700; font-size: 1.05rem; color: #111; }
.oh-status { font-size: 0.78rem; }
.oh-completed .oh-status { color: #1aa260; }
.oh-processing .oh-status { color: #0d6efd; }
.oh-pending .oh-status   { color: #d39200; }
.oh-cancelled .oh-status { color: #d93025; }

.oh-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 4px;
}
.oh-cta-dark    { background: #111; color: #fff; }
.oh-cta-dark:hover  { background: #000; color: #fff; transform: translateY(-1px); }
.oh-cta-warning { background: #ffb300; color: #2a2300; }
.oh-cta-warning:hover { background: #ffa000; color: #1a1500; }
.oh-cta .chev { font-size: 0.65rem; transition: transform 0.25s; }
.oh-cta[aria-expanded="true"] .chev { transform: rotate(180deg); }
.oh-cta-badge {
    background: rgba(255,255,255,0.2);
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
}

/* Detail block */
.oh-detail {
    border-top: 1px solid #f0f0f0;
    padding: 18px 20px 22px;
    background: #fafafa;
}
.oh-item {
    display: flex; align-items: flex-start; gap: 10px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 10px;
    padding: 10px 12px;
}
.oh-item-num {
    background: #111;
    color: #fff;
    border-radius: 999px;
    padding: 1px 8px;
    font-size: 0.72rem;
    font-weight: 700;
    flex: 0 0 auto;
    margin-top: 2px;
}
.oh-item-content {
    flex: 1 1 auto;
    margin: 0;
    font-family: 'Inter', monospace;
    font-size: 0.86rem;
    color: #222;
    white-space: pre-wrap;
    word-break: break-word;
}
.oh-item-copy {
    border: 1px solid #e0e0e0;
    background: #fff;
    border-radius: 8px;
    padding: 4px 8px;
    color: #555;
    cursor: pointer;
    flex: 0 0 auto;
}
.oh-item-copy:hover { background: #f4f4f4; color: #111; }

/* Out-of-stock notice */
.oh-empty-stock {
    background: #fffaf0;
    border: 1px solid #ffe4a8;
    border-radius: 12px;
    padding: 18px 18px 16px;
}
.oh-empty-icon {
    width: 44px; height: 44px;
    border-radius: 999px;
    background: #fff2cc;
    color: #d39200;
    display: flex; align-items: center; justify-content: center;
    flex: 0 0 auto;
    font-size: 1.2rem;
}
.oh-code-box {
    display: flex; align-items: center; gap: 10px;
    background: #fff;
    border: 1px solid #efefef;
    border-radius: 10px;
    padding: 10px 12px;
}
.oh-code-box .btn {
    flex: 0 0 auto;
    white-space: nowrap;
    padding-left: 12px !important;
    padding-right: 12px !important;
}
.oh-code-box code {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111;
    background: transparent;
}

/* Responsive */
@media (max-width: 575.98px) {
    .oh-row { flex-wrap: wrap; }
    .oh-side {
        align-items: flex-start;
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .oh-meta { width: 100%; }
    .oh-thumb { display: none; }
    .oh-date { margin-left: 0; }
}
</style>
<style>
.rating-stars { display: inline-flex; flex-direction: row-reverse; }
.rating-stars input { display: none; }
.rating-stars label { color: #ddd !important; transition: color 0.2s; }
.rating-stars input:checked ~ label, .rating-stars label:hover, .rating-stars label:hover ~ label { color: #ffc107 !important; }
</style>

<script>
function copyOrderText(btn, value) {
    if (!value) return;
    navigator.clipboard.writeText(value).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
        setTimeout(() => btn.innerHTML = original, 1500);
    });
}
function copyAllItems(btn) {
    const text = decodeURIComponent(btn.dataset.items || '');
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-success me-1"></i>Đã sao chép';
        setTimeout(() => btn.innerHTML = original, 1500);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const reviewBtns = document.querySelectorAll('.btn-review');
    const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
    reviewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('reviewOrderId').value = this.dataset.order;
            document.getElementById('reviewProductId').value = this.dataset.product;
            document.getElementById('reviewProductName').innerText = this.dataset.name;
            reviewModal.show();
        });
    });
});

</script>
