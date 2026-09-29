<?php
$loginError = $_SESSION['login_error'] ?? null;
$oldLoginEmail = $_SESSION['old_login_email'] ?? '';
unset($_SESSION['login_error'], $_SESSION['old_login_email']);
?>

<div class="auth-page-wrapper d-flex align-items-center justify-content-center py-4 py-md-5">
    <div class="auth-card-container w-100" style="max-width: 460px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="box-shadow: 0 20px 45px -10px rgba(99, 102, 241, 0.12), 0 0 1px 1px rgba(0,0,0,0.03) !important;">
            <div class="card-body p-4 p-sm-5">
                <!-- Brand / Icon Badge -->
                <div class="text-center mb-4">
                    <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center mb-3 rounded-circle text-white shadow-sm"
                         style="width: 56px; height: 56px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.4rem;">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Đăng nhập</h3>
                    <p class="text-muted small mb-0">Chào mừng bạn quay lại với <?php echo htmlspecialchars(SITENAME); ?></p>
                </div>

                <!-- Expired notice banner (shown only when 5-min guest expired) -->
                <?php if (!empty($isGuestExpired)): ?>
                <div class="alert alert-warning border-0 rounded-3 mb-4 text-start small">
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
                <div class="alert alert-danger py-2.5 px-3 rounded-3 small fw-medium d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                    <span><?php echo htmlspecialchars($loginError); ?></span>
                </div>
                <?php endif; ?>

                <?php if (GoogleAuth::isConfigured()): ?>
                <!-- Google Login Button -->
                <a href="<?php echo url('index.php?action=googleLogin'); ?>" 
                   class="btn w-100 py-2.5 mb-3 fw-semibold d-flex align-items-center justify-content-center gap-2 border bg-white text-dark rounded-3 shadow-none hover-lift"
                   style="border-color: #e2e8f0; transition: all 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.6 2.5 30.1 0 24 0 14.8 0 6.9 5.4 3 13.3l7.8 6C12.6 13.3 17.9 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4 7.1-10 7.1-17z"/>
                        <path fill="#FBBC05" d="M10.8 28.7A14.5 14.5 0 0 1 9.5 24c0-1.6.3-3.2.8-4.7l-7.8-6A24 24 0 0 0 0 24c0 3.9.9 7.5 2.5 10.7l8.3-6z"/>
                        <path fill="#34A853" d="M24 48c6.1 0 11.2-2 15-5.5l-7.5-5.8c-2 1.4-4.6 2.3-7.5 2.3-6.1 0-11.4-3.8-13.2-9.3l-8.3 6C6.9 42.6 14.8 48 24 48z"/>
                    </svg>
                    <span>Tiếp tục với Google</span>
                </a>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                    <span class="text-muted small px-1">hoặc đăng nhập với email</span>
                    <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="<?php echo url('index.php?action=login'); ?>" id="pageLoginForm">
                    <?php echo Csrf::field(); ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" name="email" id="loginEmail" class="form-control bg-light border-0 py-2.5"
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
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="loginPassword" class="form-control bg-light border-0 py-2.5"
                                   placeholder="••••••••" required>
                            <button type="button" class="input-group-text bg-light border-0 text-muted pe-3 toggle-password-btn" 
                                    tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('loginPassword', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember" value="1" checked>
                            <label class="form-check-label text-muted user-select-none" for="rememberMe">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <button type="submit" id="loginSubmitBtn" class="btn btn-buy w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <span class="btn-text">Đăng Nhập</span>
                    </button>
                </form>
            </div>

            <div class="card-footer bg-light border-0 py-3 text-center">
                <span class="small text-muted">Chưa có tài khoản?</span>
                <a href="<?php echo Url::register(); ?>" class="small fw-bold text-primary text-decoration-none ms-1">
                    Đăng ký ngay <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
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
</script>
