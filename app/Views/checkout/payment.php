<?php
$isSuccess = $isSuccess ?? false;
$deliveredItems = $order['delivered_items'] ?? [];
$qty = $order['quantity'] ?? 1;
$got = count($deliveredItems);
$missing = max(0, $qty - $got);
?>
<div class="bg-light min-vh-100 py-5">
    <div class="container">
        <div class="mb-4 text-center">
            <h2 class="fw-bold mb-1 text-dark"><?= $isSuccess ? 'Thanh toán hoàn tất' : 'Thanh toán đơn hàng' ?></h2>
            <p class="text-muted small">Mã đơn hàng: <span class="fw-bold text-dark">#<?= htmlspecialchars($order['id']) ?></span></p>
        </div>

        <!-- Stepper -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="d-flex justify-content-between position-relative stepper-wrapper">
                    <div class="stepper-item completed">
                        <div class="step-counter"><i class="fa-solid fa-check"></i></div>
                        <div class="step-name small mt-2 fw-bold text-muted">Thông tin</div>
                    </div>
                    <div class="stepper-item <?= $isSuccess ? 'completed' : 'active' ?>">
                        <div class="step-counter"><?= $isSuccess ? '<i class="fa-solid fa-check"></i>' : '2' ?></div>
                        <div class="step-name small mt-2 fw-bold <?= $isSuccess ? 'text-muted' : 'text-dark' ?>">Thanh toán</div>
                    </div>
                    <div class="stepper-item <?= $isSuccess ? 'active' : 'text-muted' ?>">
                        <div class="step-counter">3</div>
                        <div class="step-name small mt-2 fw-bold <?= $isSuccess ? 'text-dark' : '' ?>">Hoàn tất</div>
                    </div>
                    <div class="stepper-line"></div>
                </div>
            </div>
        </div>

        <?php if ($isSuccess): ?>
            <!-- STEP 3: SUCCESS & DELIVERED ITEMS -->
            <div class="row justify-content-center animate__animated animate__zoomIn">
                <div class="col-md-8 col-lg-6 text-center">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-center">
                        <div class="success-icon-bg mx-auto mb-4 animate__animated animate__bounceIn">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-3">Thanh toán thành công!</h2>
                        <p class="text-muted fs-6 mb-4">Giao dịch của bạn đã được xác nhận. Chi tiết sản phẩm đã mua:</p>

                        <?php if ($got === 0): ?>
                            <!-- Out of stock - manual deliver -->
                            <div class="card border-0 rounded-4 p-4 text-start bg-light" style="border-left:4px solid #ffc107 !important;">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;">
                                        <i class="fa-solid fa-circle-exclamation text-warning fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Giao hàng thủ công</h6>
                                        <p class="text-muted small mb-0">Hệ thống hiện tại hết gói sẵn có trong kho. Bạn hãy Coppy hàng dưới đây gửi cho Admin qua Telegram hoặc Zalo để nhận tài khoản/key lập tức.</p>
                                    </div>
                                </div>
                                <div class="bg-white border rounded-3 p-3 d-flex align-items-center gap-2 mb-3">
                                    <code class="flex-grow-1 fs-5 fw-bold text-dark" id="success-order-code">#<?= htmlspecialchars($order['id']) ?></code>
                                    <button class="btn btn-dark btn-sm px-3" type="button" onclick="copyText('#<?= htmlspecialchars($order['id']) ?>')">
                                        <i class="fa-regular fa-copy me-1"></i>Sao chép
                                    </button>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="https://t.me/specademy" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-brands fa-telegram me-1"></i>Telegram Admin
                                    </a>
                                    <a href="<?= url('index.php?action=orderHistory') ?>" class="btn btn-outline-dark btn-sm">
                                        <i class="fa-solid fa-clock-rotate-left me-1"></i>Xem trong lịch sử
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Delivered items list -->
                            <div class="card border-0 rounded-4 p-4 text-start bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <h6 class="fw-bold text-success mb-0">
                                        <i class="fa-solid fa-box-open me-2"></i><?= $missing > 0 ? "Đã giao $got/$qty sản phẩm" : "Sản phẩm đã giao ($got)" ?>
                                    </h6>
                                    <button class="btn btn-sm btn-outline-dark" type="button" onclick="copyText(<?= htmlspecialchars(json_encode(implode("\n\n", $deliveredItems)), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fa-regular fa-copy me-1"></i>Sao chép tất cả
                                    </button>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($deliveredItems as $index => $itemContent): ?>
                                        <div class="d-flex align-items-start gap-2 p-2 rounded-3 bg-white border">
                                            <span class="badge bg-dark rounded-pill align-self-start mt-1">#<?= $index + 1 ?></span>
                                            <pre class="small mb-0 flex-grow-1" style="white-space:pre-wrap;word-break:break-word;font-family:'Inter',monospace;font-size:0.85rem;"><?= htmlspecialchars($itemContent) ?></pre>
                                            <button class="btn btn-sm btn-light border" type="button" onclick="copyText(<?= htmlspecialchars(json_encode($itemContent), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($missing > 0): ?>
                                    <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 small">
                                        <i class="fa-solid fa-circle-info me-2"></i>
                                        Còn <strong><?= $missing ?></strong> sản phẩm chưa giao do hết kho. Hãy copy mã đơn
                                        <code class="px-2 py-1 bg-white border rounded">#<?= htmlspecialchars($order['id']) ?></code>
                                        và gửi cho admin để được giao bổ sung.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex gap-3 justify-content-center mt-5">
                            <a href="<?= url('index.php?action=orderHistory') ?>" class="btn btn-black px-4 py-3 rounded-4 fw-bold shadow">
                                <i class="fa-solid fa-clock-rotate-left me-2"></i> LỊCH SỬ ĐƠN HÀNG
                            </a>
                            <a href="<?= url() ?>" class="btn btn-outline-dark px-4 py-3 rounded-4 fw-bold">VỀ TRANG CHỦ</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- STEP 2: PENDING PAYMENT (QR & COUNTDOWN) -->
            <div class="row justify-content-center animate__animated animate__fadeIn">
                <div class="col-md-7 col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                        <div id="payment-header-banner" class="p-4 text-center border-bottom <?= $isExpired ? 'bg-danger bg-opacity-10 border-danger border-opacity-25' : 'bg-warning bg-opacity-10 border-warning border-opacity-25' ?>">
                            <h5 id="payment-header-title" class="fw-bold text-dark mb-1"><?= $isExpired ? 'Đơn hàng đã hết hạn' : 'Đang chờ thanh toán...' ?></h5>
                            <p id="payment-header-subtitle" class="text-muted small mb-0"><?= $isExpired ? 'Đã hết thời gian 5 phút để thực hiện giao dịch' : 'Đơn hàng sẽ tự động kiểm tra trạng thái mỗi 5 giây' ?></p>
                        </div>
                        
                        <div class="p-4 p-md-5 text-center">
                            <!-- Expired state UI -->
                            <div id="payment-expired-container" class="<?= $isExpired ? '' : 'd-none' ?>">
                                <div class="expired-icon-bg mx-auto mb-4 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-hourglass-end fs-2 animate__animated animate__shakeX animate__infinite" style="animation-duration: 2s;"></i>
                                </div>
                                <h4 class="fw-bold text-danger mb-2">Hết hạn thanh toán</h4>
                                <p class="text-muted small mb-4">Mã QR Code đã đóng để tránh giao dịch sai lệch. Vui lòng quay lại trang chủ và tạo đơn hàng mới.</p>
                                <a href="<?= url() ?>" class="btn btn-black w-100 py-3 rounded-3 fw-bold shadow-sm">
                                    <i class="fa-solid fa-cart-shopping me-2"></i>Tạo đơn hàng mới
                                </a>
                            </div>

                            <!-- Active payment UI -->
                            <div id="payment-active-container" class="<?= $isExpired ? 'd-none' : '' ?>">
                                <!-- Countdown Badge -->
                                <div class="badge bg-light text-dark border p-2 px-3 rounded-pill mb-4">
                                    <i class="fa-solid fa-clock me-2 text-primary"></i> Đơn hàng hết hạn sau: <span id="countdown" class="fw-bold">05:00</span>
                                </div>

                                <!-- QR Code SePay -->
                                <div class="qr-box p-3 bg-white rounded-4 border shadow-sm mb-4 d-inline-block position-relative overflow-hidden">
                                    <?php
                                    $bankId = trim((string) ($settings['bank_id'] ?? ''));
                                    $accountNo = trim((string) ($settings['bank_account'] ?? ''));
                                    $accountName = trim((string) ($settings['bank_name'] ?? ''));
                                    $amount = (int) round((float) ($order['amount'] ?? 0));
                                    $bankConfigMissing = ($bankId === '' || $accountNo === '' || $accountName === '');
                                    $orderIdForPayment = strtoupper((string) ($order['id'] ?? ''));
                                    $paymentMemoCode = sprintf('%06d', abs(crc32($orderIdForPayment)) % 1000000);
                                    $paymentMemo = trim($paymentMemoCode . ' ' . $orderIdForPayment);
                                    
                                    $qrUrl = 'https://qr.sepay.vn/img?acc=' . rawurlencode($accountNo)
                                        . '&bank=' . rawurlencode($bankId)
                                        . '&amount=' . rawurlencode((string) $amount)
                                        . '&des=' . rawurlencode($paymentMemo);
                                    ?>
                                    <?php if ($bankConfigMissing): ?>
                                        <div class="alert alert-danger text-start mb-0" style="max-width: 280px;">
                                            Thiếu cấu hình ngân hàng. Vui lòng nhập Ngân hàng, Số tài khoản và Tên chủ tài khoản trong admin.
                                        </div>
                                    <?php else: ?>
                                        <img src="<?= htmlspecialchars($qrUrl, ENT_QUOTES, 'UTF-8') ?>" alt="SePay QR" class="sepay-qr-img">
                                    <?php endif; ?>
                                    
                                    <!-- Laser scanning visual effect -->
                                    <div class="scanning-line"></div>
                                </div>

                                <div class="bank-info-card text-start bg-light p-3 p-md-4 rounded-4 mb-4">
                                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2" style="font-size: 0.9rem;">Thông tin chuyển khoản ngân hàng</h6>
                                    <div class="row g-2 g-md-3">
                                        <div class="col-6">
                                            <span class="text-muted smaller d-block">Ngân hàng</span>
                                            <span class="text-dark fw-bold small"><?= htmlspecialchars($bankId) ?></span>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted smaller d-block">Chủ tài khoản</span>
                                            <span class="text-dark fw-bold small"><?= htmlspecialchars($accountName) ?></span>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="p-2 bg-white rounded-3 border d-flex justify-content-between align-items-center">
                                                <div class="overflow-hidden">
                                                    <span class="text-muted smaller d-block">Số tài khoản</span>
                                                    <span class="text-primary fw-bold d-block text-truncate" id="bank-number" style="font-size: 1.1rem;"><?= htmlspecialchars($accountNo) ?></span>
                                                </div>
                                                <button class="btn btn-sm btn-light border ms-2" onclick="copyText('<?= htmlspecialchars($accountNo) ?>')"><i class="fa-regular fa-copy"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="p-2 bg-white rounded-3 border d-flex justify-content-between align-items-center" style="border-left: 4px solid #dc3545 !important;">
                                                <div class="overflow-hidden">
                                                    <span class="text-muted smaller d-block">Nội dung chuyển khoản (MEMO)</span>
                                                    <span class="text-danger fw-bold d-block text-truncate" id="bank-note" style="font-size: 1.2rem;"><?= htmlspecialchars($paymentMemo) ?></span>
                                                </div>
                                                <button class="btn btn-sm btn-light border ms-2" onclick="copyText(<?= htmlspecialchars(json_encode($paymentMemo), ENT_QUOTES, 'UTF-8') ?>)"><i class="fa-regular fa-copy"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Manual Check Button (just reloads page, which redirects if completed) -->
                                <a href="<?= url('index.php?action=payment&id=' . urlencode($order['id'])) ?>" class="btn btn-black w-100 py-3 rounded-3 fw-bold mb-3 shadow-sm">
                                    <i class="fa-solid fa-rotate me-2"></i>Tôi đã chuyển khoản (Bấm để kiểm tra)
                                </a>

                                <!-- Demo Mode Payment simulation form -->
                                <?php if (($settings['demo_payment_active'] ?? '0') === '1'): ?>
                                <div class="alert alert-warning border-0 rounded-4 mt-2 text-start p-3">
                                    <div class="fw-bold mb-1 text-dark small"><i class="fa-solid fa-flask me-2 text-warning"></i>Chế độ Demo đang bật</div>
                                    <div class="text-muted smaller mb-3">Bấm nút bên dưới để giả lập đã chuyển khoản thành công, hệ thống sẽ tự động giao hàng.</div>
                                    <form method="POST" action="<?= url('index.php?action=paymentDemo') ?>">
                                        <?php echo Csrf::field(); ?>
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                                        <button type="submit" class="btn btn-success w-100 fw-bold py-2 rounded-3 text-white border-0">
                                            <i class="fa-solid fa-circle-check me-2"></i>Demo: Giả lập thanh toán thành công
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="text-center mt-3">
                                <a href="<?= url() ?>" class="text-muted smaller text-decoration-none">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại trang chủ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stepper-wrapper { height: 60px; margin-top: 10px; }
