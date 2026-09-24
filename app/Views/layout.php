<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="app-base" content="<?php echo htmlspecialchars(rtrim(URLROOT, '/') . '/', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="google-site-verification" content="79jdDTXZY_GoXLNG1lAAbYH-B8Zeay309QOFuS31NX8">
<?php echo Seo::render($settings ?? []); ?>
<?php if (!empty($metaRefresh)): ?>
    <meta http-equiv="refresh" content="5">
<?php endif; ?>
    <link rel="icon" type="image/png" sizes="180x180" href="<?php echo asset('images/fvcoin-180.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo asset('images/fvcoin-180.png'); ?>">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Link to separated CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <style>
        body.guest-expired-lockout {
            overflow: hidden !important;
        }
        body.guest-expired-lockout > *:not(.modal):not(.modal-backdrop):not(#app-toast-container) {
            filter: blur(4px) grayscale(15%);
            pointer-events: none !important;
            user-select: none !important;
        }
        .modal.forced-lockout .btn-close {
            display: none !important;
        }
        @keyframes modalShakeAnim {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        .shake-animation {
            animation: modalShakeAnim 0.35s ease-in-out;
        }
    </style>
</head>

<?php
$currentUser = $_SESSION['user'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$loginError = $_SESSION['login_error'] ?? null;
$oldLoginEmail = $_SESSION['old_login_email'] ?? '';
unset($_SESSION['login_error'], $_SESSION['old_login_email']);

$registerError = $_SESSION['register_error'] ?? null;
$oldRegisterName = $_SESSION['old_register_name'] ?? '';
$oldRegisterEmail = $_SESSION['old_register_email'] ?? '';
unset($_SESSION['register_error'], $_SESSION['old_register_name'], $_SESSION['old_register_email']);

$isBot = Seo::isBot();
$isGuestExpired = !$isBot && !Auth::check() && (!empty($_SESSION['guest_expired']) || ((time() - (int)($_SESSION['guest_started_at'] ?? time())) >= 300));
?>
<body class="<?php echo $isGuestExpired ? 'guest-expired-lockout' : ''; ?>">
    <script>
        window.APP_USER_LOGGED_IN = <?php echo $currentUser ? 'true' : 'false'; ?>;
        window.isGuestExpired = <?php echo $isGuestExpired ? 'true' : 'false'; ?>;
        window.isSwitchingAuthModal = false;

        window.switchModal = function(fromModal, toModal) {
            const fromEl = typeof fromModal === 'string' ? document.querySelector(fromModal) : fromModal;
            const toEl = typeof toModal === 'string' ? document.querySelector(toModal) : toModal;

            if (!toEl || typeof bootstrap === 'undefined') return;

            window.isSwitchingAuthModal = true;
            const isLocked = !!(window.isGuestExpired && !window.APP_USER_LOGGED_IN);
            const toInstance = bootstrap.Modal.getOrCreateInstance(toEl, {
                backdrop: isLocked ? 'static' : true,
                keyboard: !isLocked
            });

            if (fromEl && fromEl.classList.contains('show')) {
                const fromInstance = bootstrap.Modal.getInstance(fromEl) || bootstrap.Modal.getOrCreateInstance(fromEl);
                const onHidden = function () {
                    fromEl.removeEventListener('hidden.bs.modal', onHidden);
                    toInstance.show();
                    setTimeout(() => { window.isSwitchingAuthModal = false; }, 150);
                };
                fromEl.addEventListener('hidden.bs.modal', onHidden);
                fromInstance.hide();
            } else {
                toInstance.show();
                setTimeout(() => { window.isSwitchingAuthModal = false; }, 150);
            }
        };
    </script>
    <!-- Global Toast Notification Container -->
    <div id="app-toast-container" role="region" aria-label="Thông báo" aria-live="polite"></div>
    <div class="mini-banner">
        <div class="marquee-wrapper">
            <span class="marquee-item"><i class="fa-solid fa-fire text-danger me-1"></i> <strong>HỆ THỐNG TÀI KHOẢN PREMIUM TỰ ĐỘNG 24/7:</strong> Cung cấp ChatGPT Plus, API, YouTube Premium, Github Copilot, Canva Pro, Netflix... chính hãng giá tốt nhất thị trường!</span>
            <span class="marquee-item"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> <strong>CẢNH BÁO:</strong> Hiện nay có rất nhiều đối tượng giả mạo Shop trên mạng xã hội. Quý khách vui lòng chỉ giao dịch qua các cổng liên hệ trên website! ZALO Admin: <?php echo htmlspecialchars(!empty($settings['zalo']) ? $settings['zalo'] : '0772698113'); ?></span>
            <span class="marquee-item"><i class="fa-solid fa-bolt text-warning me-1"></i> <strong>KHUYẾN MÃI:</strong> Giảm giá cực sâu cho khách hàng mua số lượng lớn hoặc khách sỉ. Liên hệ Zalo/Telegram để nhận ưu đãi!</span>
            <span class="marquee-item"><i class="fa-solid fa-clock text-info me-1"></i> <strong>HỖ TRỢ KHÁCH HÀNG:</strong> Phục vụ liên tục từ 08:00 đến 23:30 hàng ngày (kể cả Thứ 7 và Chủ Nhật).</span>
            <!-- Duplicate for infinite seamless scroll -->
            <span class="marquee-item"><i class="fa-solid fa-fire text-danger me-1"></i> <strong>HỆ THỐNG TÀI KHOẢN PREMIUM TỰ ĐỘNG 24/7:</strong> Cung cấp ChatGPT Plus, API, YouTube Premium, Github Copilot, Canva Pro, Netflix... chính hãng giá tốt nhất thị trường!</span>
            <span class="marquee-item"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> <strong>CẢNH BÁO:</strong> Hiện nay có rất nhiều đối tượng giả mạo Shop trên mạng xã hội. Quý khách vui lòng chỉ giao dịch qua các cổng liên hệ trên website! ZALO Admin: <?php echo htmlspecialchars(!empty($settings['zalo']) ? $settings['zalo'] : '0772698113'); ?></span>
            <span class="marquee-item"><i class="fa-solid fa-bolt text-warning me-1"></i> <strong>KHUYẾN MÃI:</strong> Giảm giá cực sâu cho khách hàng mua số lượng lớn hoặc khách sỉ. Liên hệ Zalo/Telegram để nhận ưu đãi!</span>
            <span class="marquee-item"><i class="fa-solid fa-clock text-info me-1"></i> <strong>HỖ TRỢ KHÁCH HÀNG:</strong> Phục vụ liên tục từ 08:00 đến 23:30 hàng ngày (kể cả Thứ 7 và Chủ Nhật).</span>
        </div>
    </div>

    <header class="vibrant-header sticky-top py-3 shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-lg-2">
                    <a href="<?php echo url(); ?>" class="text-decoration-none text-dark fs-4 fw-bold logo-premium"
                        style="letter-spacing: -1px; white-space: nowrap;">
                        <i class="fa-solid fa-circle-nodes me-1"></i>AI<span class="text-muted fw-light">CỦA TÔI</span>
                    </a>
                </div>

                <!-- Thanh điều hướng (Chỉ hiện Desktop) -->
                <div class="col-lg-5 d-none d-lg-block">
                    <?php
                    $currentAction = $_GET['action'] ?? 'index';
                    $activeTab = $tab ?? ($_GET['tab'] ?? 'home');
                    if (!in_array($activeTab, ['home', 'products', 'blog'])) {
                        $activeTab = 'home';
                    }
                    ?>
                    <div class="header-nav-wrapper">
                        <a href="<?php echo Url::home(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'home') ? 'active' : ''; ?>">Trang Chủ</a>
                        <a href="<?php echo Url::products(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'products') ? 'active' : ''; ?>">Sản Phẩm</a>
                        <a href="<?php echo Url::blogs(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'blog') ? 'active' : ''; ?>">Tạp Chí</a>
                        <a href="<?php echo Url::about(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo $currentAction === 'about' ? 'active' : ''; ?>">Giới Thiệu</a>
                        <a href="<?php echo Url::contact(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo $currentAction === 'contact' ? 'active' : ''; ?>">Liên Hệ</a>
                    </div>
                </div>

                <!-- Thanh tìm kiếm (Ẩn trên Mobile, chỉ hiện Desktop) -->
                <div class="col-12 col-lg-2 order-3 order-lg-2 d-none d-lg-block position-relative">
                    <form class="d-flex search-form" action="<?php echo Url::products(); ?>" method="GET" role="search">
                        <input class="form-control" type="search" name="q" value="<?php echo htmlspecialchars($searchQuery ?? ($_GET['q'] ?? '')); ?>"
                            placeholder="Tìm kiếm..."
                            aria-label="Search">
                        <button class="btn px-3" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <!-- Cụm nút bấm phải -->
                <div class="col-6 col-lg-3 order-2 order-lg-3 d-flex justify-content-end align-items-center gap-2">
                    <div class="d-none d-md-flex me-3 align-items-center gap-3">
                        <?php if ($currentUser): ?>
                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                <a href="<?php echo url('index.php?action=adminDashboard'); ?>"
                                    class="btn btn-dark btn-sm fw-bold rounded-pill px-3">
                                    <i class="fa-solid fa-shield-halved me-1"></i>Admin
                                </a>
                            <?php endif; ?>
                            <div class="dropdown account-dropdown">
                                <button class="account-toggle btn btn-light border rounded-pill d-flex align-items-center gap-1 px-2 py-1 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="<?php echo htmlspecialchars($currentUser['name']); ?>">
                                    <span class="account-avatar rounded-circle text-white d-inline-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem; background: var(--vip-gradient) !important;"><?php echo htmlspecialchars(strtoupper(mb_substr($currentUser['name'], 0, 1))); ?></span>
                                    <i class="fa-solid fa-chevron-down account-chevron ms-1 text-muted" style="font-size: 0.7rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end account-menu shadow-sm border-0" style="border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
                                    <li class="px-3 py-2 bg-light rounded-top">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 200px;"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></small>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                        <li><a class="dropdown-item fw-bold py-2" href="<?php echo url('index.php?action=adminDashboard'); ?>"><i class="fa-solid fa-shield-halved me-2 text-primary"></i>Trang quản trị</a></li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item py-2" href="<?php echo url('index.php?action=profile'); ?>"><i class="fa-regular fa-user me-2"></i>Profile</a></li>
                                    <li><a class="dropdown-item py-2" href="<?php echo url('index.php?action=orderHistory'); ?>"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử đơn hàng</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li><a class="dropdown-item text-danger py-2" href="<?php echo url('index.php?action=logout'); ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <button type="button" class="header-link btn btn-link p-0 border-0 shadow-none text-secondary" style="text-decoration:none;" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
                            <button type="button" class="header-link fw-bold text-dark btn btn-link p-0 border-0 shadow-none" style="text-decoration:none;" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</button>
                        <?php endif; ?>
                    </div>

                    <!-- Nút Search (Chỉ Mobile) -->
                    <button class="header-icon-btn d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearchCollapse" aria-expanded="false" aria-controls="mobileSearchCollapse" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <!-- Nút Giỏ hàng (Tròn) -->
                    <a href="<?php echo Url::cart(); ?>" class="header-icon-btn position-relative text-decoration-none" title="Giỏ hàng">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cart-count" class="position-absolute badge rounded-pill bg-dark border border-light"
                            style="top: 0px; right: -2px; font-size: 0.65rem; padding: 0.25em 0.4em;">
                            <?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : '0'; ?>
                        </span>
                    </a>

                    <!-- Nút Avatar (Chỉ Mobile) -->
                    <?php if ($currentUser): ?>
                        <div class="dropdown d-md-none">
                            <button class="header-icon-btn bg-dark text-white fw-bold" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false"
                                style="border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                <?php echo htmlspecialchars(strtoupper(substr($currentUser['name'], 0, 1))); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end account-menu shadow-sm">
                                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <li><a class="dropdown-item fw-bold"
                                            href="<?php echo url('index.php?action=adminDashboard'); ?>"><i
                                                class="fa-solid fa-shield-halved"></i>Trang quản trị</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?php echo url('index.php?action=profile'); ?>"><i
                                            class="fa-regular fa-user"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo url('index.php?action=orderHistory'); ?>"><i
                                            class="fa-solid fa-clock-rotate-left"></i>Lịch sử đơn hàng</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger"
                                        href="<?php echo url('index.php?action=logout'); ?>"><i
                                            class="fa-solid fa-right-from-bracket"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <button class="header-icon-btn bg-dark text-white fw-bold d-md-none" type="button"
                            data-bs-toggle="modal" data-bs-target="#loginModal"
                            style="border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            S
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="collapse d-lg-none bg-light border-bottom p-3" id="mobileSearchCollapse">
        <form class="d-flex search-form w-100" action="<?php echo Url::products(); ?>" method="GET" role="search">
            <input class="form-control me-2" type="search" name="q" value="<?php echo htmlspecialchars($searchQuery ?? ($_GET['q'] ?? '')); ?>"
                placeholder="Tìm kiếm sản phẩm (gpt, git, yt)..." aria-label="Search">
            <button class="btn btn-dark px-3" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <main class="container py-3 flex-grow-1">
        <?php
        // Flash messages are now dispatched via the AppNotify toast system (see script below)
        $flashSuccessJs = $flashSuccess ? json_encode(htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8')) : 'null';
        $flashErrorJs   = $flashError   ? json_encode(htmlspecialchars($flashError,   ENT_QUOTES, 'UTF-8')) : 'null';
        ?>
        
        <?php
        // Navigation Tab Component
        require_once 'partials/navigation.php';

        // Nội dung thay đổi sẽ được nhúng ở đây
        if (isset($view)) {
            require_once $view . '.php';
        } else {
            require_once 'home.php';
        }
        ?>
    </main>

    <footer class="vibrant-footer py-5 mt-5">
        <div class="container">
            <div class="row gy-5">
                <div class="col-12 col-md-4">
                    <h5 class="text-white fw-bold mb-4" style="letter-spacing: -0.5px;">AI CỦA TÔI.</h5>
                    <p class="small text-light lh-lg pe-lg-4"><?php echo htmlspecialchars($settings['footerDesc'] ?? 'Hệ thống phân phối giải pháp phần mềm, tài khoản dịch vụ số nhanh chóng và uy tín.'); ?>
                    </p>

                </div>
                <div class="col-6 col-md-4">
                    <h6 class="text-white fw-bold mb-4 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">
                        Thông tin</h6>
                    <ul class="list-unstyled small lh-lg">
                        <li class="mb-2"><a href="<?php echo Url::about(); ?>" class="footer-link">Giới
                                thiệu</a></li>
                        <li class="mb-2"><a href="<?php echo Url::contact(); ?>"
                                class="footer-link">Liên hệ</a></li>
                        <li class="mb-2"><a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#termsModal">Điều khoản dịch vụ</a></li>
                        <li class="mb-2"><a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#privacyModal">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <h6 class="text-white fw-bold mb-4 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">
                        Thanh toán</h6>
                    <p class="small text-light mb-3">Hỗ trợ giao dịch bảo mật 24/7</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge border border-secondary text-light py-2 px-3 fw-normal rounded-pill">Bank
                            Transfer</span>
                        <span class="badge border border-secondary text-light py-2 px-3 fw-normal rounded-pill">Ví Điện
                            Tử</span>
                    </div>
                </div>
            </div>
            <hr class="border-light mt-5 mb-4" style="opacity: 0.2;">
            <div class="d-flex justify-content-between align-items-center small text-light">
                <span>&copy; <?php echo htmlspecialchars($settings['copyright'] ?? (date('Y') . ' AI CỦA TÔI')); ?>. Bản quyền được bảo hộ.</span>
                <span>Thiết kế bởi MMO VN</span>
            </div>
        </div>
    </footer>

    <!-- Modals -->
    <div class="modal fade <?php echo $isGuestExpired ? 'forced-lockout' : ''; ?>" id="loginModal"
         <?php if ($isGuestExpired): ?>data-bs-backdrop="static" data-bs-keyboard="false"<?php endif; ?>
         tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold">Đăng nhập</h4>
                    <button type="button" class="btn-close shadow-none <?php echo $isGuestExpired ? 'd-none' : ''; ?>" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Expired notice banner (shown only when expired) -->
                    <div id="loginExpiredNotice" class="alert alert-warning border-0 rounded-3 mb-3 <?php echo $isGuestExpired ? '' : 'd-none'; ?> text-start small">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-warning fs-5 mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-dark">Hết 5 phút trải nghiệm vãng lai</strong>
                                <span>Thời gian dùng thử 5 phút đã kết thúc. Vui lòng đăng nhập hoặc tạo tài khoản để tiếp tục trải nghiệm!</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alert message for login error -->
                    <div id="loginModalAlert" class="<?php echo !empty($loginError) ? '' : 'd-none'; ?> mb-3">
                        <div class="alert alert-danger py-2.5 px-3 rounded-3 small fw-medium d-flex align-items-center mb-0">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                            <span id="loginModalAlertText"><?php echo htmlspecialchars($loginError ?? ''); ?></span>
                        </div>
                    </div>

                    <?php if (GoogleAuth::isConfigured()): ?>
                    <a href="<?php echo url('index.php?action=googleLogin'); ?>" class="btn w-100 py-2 mb-3 fw-semibold d-flex align-items-center justify-content-center gap-2"
                        style="border: 2px solid #e2e8f0; background: #fff; color: #1e293b; border-radius: 12px; transition: all 0.2s;"
                        onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#6366f1';"
                        onmouseout="this.style.background='#fff'; this.style.borderColor='#e2e8f0';">
                        <svg width="20" height="20" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9 3.2l6.7-6.7C35.6 2.5 30.1 0 24 0 14.8 0 6.9 5.4 3 13.3l7.8 6C12.6 13.3 17.9 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4 7.1-10 7.1-17z"/>
                            <path fill="#FBBC05" d="M10.8 28.7A14.5 14.5 0 0 1 9.5 24c0-1.6.3-3.2.8-4.7l-7.8-6A24 24 0 0 0 0 24c0 3.9.9 7.5 2.5 10.7l8.3-6z"/>
                            <path fill="#34A853" d="M24 48c6.1 0 11.2-2 15-5.5l-7.5-5.8c-2 1.4-4.6 2.3-7.5 2.3-6.1 0-11.4-3.8-13.2-9.3l-8.3 6C6.9 42.6 14.8 48 24 48z"/>
                        </svg>
                        Tiếp tục với Google
                    </a>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <hr class="flex-grow-1 m-0">
                        <span class="text-muted small px-1">hoặc</span>
                        <hr class="flex-grow-1 m-0">
                    </div>
                    <?php endif; ?>
                    <form id="loginModalForm" method="POST" action="<?php echo url('index.php?action=login'); ?>">
                        <?php echo Csrf::field(); ?>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" id="loginModalEmail" class="form-control bg-light border-0"
                                placeholder="hello@example.com" value="<?php echo htmlspecialchars($oldLoginEmail ?? ''); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Mật khẩu</label>
                            <input type="password" name="password" id="loginModalPassword" class="form-control bg-light border-0"
                                placeholder="••••••••" required>
                        </div>
                        <div class="d-flex justify-content-between mb-4 small fw-medium">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label text-muted" for="rememberMe">Ghi nhớ</label>
                            </div>
                            <a href="#" class="text-dark text-decoration-none border-bottom border-dark">Quên mật
                                khẩu?</a>
                        </div>
                        <button type="submit" id="loginModalSubmitBtn" class="btn btn-buy w-100 py-3 rounded-3 fw-bold">
                            <span class="btn-text">Đăng Nhập</span>
                        </button>
                    </form>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <span class="small text-muted">Chưa có tài khoản? <a href="#"
                            class="text-dark fw-bold text-decoration-none ms-1" onclick="switchModal('#loginModal', '#registerModal'); return false;">Đăng ký ngay</a></span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade <?php echo $isGuestExpired ? 'forced-lockout' : ''; ?>" id="registerModal"
         <?php if ($isGuestExpired): ?>data-bs-backdrop="static" data-bs-keyboard="false"<?php endif; ?>
         tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold">Tạo tài khoản</h4>
                    <button type="button" class="btn-close shadow-none <?php echo $isGuestExpired ? 'd-none' : ''; ?>" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Expired notice banner (shown only when expired) -->
                    <div id="registerExpiredNotice" class="alert alert-warning border-0 rounded-3 mb-3 <?php echo $isGuestExpired ? '' : 'd-none'; ?> text-start small">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-warning fs-5 mt-1 flex-shrink-0"></i>
                            <div>
                                <strong class="d-block text-dark">Hết 5 phút trải nghiệm vãng lai</strong>
                                <span>Vui lòng đăng ký tài khoản (hoặc chuyển sang Đăng nhập) để tiếp tục sử dụng website.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alert message for register error -->
                    <div id="registerModalAlert" class="<?php echo !empty($registerError) ? '' : 'd-none'; ?> mb-3">
                        <div class="alert alert-danger py-2.5 px-3 rounded-3 small fw-medium d-flex align-items-center mb-0">
                            <i class="fa-solid fa-circle-exclamation me-2 fs-6 flex-shrink-0"></i>
                            <span id="registerModalAlertText"><?php echo htmlspecialchars($registerError ?? ''); ?></span>
                        </div>
                    </div>

                    <form id="registerModalForm" method="POST" action="<?php echo url('index.php?action=register'); ?>">
                        <?php echo Csrf::field(); ?>
                        <!-- Honeypot Field (Antispam Trap for Bots) -->
                        <div style="display:none !important; opacity:0; position:absolute; left:-9999px;" aria-hidden="true">
                            <input type="text" name="website_url_check" tabindex="-1" autocomplete="off" value="">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Họ tên</label>
                            <input type="text" name="name" id="registerModalName" class="form-control bg-light border-0"
                                placeholder="Tên của bạn" value="<?php echo htmlspecialchars($oldRegisterName ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" id="registerModalEmail" class="form-control bg-light border-0"
                                placeholder="hello@example.com" value="<?php echo htmlspecialchars($oldRegisterEmail ?? ''); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Mật khẩu</label>
                            <input type="password" name="password" id="registerModalPassword" class="form-control bg-light border-0"
                                placeholder="••••••••" minlength="6" required>
                        </div>
                        <button type="submit" id="registerModalSubmitBtn" class="btn btn-buy w-100 py-3 rounded-3 fw-bold">
                            <span class="btn-text">Tạo Tài Khoản</span>
                        </button>
                    </form>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <span class="small text-muted">Đã có tài khoản? <a href="#"
                            class="text-dark fw-bold text-decoration-none ms-1" onclick="switchModal('#registerModal', '#loginModal'); return false;">Đăng nhập</a></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Legal Modals -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content p-3 border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold">Điều khoản dịch vụ</h4>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4 lh-lg">
                    <?php echo nl2br(htmlspecialchars($settings['terms_of_service'] ?? 'Đang cập nhật nội dung điều khoản...')); ?>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-buy px-4" data-bs-dismiss="modal">Tôi đã hiểu</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content p-3 border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold">Chính sách bảo mật</h4>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4 lh-lg">
                    <?php echo nl2br(htmlspecialchars($settings['privacy_policy'] ?? 'Đang cập nhật nội dung chính sách...')); ?>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-buy px-4" data-bs-dismiss="modal">Tôi đã hiểu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Buttons -->
    <div class="fab-wrapper">
        <button class="fab-btn fab-totop" id="btnScrollToTop" type="button" aria-label="Lên đầu trang">
            <i class="fa-solid fa-arrow-up"></i>
            <span class="fab-tooltip">Lên đầu trang</span>
        </button>
        <?php
        $zaloValue = trim($settings['zalo'] ?? '0772698113');
        $zaloLink = 'https://zalo.me/0772698113';
        if ($zaloValue !== '') {
            if (strpos($zaloValue, 'http') === 0) {
                $zaloLink = $zaloValue;
            } else {
                $cleanPhone = preg_replace('/[^0-9]/', '', $zaloValue);
                if ($cleanPhone !== '') {
                    $zaloLink = 'https://zalo.me/' . $cleanPhone;
                }
            }
        }
        ?>
        <a href="<?= htmlspecialchars($zaloLink) ?>" target="_blank" rel="noopener noreferrer"
           class="fab-btn fab-zalo" 
           aria-label="Hỗ trợ Zalo">
            <span class="zalo-text">Zalo</span>
            <span class="fab-tooltip">Hỗ trợ Zalo</span>
        </a>
        <a href="https://t.me/specademy" target="_blank" rel="noopener noreferrer" 
           class="fab-btn fab-telegram" 
           aria-label="Hỗ trợ Telegram">
            <svg class="fab-svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.928 2.766a1.378 1.378 0 0 0-1.428-.184L2.83 10.375a1.385 1.385 0 0 0-.083 2.535l4.872 2.012 2.072 6.07a1.38 1.38 0 0 0 2.213.565l3.208-2.906 4.908 3.522a1.382 1.382 0 0 0 2.164-.81l3.375-17.18a1.38 1.38 0 0 0-.63-1.417zm-12.458 11.744-.736 2.155-.425-2.658 9.387-8.156-8.226 8.659zm9.053 5.372-4.66-3.344 3.75-8.835-7.447 7.84-4.836-1.996L20.44 4.54l-1.917 15.342z"/>
            </svg>
            <span class="fab-tooltip">Kênh Telegram</span>
        </a>
        <button id="chat-bubble-toggle" class="fab-btn fab-chat" type="button" aria-label="Mở chat hỗ trợ">
            <i class="fa-solid fa-comments"></i>
            <span id="chat-unread-badge" class="chat-badge d-none">0</span>
            <span class="fab-tooltip">Chat trực tuyến</span>
        </button>
    </div>



    <!-- Bootstrap powers the public navigation and modal components. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AppNotify system - Load from public_html root so every page uses the same deployed file -->
    <?php $mainJsVersion = is_file(public_path('assets/js/main.js')) ? filemtime(public_path('assets/js/main.js')) : time(); ?>
    <script src="/assets/js/main.js?v=<?php echo $mainJsVersion; ?>"></script>



    <!-- Link to separated JS - Already loaded above after Bootstrap -->
    <!-- <script src="<?php echo asset('js/main.js'); ?>"></script> -->

    <!-- Flash Session Notifications via AppNotify -->
    <?php if ($flashSuccess || $flashError): ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php if ($flashSuccess): ?>
        AppNotify.success(<?php echo $flashSuccessJs; ?>);
        <?php endif; ?>
        <?php if ($flashError): ?>
        AppNotify.error(<?php echo $flashErrorJs; ?>);
        <?php endif; ?>
    });
    </script>
    <?php endif; ?>

    <!-- Mobile Bottom Navigation Bar -->
    <div class="mobile-bottom-nav d-lg-none">
        <a href="<?php echo Url::home(); ?>" class="mobile-nav-item <?php echo ($currentAction === 'index' && $activeTab === 'home') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i>
            <span>Trang Chủ</span>
        </a>
        <a href="<?php echo Url::products(); ?>" class="mobile-nav-item <?php echo ($currentAction === 'index' && $activeTab === 'products') ? 'active' : ''; ?>">
            <i class="fa-solid fa-box-open"></i>
            <span>Sản Phẩm</span>
        </a>
        <a href="<?php echo Url::blogs(); ?>" class="mobile-nav-item <?php echo ($currentAction === 'index' && $activeTab === 'blog') ? 'active' : ''; ?>">
            <i class="fa-solid fa-newspaper"></i>
            <span>Tạp Chí</span>
        </a>
        <a href="<?php echo Url::about(); ?>" class="mobile-nav-item <?php echo $currentAction === 'about' ? 'active' : ''; ?>">
            <i class="fa-solid fa-circle-info"></i>
            <span>Giới Thiệu</span>
        </a>
        <a href="<?php echo Url::contact(); ?>" class="mobile-nav-item <?php echo $currentAction === 'contact' ? 'active' : ''; ?>">
            <i class="fa-solid fa-phone"></i>
            <span>Liên Hệ</span>
        </a>
    </div>

    <!-- Floating Chat Bubble Component -->
    <?php require_once APP_ROOT . '/app/Views/partials/chat_bubble.php'; ?>

    <!-- 5-Minute Guest Forced Login Lockout Handler -->
    <?php if (!Auth::check() && empty($isBot)): ?>
    <script>
    (function() {
        if (window.APP_USER_LOGGED_IN) {
            try {
                localStorage.removeItem('ainet_guest_started_at');
            } catch(e) {}
            return;
        }

        const STORAGE_KEY = 'ainet_guest_started_at';
        const GUEST_LIMIT_SECONDS = 300; // 5 minutes

        const serverStarted = <?php echo (int)($_SESSION['guest_started_at'] ?? time()); ?>;
        let localStarted = 0;
        try {
            localStarted = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10);
        } catch(e) {}

        const nowSec = Math.floor(Date.now() / 1000);
        let guestStartedAt;
        if (localStarted > 0 && localStarted <= nowSec) {
            guestStartedAt = Math.min(serverStarted, localStarted);
        } else {
            guestStartedAt = serverStarted;
        }

        try {
            localStorage.setItem(STORAGE_KEY, guestStartedAt.toString());
        } catch(e) {}

        let isExpired = <?php echo $isGuestExpired ? 'true' : 'false'; ?>;
        const hasLoginError = <?php echo !empty($loginError) ? 'true' : 'false'; ?>;
        const hasRegisterError = <?php echo !empty($registerError) ? 'true' : 'false'; ?>;

        function getElapsed() {
            return Math.floor(Date.now() / 1000) - guestStartedAt;
        }

        function checkExpiredStatus() {
            if (isExpired) return true;
            if (getElapsed() >= GUEST_LIMIT_SECONDS) {
                isExpired = true;
                window.isGuestExpired = true;
                return true;
            }
            return false;
        }

        function enforceLockout(preferredModalId) {
            if (window.APP_USER_LOGGED_IN) return;
            isExpired = true;
            window.isGuestExpired = true;
            document.body.classList.add('guest-expired-lockout');

            const loginModalEl = document.getElementById('loginModal');
            const regModalEl = document.getElementById('registerModal');
            const loginNotice = document.getElementById('loginExpiredNotice');
            const regNotice = document.getElementById('registerExpiredNotice');

            if (loginNotice) loginNotice.classList.remove('d-none');
            if (regNotice) regNotice.classList.remove('d-none');

            [loginModalEl, regModalEl].forEach(el => {
                if (el) {
                    el.classList.add('forced-lockout');
                    el.setAttribute('data-bs-backdrop', 'static');
                    el.setAttribute('data-bs-keyboard', 'false');
                    const closeBtn = el.querySelector('.btn-close');
                    if (closeBtn) closeBtn.style.display = 'none';
                }
            });

            // Close any non-auth modals that might be open
            document.querySelectorAll('.modal.show').forEach(m => {
                if (m.id !== 'loginModal' && m.id !== 'registerModal') {
                    if (typeof bootstrap !== 'undefined') {
                        const inst = bootstrap.Modal.getInstance(m);
                        if (inst) inst.hide();
                    }
                }
            });

            const targetId = preferredModalId || (hasRegisterError ? '#registerModal' : '#loginModal');
            const targetEl = document.querySelector(targetId) || loginModalEl;

            if (targetEl && typeof bootstrap !== 'undefined') {
                const otherEl = targetEl.id === 'loginModal' ? regModalEl : loginModalEl;
                const isOtherOpen = otherEl && otherEl.classList.contains('show');
                const isTargetOpen = targetEl.classList.contains('show');

                if (!isTargetOpen && !isOtherOpen) {
                    const inst = bootstrap.Modal.getOrCreateInstance(targetEl, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    inst.show();
                }
            }
        }

        // Timer interval check every 1s
        const timerInterval = setInterval(() => {
            if (window.APP_USER_LOGGED_IN) {
                clearInterval(timerInterval);
                return;
            }
            if (checkExpiredStatus()) {
                enforceLockout();
            }
        }, 1000);

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && checkExpiredStatus()) {
                enforceLockout();
            }
        });

        // Anti-bypass click & key capture when locked
        document.addEventListener('click', function(e) {
            if (isExpired && !window.APP_USER_LOGGED_IN) {
                if (!e.target.closest('#loginModal') && !e.target.closest('#registerModal') && !e.target.closest('.modal-content')) {
                    e.preventDefault();
                    e.stopPropagation();
                    enforceLockout();
                }
            }
        }, true);

        // Guard against closing modal when expired
        ['loginModal', 'registerModal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('hidden.bs.modal', () => {
                    if (!window.APP_USER_LOGGED_IN && isExpired && !window.isSwitchingAuthModal) {
                        setTimeout(() => {
                            enforceLockout('#' + id);
                        }, 100);
                    }
                });
            }
        });

        // Setup AJAX handler for Login Form
        const loginForm = document.getElementById('loginModalForm');
        if (loginForm) {
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('loginModalSubmitBtn');
                const alertBox = document.getElementById('loginModalAlert');
                const alertText = document.getElementById('loginModalAlertText');
                const passwordInput = document.getElementById('loginModalPassword');

                if (alertBox) alertBox.classList.add('d-none');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang đăng nhập...';
                }

                try {
                    const formData = new FormData(loginForm);
                    const response = await fetch(loginForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Đăng nhập thành công!';
                        }
                        window.location.href = data.redirect || window.location.href;
                    } else {
                        if (alertBox && alertText) {
                            alertText.textContent = data.message || 'Email hoặc mật khẩu không đúng.';
                            alertBox.classList.remove('d-none');
                        }
                        if (passwordInput) {
                            passwordInput.value = '';
                            passwordInput.focus();
                        }
                        const modalContent = loginForm.closest('.modal-content');
                        if (modalContent) {
                            modalContent.classList.remove('shake-animation');
                            void modalContent.offsetWidth;
                            modalContent.classList.add('shake-animation');
                        }
                        if (checkExpiredStatus()) {
                            enforceLockout('#loginModal');
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<span class="btn-text">Đăng Nhập</span>';
                        }
                    }
                } catch(err) {
                    // Fallback to native form submission
                    loginForm.submit();
                }
            });
        }

        // Setup AJAX handler for Register Form
        const registerForm = document.getElementById('registerModalForm');
        if (registerForm) {
            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('registerModalSubmitBtn');
                const alertBox = document.getElementById('registerModalAlert');
                const alertText = document.getElementById('registerModalAlertText');
                const passwordInput = document.getElementById('registerModalPassword');

                if (alertBox) alertBox.classList.add('d-none');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tạo tài khoản...';
                }

                try {
                    const formData = new FormData(registerForm);
                    const response = await fetch(registerForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Đăng ký thành công!';
                        }
                        window.location.href = data.redirect || window.location.href;
                    } else {
                        if (alertBox && alertText) {
                            alertText.textContent = data.message || 'Đăng ký không thành công. Vui lòng thử lại.';
                            alertBox.classList.remove('d-none');
                        }
                        if (passwordInput) {
                            passwordInput.value = '';
                            passwordInput.focus();
                        }
                        const modalContent = registerForm.closest('.modal-content');
                        if (modalContent) {
                            modalContent.classList.remove('shake-animation');
                            void modalContent.offsetWidth;
                            modalContent.classList.add('shake-animation');
                        }
                        if (checkExpiredStatus()) {
                            enforceLockout('#registerModal');
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<span class="btn-text">Tạo Tài Khoản</span>';
                        }
                    }
                } catch(err) {
                    registerForm.submit();
                }
            });
        }

        // Init on DOM ready
        function initOnReady() {
            if (checkExpiredStatus()) {
                enforceLockout(hasRegisterError ? '#registerModal' : '#loginModal');
            } else if (hasLoginError) {
                const loginEl = document.getElementById('loginModal');
                if (loginEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(loginEl).show();
                }
            } else if (hasRegisterError) {
                const regEl = document.getElementById('registerModal');
                if (regEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(regEl).show();
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initOnReady);
        } else {
            initOnReady();
        }
    })();
    </script>
    <?php endif; ?>

</body>

</html>
