<div class="cart-page py-5">
    <div class="container fade-in-element">
        <h2 class="fw-bold mb-5"><i class="fa-solid fa-cart-shopping me-3"></i>Giỏ hàng của bạn</h2>

        <?php if (empty($cart)): ?>
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                <div class="mb-4">
                    <i class="fa-solid fa-cart-plus fs-1 text-muted opacity-25"></i>
                </div>
                <h4 class="fw-bold">Giỏ hàng trống</h4>
                <p class="text-muted mb-4">Bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
                <a href="<?php echo url(); ?>" class="btn btn-buy px-5 py-3 rounded-pill">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Sản phẩm</th>
                                        <th class="py-3 border-0 text-center">Số lượng</th>
                                        <th class="py-3 border-0 text-end">Giá</th>
                                        <th class="py-3 border-0 text-end pe-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $item): ?>
                                        <tr>
                                            <td class="ps-4 py-4 border-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= htmlspecialchars($item['image']) ?>" class="rounded-3 shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($item['title']) ?></h6>
                                                        <span class="small text-muted"><?= number_format($item['price'], 0, ',', '.') ?>đ / sản phẩm</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 border-0 text-center">
                                                <div class="d-inline-flex align-items-center bg-light rounded-pill p-1">
                                                    <a class="btn btn-sm btn-light rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; text-decoration: none;" href="<?= url('index.php?action=updateCartQuantity&id=' . urlencode($item['id']) . '&change=-1') ?>">
                                                        <i class="fa-solid fa-minus fs-xs"></i>
                                                    </a>
                                                    <span class="px-3 fw-bold"><?= $item['quantity'] ?></span>
                                                    <a class="btn btn-sm btn-light rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; text-decoration: none;" href="<?= url('index.php?action=updateCartQuantity&id=' . urlencode($item['id']) . '&change=1') ?>">
                                                        <i class="fa-solid fa-plus fs-xs"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="py-4 border-0 text-end fw-bold text-dark">
                                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                            </td>
                                            <td class="py-4 border-0 text-end pe-4">
                                                <a href="<?= url('index.php?action=removeFromCart&id=' . $item['id']) ?>" class="text-danger opacity-50 hover-opacity-100 transition-all">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                        <h5 class="fw-bold mb-4">Tổng cộng</h5>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Tạm tính</span>
                            <span><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 fs-5 fw-bold text-dark border-top pt-3">
                            <span>Thành tiền</span>
                            <span class="text-primary"><?= number_format($total, 0, ',', '.') ?>đ</span>
                        </div>
                        <p class="small text-muted mb-4"><i class="fa-solid fa-circle-info me-2"></i>Vì tính chất dịch vụ số, vui lòng thanh toán từng sản phẩm để nhận tài khoản ngay lập tức.</p>
                        
                        <div class="d-grid gap-2">
                            <?php foreach ($cart as $item): ?>
                                <a href="<?= url('index.php?action=checkoutPage&product_id=' . $item['id'] . '&variant_idx=0') ?>" class="btn btn-dark py-3 rounded-3 d-flex justify-content-between align-items-center px-4" data-auth-required="true">
                                    <span>Mua ngay <?= htmlspecialchars($item['title']) ?></span>
                                    <i class="fa-solid fa-chevron-right fs-small"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        
                        <a href="<?php echo url(); ?>" class="btn btn-light w-100 py-3 rounded-3 mt-3 fw-bold text-muted">
                            <i class="fa-solid fa-arrow-left me-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<style>
.cart-page { min-height: 70vh; background-color: #f8f9fa; }
.hover-opacity-100:hover { opacity: 1 !important; }
.transition-all { transition: all 0.3s ease; }
.fs-small { font-size: 0.8rem; }
.fs-xs { font-size: 0.7rem; }
</style>
