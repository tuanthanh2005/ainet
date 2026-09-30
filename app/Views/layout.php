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

// Dữ liệu footer tối ưu SEO và liên kết nội bộ cho Googlebot
$footerCategories = Cache::remember('footer_seo_cats', 300, function() {
    try {
        return Category::getAll();
    } catch (Throwable $e) {
        return [];
    }
});

$footerHotProducts = Cache::remember('footer_seo_prods', 300, function() {
    try {
        $all = Product::getAll();
        $active = array_filter($all, fn($p) => ($p['status'] ?? 'active') === 'active');
        return array_slice($active, 0, 6);
    } catch (Throwable $e) {
        return [];
    }
});

// Top 4 sản phẩm bán chạy nhất cho Search Modal
$topSellingProducts = Cache::remember('search_top_selling_4', 120, function() {
    try {
        return Product::getTopSelling(4);
    } catch (Throwable $e) {
        return [];
    }
});

$zaloGroupLink = !empty($settings['zalo_group']) ? $settings['zalo_group'] : 'https://zalo.me/g/ifaku0ggmtg4xhxi7k0u';
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
            <span class="marquee-item"><i class="fa-solid fa-users text-primary me-1"></i> <strong>NHÓM ZALO:</strong> Tham gia cộng đồng khách hàng để nhận quà tặng & hỗ trợ nhanh: <a href="<?php echo htmlspecialchars($zaloGroupLink); ?>" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-underline fw-bold">Bấm vào đây để tham gia</a></span>
            <span class="marquee-item"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> <strong>CẢNH BÁO:</strong> Hiện nay có rất nhiều đối tượng giả mạo Shop trên mạng xã hội. Quý khách vui lòng chỉ giao dịch qua các cổng liên hệ trên website! ZALO Admin: <?php echo htmlspecialchars(!empty($settings['zalo']) ? $settings['zalo'] : '0772698113'); ?></span>
            <span class="marquee-item"><i class="fa-solid fa-bolt text-warning me-1"></i> <strong>KHUYẾN MÃI:</strong> Giảm giá cực sâu cho khách hàng mua số lượng lớn hoặc khách sỉ. Liên hệ Zalo/Telegram để nhận ưu đãi!</span>
            <span class="marquee-item"><i class="fa-solid fa-clock text-info me-1"></i> <strong>HỖ TRỢ KHÁCH HÀNG:</strong> Phục vụ liên tục từ 08:00 đến 23:30 hàng ngày (kể cả Thứ 7 và Chủ Nhật).</span>
            <!-- Duplicate for infinite seamless scroll -->
            <span class="marquee-item"><i class="fa-solid fa-fire text-danger me-1"></i> <strong>HỆ THỐNG TÀI KHOẢN PREMIUM TỰ ĐỘNG 24/7:</strong> Cung cấp ChatGPT Plus, API, YouTube Premium, Github Copilot, Canva Pro, Netflix... chính hãng giá tốt nhất thị trường!</span>
            <span class="marquee-item"><i class="fa-solid fa-users text-primary me-1"></i> <strong>NHÓM ZALO:</strong> Tham gia cộng đồng khách hàng để nhận quà tặng & hỗ trợ nhanh: <a href="<?php echo htmlspecialchars($zaloGroupLink); ?>" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-underline fw-bold">Bấm vào đây để tham gia</a></span>
            <span class="marquee-item"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> <strong>CẢNH BÁO:</strong> Hiện nay có rất nhiều đối tượng giả mạo Shop trên mạng xã hội. Quý khách vui lòng chỉ giao dịch qua các cổng liên hệ trên website! ZALO Admin: <?php echo htmlspecialchars(!empty($settings['zalo']) ? $settings['zalo'] : '0772698113'); ?></span>
            <span class="marquee-item"><i class="fa-solid fa-bolt text-warning me-1"></i> <strong>KHUYẾN MÃI:</strong> Giảm giá cực sâu cho khách hàng mua số lượng lớn hoặc khách sỉ. Liên hệ Zalo/Telegram để nhận ưu đãi!</span>
            <span class="marquee-item"><i class="fa-solid fa-clock text-info me-1"></i> <strong>HỖ TRỢ KHÁCH HÀNG:</strong> Phục vụ liên tục từ 08:00 đến 23:30 hàng ngày (kể cả Thứ 7 và Chủ Nhật).</span>
        </div>
    </div>

    <header class="vibrant-header sticky-top py-2.5 py-lg-3 shadow-sm">
        <div class="container-fluid px-3 px-xl-4" style="max-width: 1440px;">
            <div class="d-flex align-items-center justify-content-between gap-2 gap-xl-3">
                <!-- 1. Logo (Cố định, không bị co hoặc đè) -->
                <div class="header-logo-wrap flex-shrink-0">
                    <a href="<?php echo url(); ?>" class="text-decoration-none text-dark fs-4 fw-bold logo-premium"
                        style="letter-spacing: -1px; white-space: nowrap;">
                        <i class="fa-solid fa-circle-nodes me-1"></i>AI<span class="text-muted fw-light">CỦA TÔI</span>
                    </a>
                </div>

                <!-- 2. Thanh điều hướng (Chỉ hiện Desktop) -->
                <div class="header-nav-center flex-grow-1 d-none d-lg-flex justify-content-center px-1 overflow-hidden">
                    <?php
                    $currentAction = $_GET['action'] ?? 'index';
                    $activeTab = $tab ?? ($_GET['tab'] ?? 'home');
                    if (!in_array($activeTab, ['home', 'products', 'blog'])) {
                        $activeTab = 'home';
                    }
                    ?>
                    <div class="header-nav-wrapper">
                        <a href="<?php echo Url::home(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'home') ? 'active' : ''; ?>" aria-label="Trang Chủ" title="Trang Chủ"><i class="fa-solid fa-house"></i><span>Trang Chủ</span></a>
                        <a href="<?php echo Url::products(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'products') ? 'active' : ''; ?>" aria-label="Sản Phẩm" title="Sản Phẩm"><i class="fa-solid fa-bag-shopping"></i><span>Sản Phẩm</span></a>
                        <a href="<?php echo Url::blogs(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo ($currentAction === 'index' && $activeTab === 'blog') ? 'active' : ''; ?>" aria-label="Tạp Chí" title="Tạp Chí"><i class="fa-regular fa-newspaper"></i><span>Tạp Chí</span></a>
                        <a href="<?php echo Url::about(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo $currentAction === 'about' ? 'active' : ''; ?>" aria-label="Giới Thiệu" title="Giới Thiệu"><i class="fa-solid fa-circle-info"></i><span>Giới Thiệu</span></a>
                        <a href="<?php echo Url::contact(); ?>"
                           class="header-nav-btn text-decoration-none <?php echo $currentAction === 'contact' ? 'active' : ''; ?>" aria-label="Liên Hệ" title="Liên Hệ"><i class="fa-solid fa-headset"></i><span>Liên Hệ</span></a>
                        <a href="<?php echo htmlspecialchars($zaloGroupLink); ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="header-nav-btn header-nav-zalo text-decoration-none" aria-label="Nhóm Zalo" title="Tham gia Nhóm Zalo hỗ trợ & săn ưu đãi">
                            <i class="fa-solid fa-users text-primary"></i>
                            <span class="zalo-nav-label">Nhóm Zalo</span>
                            <span class="badge bg-danger text-white rounded-pill ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Mới</span>
                        </a>
                        <button type="button" 
                           class="header-nav-btn header-nav-voucher text-decoration-none border-0 bg-transparent disabled"
                           aria-disabled="true"
                           onclick="if(window.AppNotify) AppNotify.info('Tính năng Nhận Voucher đang được hoàn thiện và sẽ sớm ra mắt!', 'Đang phát triển');"
                           title="Tính năng Nhận Voucher đang phát triển (Soon)">
                            <i class="fa-solid fa-ticket text-warning"></i>
                            <span class="voucher-nav-label">Nhận Voucher</span>
                            <span class="badge bg-secondary bg-opacity-75 text-white rounded-pill ms-1" style="font-size: 0.58rem; padding: 2px 5px; letter-spacing: 0.3px;">Soon</span>
                        </button>
                    </div>
                </div>

                <!-- 3. Cụm nút bấm phải (Cố định, không bị co hoặc đè) -->
                <div class="header-actions-wrap flex-shrink-0 d-flex justify-content-end align-items-center gap-2">
                    <?php if ($currentUser && ($currentUser['role'] ?? '') === 'admin'): ?>
                        <a href="<?php echo url('index.php?action=adminDashboard'); ?>"
                            class="btn btn-dark btn-sm fw-bold rounded-pill px-2.5 py-1 d-none d-sm-inline-flex align-items-center gap-1" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-shield-halved"></i><span>Admin</span>
                        </a>
                    <?php endif; ?>

                    <!-- Nút Kính lúp Tìm kiếm (Desktop & Mobile) -->
                    <button class="header-icon-btn header-search-trigger" type="button" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Tìm kiếm sản phẩm" title="Tìm kiếm (Ctrl+K)">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <!-- Nút Giỏ hàng (Tròn) -->
                    <a href="<?php echo Url::cart(); ?>" class="header-icon-btn position-relative text-decoration-none" title="Giỏ hàng" aria-label="Giỏ hàng">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cart-count" class="position-absolute badge rounded-pill bg-dark border border-light"
                            style="top: 0px; right: -2px; font-size: 0.65rem; padding: 0.25em 0.4em;">
                            <?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : '0'; ?>
                        </span>
                    </a>

                    <!-- Nút Tài khoản dạng Icon (Đăng nhập / Đăng ký hoặc Avatar khi đã đăng nhập) -->
                    <?php if ($currentUser): ?>
                        <div class="dropdown account-dropdown">
                            <button class="header-icon-btn p-0 text-white fw-bold d-inline-flex align-items-center justify-content-center"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                    title="<?php echo htmlspecialchars($currentUser['name']); ?>"
                                    style="background: var(--vip-gradient) !important; border: 2px solid #fff; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);">
                                <?php echo htmlspecialchars(strtoupper(mb_substr($currentUser['name'], 0, 1))); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end account-menu shadow-sm border-0 p-2" style="border-radius: 14px; min-width: 210px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
                                <li class="px-3 py-2 bg-light rounded-top mb-1">
                                    <div class="fw-bold text-dark text-truncate"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                                    <small class="text-muted text-truncate d-block"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></small>
                                </li>
                                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                    <li><a class="dropdown-item fw-bold py-2 rounded-2" href="<?php echo url('index.php?action=adminDashboard'); ?>"><i class="fa-solid fa-shield-halved me-2 text-primary"></i>Trang quản trị</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item py-2 rounded-2" href="<?php echo url('index.php?action=profile'); ?>"><i class="fa-regular fa-user me-2"></i>Profile cá nhân</a></li>
                                <li><a class="dropdown-item py-2 rounded-2" href="<?php echo url('index.php?action=orderHistory'); ?>"><i class="fa-solid fa-clock-rotate-left me-2"></i>Lịch sử đơn hàng</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item text-danger py-2 rounded-2" href="<?php echo url('index.php?action=logout'); ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="dropdown account-dropdown">
                            <button class="header-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Đăng nhập / Đăng ký" aria-label="Tài khoản">
                                <i class="fa-regular fa-user"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="border-radius: 14px; min-width: 190px; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;">
                                <li class="px-3 py-2 bg-light rounded-top mb-1">
                                    <span class="fw-bold text-dark small d-block">Tài khoản</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">Đăng nhập để xem đơn hàng</small>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 fw-semibold rounded-2 d-flex align-items-center gap-2" href="<?php echo Url::login(); ?>">
                                        <i class="fa-solid fa-right-to-bracket text-primary" style="width: 16px;"></i>
                                        <span>Đăng nhập</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 fw-semibold rounded-2 d-flex align-items-center gap-2" href="<?php echo Url::register(); ?>">
                                        <i class="fa-solid fa-user-plus text-success" style="width: 16px;"></i>
                                        <span>Đăng ký</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
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

    <!-- ================= SEO OPTIMIZED MEGA FOOTER ================= -->
    <footer class="vibrant-footer py-5 mt-5" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
        <div class="container">
            <!-- 1. Dải cam kết uy tín & E-E-A-T cho Google và Người dùng -->
            <div class="footer-trust-strip">
                <div class="row g-4">
                    <div class="col-6 col-lg-3">
                        <div class="footer-trust-box">
                            <div class="footer-trust-icon"><i class="fa-solid fa-bolt"></i></div>
                            <div>
                                <strong class="d-block text-white small">Kích Hoạt Tự Động 24/7</strong>
                                <span class="text-secondary" style="font-size: 0.78rem;">Giao tài khoản qua hệ thống 30s</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="footer-trust-box">
                            <div class="footer-trust-icon"><i class="fa-solid fa-shield-halved"></i></div>
                            <div>
                                <strong class="d-block text-white small">Bảo Hành 1 Đổi 1</strong>
                                <span class="text-secondary" style="font-size: 0.78rem;">Uy tín trọn thời hạn gói dịch vụ</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="footer-trust-box">
                            <div class="footer-trust-icon"><i class="fa-solid fa-tags"></i></div>
                            <div>
                                <strong class="d-block text-white small">Tiết Kiệm Tới 70%</strong>
                                <span class="text-secondary" style="font-size: 0.78rem;">Giá rẻ nhất thị trường bản quyền</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="footer-trust-box">
                            <div class="footer-trust-icon"><i class="fa-solid fa-headset"></i></div>
                            <div>
                                <strong class="d-block text-white small">Hỗ Trợ Kỹ Thuật 24/7</strong>
                                <span class="text-secondary" style="font-size: 0.78rem;">Zalo & Telegram trực liên tục</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Hệ thống Internal Links Silo (4 Cột chính) -->
            <div class="row gy-4 gx-lg-5">
                <!-- Cột 1: Thông tin doanh nghiệp & E-E-A-T -->
                <div class="col-12 col-md-6 col-lg-4">
                    <h5 class="text-white fw-bold mb-3 d-flex align-items-center gap-2" style="letter-spacing: -0.5px;">
                        <span style="background: var(--vip-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">AI CỦA TÔI</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small" style="font-size: 0.65rem;">Official</span>
                    </h5>
                    <p class="small text-secondary lh-lg mb-3">
                        <?php echo htmlspecialchars($settings['footerDesc'] ?? 'Hệ sinh thái phân phối tài khoản trí tuệ nhân tạo (ChatGPT Plus, Claude Pro, Midjourney), công cụ lập trình và giải trí bản quyền số 1 Việt Nam.'); ?>
                    </p>

                    <div class="small text-secondary lh-lg mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1.5">
                            <i class="fa-solid fa-phone-volume text-warning" style="width: 16px;"></i>
                            <span>Hotline/Zalo: <a href="https://zalo.me/<?php echo htmlspecialchars($settings['zalo'] ?? '0772698113'); ?>" class="text-white text-decoration-none fw-semibold" target="_blank" rel="noopener"><?php echo htmlspecialchars($settings['zalo'] ?? '0772698113'); ?></a> <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 0.65rem;">Ưu tiên</span></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1.5">
                            <i class="fa-brands fa-telegram text-info" style="width: 16px;"></i>
                            <span>Telegram: <a href="<?php echo htmlspecialchars($settings['socialLink'] ?? 'https://t.me/aicuatoi'); ?>" class="text-white text-decoration-none fw-semibold" target="_blank" rel="noopener">Hỗ trợ kỹ thuật 24/7</a></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1.5">
                            <i class="fa-solid fa-clock text-success" style="width: 16px;"></i>
                            <span>Giờ làm việc: 08:00 - 23:30 (Cả Thứ 7, CN & Lễ)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1.5">
                            <i class="fa-solid fa-users text-primary" style="width: 16px;"></i>
                            <span>Nhóm Zalo: <a href="<?php echo htmlspecialchars($zaloGroupLink); ?>" class="text-white text-decoration-none fw-semibold" target="_blank" rel="noopener">Cộng đồng khách hàng</a> <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill" style="font-size: 0.65rem;">Ưu đãi</span></span>
                        </div>
                    </div>
                </div>

                <!-- Cột 2: Danh mục sản phẩm (Category Silo Links cho Googlebot) -->
                <div class="col-6 col-md-6 col-lg-3">
                    <h6 class="footer-heading">Danh mục dịch vụ</h6>
                    <ul class="list-unstyled small lh-lg mb-0" itemscope itemtype="https://schema.org/SiteNavigationElement">
                        <?php if (!empty($footerCategories)): ?>
                            <?php foreach ($footerCategories as $cat): ?>
                                <li class="mb-2" itemprop="name" style="min-width: 0;">
                                    <a href="<?php echo Url::category($cat['slug']); ?>" class="footer-link" itemprop="url" title="<?php echo htmlspecialchars($cat['name']); ?>">
                                        <i class="fa-solid fa-chevron-right me-2 text-secondary flex-shrink-0" style="font-size: 0.65rem;"></i>
                                        <span class="text-truncate"><?php echo htmlspecialchars($cat['name']); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="mb-2"><a href="<?php echo Url::category('chatgpt'); ?>" class="footer-link"><i class="fa-solid fa-chevron-right me-2 text-secondary flex-shrink-0" style="font-size: 0.65rem;"></i><span class="text-truncate">Tài khoản ChatGPT Plus</span></a></li>
                            <li class="mb-2"><a href="<?php echo Url::category('youtube'); ?>" class="footer-link"><i class="fa-solid fa-chevron-right me-2 text-secondary flex-shrink-0" style="font-size: 0.65rem;"></i><span class="text-truncate">YouTube Premium</span></a></li>
                            <li class="mb-2"><a href="<?php echo Url::category('github'); ?>" class="footer-link"><i class="fa-solid fa-chevron-right me-2 text-secondary flex-shrink-0" style="font-size: 0.65rem;"></i><span class="text-truncate">GitHub Copilot Pro</span></a></li>
                        <?php endif; ?>
                        <li class="mt-2.5 pt-2 border-top border-secondary border-opacity-25">
                            <a href="<?php echo Url::products(); ?>" class="footer-link text-primary fw-semibold">
                                <i class="fa-solid fa-grid-2 me-2 flex-shrink-0"></i><span>Xem tất cả sản phẩm &rarr;</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Cột 3: Sản phẩm bán chạy nhất (Direct Money Links) -->
                <div class="col-6 col-md-6 col-lg-3">
                    <h6 class="footer-heading">Gói dịch vụ nổi bật</h6>
                    <ul class="list-unstyled small lh-lg mb-0" itemscope itemtype="https://schema.org/SiteNavigationElement">
                        <?php if (!empty($footerHotProducts)): ?>
                            <?php foreach ($footerHotProducts as $hp): ?>
                                <li class="mb-2" itemprop="name" style="min-width: 0;">
                                    <a href="<?php echo Url::product($hp); ?>" class="footer-link" itemprop="url" title="Mua <?php echo htmlspecialchars($hp['title']); ?> giá rẻ chính hãng">
                                        <i class="fa-solid fa-fire text-danger me-2 flex-shrink-0" style="font-size: 0.75rem;"></i>
                                        <span class="text-truncate"><?php echo htmlspecialchars($hp['title']); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="mb-2"><a href="<?php echo Url::products(); ?>" class="footer-link"><i class="fa-solid fa-fire text-danger me-2 flex-shrink-0" style="font-size: 0.75rem;"></i><span class="text-truncate">ChatGPT Plus Chính Chủ</span></a></li>
                            <li class="mb-2"><a href="<?php echo Url::products(); ?>" class="footer-link"><i class="fa-solid fa-fire text-danger me-2 flex-shrink-0" style="font-size: 0.75rem;"></i><span class="text-truncate">YouTube Premium 1 Năm</span></a></li>
                            <li class="mb-2"><a href="<?php echo Url::products(); ?>" class="footer-link"><i class="fa-solid fa-fire text-danger me-2 flex-shrink-0" style="font-size: 0.75rem;"></i><span class="text-truncate">Canva Pro Bản Quyền</span></a></li>
                            <li class="mb-2"><a href="<?php echo Url::products(); ?>" class="footer-link"><i class="fa-solid fa-fire text-danger me-2 flex-shrink-0" style="font-size: 0.75rem;"></i><span class="text-truncate">Netflix Premium 4K UHD</span></a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Cột 4: Hỗ trợ & SEO Indexing (Sitemap XML + Robots.txt) -->
                <div class="col-12 col-md-6 col-lg-2">
                    <h6 class="footer-heading">Thông tin & Hỗ trợ</h6>
                    <ul class="list-unstyled small lh-lg mb-3">
                        <li class="mb-2"><a href="<?php echo Url::about(); ?>" class="footer-link" title="Giới thiệu về AI CỦA TÔI">Giới thiệu</a></li>
                        <li class="mb-2"><a href="<?php echo Url::blogs(); ?>" class="footer-link" title="Tạp chí tin tức và thủ thuật AI">Tạp chí AI</a></li>
                        <li class="mb-2"><a href="<?php echo Url::contact(); ?>" class="footer-link" title="Thông tin liên hệ & Hỗ trợ kỹ thuật">Liên hệ hỗ trợ</a></li>
                        <li class="mb-2"><a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#termsModal">Điều khoản dịch vụ</a></li>
                        <li class="mb-2"><a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#privacyModal">Chính sách bảo mật</a></li>
                    </ul>

                    <!-- Google Indexing Tools for Fast Crawling -->
                    <div class="pt-2 border-top border-secondary border-opacity-25">
                        <div class="text-uppercase text-secondary fw-bold mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Google Crawl & Index:</div>
                        <div class="d-flex flex-column gap-1">
                            <a href="<?php echo Url::sitemap(); ?>" class="footer-link text-warning fw-semibold" target="_blank" rel="noopener" title="Sơ đồ website Google XML Sitemap">
                                <i class="fa-solid fa-sitemap me-2 flex-shrink-0"></i>Sitemap XML
                            </a>
                            <a href="<?php echo Url::robots(); ?>" class="footer-link text-secondary" target="_blank" rel="noopener" title="Tệp điều hướng robots.txt">
                                <i class="fa-solid fa-robot me-2 flex-shrink-0"></i>Robots.txt
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Dải từ khóa tìm kiếm SEO phổ biến (SEO Tag Cloud for Google Indexing) -->
            <div class="footer-tag-cloud">
                <div class="d-flex align-items-center flex-wrap gap-1">
                    <span class="text-white small fw-bold me-2"><i class="fa-solid fa-magnifying-glass me-1 text-primary"></i>Từ khóa tìm kiếm:</span>
                    <a href="<?php echo Url::search('chatgpt'); ?>" class="footer-tag-link" title="Mua ChatGPT Plus giá rẻ">Mua ChatGPT Plus</a>
                    <a href="<?php echo Url::search('claude'); ?>" class="footer-tag-link" title="Tài khoản Claude Pro 3.5">Claude Pro</a>
                    <a href="<?php echo Url::search('canva'); ?>" class="footer-tag-link" title="Nâng cấp Canva Pro chính chủ">Canva Pro vĩnh viễn</a>
                    <a href="<?php echo Url::search('youtube'); ?>" class="footer-tag-link" title="Mua YouTube Premium giá rẻ">YouTube Premium 1 năm</a>
                    <a href="<?php echo Url::search('netflix'); ?>" class="footer-tag-link" title="Tài khoản Netflix 4K UHD">Netflix Premium 4K</a>
                    <a href="<?php echo Url::search('github'); ?>" class="footer-tag-link" title="Tài khoản GitHub Copilot Pro">GitHub Copilot</a>
                    <a href="<?php echo Url::search('midjourney'); ?>" class="footer-tag-link" title="Mua tài khoản Midjourney bản quyền">Midjourney AI</a>
                    <a href="<?php echo Url::search('cursor'); ?>" class="footer-tag-link" title="Tài khoản Cursor AI Pro">Cursor Pro</a>
                    <a href="<?php echo Url::search('duolingo'); ?>" class="footer-tag-link" title="Duolingo Super học ngoại ngữ">Duolingo Super</a>
                    <a href="<?php echo Url::search('office'); ?>" class="footer-tag-link" title="Bản quyền Office 365 chính hãng">Office 365 bản quyền</a>
                </div>
            </div>

            <!-- 4. Thanh toán & Bản quyền -->
            <hr class="border-secondary border-opacity-25 mt-4 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 small text-secondary">
                <div>
                    <span>&copy; <?php echo htmlspecialchars($settings['copyright'] ?? (date('Y') . ' AI CỦA TÔI')); ?>. Bản quyền được bảo hộ.</span>
                    <span class="ms-2 d-none d-md-inline text-secondary opacity-75">| Nền tảng tài khoản số chính hãng hàng đầu Việt Nam.</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary bg-opacity-25 text-light fw-normal py-1.5 px-2.5 rounded-pill"><i class="fa-solid fa-qrcode me-1 text-success"></i>VietQR 24/7</span>
                    <span class="badge bg-secondary bg-opacity-25 text-light fw-normal py-1.5 px-2.5 rounded-pill"><i class="fa-solid fa-building-columns me-1 text-info"></i>SePay Bank</span>
                    <span class="badge bg-secondary bg-opacity-25 text-light fw-normal py-1.5 px-2.5 rounded-pill"><i class="fa-solid fa-shield-halved me-1 text-warning"></i>Bảo Mật SSL</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Schema.org Organization Structured Data for Googlebot Discovery -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "OnlineStore",
        "name": "<?php echo htmlspecialchars(SITENAME); ?>",
        "url": "<?php echo rtrim(URLROOT, '/'); ?>",
        "logo": "<?php echo rtrim(URLROOT, '/') . '/assets/images/logo.png'; ?>",
        "description": "<?php echo htmlspecialchars($settings['footerDesc'] ?? 'Hệ thống cung cấp giải pháp phần mềm, tài khoản trí tuệ nhân tạo AI và giải trí bản quyền số 1 Việt Nam.'); ?>",
        "telephone": "<?php echo htmlspecialchars($settings['zalo'] ?? '0772698113'); ?>",
        "priceRange": "$$",
        "paymentAccepted": "Bank Transfer, VietQR, MoMo",
        "currenciesAccepted": "VND",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo htmlspecialchars($settings['zalo'] ?? '0772698113'); ?>",
            "contactType": "customer service",
            "availableLanguage": ["Vietnamese", "English"]
        }
    }
    </script>

    <!-- Modals -->
    <!-- ================= QUICK SEARCH & TOP SELLING MODAL ================= -->
    <div class="modal fade search-spotlight-modal" id="searchModal" tabindex="-1" aria-labelledby="searchModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
                <!-- Search input header -->
                <div class="modal-header border-bottom border-light p-3 p-md-4 bg-white position-relative">
                    <form id="spotlightSearchForm" class="w-100 m-0" action="<?php echo Url::products(); ?>" method="GET" role="search" onsubmit="return handleSpotlightSubmit(event)">
                        <div class="spotlight-search-box d-flex align-items-center px-3 py-2 rounded-4 border bg-light bg-opacity-50">
                            <i class="fa-solid fa-magnifying-glass text-primary fs-5 me-2 flex-shrink-0"></i>
                            <input type="text" inputmode="search" id="spotlightSearchInput" name="q" 
                                   class="form-control border-0 bg-transparent shadow-none p-0 fs-6 text-dark" 
                                   placeholder="Tìm kiếm tài khoản AI, ChatGPT, YouTube, Canva..." 
                                   autocomplete="off" aria-label="Tìm kiếm sản phẩm">
                            <button type="button" id="spotlightClearBtn" class="btn btn-sm btn-link text-muted p-0 text-decoration-none d-none me-2" aria-label="Xóa" title="Xóa từ khóa">
                                <i class="fa-solid fa-xmark fs-6"></i>
                            </button>
                            <span class="badge bg-light text-muted border rounded-2 px-1.5 py-1 d-none d-md-inline-block font-monospace" style="font-size: 0.7rem;">ESC</span>
                        </div>
                    </form>
                    <button type="button" class="btn-close ms-2 shadow-none flex-shrink-0" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <!-- Hot tags suggestion chips -->
                <div class="search-tags-bar px-3 px-md-4 py-2 border-bottom bg-light bg-opacity-50 d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.8rem;">
                    <span class="text-muted fw-semibold me-1"><i class="fa-solid fa-bolt text-warning me-1"></i>Từ khóa hot:</span>
                    <button type="button" class="search-tag-chip btn btn-xs btn-white border rounded-pill px-2.5 py-1 text-dark" data-keyword="chatgpt">ChatGPT Plus</button>
                    <button type="button" class="search-tag-chip btn btn-xs btn-white border rounded-pill px-2.5 py-1 text-dark" data-keyword="claude">Claude Pro</button>
                    <button type="button" class="search-tag-chip btn btn-xs btn-white border rounded-pill px-2.5 py-1 text-dark" data-keyword="youtube">YouTube Premium</button>
                    <button type="button" class="search-tag-chip btn btn-xs btn-white border rounded-pill px-2.5 py-1 text-dark" data-keyword="canva">Canva Pro</button>
                    <button type="button" class="search-tag-chip btn btn-xs btn-white border rounded-pill px-2.5 py-1 text-dark" data-keyword="netflix">Netflix 4K</button>
                    <button type="button" class="search-tag-chip btn btn-xs btn-white border rounded-pill px-2.5 py-1 text-dark" data-keyword="github">GitHub Copilot</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body p-3 p-md-4" style="max-height: 60vh; overflow-y: auto;">
                    <!-- 1. Gợi ý 4 sản phẩm bán nhiều nhất (Default State) -->
                    <div id="spotlightTopSellingSection">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger" style="width: 28px; height: 28px;">
                                    <i class="fa-solid fa-fire" style="font-size: 0.9rem;"></i>
                                </span>
                                <h6 class="fw-bold mb-0 text-dark">Gợi ý sản phẩm bán chạy nhất</h6>
                            </div>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                <i class="fa-solid fa-crown me-1"></i>Top 4 mua nhiều
                            </span>
                        </div>

                        <div class="row g-3">
                            <?php if (!empty($topSellingProducts)): ?>
                                <?php foreach ($topSellingProducts as $tp): ?>
                                    <?php
                                    $tpPrice = (float) ($tp['price'] ?? 0);
                                    $tpOrig = (float) ($tp['original_price'] ?? 0);
                                    $tpHasDiscount = $tpOrig > $tpPrice && $tpPrice > 0;
                                    $tpRating = (float) ($tp['rating'] ?? 5);
                                    $tpSold = (int) ($tp['sold_count'] ?? 0);
                                    $tpUrl = Url::product($tp);
                                    $tpImg = !empty($tp['image']) ? $tp['image'] : image_url('assets/images/placeholder.png');
                                    ?>
                                    <div class="col-12 col-md-6">
                                        <a href="<?php echo htmlspecialchars($tpUrl); ?>" class="spotlight-product-card text-decoration-none d-flex align-items-center gap-3 p-2.5 rounded-3 border bg-white h-100">
                                            <div class="spotlight-product-thumb rounded-3 overflow-hidden flex-shrink-0 position-relative" style="width: 64px; height: 64px; background: #f3f4f6;">
                                                <img src="<?php echo htmlspecialchars($tpImg); ?>" alt="<?php echo htmlspecialchars($tp['title']); ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                                                <?php if ($tpHasDiscount): ?>
                                                    <span class="badge bg-danger position-absolute top-0 start-0 m-1 px-1 py-0.5" style="font-size: 0.6rem;">-<?php echo round((1 - $tpPrice / $tpOrig) * 100); ?>%</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="spotlight-product-info flex-grow-1 min-w-0">
                                                <div class="fw-bold text-dark text-truncate small mb-1" title="<?php echo htmlspecialchars($tp['title']); ?>">
                                                    <?php echo htmlspecialchars($tp['title']); ?>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mb-1" style="font-size: 0.72rem;">
                                                    <span class="text-warning"><i class="fa-solid fa-star me-0.5"></i><?php echo number_format($tpRating, 1); ?></span>
                                                    <span class="text-muted opacity-50">•</span>
                                                    <span class="text-muted"><i class="fa-solid fa-cart-shopping me-1 text-danger"></i>Đã bán <strong><?php echo number_format($tpSold, 0, ',', '.'); ?></strong></span>
                                                </div>
                                                <div class="d-flex align-items-baseline gap-1.5">
                                                    <span class="fw-bold text-primary" style="font-size: 0.88rem;"><?php echo number_format($tpPrice, 0, ',', '.'); ?>đ</span>
                                                    <?php if ($tpHasDiscount): ?>
                                                        <span class="text-muted text-decoration-line-through" style="font-size: 0.72rem;"><?php echo number_format($tpOrig, 0, ',', '.'); ?>đ</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="spotlight-product-arrow text-muted ps-1 flex-shrink-0">
                                                <i class="fa-solid fa-chevron-right small"></i>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-4 text-muted small">
                                    Đang tải danh sách sản phẩm...
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 2. Kết quả tìm kiếm trực tiếp (Live Search Results) -->
                    <div id="spotlightSearchResultsSection" class="d-none">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass text-primary"></i>
                                <span>Kết quả tìm kiếm cho "<span id="spotlightQueryDisplay" class="text-primary"></span>"</span>
                            </h6>
                            <span id="spotlightResultCount" class="badge bg-light text-muted border rounded-pill px-2.5 py-1 small">0 kết quả</span>
                        </div>
                        <div id="spotlightResultsList" class="d-flex flex-column gap-2">
                            <!-- Items injected via JS -->
                        </div>
                        <div id="spotlightNoResults" class="text-center py-5 d-none">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-3 mb-3 text-muted">
                                <i class="fa-solid fa-face-frown fs-2"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Không tìm thấy sản phẩm phù hợp</h6>
                            <p class="text-muted small mb-3">Hãy thử tìm từ khoá khác hoặc tham gia Nhóm Zalo để yêu cầu sản phẩm mới.</p>
                            <a href="<?php echo htmlspecialchars($zaloGroupLink); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fa-solid fa-users me-1"></i>Hỏi trong Nhóm Zalo
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer border-top bg-light bg-opacity-75 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <a href="<?php echo htmlspecialchars($zaloGroupLink); ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none d-flex align-items-center gap-2 small text-dark fw-semibold py-1">
                        <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.72rem;">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <span>Tham gia <strong class="text-primary">Nhóm Zalo</strong> săn deal & nhận hỗ trợ nhanh &rarr;</span>
                    </a>
                    <a href="<?php echo Url::products(); ?>" class="btn btn-buy btn-sm px-3 rounded-pill fw-semibold">
                        Xem tất cả sản phẩm <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
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
        <a href="<?php echo htmlspecialchars($zaloGroupLink); ?>" target="_blank" rel="noopener noreferrer" 
           class="fab-btn fab-zalo-group" 
           aria-label="Nhóm Zalo Cộng Đồng">
            <i class="fa-solid fa-users"></i>
            <span class="fab-tooltip">Nhóm Zalo Ưu Đãi</span>
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

    <!-- Recent Purchase Notification Toast Component -->
    <?php require_once APP_ROOT . '/app/Views/partials/recent_purchase_popup.php'; ?>

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
