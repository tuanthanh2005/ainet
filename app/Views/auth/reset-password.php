<?php
$resetError = $_SESSION['reset_error'] ?? null;
unset($_SESSION['reset_error']);
$token = htmlspecialchars($token ?? ($_GET['token'] ?? ''));
$email = htmlspecialchars($email ?? ($_GET['email'] ?? ''));
$isValidToken = !empty($isValidToken);
?>

<div class="auth-page-wrapper">
    <div class="auth-split-container">
        <div class="auth-split-card">
            <div class="row g-0">
                <!-- Left Column: Hero / Security Recommendations -->
                <div class="col-lg-5 col-md-5 d-none d-md-block">
                    <div class="auth-hero-panel">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <span class="auth-hero-badge">
                                    <i class="fa-solid fa-lock-open text-warning"></i> Đổi mật khẩu
                                </span>
                            </div>
                            <h3 class="fw-bold mb-2 text-white" style="letter-spacing: -0.5px;">Thiết lập mật khẩu mới</h3>
                            <p class="text-white-50 small mb-4">Tạo mật khẩu an toàn và dễ nhớ để bảo vệ tài khoản cũng như các gói dịch vụ của bạn.</p>

                            <div class="auth-benefits-list">
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-key text-warning"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Độ dài tối thiểu</strong>
                                        <span class="text-white-50 small">Sử dụng ít nhất 6 ký tự kết hợp chữ hoa, chữ thường và số.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-shield-halved text-info"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Bảo mật tuyệt đối</strong>
                                        <span class="text-white-50 small">Mật khẩu mới được băm và mã hóa một chiều trong hệ cơ sở dữ liệu.</span>
                                    </div>
                                </div>
                                <div class="auth-benefit-item">
                                    <div class="auth-benefit-icon">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-success"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-white">Đăng nhập ngay lập tức</strong>
                                        <span class="text-white-50 small">Có thể đăng nhập và tiếp tục sử dụng ngay sau khi cập nhật thành công.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-top border-white border-opacity-10 d-flex align-items-center gap-2">
                            <span class="text-white-50 small"><i class="fa-solid fa-circle-check text-success me-1"></i> Xác thực token mã hóa SHA-256</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Reset Form -->
                <div class="col-lg-7 col-md-7">
                    <div class="auth-form-panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Đặt lại mật khẩu</h3>
                                <p class="text-muted small mb-0">Nhập mật khẩu mới cho tài khoản của bạn</p>
                            </div>
                            <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center rounded-circle text-white shadow-sm flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.15rem;">
                                <i class="fa-solid fa-lock-open"></i>
                            </div>
                        </div>

                        <!-- Alert error -->
                        <?php if (!empty($resetError)): ?>
                        <div class="alert alert-danger py-2 px-3 rounded-3 small fw-medium d-flex align-items-center mb-3">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                            <span><?php echo htmlspecialchars($resetError); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (!$isValidToken): ?>
                        <!-- Invalid or expired token screen -->
                        <div class="text-center py-4">
                            <div class="text-warning mb-3">
                                <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Liên kết không hợp lệ hoặc đã hết hạn</h5>
                            <p class="text-muted small mb-4">
                                Liên kết đặt lại mật khẩu chỉ có hiệu lực trong vòng 30 phút hoặc đã được sử dụng trước đó.
                            </p>
                            <a href="<?php echo Url::forgotPassword(); ?>" class="btn btn-buy w-100 py-2.5 rounded-3 fw-bold">
                                <i class="fa-solid fa-rotate-right me-1"></i> Yêu cầu gửi lại liên kết mới
                            </a>
                        </div>
                        <?php else: ?>
                        <!-- Reset Password Form -->
                        <form method="POST" action="<?php echo url('index.php?action=reset_password'); ?>" id="resetForm">
                            <?php echo Csrf::field(); ?>
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <input type="hidden" name="email" value="<?php echo $email; ?>">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Tài khoản</label>
                                <div class="input-group auth-input-group">
                                    <span class="input-group-text ps-3">
                                        <i class="fa-regular fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control py-2 text-muted"
                                           value="<?php echo $email; ?>" disabled readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Mật khẩu mới</label>
                                <div class="input-group auth-input-group">
                                    <span class="input-group-text ps-3">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="resetPassword" class="form-control py-2"
                                           placeholder="Tối thiểu 6 ký tự" minlength="6" required autofocus>
                                    <button type="button" class="input-group-text pe-3 toggle-password-btn" 
                                            tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('resetPassword', this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Nhập lại mật khẩu mới</label>
                                <div class="input-group auth-input-group">
                                    <span class="input-group-text ps-3">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="resetPasswordConfirm" class="form-control py-2"
                                           placeholder="Khớp với mật khẩu trên" minlength="6" required>
                                    <button type="button" class="input-group-text pe-3 toggle-password-btn" 
                                            tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('resetPasswordConfirm', this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <div id="resetMismatchNote" class="text-danger small mt-1 d-none" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>Mật khẩu nhập lại chưa khớp!
                                </div>
                            </div>

                            <button type="submit" id="resetSubmitBtn" class="btn btn-buy w-100 py-2.5 rounded-3 fw-bold shadow-sm mb-3">
                                <span class="btn-text"><i class="fa-solid fa-check me-2"></i>Lưu Mật Khẩu Mới</span>
                            </button>
                        </form>
                        <?php endif; ?>

                        <div class="text-center pt-2 border-top">
                            <a href="<?php echo Url::login(); ?>" class="small fw-semibold text-secondary text-decoration-none">
                                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Đăng nhập
                            </a>
                        </div>
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

const resetPwd = document.getElementById('resetPassword');
const resetConfirm = document.getElementById('resetPasswordConfirm');
const resetNote = document.getElementById('resetMismatchNote');
const resetForm = document.getElementById('resetForm');

function checkResetMatch() {
    if (!resetPwd || !resetConfirm || !resetNote) return true;
    if (resetConfirm.value.length > 0 && resetPwd.value !== resetConfirm.value) {
        resetNote.classList.remove('d-none');
        resetConfirm.classList.add('is-invalid');
        return false;
    } else {
        resetNote.classList.add('d-none');
        resetConfirm.classList.remove('is-invalid');
        return true;
    }
}

resetConfirm?.addEventListener('input', checkResetMatch);
resetPwd?.addEventListener('input', function() {
    if (resetConfirm && resetConfirm.value.length > 0) {
        checkResetMatch();
    }
});

resetForm?.addEventListener('submit', function(e) {
    if (!checkResetMatch()) {
        e.preventDefault();
        resetConfirm.focus();
        return false;
    }
    const btn = document.getElementById('resetSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang lưu mật khẩu...';
    }
});
</script>
