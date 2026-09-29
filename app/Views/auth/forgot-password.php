<?php
$forgotError = $_SESSION['forgot_error'] ?? null;
$forgotSuccess = $_SESSION['forgot_success'] ?? null;
$oldForgotEmail = $_SESSION['old_forgot_email'] ?? '';
unset($_SESSION['forgot_error'], $_SESSION['forgot_success'], $_SESSION['old_forgot_email']);
?>

<div class="auth-page-wrapper">
    <div class="auth-split-container">
        <div class="auth-split-card">
            <div class="row g-0">
                <!-- Left Column: Hero / Security Information -->
                <div class="col-lg-5 col-md-5 d-none d-md-block">
                    <div class="auth-hero-panel">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="auth-hero-badge">
                                    <i class="fa-solid fa-shield-halved text-warning"></i> Bảo mật tài khoản
                                </span>
                            </div>
                            <h3 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Khôi phục mật khẩu an toàn</h3>
                            <p class="text-white-50 small mb-4">Chúng tôi sẽ gửi liên kết đặt lại mật khẩu bảo mật qua hòm thư điện tử của bạn.</p>

                            <div class="auth-benefits-list">
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-envelope-circle-check text-info"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Xác thực qua Email</strong>
                                        <span class="text-white-50 small">Liên kết đặt lại mật khẩu mã hóa chỉ gửi tới chính chủ tài khoản.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-clock text-warning"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Hiệu lực trong 30 phút</strong>
                                        <span class="text-white-50 small">Mã khôi phục tự động hết hạn sau 30 phút để bảo vệ dữ liệu.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-headset text-success"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Hỗ trợ khẩn cấp 24/7</strong>
                                        <span class="text-white-50 small">Nếu không nhận được email, vui lòng liên hệ Zalo Admin để được trợ giúp.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-top border-white border-opacity-10 d-flex align-items-center gap-2">
                            <span class="text-white-50 small"><i class="fa-solid fa-lock me-1"></i> Mã hóa an toàn SSL 256-bit</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Forgot Form -->
                <div class="col-lg-7 col-md-7">
                    <div class="auth-form-panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Quên mật khẩu?</h3>
                                <p class="text-muted small mb-0">Nhập email của bạn để lấy lại quyền truy cập</p>
                            </div>
                            <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center rounded-circle text-white shadow-sm flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.15rem;">
                                <i class="fa-solid fa-key"></i>
                            </div>
                        </div>

                        <!-- Alert success -->
                        <?php if (!empty($forgotSuccess)): ?>
                        <div class="alert alert-success py-2.5 px-3 rounded-3 small fw-medium mb-3 text-start">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fa-solid fa-circle-check text-success fs-5 mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong class="d-block mb-1">Đã gửi email khôi phục!</strong>
                                    <span><?php echo htmlspecialchars($forgotSuccess); ?></span>
                                    <div class="mt-1 text-muted" style="font-size: 0.78rem;">
                                        Nếu không thấy trong Hộp thư đến, vui lòng kiểm tra thư mục <strong>Spam / Thư rác</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Alert error -->
                        <?php if (!empty($forgotError)): ?>
                        <div class="alert alert-danger py-2 px-3 rounded-3 small fw-medium d-flex align-items-center mb-3">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                            <span><?php echo htmlspecialchars($forgotError); ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- Forgot Form -->
                        <form method="POST" action="<?php echo url('index.php?action=forgot_password'); ?>" id="forgotForm">
                            <?php echo Csrf::field(); ?>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Địa chỉ Email đăng ký</label>
                                <div class="input-group auth-input-group">
                                    <span class="input-group-text ps-3">
                                        <i class="fa-regular fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="forgotEmail" class="form-control py-2"
                                           placeholder="hello@example.com" value="<?php echo htmlspecialchars($oldForgotEmail); ?>" required autofocus>
                                </div>
                            </div>

                            <!-- Captcha Section -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="forgotCaptchaInput" class="form-label small fw-bold text-dark mb-0">Mã bảo vệ (Captcha)</label>
                                    <a href="javascript:void(0)" id="refreshForgotCaptchaBtn" class="text-decoration-none text-muted small" title="Đổi mã khác" style="font-size: 0.78rem;">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i>Đổi mã khác
                                    </a>
                                </div>
                                <div class="row g-2 align-items-center">
                                    <div class="col-sm-6">
                                        <input type="text" name="captcha" id="forgotCaptchaInput" class="form-control auth-input-group py-2 text-uppercase fw-bold"
                                               placeholder="Nhập 5 ký tự" maxlength="6" autocomplete="off" required style="letter-spacing: 2px;">
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="bg-light rounded-3 p-1 border d-flex align-items-center justify-content-center" style="height: 42px; background-color: #f8fafc !important;">
                                            <img id="forgotCaptchaImg" src="<?php echo url('index.php?action=captcha'); ?>" 
                                                 alt="Captcha" class="rounded cursor-pointer" title="Nhấp vào hình để đổi mã khác"
                                                 style="height: 34px; width: 125px; object-fit: contain; cursor: pointer; display: block;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="forgotSubmitBtn" class="btn btn-buy w-100 py-2.5 rounded-3 fw-bold shadow-sm mb-3">
                                <span class="btn-text"><i class="fa-regular fa-paper-plane me-2"></i>Gửi liên kết khôi phục</span>
                            </button>

                            <div class="text-center pt-2 border-top">
                                <a href="<?php echo Url::login(); ?>" class="small fw-semibold text-secondary text-decoration-none">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Đăng nhập
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
// Captcha Refresh
function refreshForgotCaptcha() {
    const captchaImg = document.getElementById('forgotCaptchaImg');
    if (captchaImg) {
        const baseUrl = '<?php echo url('index.php?action=captcha'); ?>';
        captchaImg.src = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 't=' + new Date().getTime();
    }
}

document.getElementById('refreshForgotCaptchaBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    refreshForgotCaptcha();
    const input = document.getElementById('forgotCaptchaInput');
    if (input) { input.value = ''; input.focus(); }
});

document.getElementById('forgotCaptchaImg')?.addEventListener('click', function() {
    refreshForgotCaptcha();
    const input = document.getElementById('forgotCaptchaInput');
    if (input) { input.value = ''; input.focus(); }
});

document.getElementById('forgotForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('forgotSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang gửi email...';
    }
});
</script>