.stepper-item { z-index: 2; background: #f8f9fa; text-align: center; width: 90px; }
.step-counter { width: 40px; height: 40px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: bold; color: #adb5bd; border: 4px solid #fff; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.stepper-item.active .step-counter { background: #000; color: #fff; transform: scale(1.2); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
.stepper-item.completed .step-counter { background: #198754; color: #fff; }
.stepper-line { position: absolute; top: 20px; left: 10%; width: 80%; height: 4px; background: #e9ecef; z-index: 1; border-radius: 2px; }
.success-icon-bg { width: 100px; height: 100px; background: #198754; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 50px; box-shadow: 0 15px 30px rgba(25,135,84,0.3); }
.qr-box {
    width: min(280px, 100%);
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
    border: 2px dashed #dee2e6 !important;
}
.sepay-qr-img {
    display: block;
    width: 100%;
    height: auto;
    aspect-ratio: 1 / 1;
    object-fit: contain;
}
.scanning-line {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(to right, transparent, #007bff, transparent);
    box-shadow: 0 0 15px #007bff, 0 0 5px #fff;
    z-index: 5;
    animation: scan 2.5s ease-in-out infinite;
    opacity: 0.8;
}
@keyframes scan {
    0% { top: 0%; }
    50% { top: 98%; }
    100% { top: 0%; }
}
.smaller { font-size: 0.75rem; }

/* Mobile Optimization */
@media (max-width: 768px) {
    .stepper-item { width: 70px; }
    .step-counter { width: 32px; height: 32px; font-size: 0.8rem; border-width: 2px; }
    .step-name { font-size: 0.65rem !important; }
    .stepper-line { top: 16px; }
    .card { padding: 20px !important; }
}
</style>

<script>
// Clipboard Copy helper
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        AppNotify.success('Đã sao chép: ' + text, 'Sao chép');
    });
}

<?php if (!$isSuccess): ?>
<?php
$db = Database::getInstance();
$elapsed = 0;
try {
    $dbTimeStr = $db->query("SELECT NOW()")->fetchColumn();
    if ($dbTimeStr) {
        $elapsed = max(0, strtotime($dbTimeStr) - strtotime($order['created_at']));
    } else {
        $elapsed = max(0, time() - strtotime($order['created_at']));
    }
} catch (Throwable $e) {
    $elapsed = max(0, time() - strtotime($order['created_at']));
}
$timeLeft = max(0, 300 - $elapsed);
?>
// Countdown Timer (5 minutes)
let timeLeft = <?= $timeLeft ?>;
const timerEl = document.getElementById('countdown');
if (timerEl && timeLeft > 0) {
    const orderStatusUrl = <?= json_encode(url('index.php?action=checkOrderStatus&id=' . urlencode($order['id']))) ?>;
    const pollOrderStatus = () => {
        fetch(orderStatusUrl, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(response => response.ok ? response.json() : null)
            .then(data => {
                if (data && data.success && data.status === 'completed' && data.redirect) {
                    window.location.href = data.redirect;
                }
            })
            .catch(() => {});
    };

    pollOrderStatus();
    const statusInterval = setInterval(pollOrderStatus, 5000);

    const updateTimerDisplay = () => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerEl.innerText = `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    };
    
    updateTimerDisplay(); // Initial display update
    
    const timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            clearInterval(statusInterval);
            
            // Dynamic switch to expired view
            const activeCont = document.getElementById('payment-active-container');
            const expiredCont = document.getElementById('payment-expired-container');
            if (activeCont) activeCont.classList.add('d-none');
            if (expiredCont) expiredCont.classList.remove('d-none');
            
            const headerBanner = document.getElementById('payment-header-banner');
            if (headerBanner) {
                headerBanner.classList.remove('bg-warning', 'bg-opacity-10', 'border-warning', 'border-opacity-25');
                headerBanner.classList.add('bg-danger', 'bg-opacity-10', 'border-danger', 'border-opacity-25');
            }
            
            const headerTitle = document.getElementById('payment-header-title');
            if (headerTitle) headerTitle.innerText = 'Đơn hàng đã hết hạn';
            
            const headerSubtitle = document.getElementById('payment-header-subtitle');
            if (headerSubtitle) headerSubtitle.innerText = 'Đã hết thời gian 5 phút để thực hiện giao dịch';

            AppNotify.error('Thời gian thanh toán đã hết, vui lòng tạo đơn hàng mới.', 'Đơn hàng hết hạn');
        }
    }, 1000);
}
<?php endif; ?>
</script>
