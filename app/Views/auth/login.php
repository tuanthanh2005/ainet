<?php
$loginError = $_SESSION['login_error'] ?? null;
$oldLoginEmail = $_SESSION['old_login_email'] ?? '';
unset($_SESSION['login_error'], $_SESSION['old_login_email']);
?>

<div class="auth-page-wrapper">
    <div class="auth-split-container">
        <div class="auth-split-card">
            <div class="row g-0">
                <!-- Left Column: Hero / Welcome (Desktop & Tablet) -->
                <div class="col-lg-5 col-md-5 d-none d-md-block">
                    <div class="auth-hero-panel">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="auth-hero-badge">
                                    <i class="fa-solid fa-sparkles text-warning"></i> AI Premium #1
                                </span>
                            </div>
                            <h3 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Chào mừng bạn trở lại!</h3>
                            <p class="text-white-50 small mb-4">Đăng nhập tài khoản để quản lý đơn hàng, thông tin gia hạn và tận hưởng trọn vẹn dịch vụ.</p>

                            <div class="auth-benefits-list">
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-rocket text-warning"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Quản lý dễ dàng</strong>
                                        <span class="text-white-50 small">Tra cứu nhanh lịch sử đơn hàng và tài khoản kích hoạt mọi lúc.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-shield-halved text-info"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Bảo mật đa tầng</strong>
                                        <span class="text-white-50 small">Mã hóa thông tin cá nhân và phiên đăng nhập an toàn tối đa.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-crown text-warning"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Ưu đãi thành viên</strong>
                                        <span class="text-white-50 small">Hưởng chiết khấu đặc quyền và quà tặng tri ân định kỳ.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-top border-white border-opacity-10 d-flex align-items-center gap-2">
                            <div class="text-warning small">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <span class="text-white-50 small">Đồng hành cùng 10.000+ người dùng</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Login Form -->
                <div class="col-lg-7 col-md-7">
                    <div class="auth-form-panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Đăng nhập tài khoản</h3>
                                <p class="text-muted small mb-0">Truy cập hệ thống <?php echo htmlspecialchars(SITENAME); ?></p>
                            </div>
                            <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center rounded-circle text-white shadow-sm flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.15rem;">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </div>
                        </div>

                        <!-- Expired notice banner (shown only when guest session actually expired) -->
                        <?php if (!empty($isGuestSessionExpired)): ?>
                        <div class="alert alert-warning border-0 rounded-3 mb-3 text-start small py-2 px-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-warning fs-5 mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong class="d-block text-dark">Hết 5 phút trải nghiệm vãng lai</strong>
                                    <span>Vui lòng đăng nhập hoặc tạo tài khoản mới để tiếp tục mua sắm và sử dụng dịch vụ.</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Alert error -->
                        <?php if (!empty($loginError)): ?>
                        <div class="alert alert-danger py-2 px-3 rounded-3 small fw-medium d-flex align-items-center mb-3">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                            <span><?php echo htmlspecialchars($loginError); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (GoogleAuth::isConfigured()): ?>
                        <!-- Google Login Button -->
                        <a href="<?php echo url('index.php?action=googleLogin'); ?>" 
                           class="btn w-100 py-2 mb-3 fw-semibold d-flex align-items-center justify-content-center gap-2 border bg-white text-dark rounded-3 shadow-none hover-lift"
                           style="border-color: #e2e8f0; font-size: 0.9rem;">
                            <svg width="18" height="18" viewBox="0 0 48 48">
                                <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.6 2.5 30.1 0 24 0 14.8 0 6.9 5.4 3 13.3l7.8 6C12.6 13.3 17.9 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4 7.1-10 7.1-17z"/>
                                <path fill="#FBBC05" d="M10.8 28.7A14.5 14.5 0 0 1 9.5 24c0-1.6.3-3.2.8-4.7l-7.8-6A24 24 0 0 0 0 24c0 3.9.9 7.5 2.5 10.7l8.3-6z"/>
                                <path fill="#34A853" d="M24 48c6.1 0 11.2-2 15-5.5l-7.5-5.8c-2 1.4-4.6 2.3-7.5 2.3-6.1 0-11.4-3.8-13.2-9.3l-8.3 6C6.9 42.6 14.8 48 24 48z"/>
                            </svg>
                            <span>Tiếp tục với Google</span>
                        </a>

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                            <span class="text-muted small px-1" style="font-size: 0.78rem;">hoặc đăng nhập bằng email</span>
                            <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                        </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form method="POST" action="<?php echo url('index.php?action=login'); ?>" id="pageLoginForm">
                            <?php echo Csrf::field(); ?>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Địa chỉ Email</label>
                                <div class="input-group auth-input-group">
                                    <span class="input-group-text ps-3">
                                        <i class="fa-regular fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="loginEmail" class="form-control py-2"
                                           placeholder="hello@example.com" value="<?php echo htmlspecialchars($oldLoginEmail); ?>" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold text-dark mb-0">Mật khẩu</label>
                                    <a href="<?php echo Url::forgotPassword(); ?>" class="text-decoration-none text-primary small" style="font-size: 0.8rem;">
                                        Quên mật khẩu?
                                    </a>
                                </div>
                                <div class="input-group auth-input-group">
                                    <span class="input-group-text ps-3">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="loginPassword" class="form-control py-2"
                                           placeholder="••••••••" required>
                                    <button type="button" class="input-group-text pe-3 toggle-password-btn" 
                                            tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('loginPassword', this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3 small">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember" value="1" checked>
                                    <label class="form-check-label text-muted user-select-none" for="rememberMe">Ghi nhớ đăng nhập</label>
                                </div>
                            </div>

                            <button type="submit" id="loginSubmitBtn" class="btn btn-buy w-100 py-2.5 rounded-3 fw-bold shadow-sm mb-3">
                                <span class="btn-text"><i class="fa-solid fa-right-to-bracket me-2"></i>Đăng Nhập</span>
                            </button>

                            <div class="text-center pt-2 border-top">
                                <span class="small text-muted">Chưa có tài khoản?</span>
                                <a href="<?php echo Url::register(); ?>" class="small fw-bold text-primary text-decoration-none ms-1">
                                    Đăng ký ngay <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

document.getElementById('pageLoginForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('loginSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang đăng nhập...';
    }
});
</script>
