<?php
$forgotError = $_SESSION['forgot_error'] ?? null;
$forgotSuccess = $_SESSION['forgot_success'] ?? null;
$oldForgotEmail = $_SESSION['old_forgot_email'] ?? '';
unset($_SESSION['forgot_error'], $_SESSION['forgot_success'], $_SESSION['old_forgot_email']);
?>

<div class="auth-page-wrapper d-flex align-items-center justify-content-center py-4 py-md-5">
    <div class="auth-card-container w-100" style="max-width: 460px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="box-shadow: 0 20px 45px -10px rgba(99, 102, 241, 0.12), 0 0 1px 1px rgba(0,0,0,0.03) !important;">
            <div class="card-body p-4 p-sm-5">
                <!-- Brand / Icon Badge -->
                <div class="text-center mb-4">
                    <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center mb-3 rounded-circle text-white shadow-sm"
                         style="width: 56px; height: 56px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.4rem;">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Quên mật khẩu?</h3>
                    <p class="text-muted small mb-0">Nhập email tài khoản của bạn để nhận liên kết khôi phục mật khẩu</p>
                </div>

                <!-- Alert success -->
                <?php if (!empty($forgotSuccess)): ?>
                <div class="alert alert-success py-3 px-3 rounded-3 small fw-medium mb-4 text-start">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-circle-check text-success fs-5 mt-1 flex-shrink-0"></i>
                        <div>
                            <strong class="d-block mb-1">Đã gửi email khôi phục!</strong>
                            <span><?php echo htmlspecialchars($forgotSuccess); ?></span>
                            <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                                Lưu ý: Nếu không thấy trong Hộp thư đến, vui lòng kiểm tra thư mục <strong>Spam / Thư rác</strong>.
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Alert error -->
                <?php if (!empty($forgotError)): ?>
                <div class="alert alert-danger py-2.5 px-3 rounded-3 small fw-medium d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                    <span><?php echo htmlspecialchars($forgotError); ?></span>
                </div>
                <?php endif; ?>

                <!-- Forgot Form -->
                <form method="POST" action="<?php echo url('index.php?action=forgot_password'); ?>" id="forgotForm">
                    <?php echo Csrf::field(); ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" name="email" id="forgotEmail" class="form-control bg-light border-0 py-2.5"
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
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-grow-1">
                                <input type="text" name="captcha" id="forgotCaptchaInput" class="form-control bg-light border-0 py-2 text-uppercase fw-bold"
                                       placeholder="Nhập mã" maxlength="6" autocomplete="off" required style="letter-spacing: 2px;">
                            </div>
                            <div class="bg-light rounded-3 p-1 border flex-shrink-0 d-flex align-items-center justify-content-center" style="height: 42px; width: 130px; background-color: #f8fafc !important;">
                                <img id="forgotCaptchaImg" src="<?php echo url('index.php?action=captcha'); ?>" 
                                     alt="Captcha" class="rounded cursor-pointer" title="Nhấp vào hình để đổi mã khác"
                                     style="height: 36px; width: 120px; object-fit: contain; cursor: pointer; display: block;">
                            </div>
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            Nhập mã captcha bên cạnh để xác thực yêu cầu gửi thư.
                        </div>
                    </div>

                    <button type="submit" id="forgotSubmitBtn" class="btn btn-buy w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <span class="btn-text"><i class="fa-regular fa-paper-plane me-2"></i>Gửi liên kết khôi phục</span>
                    </button>
                </form>
            </div>

            <div class="card-footer bg-light border-0 py-3 text-center">
                <a href="<?php echo Url::login(); ?>" class="small fw-semibold text-secondary text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Đăng nhập
                </a>
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
