<?php
$registerError = $_SESSION['register_error'] ?? null;
$oldRegisterName = $_SESSION['old_register_name'] ?? '';
$oldRegisterEmail = $_SESSION['old_register_email'] ?? '';
unset($_SESSION['register_error'], $_SESSION['old_register_name'], $_SESSION['old_register_email']);
?>

<div class="auth-page-wrapper">
    <div class="auth-split-container auth-wide">
        <div class="auth-split-card">
            <div class="row g-0">
                <!-- Left Column: Hero / Value Props (Desktop & Tablet) -->
                <div class="col-lg-5 col-md-5 d-none d-md-block">
                    <div class="auth-hero-panel">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="auth-hero-badge">
                                    <i class="fa-solid fa-sparkles text-warning"></i> AI Premium #1
                                </span>
                            </div>
                            <h3 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Gia nhập cộng đồng <?php echo htmlspecialchars(SITENAME); ?></h3>
                            <p class="text-white-50 small mb-4">Trải nghiệm các gói tài khoản trí tuệ nhân tạo và giải trí bản quyền với chi phí tiết kiệm nhất.</p>

                            <div class="auth-benefits-list">
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-bolt text-warning"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Kích hoạt siêu tốc</strong>
                                        <span class="text-white-50 small">Hệ thống xử lý tự động giao tài khoản 24/7 chỉ sau 30 giây.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-shield-halved text-info"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Bảo hành 1 đổi 1</strong>
                                        <span class="text-white-50 small">Cam kết hỗ trợ uy tín trọn thời gian sử dụng gói dịch vụ.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-tags text-success"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Tiết kiệm tới 70%</strong>
                                        <span class="text-white-50 small">Mức giá ưu đãi và nhiều chính sách tích lũy điểm thưởng hấp dẫn.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-top border-white border-opacity-10 d-flex align-items-center gap-2">
                            <div class="text-warning small">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                            </div>
                            <span class="text-white-50 small">10.000+ khách hàng tin dùng</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Register Form (Wide & Compact) -->
                <div class="col-lg-7 col-md-7">
                    <div class="auth-form-panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Tạo tài khoản mới</h3>
                                <p class="text-muted small mb-0">Đăng ký nhanh chóng chỉ trong vài bước đơn giản</p>
                            </div>
                            <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center rounded-circle text-white shadow-sm flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.15rem;">
                                <i class="fa-solid fa-user-plus"></i>
                            </div>
                        </div>

                        <!-- Expired notice banner (shown only when guest session actually expired) -->
                        <?php if (!empty($isGuestSessionExpired)): ?>
                        <div class="alert alert-warning border-0 rounded-3 mb-3 text-start small py-2 px-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-warning fs-5 mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong class="d-block text-dark">Hết 5 phút trải nghiệm vãng lai</strong>
                                    <span>Vui lòng đăng ký tài khoản (hoặc đăng nhập) để tiếp tục mua sắm và sử dụng dịch vụ.</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Alert error -->
                        <?php if (!empty($registerError)): ?>
                        <div class="alert alert-danger py-2 px-3 rounded-3 small fw-medium d-flex align-items-center mb-3">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                            <span><?php echo htmlspecialchars($registerError); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (GoogleAuth::isConfigured()): ?>
                        <!-- Google Sign-Up Button -->
                        <a href="<?php echo url('index.php?action=googleLogin'); ?>" 
                           class="btn w-100 py-2 mb-3 fw-semibold d-flex align-items-center justify-content-center gap-2 border bg-white text-dark rounded-3 shadow-none hover-lift"
                           style="border-color: #e2e8f0; font-size: 0.9rem;">
                            <svg width="18" height="18" viewBox="0 0 48 48">
                                <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.6 2.5 30.1 0 24 0 14.8 0 6.9 5.4 3 13.3l7.8 6C12.6 13.3 17.9 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4 7.1-10 7.1-17z"/>
                                <path fill="#FBBC05" d="M10.8 28.7A14.5 14.5 0 0 1 9.5 24c0-1.6.3-3.2.8-4.7l-7.8-6A24 24 0 0 0 0 24c0 3.9.9 7.5 2.5 10.7l8.3-6z"/>
                                <path fill="#34A853" d="M24 48c6.1 0 11.2-2 15-5.5l-7.5-5.8c-2 1.4-4.6 2.3-7.5 2.3-6.1 0-11.4-3.8-13.2-9.3l-8.3 6C6.9 42.6 14.8 48 24 48z"/>
                            </svg>
                            <span>Đăng ký nhanh bằng Google</span>
                        </a>

                        <div class="d-flex align-items-center gap-2 mb-3">
                            <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                            <span class="text-muted small px-1" style="font-size: 0.78rem;">hoặc điền thông tin bên dưới</span>
                            <hr class="flex-grow-1 m-0" style="opacity: 0.15;">
                        </div>
                        <?php endif; ?>

                        <!-- Register Form (2 Columns Grid for compactness) -->
                        <form method="POST" action="<?php echo url('index.php?action=register'); ?>" id="pageRegisterForm">
                            <?php echo Csrf::field(); ?>

                            <!-- Honeypot Field -->
                            <div style="display:none !important; opacity:0; position:absolute; left:-9999px;" aria-hidden="true">
                                <input type="text" name="website_url_check" tabindex="-1" autocomplete="off" value="">
                            </div>

                            <!-- Row 1: Name & Email -->
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Họ và tên</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text ps-3">
                                            <i class="fa-regular fa-user"></i>
                                        </span>
                                        <input type="text" name="name" id="registerName" class="form-control py-2"
                                               placeholder="Nguyễn Văn A" value="<?php echo htmlspecialchars($oldRegisterName); ?>" required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Địa chỉ Email</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text ps-3">
                                            <i class="fa-regular fa-envelope"></i>
                                        </span>
                                        <input type="email" name="email" id="registerEmail" class="form-control py-2"
                                               placeholder="hello@example.com" value="<?php echo htmlspecialchars($oldRegisterEmail); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Password & Confirm Password -->
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Mật khẩu</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text ps-3">
                                            <i class="fa-solid fa-lock"></i>
                                        </span>
                                        <input type="password" name="password" id="registerPassword" class="form-control py-2"
                                               placeholder="Tối thiểu 6 ký tự" minlength="6" required>
                                        <button type="button" class="input-group-text pe-3 toggle-password-btn" 
                                                tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('registerPassword', this)">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Nhập lại mật khẩu</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text ps-3">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </span>
                                        <input type="password" name="password_confirmation" id="registerPasswordConfirm" class="form-control py-2"
                                               placeholder="Khớp với mật khẩu" minlength="6" required>
                                        <button type="button" class="input-group-text pe-3 toggle-password-btn" 
                                                tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('registerPasswordConfirm', this)">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="passwordMismatchNote" class="text-danger small mb-2 d-none" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>Mật khẩu nhập lại chưa khớp! Vui lòng kiểm tra lại.
                            </div>

                            <!-- Row 3: Captcha Section -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="registerCaptchaInput" class="form-label small fw-bold text-dark mb-0">Mã bảo vệ (Captcha chống bot)</label>
                                    <a href="javascript:void(0)" id="refreshRegisterCaptchaBtn" class="text-decoration-none text-muted small" title="Đổi mã khác" style="font-size: 0.78rem;">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i>Đổi mã khác
                                    </a>
                                </div>
                                <div class="row g-2 align-items-center">
                                    <div class="col-sm-6">
                                        <input type="text" name="captcha" id="registerCaptchaInput" class="form-control auth-input-group py-2 text-uppercase fw-bold"
                                               placeholder="Nhập 5 ký tự" maxlength="6" autocomplete="off" required style="letter-spacing: 2px;">
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="bg-light rounded-3 p-1 border d-flex align-items-center justify-content-center" style="height: 42px; background-color: #f8fafc !important;">
                                            <img id="registerCaptchaImg" src="<?php echo url('index.php?action=captcha'); ?>" 
                                                 alt="Captcha" class="rounded cursor-pointer" title="Nhấp vào hình để đổi mã khác"
                                                 style="height: 34px; width: 125px; object-fit: contain; cursor: pointer; display: block;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="registerSubmitBtn" class="btn btn-buy w-100 py-2.5 rounded-3 fw-bold shadow-sm mb-3">
                                <span class="btn-text"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Đăng Ký Tài Khoản</span>
                            </button>

                            <div class="text-center pt-2 border-top">
                                <span class="small text-muted">Đã có tài khoản?</span>
                                <a href="<?php echo Url::login(); ?>" class="small fw-bold text-primary text-decoration-none ms-1">
                                    Đăng nhập ngay <i class="fa-solid fa-arrow-right ms-1"></i>
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
