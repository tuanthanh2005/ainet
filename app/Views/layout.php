<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI CỦA TÔI - Cửa Hàng Dịch Vụ Cao Cấp</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Link to separated CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="mini-banner text-center">
        <i class="fa-solid fa-gem me-2"></i> <?php echo htmlspecialchars($settings['bannerText']); ?> - ZALO: <?php echo htmlspecialchars($settings['zalo']); ?> <i class="fa-solid fa-gem ms-2"></i>
    </div>

    <header class="vibrant-header sticky-top py-3 shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-lg-3">
                    <a href="index.php" class="text-decoration-none text-dark fs-3 fw-bold" style="letter-spacing: -1px;">
                        <i class="fa-solid fa-circle-nodes me-1"></i>AI<span class="text-muted fw-light">CỦA TÔI</span>
                    </a>
                </div>

                <!-- Thanh tìm kiếm (Ẩn trên Mobile, chỉ hiện Desktop) -->
                <div class="col-12 col-lg-5 order-3 order-lg-2 d-none d-lg-block">
                    <form class="d-flex search-form">
                        <input class="form-control" type="search" placeholder="Tìm kiếm dịch vụ (vd: ChatGPT, Netflix)..." aria-label="Search">
                        <button class="btn px-4" type="button"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <!-- Cụm nút bấm phải -->
                <div class="col-6 col-lg-4 order-2 order-lg-3 d-flex justify-content-end align-items-center gap-2">
                    <div class="d-none d-md-flex me-3">
                        <a href="#" class="header-link me-3" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</a>
                        <a href="#" class="header-link fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</a>
                    </div>
                    
                    <!-- Nút Search (Chỉ Mobile) -->
                    <button class="header-icon-btn d-lg-none" type="button">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    
                    <!-- Nút Giỏ hàng (Tròn) -->
                    <button class="header-icon-btn position-relative" type="button">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cart-count" class="position-absolute badge rounded-pill bg-dark border border-light" style="top: 0px; right: -2px; font-size: 0.65rem; padding: 0.25em 0.4em;">0</span>
                    </button>

                    <!-- Nút Avatar (Chỉ Mobile) -->
                    <button class="header-icon-btn bg-dark text-white fw-bold d-md-none" type="button" data-bs-toggle="modal" data-bs-target="#loginModal" style="border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        S
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-3 flex-grow-1">
        <?php 
            // Nội dung thay đổi sẽ được nhúng ở đây
            if (isset($view)) {
                require_once $view; 
            } else {
                require_once 'views/home.php';
            }
        ?>
    </main>

    <footer class="vibrant-footer py-5 mt-5">
        <div class="container">
            <div class="row gy-5">
                <div class="col-12 col-md-4">
                    <h5 class="text-white fw-bold mb-4" style="letter-spacing: -0.5px;">AI CỦA TÔI.</h5>
                    <p class="small text-light lh-lg pe-lg-4"><?php echo htmlspecialchars($settings['footerDesc']); ?></p>
                    <div class="d-flex gap-4 mt-4">
                        <a href="<?php echo htmlspecialchars($settings['socialLink']); ?>" class="footer-link fs-5" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="footer-link fs-5"><i class="fa-brands fa-github"></i></a>
                        <a href="#" class="footer-link fs-5"><i class="fa-brands fa-figma"></i></a>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <h6 class="text-white fw-bold mb-4 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">Thông tin</h6>
                    <ul class="list-unstyled small lh-lg">
                        <li class="mb-2"><a href="#" class="footer-link">Giới thiệu</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Điều khoản dịch vụ</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <h6 class="text-white fw-bold mb-4 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">Thanh toán</h6>
                    <p class="small text-light mb-3">Hỗ trợ giao dịch bảo mật 24/7</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge border border-secondary text-light py-2 px-3 fw-normal rounded-pill">Bank Transfer</span>
                        <span class="badge border border-secondary text-light py-2 px-3 fw-normal rounded-pill">Ví Điện Tử</span>
                    </div>
                </div>
            </div>
            <hr class="border-light mt-5 mb-4" style="opacity: 0.2;">
            <div class="d-flex justify-content-between align-items-center small text-light">
                <span>&copy; <?php echo htmlspecialchars($settings['copyright']); ?>. Bản quyền được bảo hộ.</span>
                <span>Thiết kế bởi Tuan Tran</span>
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
                    <form>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control bg-light border-0" placeholder="hello@example.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Mật khẩu</label>
                            <input type="password" class="form-control bg-light border-0" placeholder="••••••••">
                        </div>
                        <div class="d-flex justify-content-between mb-4 small fw-medium">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label text-muted" for="rememberMe">Ghi nhớ</label>
                            </div>
                            <a href="#" class="text-dark text-decoration-none border-bottom border-dark">Quên mật khẩu?</a>
                        </div>
                        <button type="button" class="btn btn-buy w-100 py-3 rounded-3">Đăng Nhập</button>
                    </form>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <span class="small text-muted">Chưa có tài khoản? <a href="#" class="text-dark fw-bold text-decoration-none ms-1" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký ngay</a></span>
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
                    <form>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Họ tên</label>
                            <input type="text" class="form-control bg-light border-0" placeholder="Tên của bạn">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control bg-light border-0" placeholder="hello@example.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Mật khẩu</label>
                            <input type="password" class="form-control bg-light border-0" placeholder="••••••••">
                        </div>
                        <button type="button" class="btn btn-buy w-100 py-3 rounded-3">Tạo Tài Khoản</button>
                    </form>
                </div>
                <div class="modal-footer border-0 justify-content-center pt-0 pb-4">
                    <span class="small text-muted">Đã có tài khoản? <a href="#" class="text-dark fw-bold text-decoration-none ms-1" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</a></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Buttons -->
    <div class="fab-wrapper">
        <button class="fab-btn fab-totop" id="btnScrollToTop" title="Lên đầu trang">
            <i class="fa-solid fa-arrow-up"></i>
        </button>
        <a href="#" target="_blank" class="fab-btn fab-telegram" title="Hỗ trợ Telegram">
            <i class="fa-brands fa-telegram"></i>
        </a>
        <a href="#" target="_blank" class="fab-btn fab-messenger" title="Hỗ trợ Messenger">
            <i class="fa-brands fa-facebook-messenger"></i>
        </a>
        <a href="#" target="_blank" class="fab-btn fab-chat" title="Hỗ trợ Trực tuyến">
            <i class="fa-regular fa-comment"></i>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Link to separated JS -->
    <script src="assets/js/main.js"></script>

</body>
</html>
