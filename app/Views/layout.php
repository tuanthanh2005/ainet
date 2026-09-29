<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="app-base" content="<?php echo htmlspecialchars(rtrim(URLROOT, '/') . '/', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="google-site-verification" content="79jdDTXZY_GoXLNG1lAAbYH-B8Zeay309QOFuS31NX8">
    <meta name="google-site-verification" content="1gjTYrRzQb_FlsYEZ3ES-ni3DB8U_nxQ2IQVFmdUjRM">
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

$currentAction = $_GET['action'] ?? '';
$isAuthPage = in_array($currentAction, ['login', 'register', 'forgot_password', 'reset_password']);
$isBot = Seo::isBot();
$isGuestSessionExpired = !$isBot && !Auth::check() && (!empty($_SESSION['guest_expired']) || ((time() - (int)($_SESSION['guest_started_at'] ?? time())) >= 300));
$isGuestExpired = !$isAuthPage && $isGuestSessionExpired;
?>
<body>
    <script>
        window.APP_USER_LOGGED_IN = <?php echo $currentUser ? 'true' : 'false'; ?>;
        window.isGuestExpired = <?php echo $isGuestExpired ? 'true' : 'false'; ?>;
        window.APP_LOGIN_URL = '<?php echo Url::login(); ?>';
        window.APP_REGISTER_URL = '<?php echo Url::register(); ?>';
        window.isSwitchingAuthModal = false;

        window.switchModal = function(fromModal, toModal) {
            if (toModal === '#loginModal' || toModal === 'loginModal') {
                window.location.href = window.APP_LOGIN_URL;
                return;
            }
            if (toModal === '#registerModal' || toModal === 'registerModal') {
                window.location.href = window.APP_REGISTER_URL;
                return;
            }
            const toEl = typeof toModal === 'string' ? document.querySelector(toModal) : toModal;
            if (!toEl || typeof bootstrap === 'undefined') return;

            const fromEl = typeof fromModal === 'string' ? document.querySelector(fromModal) : fromModal;
            window.isSwitchingAuthModal = true;
            const toInstance = bootstrap.Modal.getOrCreateInstance(toEl);

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
                            <a href="<?php echo Url::login(); ?>" class="header-link text-secondary text-decoration-none" style="padding: 6px 12px;">Đăng nhập</a>
                            <a href="<?php echo Url::register(); ?>" class="header-link fw-bold text-dark text-decoration-none" style="padding: 6px 12px;">Đăng ký</a>
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
                        <a href="<?php echo Url::login(); ?>" class="header-icon-btn bg-dark text-white fw-bold d-md-none text-decoration-none d-flex align-items-center justify-content-center"
                            style="border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);" title="Đăng nhập">
                            <i class="fa-regular fa-user" style="font-size: 0.95rem;"></i>
                        </a>
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

        <?php if ($isGuestExpired && empty($currentUser) && !in_array($_GET['action'] ?? '', ['login', 'register', 'forgot_password', 'reset_password'])): ?>
        <div class="alert border-0 rounded-4 p-3 shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" 
             style="background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%) !important; border: 1px solid #fde68a !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-25 p-2 text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                </div>
                <div>
                    <strong class="d-block text-dark">Thời gian trải nghiệm vãng lai 5 phút đã hết</strong>
                    <span class="small text-muted">Vui lòng đăng nhập hoặc tạo tài khoản mới để tiếp tục mua sắm và sử dụng đầy đủ tính năng!</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo Url::login(); ?>" class="btn btn-outline-dark btn-sm px-3 py-2 rounded-3 fw-bold">Đăng nhập</a>
                <a href="<?php echo Url::register(); ?>" class="btn btn-buy btn-sm px-3 py-2 rounded-3 fw-bold">Tạo tài khoản</a>
            </div>
        </div>
        <?php endif; ?>
        
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

    <!-- 5-Minute Guest Timeout Handler -->
    <?php if (!Auth::check() && empty($isBot)): ?>
    <script>
    (function() {
        if (window.APP_USER_LOGGED_IN) {
            try { localStorage.removeItem('ainet_guest_started_at'); } catch(e) {}
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

        const currentAction = '<?php echo addslashes($_GET['action'] ?? ''); ?>';
        const isAuthPage = ['login', 'register', 'forgot_password', 'reset_password'].includes(currentAction);

        function checkGuestTimeout() {
            if (window.APP_USER_LOGGED_IN || isAuthPage) return;
            const elapsed = Math.floor(Date.now() / 1000) - guestStartedAt;
            if (elapsed >= GUEST_LIMIT_SECONDS) {
                window.location.href = '<?php echo Url::login(); ?>';
            }
        }

        setInterval(checkGuestTimeout, 2000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) checkGuestTimeout();
        });
        checkGuestTimeout();
    })();
    </script>
    <?php endif; ?>

</body>

</html>
