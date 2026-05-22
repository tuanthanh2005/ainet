<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="app-base" content="<?php echo htmlspecialchars(rtrim(URLROOT, '/') . '/', ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#0f172a">
<?php echo Seo::render($settings ?? []); ?>
<?php if (!empty($metaRefresh)): ?>
    <meta http-equiv="refresh" content="5">
<?php endif; ?>
    <link rel="icon" type="image/png" href="<?php echo asset('images/fvcoin.png'); ?>">
    <link rel="icon" type="image/png" href="<?php echo asset('images/fvcoin.pgn'); ?>">
    <link rel="icon" type="image/png" href="<?php echo url('fvcoin.png'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Link to separated CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>

<body>
    <?php
    $currentUser = $_SESSION['user'] ?? null;
    $flashSuccess = $_SESSION['flash_success'] ?? null;
    $flashError = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    ?>
    <script>
        window.APP_USER_LOGGED_IN = <?php echo $currentUser ? 'true' : 'false'; ?>;
    </script>
    <!-- Global Toast Notification Container -->
    <div id="app-toast-container" role="region" aria-label="Thông báo" aria-live="polite"></div>
    <div class="mini-banner">
        <div class="marquee-wrapper">
            <span class="marquee-item">🔥 <strong>HỆ THỐNG TÀI KHOẢN PREMIUM TỰ ĐỘNG 24/7:</strong> Cung cấp ChatGPT Plus, API, YouTube Premium, Github Copilot, Canva Pro, Netflix... chính hãng giá tốt nhất thị trường!</span>
            <span class="marquee-item">⚠️ <strong>CẢNH BÁO:</strong> Hiện nay có rất nhiều đối tượng giả mạo Shop trên mạng xã hội. Quý khách vui lòng chỉ giao dịch qua các cổng liên hệ trên website! ZALO Admin: <?php echo htmlspecialchars($settings['zalo'] ?? ''); ?></span>
            <span class="marquee-item">⚡ <strong>KHUYẾN MÃI:</strong> Giảm giá cực sâu cho khách hàng mua số lượng lớn hoặc khách sỉ. Liên hệ Zalo/Telegram để nhận ưu đãi!</span>
            <span class="marquee-item">⏰ <strong>HỖ TRỢ KHÁCH HÀNG:</strong> Phục vụ liên tục từ 08:00 đến 23:30 hàng ngày (kể cả Thứ 7 và Chủ Nhật).</span>
            <!-- Duplicate for infinite seamless scroll -->
            <span class="marquee-item">🔥 <strong>HỆ THỐNG TÀI KHOẢN PREMIUM TỰ ĐỘNG 24/7:</strong> Cung cấp ChatGPT Plus, API, YouTube Premium, Github Copilot, Canva Pro, Netflix... chính hãng giá tốt nhất thị trường!</span>
            <span class="marquee-item">⚠️ <strong>CẢNH BÁO:</strong> Hiện nay có rất nhiều đối tượng giả mạo Shop trên mạng xã hội. Quý khách vui lòng chỉ giao dịch qua các cổng liên hệ trên website! ZALO Admin: <?php echo htmlspecialchars($settings['zalo'] ?? ''); ?></span>
            <span class="marquee-item">⚡ <strong>KHUYẾN MÃI:</strong> Giảm giá cực sâu cho khách hàng mua số lượng lớn hoặc khách sỉ. Liên hệ Zalo/Telegram để nhận ưu đãi!</span>
            <span class="marquee-item">⏰ <strong>HỖ TRỢ KHÁCH HÀNG:</strong> Phục vụ liên tục từ 08:00 đến 23:30 hàng ngày (kể cả Thứ 7 và Chủ Nhật).</span>
        </div>
    </div>

    <header class="vibrant-header sticky-top py-3 shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-lg-3">
                    <a href="<?php echo url(); ?>" class="text-decoration-none text-dark fs-3 fw-bold"
                        style="letter-spacing: -1px;">
                        <i class="fa-solid fa-circle-nodes me-1"></i>AI<span class="text-muted fw-light">CỦA TÔI</span>
                    </a>
                </div>

                <!-- Thanh tìm kiếm (Ẩn trên Mobile, chỉ hiện Desktop) -->
                <div class="col-12 col-lg-5 order-3 order-lg-2 d-none d-lg-block position-relative">
                    <form class="d-flex search-form" action="<?php echo url('index.php'); ?>" method="GET" role="search">
                        <input type="hidden" name="tab" value="products">
                        <input class="form-control" type="search" name="q" value="<?php echo htmlspecialchars($searchQuery ?? ($_GET['q'] ?? '')); ?>"
                            placeholder="Tìm kiếm sản phẩm (vd: gpt, git, yt)..."
                            aria-label="Search">
                        <button class="btn px-4" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <!-- Cụm nút bấm phải -->
                <div class="col-6 col-lg-4 order-2 order-lg-3 d-flex justify-content-end align-items-center gap-2">
                    <div class="d-none d-md-flex me-3 align-items-center gap-3">
                        <?php if ($currentUser): ?>
                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                <a href="<?php echo url('index.php?action=adminDashboard'); ?>"
                                    class="btn btn-dark btn-sm fw-bold rounded-pill px-3">
                                    <i class="fa-solid fa-shield-halved me-1"></i>Admin
                                </a>
                            <?php endif; ?>
                            <div class="dropdown account-dropdown">
                                <button class="account-toggle" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <span
                                        class="account-avatar"><?php echo htmlspecialchars(strtoupper(substr($currentUser['name'], 0, 1))); ?></span>
                                    <span class="account-greeting">Hi,
                                        <?php echo htmlspecialchars($currentUser['name']); ?></span>
                                    <i class="fa-solid fa-chevron-down account-chevron"></i>
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
                                    <li><a class="dropdown-item"
                                            href="<?php echo url('index.php?action=orderHistory'); ?>"><i
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
                            <button type="button" class="header-link btn btn-link p-0 border-0 shadow-none" style="text-decoration:none;" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
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
        <form class="d-flex search-form w-100" action="<?php echo url('index.php'); ?>" method="GET" role="search">
            <input type="hidden" name="tab" value="products">
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
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold">Đăng nhập</h4>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                    <form method="POST" action="<?php echo url('index.php?action=login'); ?>">
                        <?php echo Csrf::field(); ?>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0"
                                placeholder="hello@example.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Mật khẩu</label>
                            <input type="password" name="password" class="form-control bg-light border-0"
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
                        <button type="submit" class="btn btn-buy w-100 py-3 rounded-3">Đăng Nhập</button>
                    </form>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <span class="small text-muted">Chưa có tài khoản? <a href="#"
                            class="text-dark fw-bold text-decoration-none ms-1" data-bs-toggle="modal"
                            data-bs-target="#registerModal">Đăng ký ngay</a></span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold">Tạo tài khoản</h4>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?php echo url('index.php?action=register'); ?>">
                        <?php echo Csrf::field(); ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Họ tên</label>
                            <input type="text" name="name" class="form-control bg-light border-0"
                                placeholder="Tên của bạn" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0"
                                placeholder="hello@example.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Mật khẩu</label>
                            <input type="password" name="password" class="form-control bg-light border-0"
                                placeholder="••••••••" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-buy w-100 py-3 rounded-3">Tạo Tài Khoản</button>
                    </form>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <span class="small text-muted">Đã có tài khoản? <a href="#"
                            class="text-dark fw-bold text-decoration-none ms-1" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Đăng nhập</a></span>
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
        <button class="fab-btn fab-totop" id="btnScrollToTop" title="Lên đầu trang">
            <i class="fa-solid fa-arrow-up"></i>
        </button>
        <a href="https://t.me/specademy" target="_blank" class="fab-btn fab-telegram" title="Hỗ trợ Telegram">
            <i class="fa-brands fa-telegram"></i>
        </a>

        <?php if (!$currentUser || ($currentUser['role'] ?? 'user') !== 'admin'): ?>
            <button type="button" class="fab-btn fab-chat position-relative" id="btnOpenChat" title="Chat với Admin">
                <i class="fa-regular fa-comment"></i>
                <span id="chat-unread-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                      style="font-size: 0.7rem;">0</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Bootstrap & SweetAlert2 — Load TRƯỚC các modal để data-bs-toggle hoạt động -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- AppNotify system - Load from public_html root so every page uses the same deployed file -->
    <?php $mainJsVersion = is_file(public_path('assets/js/main.js')) ? filemtime(public_path('assets/js/main.js')) : time(); ?>
    <script src="/assets/js/main.js?v=<?php echo $mainJsVersion; ?>"></script>

    <?php if ($currentUser && ($currentUser['role'] ?? 'user') !== 'admin'): ?>
    <!-- User chat box -->
    <div id="user-chatbox" class="user-chatbox shadow-lg" style="display:none;">
        <div class="user-chatbox-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center position-relative" style="width:36px;height:36px;">
                    <i class="fa-solid fa-headset"></i>
                    <span class="online-status-dot"></span>
                </div>
                <div>
                    <div class="fw-bold small text-white">Hỗ trợ AI CỦA TÔI</div>
                    <div class="small text-white-50" style="font-size:0.75rem;">Phản hồi trong vài phút</div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-link text-white p-0" id="btnCloseChat" title="Đóng">
                <i class="fa-solid fa-xmark fs-5"></i>
            </button>
        </div>
        <div id="user-chat-messages" class="user-chat-messages"></div>
        <div id="user-chat-attachment-preview" class="user-chat-preview d-none"></div>
        <div class="user-chat-counter px-2 py-1 text-end text-muted" id="user-chat-counter" style="font-size: 0.75rem; background: #fff; border-top: 1px solid var(--border-color); display: none;">0/700</div>
        <form id="user-chat-form" class="user-chat-form" autocomplete="off" enctype="multipart/form-data">
            <button type="button" class="btn btn-light border-0 chat-attach-btn" id="btnAttachFile" title="Đính kèm tệp">
                <i class="fa-solid fa-paperclip"></i>
            </button>
            <input type="file" id="user-chat-file" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.txt,.csv">
            <textarea id="user-chat-input" class="form-control form-control-sm chat-textarea" placeholder="Nhập tin nhắn..." maxlength="700" rows="1"></textarea>
            <button type="submit" class="btn btn-dark btn-sm rounded-circle" id="user-chat-send" title="Gửi"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
    <script>
    (function () {
        const APP_BASE = document.querySelector('meta[name="app-base"]').getAttribute('content');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const box = document.getElementById('user-chatbox');
        const list = document.getElementById('user-chat-messages');
        const form = document.getElementById('user-chat-form');
        const input = document.getElementById('user-chat-input');
        const fileInput = document.getElementById('user-chat-file');
        const attachBtn = document.getElementById('btnAttachFile');
        const preview = document.getElementById('user-chat-attachment-preview');
        const badge = document.getElementById('chat-unread-badge');
        const btnOpen = document.getElementById('btnOpenChat');
        const btnClose = document.getElementById('btnCloseChat');
        const charCounter = document.getElementById('user-chat-counter');
        const sendBtn = document.getElementById('user-chat-send');
        let lastId = 0;
        let pollTimer = null;
        let pendingFile = null;
        let isSending = false;
        const renderedIds = new Set();

        function playNotificationSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                // Note 1: D5
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime);
                gain1.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
                osc1.start(audioCtx.currentTime);
                osc1.stop(audioCtx.currentTime + 0.4);
                
                // Note 2: A5
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.12);
                gain2.gain.setValueAtTime(0.15, audioCtx.currentTime + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.62);
                osc2.start(audioCtx.currentTime + 0.12);
                osc2.stop(audioCtx.currentTime + 0.62);
            } catch (e) {
                console.error("Audio error:", e);
            }
        }

        function escapeHtml(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        function setBadge(n) {
            if (n > 0) {
                badge.textContent = n > 99 ? '99+' : n;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        function formatBytes(b) {
            if (!b) return '';
            if (b < 1024) return b + ' B';
            if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
            return (b / 1024 / 1024).toFixed(2) + ' MB';
        }

        function renderAttachment(m) {
            if (!m.attachment_path) return '';
            const url = APP_BASE + 'index.php?action=chatFile&id=' + m.id;
            const isImage = (m.attachment_mime || '').indexOf('image/') === 0;
            if (isImage) {
                return `<a href="${url}" target="_blank" class="chat-att-img"><img src="${url}" alt="${escapeHtml(m.attachment_name)}"></a>`;
            }
            return `<a href="${url}" target="_blank" class="chat-att-file">
                        <i class="fa-solid fa-file-arrow-down"></i>
                        <div class="chat-att-meta">
                            <div class="fw-bold small text-truncate" style="max-width:200px;">${escapeHtml(m.attachment_name)}</div>
                            <div class="small text-muted">${formatBytes(m.attachment_size)}</div>
                        </div>
                    </a>`;
        }

        function renderMessage(m) {
            if (m.id && renderedIds.has(String(m.id))) return;
            if (m.id) renderedIds.add(String(m.id));
            const isMe = m.sender === 'user';
            const time = m.created_at ? new Date(m.created_at.replace(' ', 'T')).toLocaleTimeString('vi-VN', { hour:'2-digit', minute:'2-digit' }) : '';
            const cls  = isMe ? 'msg-me' : 'msg-them';
            const bodyHtml = m.body ? `<div class="bubble">${escapeHtml(m.body)}</div>` : '';
            const attHtml  = renderAttachment(m);
            list.insertAdjacentHTML('beforeend',
                `<div class="chat-msg ${cls}">${bodyHtml}${attHtml ? `<div class="bubble bubble-att">${attHtml}</div>` : ''}<div class="meta">${time}</div></div>`);
            list.scrollTop = list.scrollHeight;
        }

        function renderPendingMessage(body) {
            const time = new Date().toLocaleTimeString('vi-VN', { hour:'2-digit', minute:'2-digit' });
            const bodyHtml = body ? `<div class="bubble">${escapeHtml(body)}</div>` : '';
            list.insertAdjacentHTML('beforeend',
                `<div class="chat-msg msg-me chat-pending">${bodyHtml}<div class="meta">${time}</div></div>`);
            list.scrollTop = list.scrollHeight;
            return list.querySelector('.chat-pending:last-child');
        }

        function poll() {
            fetch(APP_BASE + 'index.php?action=chatPoll&since=' + lastId, { credentials:'same-origin' })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    let playSound = false;
                    (data.messages || []).forEach(m => {
                        renderMessage(m);
                        if (m.sender === 'admin') {
                            playSound = true;
                        }
                        if (m.id > lastId) lastId = m.id;
                    });
                    if (playSound) {
                        playNotificationSound();
                    }
                    if (box.style.display === 'flex') setBadge(0);
                    else setBadge(data.unread || 0);
                })
                .catch(()=>{});
        }

        function pollBadgeOnly() {
            fetch(APP_BASE + 'index.php?action=chatUnread', { credentials:'same-origin' })
                .then(r => r.json())
                .then(d => { 
                    if (d.success) {
                        const newUnread = d.unread || 0;
                        const badgeText = badge.textContent;
                        const isBadgeHidden = badge.classList.contains('d-none');
                        const currentUnread = isBadgeHidden ? 0 : (parseInt(badgeText) || 0);
                        if (newUnread > currentUnread) {
                            playNotificationSound();
                        }
                        setBadge(newUnread);
                    } 
                })
                .catch(()=>{});
        }

        function clearAttachment() {
            pendingFile = null;
            fileInput.value = '';
            preview.classList.add('d-none');
            preview.innerHTML = '';
        }

        function showAttachmentPreview(file) {
            pendingFile = file;
            const isImage = file.type.indexOf('image/') === 0;
            preview.classList.remove('d-none');
            if (isImage) {
                const url = URL.createObjectURL(file);
                preview.innerHTML = `<div class="d-flex align-items-center gap-2">
                    <img src="${url}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                    <div class="flex-grow-1 small text-truncate">${escapeHtml(file.name)}</div>
                    <button type="button" class="btn btn-sm text-danger p-0 border-0" id="btnRemoveAttach"><i class="fa-solid fa-xmark"></i></button>
                </div>`;
            } else {
                preview.innerHTML = `<div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file fs-4 text-muted"></i>
                    <div class="flex-grow-1 small text-truncate">${escapeHtml(file.name)} <span class="text-muted">(${formatBytes(file.size)})</span></div>
                    <button type="button" class="btn btn-sm text-danger p-0 border-0" id="btnRemoveAttach"><i class="fa-solid fa-xmark"></i></button>
                </div>`;
            }
            preview.querySelector('#btnRemoveAttach').addEventListener('click', clearAttachment);
        }

        attachBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            const f = fileInput.files[0];
            if (!f) return;
            if (f.size > 10 * 1024 * 1024) {
                AppNotify.warning('Tối đa 10MB mỗi tệp.', 'Tệp quá lớn');
                fileInput.value = '';
                return;
            }
            showAttachmentPreview(f);
        });

        // Auto-grow textarea
        function autoGrow() {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        }
        input.addEventListener('input', () => {
            autoGrow();
            const len = input.value.length;
            if (len > 0) {
                charCounter.textContent = len + '/700';
                charCounter.style.display = 'block';
                if (len >= 700) {
                    charCounter.classList.add('text-danger');
                } else {
                    charCounter.classList.remove('text-danger');
                }
            } else {
                charCounter.style.display = 'none';
            }
        });
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });

        btnOpen.addEventListener('click', () => {
            box.style.display = 'flex';
            setBadge(0);
            poll();
            if (pollTimer) clearInterval(pollTimer);
            pollTimer = setInterval(poll, 4000);
        });
        btnClose.addEventListener('click', () => {
            box.style.display = 'none';
            if (pollTimer) clearInterval(pollTimer);
            pollTimer = setInterval(pollBadgeOnly, 15000);
        });

        form.addEventListener('submit', e => {
            e.preventDefault();
            if (isSending) return;
            const body = input.value.trim();
            if (!body && !pendingFile) return;
            isSending = true;
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            const fd = new FormData();
            fd.append('body', body);
            fd.append('csrf_token', csrf);
            if (pendingFile) fd.append('file', pendingFile);
            const hadFile = !!pendingFile;
            input.value = '';
            autoGrow();
            clearAttachment();
            charCounter.style.display = 'none';
            const pendingEl = !hadFile ? renderPendingMessage(body) : null;
            fetch(APP_BASE + 'index.php?action=chatSend', {
                method: 'POST',
                headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-Token': csrf },
                body: fd, credentials:'same-origin'
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    if (pendingEl && d.id) {
                        renderedIds.add(String(d.id));
                        pendingEl.classList.remove('chat-pending');
                    }
                    if (d.id && d.id > lastId) lastId = d.id;
                    poll();
                } else {
                    if (pendingEl) pendingEl.remove();
                    AppNotify.error(d.message || 'Không thể gửi tin nhắn.', 'Lỗi gửi tin');
                    input.value = body;
                    autoGrow();
                }
            }).catch(() => {
                if (pendingEl) pendingEl.remove();
                AppNotify.error('Không thể kết nối server.', 'Lỗi gửi tin');
                input.value = body;
                autoGrow();
            }).finally(() => {
                isSending = false;
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
            });
        });

        pollBadgeOnly();
        pollTimer = setInterval(pollBadgeOnly, 15000);
    })();
    </script>
    <?php endif; ?>

    <!-- Link to separated JS - Already loaded above after Bootstrap -->
    <!-- <script src="<?php echo asset('js/main.js'); ?>"></script> -->

    <!-- Debug Log for Modals -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('[Debug] DOM loaded. Bootstrap check:', typeof bootstrap !== 'undefined' ? 'OK' : 'MISSING');
        
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-bs-target');
                console.log('[Debug] Button clicked:', this.textContent.trim(), '| Target:', target);
                const el = document.querySelector(target);
                if (!el) {
                    console.error('[Debug] Target modal element not found:', target);
                } else {
                    console.log('[Debug] Target modal element exists in DOM:', el);
                }
                if (typeof bootstrap === 'undefined') {
                    console.error('[Debug] Bootstrap JS is not loaded!');
                } else {
                    console.log('[Debug] Bootstrap JS is loaded.');
                }
            });
        });
    });
    </script>

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

    <?php if (!$currentUser): ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnOpen = document.getElementById('btnOpenChat');
        if (btnOpen) {
            btnOpen.addEventListener('click', (e) => {
                e.preventDefault();
                Swal.fire({
                    title: 'Yêu cầu đăng nhập',
                    text: 'Bạn cần đăng nhập tài khoản để chat trực tiếp với Admin.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Đăng nhập ngay',
                    cancelButtonText: 'Đóng',
                    confirmButtonColor: '#6366f1'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const appBase = document.querySelector('meta[name="app-base"]')?.getAttribute('content') || '';
                        window.location.href = appBase + 'index.php?action=login';
                    }
                });
            });
        }
    });
    </script>
    <?php endif; ?>

</body>

</html>
