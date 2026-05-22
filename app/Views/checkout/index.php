<?php
$variantIdx = isset($_GET['variant_idx']) ? (int)$_GET['variant_idx'] : 0;
?>
<div class="bg-light min-vh-100 py-5">
    <div class="container">
        <div class="mb-4 text-center" id="checkout-header">
            <h2 class="fw-bold mb-1 text-dark">Xác nhận Đơn hàng</h2>
            <p class="text-muted small">Cung cấp thông tin để chúng tôi phục vụ bạn tốt nhất</p>
        </div>

        <!-- Stepper -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="d-flex justify-content-between position-relative stepper-wrapper">
                    <div class="stepper-item active" id="step-1-indicator">
                        <div class="step-counter">1</div>
                        <div class="step-name small mt-2 fw-bold text-dark">Thông tin</div>
                    </div>
                    <div class="stepper-item text-muted" id="step-2-indicator">
                        <div class="step-counter">2</div>
                        <div class="step-name small mt-2 fw-bold">Thanh toán</div>
                    </div>
                    <div class="stepper-item text-muted" id="step-3-indicator">
                        <div class="step-counter">3</div>
                        <div class="step-name small mt-2 fw-bold">Hoàn tất</div>
                    </div>
                    <div class="stepper-line"></div>
                </div>
            </div>
        </div>

        <!-- STEP 1: INFORMATION FORM -->
        <div id="section-info" class="animate__animated animate__fadeIn">
            <form method="POST" action="<?= url('index.php?action=checkout') ?>">
                <?php echo Csrf::field(); ?>
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                <input type="hidden" name="variant_idx" value="<?= htmlspecialchars($variantIdx) ?>">

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <h5 class="fw-bold mb-4 d-flex align-items-center">
                                <i class="fa-regular fa-user me-2 text-primary"></i> Thông tin khách hàng
                            </h5>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light border-0" id="c_name" name="name" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control bg-light border-0 py-2" id="c_email" name="email" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" readonly required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3 py-2 bg-light border-0" id="c_phone" name="phone" placeholder="0123456789" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold">Ghi chú đơn hàng</label>
                                <textarea class="form-control rounded-3" id="c_note" name="note" rows="3" placeholder="Ghi chú cho đơn hàng (tùy chọn)"></textarea>
                            </div>
                        </div>

                        <?php if ($variant['is_upgrade'] ?? false): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-0 mb-4 overflow-hidden" style="border: 2px solid #ffc107 !important;">
                            <div class="bg-warning bg-opacity-10 p-4">
                                <h5 class="fw-bold mb-1 d-flex align-items-center text-dark">
                                    <i class="fa-solid fa-key me-2 text-warning"></i> Thông tin tài khoản cần nâng cấp
                                </h5>
                                <p class="text-muted smaller mb-4">Chúng tôi cần tài khoản của bạn để tiến hành nâng cấp.</p>
                                
                                <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Email tài khoản <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control bg-light border-0 py-2" name="upgrade_email" placeholder="Nhập email tài khoản cần nâng cấp" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Mật khẩu tài khoản <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control bg-light border-0 py-2" id="u_pass" name="upgrade_pass" placeholder="Nhập mật khẩu tài khoản" required>
                                            <button class="btn btn-light border-0" type="button" onclick="togglePass()"><i class="fa-regular fa-eye text-muted"></i></button>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold">Telegram / Facebook <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control bg-light border-0 py-2" name="upgrade_link" placeholder="Username Telegram hoặc link Facebook" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h5 class="fw-bold mb-4 d-flex align-items-center">
                                <i class="fa-regular fa-credit-card me-2 text-primary"></i> Phương Thức Thanh Toán
                            </h5>
                            <div class="payment-methods">
                                <label class="payment-method-item active d-flex align-items-center p-3 rounded-4 border mb-3 cursor-pointer" for="pay_qr">
                                    <input type="radio" name="payment_method" id="pay_qr" value="qr" checked class="me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark small">Chuyển khoản ngân hàng</div>
                                        <div class="text-muted smaller">Thanh toán qua QR Code - Tự động xác nhận</div>
                                    </div>
                                    <i class="fa-solid fa-qrcode fs-4 text-primary"></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 2rem;">
                            <h5 class="fw-bold mb-4"><i class="fa-solid fa-cart-shopping me-2 text-primary"></i> Đơn Hàng</h5>
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <img src="<?= htmlspecialchars($product['image'] ?? '') ?>" class="rounded-3 border me-3" style="width: 55px; height: 55px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark small text-truncate-2"><?= htmlspecialchars($product['title']) ?></div>
                                    <div class="text-muted smaller"><?= htmlspecialchars($variant['name']) ?></div>
                                </div>
                                <div class="fw-bold text-dark small"><?= number_format($variant['price'], 0, ',', '.') ?>đ</div>
                            </div>

                            <?php $isUpgrade = !empty($variant['is_upgrade']); ?>
                            <?php if (!$isUpgrade): ?>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="small fw-bold">Số lượng</span>
                                <div class="qty-stepper d-inline-flex align-items-center bg-light rounded-pill p-1">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle p-0" style="width:32px;height:32px;" onclick="changeQty(-1)">
                                        <i class="fa-solid fa-minus small"></i>
                                    </button>
                                    <span class="px-3 fw-bold" id="c_quantity_label">1</span>
                                    <input type="hidden" id="c_quantity" name="quantity" value="1" min="1" max="99">
                                    <button type="button" class="btn btn-light btn-sm rounded-circle p-0" style="width:32px;height:32px;" onclick="changeQty(1)">
                                        <i class="fa-solid fa-plus small"></i>
                                    </button>
                                </div>
                            </div>
                            <?php else: ?>
                            <input type="hidden" id="c_quantity" name="quantity" value="1">
                            <?php endif; ?>

                            <div class="bg-light p-3 rounded-4 mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Đơn giá:</span>
                                    <span class="text-dark small fw-bold"><?= number_format($variant['price'], 0, ',', '.') ?>đ</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Số lượng:</span>
                                    <span class="text-dark small fw-bold" id="summary_qty">× 1</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Phí xử lý:</span>
                                    <span class="text-success small fw-bold">Miễn phí</span>
                                </div>
                                <hr class="my-2 border-dashed">
                                <div class="d-flex justify-content-between">
                                    <span class="text-dark fw-bold">Tổng cộng:</span>
                                    <span class="text-primary fw-bold fs-5" id="summary_total"><?= number_format($variant['price'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-black w-100 py-3 rounded-3 fw-bold mb-3 shadow-sm btn-place-order">
                                ĐẶT HÀNG NGAY <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.stepper-wrapper { height: 60px; margin-top: 10px; }
.stepper-item { z-index: 2; background: #f8f9fa; text-align: center; width: 90px; }
.step-counter { width: 40px; height: 40px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: bold; color: #adb5bd; border: 4px solid #fff; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.stepper-item.active .step-counter { background: #000; color: #fff; transform: scale(1.2); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
.stepper-line { position: absolute; top: 20px; left: 10%; width: 80%; height: 4px; background: #e9ecef; z-index: 1; border-radius: 2px; }
.payment-method-item { border: 2px solid #eee; transition: all 0.3s; cursor: pointer; }
.payment-method-item:hover { border-color: #000; transform: translateY(-2px); }
.payment-method-item.active { border-color: #000; background: #f8f9fa; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
.smaller { font-size: 0.75rem; }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

/* Mobile Optimization */
@media (max-width: 768px) {
    .stepper-item { width: 70px; }
    .step-counter { width: 32px; height: 32px; font-size: 0.8rem; border-width: 2px; }
    .step-name { font-size: 0.65rem !important; }
    .stepper-line { top: 16px; }
    .card { padding: 20px !important; }
    .btn-place-order { width: 100%; padding: 16px !important; font-size: 1rem; }
    .sticky-top { position: static !important; }
}
</style>

<script>
function togglePass() {
    const p = document.getElementById('u_pass');
    if (p) p.type = p.type === 'password' ? 'text' : 'password';
}

function changeQty(delta) {
    const input = document.getElementById('c_quantity');
    const label = document.getElementById('c_quantity_label');
    if (!input) return;
    let q = parseInt(input.value, 10) || 1;
    q = Math.max(1, Math.min(99, q + delta));
    input.value = q;
    if (label) label.textContent = q;
    const unit = <?= (int) ($variant['price'] ?? 0) ?>;
    const total = unit * q;
    const totalEl = document.getElementById('summary_total');
    const qtyEl = document.getElementById('summary_qty');
    if (totalEl) totalEl.textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
    if (qtyEl) qtyEl.textContent = '× ' + q;
}
</script>
