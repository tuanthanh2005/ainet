<?php
$registerError = $_SESSION['register_error'] ?? null;
$oldRegisterName = $_SESSION['old_register_name'] ?? '';
$oldRegisterEmail = $_SESSION['old_register_email'] ?? '';
unset($_SESSION['register_error'], $_SESSION['old_register_name'], $_SESSION['old_register_email']);
?>

<div class="auth-page-wrapper d-flex align-items-center justify-content-center py-4 py-md-5">
    <div class="auth-card-container w-100" style="max-width: 480px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="box-shadow: 0 20px 45px -10px rgba(99, 102, 241, 0.12), 0 0 1px 1px rgba(0,0,0,0.03) !important;">
            <div class="card-body p-4 p-sm-5">
                <!-- Brand / Icon Badge -->
                <div class="text-center mb-4">
                    <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center mb-3 rounded-circle text-white shadow-sm"
                         style="width: 56px; height: 56px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.4rem;">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Tạo tài khoản</h3>
                    <p class="text-muted small mb-0">Đăng ký thành viên <?php echo htmlspecialchars(SITENAME); ?> nhanh chóng</p>
                </div>

                <!-- Expired notice banner (shown only when 5-min guest expired) -->
                <?php if (!empty($isGuestExpired)): ?>
                <div class="alert alert-warning border-0 rounded-3 mb-4 text-start small">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-warning fs-5 mt-1 flex-shrink-0"></i>
                        <div>
                            <strong class="d-block text-dark">Hết 5 phút trải nghiệm vãng lai</strong>
                            <span>Vui lòng đăng ký tài khoản (hoặc đăng nhập) để tiếp tục sử dụng website.</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Alert error -->
                <?php if (!empty($registerError)): ?>
                <div class="alert alert-danger py-2.5 px-3 rounded-3 small fw-medium d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                    <span><?php echo htmlspecialchars($registerError); ?></span>
                </div>
                <?php endif; ?>

                <?php if (GoogleAuth::isConfigured()): ?>
                <!-- Google Sign-Up Button -->
                <a href="<?php echo url('index.php?action=googleLogin'); ?>" 
                   class="btn w-100 py-2.5 mb-3 fw-semibold d-flex align-items-center justify-content-center gap-2 border bg-white text-dark rounded-3 shadow-none hover-lift"
                   style="border-color: #e2e8f0; transition: all 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.6 2.5 30.1 0 24 0 14.8 0 6.9 5.4 3 13.3l7.8 6C12.6 13.3 17.9 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4 7.1-10 7.1-17z"/>
                        <path fill="#FBBC05" d="M10.8 28.7A14.5 14.5 0 0 1 9.5 24c0-1.6.3-3.2.8-4.7l-7.8-6A24 24 0 0 0 0 24c0 3.9.9 7.5 2.5 10.7l8.3-6z"/>
                        <path fill="#34A853" d="M24 48c6.1 0 11.2-2 15-5.5l-7.5-5.8c-2 1.4-4.6 2.3-7.5 2.3-6.1 0-11.4-3.8-13.2-9.3l-8.3 6C6.9 42.6 14.8 48 24 48z"/>
                    </svg>
                    <span>Đăng ký nhanh với Google</span>
                </a>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                    <span class="text-muted small px-1">hoặc đăng ký bằng email</span>
                    <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                </div>
                <?php endif; ?>

                <!-- Register Form -->
                <form method="POST" action="<?php echo url('index.php?action=register'); ?>" id="pageRegisterForm">
                    <?php echo Csrf::field(); ?>

                    <!-- Honeypot Field (Antispam Trap for Bots) -->
                    <div style="display:none !important; opacity:0; position:absolute; left:-9999px;" aria-hidden="true">
                        <input type="text" name="website_url_check" tabindex="-1" autocomplete="off" value="">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Họ và tên</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input type="text" name="name" id="registerName" class="form-control bg-light border-0 py-2.5"
                                   placeholder="Nguyễn Văn A" value="<?php echo htmlspecialchars($oldRegisterName); ?>" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" name="email" id="registerEmail" class="form-control bg-light border-0 py-2.5"
                                   placeholder="hello@example.com" value="<?php echo htmlspecialchars($oldRegisterEmail); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="registerPassword" class="form-control bg-light border-0 py-2.5"
                                   placeholder="Tối thiểu 6 ký tự" minlength="6" required>
                            <button type="button" class="input-group-text bg-light border-0 text-muted pe-3 toggle-password-btn" 
                                    tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('registerPassword', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nhập lại mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="registerPasswordConfirm" class="form-control bg-light border-0 py-2.5"
                                   placeholder="Nhập lại mật khẩu trên" minlength="6" required>
                            <button type="button" class="input-group-text bg-light border-0 text-muted pe-3 toggle-password-btn" 
                                    tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('registerPasswordConfirm', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMismatchNote" class="text-danger small mt-1 d-none" style="font-size: 0.78rem;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>Mật khẩu nhập lại chưa khớp!
                        </div>
                    </div>

                    <!-- Captcha Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="registerCaptchaInput" class="form-label small fw-bold text-dark mb-0">Mã bảo vệ (Captcha)</label>
                            <a href="javascript:void(0)" id="refreshRegisterCaptchaBtn" class="text-decoration-none text-muted small" title="Đổi mã khác" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-arrows-rotate me-1"></i>Đổi mã khác
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-grow-1">
                                <input type="text" name="captcha" id="registerCaptchaInput" class="form-control bg-light border-0 py-2 text-uppercase fw-bold"
                                       placeholder="Nhập mã" maxlength="6" autocomplete="off" required style="letter-spacing: 2px;">
                            </div>
                            <div class="bg-light rounded-3 p-1 border flex-shrink-0 d-flex align-items-center justify-content-center" style="height: 42px; width: 130px; background-color: #f8fafc !important;">
                                <img id="registerCaptchaImg" src="<?php echo url('index.php?action=captcha'); ?>" 
                                     alt="Captcha" class="rounded cursor-pointer" title="Nhấp vào hình để đổi mã khác"
                                     style="height: 36px; width: 120px; object-fit: contain; cursor: pointer; display: block;">
                            </div>
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            Nhập các ký tự trong hình để bảo vệ tài khoản khỏi bot tự động.
                        </div>
                    </div>

                    <button type="submit" id="registerSubmitBtn" class="btn btn-buy w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <span class="btn-text">Đăng Ký Tài Khoản</span>
                    </button>
                </form>
            </div>

            <div class="card-footer bg-light border-0 py-3 text-center">
                <span class="small text-muted">Đã có tài khoản?</span>
                <a href="<?php echo Url::login(); ?>" class="small fw-bold text-primary text-decoration-none ms-1">
                    Đăng nhập <i class="fa-solid fa-arrow-right ms-1"></i>
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

// Captcha Refresh
function refreshRegisterCaptcha() {
    const captchaImg = document.getElementById('registerCaptchaImg');
    if (captchaImg) {
        const baseUrl = '<?php echo url('index.php?action=captcha'); ?>';
        captchaImg.src = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 't=' + new Date().getTime();
    }
}

document.getElementById('refreshRegisterCaptchaBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    refreshRegisterCaptcha();
    const input = document.getElementById('registerCaptchaInput');
    if (input) { input.value = ''; input.focus(); }
});

document.getElementById('registerCaptchaImg')?.addEventListener('click', function() {
    refreshRegisterCaptcha();
    const input = document.getElementById('registerCaptchaInput');
    if (input) { input.value = ''; input.focus(); }
});

// Password confirmation match validator
const pwdInput = document.getElementById('registerPassword');
const confirmInput = document.getElementById('registerPasswordConfirm');
const mismatchNote = document.getElementById('passwordMismatchNote');
const regForm = document.getElementById('pageRegisterForm');

function checkPasswordsMatch() {
    if (!pwdInput || !confirmInput || !mismatchNote) return true;
    if (confirmInput.value.length > 0 && pwdInput.value !== confirmInput.value) {
        mismatchNote.classList.remove('d-none');
        confirmInput.classList.add('is-invalid');
        return false;
    } else {
        mismatchNote.classList.add('d-none');
        confirmInput.classList.remove('is-invalid');
        return true;
    }
}

confirmInput?.addEventListener('input', checkPasswordsMatch);
pwdInput?.addEventListener('input', function() {
    if (confirmInput && confirmInput.value.length > 0) {
        checkPasswordsMatch();
    }
});

regForm?.addEventListener('submit', function(e) {
    if (!checkPasswordsMatch()) {
        e.preventDefault();
        confirmInput.focus();
        return false;
    }
    const btn = document.getElementById('registerSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tạo tài khoản...';
    }
});
</script>
