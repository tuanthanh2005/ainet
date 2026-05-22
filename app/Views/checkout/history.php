

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Lịch sử mua hàng</h3>
                    <p class="text-muted small mb-0">Theo dõi trạng thái các đơn hàng của bạn</p>
                </div>
                <a href="index.php" class="btn btn-black rounded-pill px-4">
                    <i class="fa-solid fa-cart-shopping me-2"></i>Tiếp tục mua sắm
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0">Mã đơn hàng</th>
                                <th class="py-3 border-0">Sản phẩm</th>
                                <th class="py-3 border-0 text-center">Số tiền</th>
                                <th class="py-3 border-0 text-center">Trạng thái</th>
                                <th class="py-3 border-0 text-end pe-4">Ngày mua</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fa-solid fa-box-open fa-3x text-light mb-3"></i>
                                            <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold">#<?= $order['id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($order['product_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($order['variant_name']) ?></small>
                                        </td>
                                        <td class="text-center fw-bold text-primary">
                                            <?= number_format($order['amount'], 0, ',', '.') ?>đ
                                        </td>
                                        <td class="text-center">
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                                    <i class="fa-solid fa-check-circle me-1"></i> Thành công
                                                </span>
                                            <?php elseif ($order['status'] === 'pending'): ?>
                                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                                    <i class="fa-solid fa-clock me-1"></i> Chờ thanh toán
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                                    <i class="fa-solid fa-times-circle me-1"></i> Đã hủy
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4 text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


