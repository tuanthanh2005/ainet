<section class="py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="profile-heading d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Tài khoản của tôi</h1>
                    <p class="text-muted mb-0">Quản lý thông tin cơ bản và mật khẩu đăng nhập.</p>
                </div>
                <a href="<?php echo url(); ?>" class="profile-home-btn btn btn-light border rounded-pill px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i>Trang chủ
                </a>
            </div>

            
            <div class="row g-4">
                <div class="col-12 col-md-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 56px; height: 56px;">
                                    <?php echo htmlspecialchars(strtoupper(substr($user['name'], 0, 1))); ?>
                                </div>
                                <div>
                                    <h2 class="h5 fw-bold mb-1"><?php echo htmlspecialchars($user['name']); ?></h2>
                                    <div class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            </div>

                            <div class="border-top pt-3 small">
                                <div class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Vai trò</span>
                                    <span class="fw-semibold text-capitalize"><?php echo htmlspecialchars($user['role']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Trạng thái</span>
                                    <span class="badge bg-success rounded-pill"><?php echo htmlspecialchars($user['status']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Ngày tạo</span>
                                    <span class="fw-semibold"><?php echo htmlspecialchars(date('d/m/Y', strtotime($user['created_at']))); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-4">Đổi mật khẩu</h2>
                            <form method="POST" action="<?php echo url('index.php?action=updatePassword'); ?>">
                                <?php echo Csrf::field(); ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" class="form-control bg-light border-0" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mật khẩu mới</label>
                                    <input type="password" name="new_password" class="form-control bg-light border-0" minlength="6" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Nhập lại mật khẩu mới</label>
                                    <input type="password" name="confirm_password" class="form-control bg-light border-0" minlength="6" required>
                                </div>
                                <button type="submit" class="btn btn-buy px-4 py-3 rounded-3">
                                    <i class="fa-solid fa-key me-2"></i>Cập nhật mật khẩu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
