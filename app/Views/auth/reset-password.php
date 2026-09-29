<?php
$resetError = $_SESSION['reset_error'] ?? null;
unset($_SESSION['reset_error']);
$token = htmlspecialchars($token ?? ($_GET['token'] ?? ''));
$email = htmlspecialchars($email ?? ($_GET['email'] ?? ''));
$isValidToken = !empty($isValidToken);
?>

<div class="auth-page-wrapper d-flex align-items-center justify-content-center py-4 py-md-5">
    <div class="auth-card-container w-100" style="max-width: 460px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="box-shadow: 0 20px 45px -10px rgba(99, 102, 241, 0.12), 0 0 1px 1px rgba(0,0,0,0.03) !important;">
            <div class="card-body p-4 p-sm-5">
                <!-- Brand / Icon Badge -->
                <div class="text-center mb-4">
                    <div class="auth-icon-badge d-inline-flex align-items-center justify-content-center mb-3 rounded-circle text-white shadow-sm"
                         style="width: 56px; height: 56px; background: var(--vip-gradient, linear-gradient(135deg, #6366f1 0%, #a855f7 100%)); font-size: 1.4rem;">
                        <i class="fa-solid fa-lock-open"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Đặt lại mật khẩu</h3>
                    <p class="text-muted small mb-0">Thiết lập mật khẩu mới an toàn cho tài khoản của bạn</p>
                </div>

                <!-- Alert error -->
                <?php if (!empty($resetError)): ?>
                <div class="alert alert-danger py-2.5 px-3 rounded-3 small fw-medium d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                    <span><?php echo htmlspecialchars($resetError); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!$isValidToken): ?>
                <!-- Invalid or expired token screen -->
                <div class="text-center py-3">
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
                        <label class="form-label small fw-bold text-dark">Tài khoản</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control bg-light border-0 py-2.5 text-muted"
                                   value="<?php echo $email; ?>" disabled readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="resetPassword" class="form-control bg-light border-0 py-2.5"
                                   placeholder="Tối thiểu 6 ký tự" minlength="6" required autofocus>
                            <button type="button" class="input-group-text bg-light border-0 text-muted pe-3 toggle-password-btn" 
                                    tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('resetPassword', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">Nhập lại mật khẩu mới</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="resetPasswordConfirm" class="form-control bg-light border-0 py-2.5"
                                   placeholder="Nhập lại mật khẩu mới trên" minlength="6" required>
                            <button type="button" class="input-group-text bg-light border-0 text-muted pe-3 toggle-password-btn" 
                                    tabindex="-1" title="Hiện/ẩn mật khẩu" onclick="togglePasswordVisibility('resetPasswordConfirm', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <div id="resetMismatchNote" class="text-danger small mt-1 d-none" style="font-size: 0.78rem;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>Mật khẩu nhập lại chưa khớp!
                        </div>
                    </div>

                    <button type="submit" id="resetSubmitBtn" class="btn btn-buy w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <span class="btn-text"><i class="fa-solid fa-check me-2"></i>Lưu Mật Khẩu Mới</span>
                    </button>
                </form>
                <?php endif; ?>
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
