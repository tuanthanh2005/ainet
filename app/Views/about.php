<?php
$zaloNumber = !empty($settings['zalo']) ? $settings['zalo'] : '0772698113';
$systemEmail = !empty($settings['system_email']) ? $settings['system_email'] : 'tetuongmmovn@gmail.com';
?>
<div class="about-page-wrapper pb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 fade-in-element">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="<?= url() ?>" class="text-muted text-decoration-none fw-medium">
                    <i class="fa-solid fa-house me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Giới thiệu</li>
        </ol>
    </nav>

    <!-- Hero Section -->
    <section class="about-hero bg-white rounded-4 border shadow-sm p-4 p-md-5 mb-5 fade-in-element">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-light border rounded-pill mb-3">
                    <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 0.65rem;">OFFICIAL</span>
                    <span class="small fw-semibold text-secondary">Nền Tảng Dịch Vụ Số & Tài Khoản AI</span>
                </div>
                <h1 class="fw-bolder mb-3 text-dark lh-sm" style="font-size: calc(1.8rem + 1vw); letter-spacing: -0.5px;">
                    Giới thiệu hệ thống <span class="text-primary">AI CỦA TÔI</span>
                </h1>
                <p class="text-muted fs-6 lh-lg mb-4 pe-lg-3" style="text-align: justify; text-justify: inter-word;">
                    <strong>AI CỦA TÔI</strong> là website chuyên phân phối, hỗ trợ nâng cấp và bảo hành các giải pháp tài khoản bản quyền thuộc nhóm <strong>Trí tuệ nhân tạo (ChatGPT Plus, GitHub Copilot, Canva Pro)</strong> và <strong>Dịch vụ số cao cấp (YouTube Premium, Netflix 4K)</strong>. Chúng tôi xây dựng quy trình thanh toán tự động qua mã VietQR, bàn giao tài khoản nhanh chóng và duy trì hỗ trợ kỹ thuật trực tiếp, minh bạch cho khách hàng trong suốt thời gian sử dụng.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="<?= Url::products() ?>" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
                        <i class="fa-solid fa-layer-group me-2"></i>Xem danh sách dịch vụ
                    </a>
                    <a href="https://zalo.me/<?= htmlspecialchars($zaloNumber) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="fa-solid fa-comment-dots me-2"></i>Zalo Admin: <?= htmlspecialchars($zaloNumber) ?>
                    </a>
                </div>
            </div>
            <div class="col-lg-5">
                <!-- Real Service Summary Card -->
                <div class="bg-light rounded-4 border p-4 shadow-sm">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-shield-halved text-success me-2"></i>Thông tin vận hành</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small px-3">Đang trực tuyến</span>
                    </h5>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-white border p-2 text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Thanh toán tự động 24/7</div>
                                <div class="text-muted">Tích hợp SePay / VietQR khớp lệnh ngân hàng tức thì.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-white border p-2 text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-solid fa-rotate"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Bảo hành 1 đổi 1 trọn gói</div>
                                <div class="text-muted">Đồng hành cam kết xử lý lỗi trong toàn bộ thời hạn gói.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-white border p-2 text-info d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Hỗ trợ email chính chủ</div>
                                <div class="text-muted">Ưu tiên nâng cấp trên tài khoản cá nhân của quý khách.</div>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-white border p-2 text-warning d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Hỗ trợ: 08:00 – 23:30</div>
                                <div class="text-muted">Hoạt động tất cả các ngày trong tuần (kể cả T7 & CN).</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Real Pillars -->
    <section class="mb-5 fade-in-element">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Cam kết thực tế & Minh bạch</h2>
            <p class="text-muted">Chúng tôi chú trọng vào trải nghiệm sử dụng ổn định và quyền lợi thực tế của khách hàng</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border rounded-4 p-4 shadow-sm bg-white hover-lift">
                    <div class="feature-icon mb-3 bg-primary-subtle text-primary rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-qrcode fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Thanh toán minh bạch</h5>
                    <p class="text-muted small mb-0 lh-base" style="text-align: justify; text-justify: inter-word;">
                        Thanh toán trực tiếp qua chuyển khoản ngân hàng với mã QR tạo tự động. Mỗi giao dịch đều có mã đối soát đơn hàng rõ ràng, an toàn tuyệt đối.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border rounded-4 p-4 shadow-sm bg-white hover-lift">
                    <div class="feature-icon mb-3 bg-success-subtle text-success rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-shield-halved fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Bảo hành trách nhiệm</h5>
                    <p class="text-muted small mb-0 lh-base" style="text-align: justify; text-justify: inter-word;">
                        Chính sách 1 đổi 1 áp dụng suốt thời hạn gói. Khi phát sinh lỗi ngoài ý muốn, đội ngũ sẽ kiểm tra, đổi mới hoặc hoàn tiền theo tỷ lệ thời gian còn lại.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border rounded-4 p-4 shadow-sm bg-white hover-lift">
                    <div class="feature-icon mb-3 bg-warning-subtle text-warning rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-user-shield fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Bảo mật dữ liệu</h5>
                    <p class="text-muted small mb-0 lh-base" style="text-align: justify; text-justify: inter-word;">
                        Với các gói nâng cấp mail chính chủ (như YouTube Family, Canva...), dữ liệu, playlist và lịch sử sử dụng hoàn toàn thuộc quyền sở hữu riêng tư của bạn.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border rounded-4 p-4 shadow-sm bg-white hover-lift">
                    <div class="feature-icon mb-3 bg-info-subtle text-info rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-headset fs-5"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Hỗ trợ người thật</h5>
                    <p class="text-muted small mb-0 lh-base" style="text-align: justify; text-justify: inter-word;">
                        Không phản hồi qua bot tự động rập khuôn. Quý khách trao đổi trực tiếp với Admin phụ trách kỹ thuật qua Zalo để được giải quyết nhanh nhất.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4-Step Order Process -->
    <section class="bg-white rounded-4 border shadow-sm p-4 p-md-5 mb-5 fade-in-element">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Quy trình mua & kích hoạt đơn hàng</h2>
            <p class="text-muted">Trải nghiệm mua sắm tự động, nhanh chóng và dễ dàng chỉ với 4 bước</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle position-relative">
                    <div class="badge bg-dark rounded-pill mb-3 px-3 py-1">Bước 1</div>
                    <h6 class="fw-bold text-dark mb-2">Chọn gói dịch vụ</h6>
                    <p class="small text-muted mb-0">Tìm kiếm sản phẩm AI hoặc giải trí cần dùng (chọn gói thời hạn 1 tháng, 3 tháng hoặc 1 năm).</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle position-relative">
                    <div class="badge bg-dark rounded-pill mb-3 px-3 py-1">Bước 2</div>
                    <h6 class="fw-bold text-dark mb-2">Quét mã VietQR</h6>
                    <p class="small text-muted mb-0">Mở ứng dụng ngân hàng quét mã QR. Hệ thống tự động điền số tiền và nội dung chuyển khoản chính xác.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle position-relative">
                    <div class="badge bg-dark rounded-pill mb-3 px-3 py-1">Bước 3</div>
                    <h6 class="fw-bold text-dark mb-2">Nhận thông tin tài khoản</h6>
                    <p class="small text-muted mb-0">Sau khi khớp lệnh ngân hàng, thông tin tài khoản hoặc link kích hoạt hiển thị ngay và lưu trong Lịch sử đơn hàng.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-3 border rounded-3 h-100 bg-light-subtle position-relative">
                    <div class="badge bg-dark rounded-pill mb-3 px-3 py-1">Bước 4</div>
                    <h6 class="fw-bold text-dark mb-2">Sử dụng & Bảo hành</h6>
                    <p class="small text-muted mb-0">Đăng nhập sử dụng theo hướng dẫn đi kèm. Cần hỗ trợ bất kỳ lúc nào, vui lòng nhắn tin trực tiếp qua Zalo Admin.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Official Contacts & Anti-Scam Notice -->
    <section class="fade-in-element">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7">
                <div class="bg-white rounded-4 border shadow-sm p-4 p-md-5 h-100">
                    <h3 class="fw-bold text-dark mb-3" style="letter-spacing: -0.5px;">Kênh liên hệ & Hỗ trợ chính thức</h3>
                    <p class="text-muted small mb-4">Để đảm bảo quyền lợi và tránh đối tượng giả mạo, quý khách vui lòng chỉ liên hệ qua các kênh đã được xác thực:</p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="small text-muted mb-1"><i class="fa-solid fa-phone text-primary me-2"></i>Hotline / Zalo Admin:</div>
                                <div class="fw-bold fs-6 text-dark"><?= htmlspecialchars($zaloNumber) ?></div>
                                <a href="https://zalo.me/<?= htmlspecialchars($zaloNumber) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary mt-2 rounded-pill px-3">
                                    Mở chat Zalo
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <div class="small text-muted mb-1"><i class="fa-regular fa-envelope text-danger me-2"></i>Email hệ thống:</div>
                                <div class="fw-bold fs-6 text-dark text-truncate" title="<?= htmlspecialchars($systemEmail) ?>"><?= htmlspecialchars($systemEmail) ?></div>
                                <a href="mailto:<?= htmlspecialchars($systemEmail) ?>" class="btn btn-sm btn-outline-danger mt-2 rounded-pill px-3">
                                    Gửi email
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="small text-muted mb-1"><i class="fa-regular fa-clock text-success me-2"></i>Thời gian trực tuyến:</div>
                                    <div class="fw-bold text-dark">08:00 – 23:30 (Thứ Hai – Chủ Nhật)</div>
                                </div>
                                <span class="badge bg-success text-white rounded-pill px-3 py-2 small">Hoạt động</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-dark text-white rounded-4 p-4 p-md-5 h-100 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white bg-opacity-10 rounded-pill mb-3 text-warning small">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span class="fw-semibold">CẢNH BÁO BẢO MẬT</span>
                        </div>
                        <h4 class="fw-bold mb-3 text-white">Lưu ý phòng tránh lừa đảo</h4>
                        <p class="text-white-50 small lh-lg mb-4" style="text-align: justify; text-justify: inter-word;">
                            Hiện nay có nhiều cá nhân mạo danh Fanpage và Admin <strong>AI CỦA TÔI</strong> để nhắn tin riêng yêu cầu chuyển khoản đến tài khoản cá nhân lạ. Quý khách lưu ý <strong>chỉ thực hiện đặt hàng và thanh toán trực tiếp qua website chính thức</strong> thông qua mã QR SePay tự động có mã đơn rõ ràng.
                        </p>
                    </div>
                    <div>
                        <a href="<?= Url::products() ?>" class="btn btn-light text-dark fw-bold rounded-pill w-100 py-3">
                            <i class="fa-solid fa-cart-shopping me-2"></i>Xem sản phẩm ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.about-page-wrapper {
    width: 100%;
}
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07) !important;
}
</style>
