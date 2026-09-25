<?php
$contactTitle = !empty($settings['contact_title']) ? $settings['contact_title'] : 'Liên hệ với chúng tôi';
$contactDesc = !empty($settings['contact_desc']) ? $settings['contact_desc'] : 'Đội ngũ hỗ trợ của AI CỦA TÔI luôn sẵn sàng đồng hành và giải đáp mọi vấn đề của bạn qua các kênh liên lạc tức thì.';

$rawZalo = trim($settings['zalo'] ?? '0772698113');
$cleanZaloPhone = preg_replace('/[^0-9]/', '', $rawZalo);
$zaloLink = (strpos($rawZalo, 'http') === 0) ? $rawZalo : ('https://zalo.me/' . ($cleanZaloPhone ?: '0772698113'));
$zaloDisplay = !empty($rawZalo) ? $rawZalo : '0772698113';

$systemEmail = trim($settings['system_email'] ?? 'tetuongmmovn@gmail.com');
if (empty($systemEmail)) {
    $systemEmail = 'tetuongmmovn@gmail.com';
}

$telegramHandle = '@specademy';
$telegramLink = 'https://t.me/specademy';

$contactMethods = json_decode($settings['contact_methods'] ?? '[]', true);
if (is_array($contactMethods)) {
    foreach ($contactMethods as $m) {
        $text = trim($m['text'] ?? '');
        $icon = trim($m['icon'] ?? '');
        if (str_contains(strtolower($text), 'telegram') || str_contains(strtolower($icon), 'telegram') || (str_starts_with($text, '@') && !str_contains($text, '.'))) {
            $handle = ltrim(trim(str_ireplace('telegram:', '', $text)), '@ ');
            if (!empty($handle)) {
                $telegramHandle = '@' . $handle;
                $telegramLink = 'https://t.me/' . $handle;
            }
        }
    }
}
?>

<div class="contact-page-wrapper pb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 fade-in-element">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="<?= url() ?>" class="text-muted text-decoration-none fw-medium">
                    <i class="fa-solid fa-house me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Liên hệ</li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <section class="contact-hero rounded-4 border shadow-sm p-4 p-md-5 mb-5 fade-in-element position-relative overflow-hidden">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-light border rounded-pill mb-3">
                    <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 0.65rem;">CSKH TRỰC TIẾP</span>
                    <span class="small fw-semibold text-secondary">Hỗ Trợ Kỹ Thuật & Bảo Hành 24/7</span>
                </div>
                <h1 class="fw-bolder mb-3 text-dark lh-sm" style="font-size: calc(1.8rem + 1vw); letter-spacing: -0.5px;">
                    <?= htmlspecialchars($contactTitle) ?>
                </h1>
                <p class="text-muted fs-6 lh-lg mb-4 pe-lg-4" style="text-align: justify; text-justify: inter-word;">
                    <?= htmlspecialchars($contactDesc) ?> Kết nối trực tiếp với đội ngũ hỗ trợ qua các kênh phản hồi tức thì dưới đây. Chúng tôi tiếp nhận và giải quyết bảo hành, đổi mới tài khoản hoặc hướng dẫn sử dụng nhanh chóng mà không cần chờ đợi.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="<?= htmlspecialchars($zaloLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-comment-dots"></i> Nhắn tin Zalo Admin
                    </a>
                    <a href="<?= htmlspecialchars($telegramLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="fa-brands fa-telegram text-info"></i> Kênh Telegram
                    </a>
                    <button type="button" class="btn btn-light border rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2" onclick="document.getElementById('chat-bubble-toggle')?.click();">
                        <i class="fa-solid fa-headset text-primary"></i> Chat Trên Website
                    </button>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-light rounded-4 border p-4 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-signal text-success"></i> Trạng thái tiếp nhận
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1.5 small d-inline-flex align-items-center gap-1.5">
                            <span class="spinner-grow spinner-grow-sm text-success" style="width: 7px; height: 7px;"></span> Đang trực tuyến
                        </span>
                    </div>
                    <div class="d-flex flex-column gap-3 small">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fa-regular fa-clock text-warning mt-1 fs-6"></i>
                            <div>
                                <div class="fw-bold text-dark">Khung giờ hỗ trợ</div>
                                <div class="text-muted"><strong>08:00 – 23:30</strong> hàng ngày (kể cả Thứ 7, CN và ngày Lễ Tết).</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fa-solid fa-bolt text-primary mt-1 fs-6"></i>
                            <div>
                                <div class="fw-bold text-dark">Tốc độ phản hồi</div>
                                <div class="text-muted">Phản hồi trung bình trong <strong>3 – 5 phút</strong> qua kênh Zalo / Telegram.</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="fa-solid fa-shield-halved text-success mt-1 fs-6"></i>
                            <div>
                                <div class="fw-bold text-dark">Chính sách bảo hành</div>
                                <div class="text-muted">Cam kết 1 đổi 1 hoặc hỗ trợ khắc phục trọn vòng đời gói dịch vụ.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Direct Contact Cards Grid -->
    <section class="mb-5 fade-in-element">
        <div class="d-flex flex-column flex-md-row md-align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Kênh Liên Hệ Trực Tiếp</h2>
                <p class="text-muted mb-0">Lựa chọn phương thức thuận tiện nhất để kết nối ngay với chuyên viên hỗ trợ</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Channel 1: Zalo -->
            <div class="col-md-6 col-xl-3">
                <div class="contact-channel-card p-4 h-100 d-flex flex-column shadow-sm" style="--channel-accent: linear-gradient(135deg, #0068ff, #0091ff);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="channel-icon-wrap channel-icon-zalo">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small px-2.5 py-1">
                            Phản hồi ~3 phút
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Zalo Admin</h5>
                    <p class="text-muted small mb-3">Kênh hỗ trợ nhanh nhất để giải quyết bảo hành, đổi mật khẩu và cấp tài khoản.</p>
                    <div class="bg-light p-2.5 rounded-3 mb-4 border d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark font-monospace" id="zaloVal"><?= htmlspecialchars($zaloDisplay) ?></span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" onclick="copyText('zaloVal')" title="Sao chép số">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <div class="mt-auto d-flex flex-column gap-2">
                        <a href="<?= htmlspecialchars($zaloLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold shadow-sm">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Mở Chat Zalo
                        </a>
                        <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3 small fw-medium" onclick="copyText('zaloVal')">
                            <i class="fa-regular fa-copy me-1"></i> Sao chép SĐT
                        </button>
                    </div>
                </div>
            </div>

            <!-- Channel 2: Telegram -->
            <div class="col-md-6 col-xl-3">
                <div class="contact-channel-card p-4 h-100 d-flex flex-column shadow-sm" style="--channel-accent: linear-gradient(135deg, #229ED9, #0088cc);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="channel-icon-wrap channel-icon-telegram">
                            <i class="fa-brands fa-telegram"></i>
                        </div>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill small px-2.5 py-1">
                            Trực tuyến 24/7
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Telegram CSKH</h5>
                    <p class="text-muted small mb-3">Hỗ trợ kỹ thuật chuyên sâu, hỗ trợ API, thông báo hệ thống và hợp tác đại lý.</p>
                    <div class="bg-light p-2.5 rounded-3 mb-4 border d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark font-monospace" id="teleVal"><?= htmlspecialchars($telegramHandle) ?></span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" onclick="copyText('teleVal')" title="Sao chép ID">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <div class="mt-auto d-flex flex-column gap-2">
                        <a href="<?= htmlspecialchars($telegramLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-info text-white w-100 py-2 rounded-3 fw-semibold shadow-sm">
                            <i class="fa-brands fa-telegram me-1"></i> Nhắn Telegram
                        </a>
                        <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3 small fw-medium" onclick="copyText('teleVal')">
                            <i class="fa-regular fa-copy me-1"></i> Sao chép ID
                        </button>
                    </div>
                </div>
            </div>

            <!-- Channel 3: Hotline -->
            <div class="col-md-6 col-xl-3">
                <div class="contact-channel-card p-4 h-100 d-flex flex-column shadow-sm" style="--channel-accent: linear-gradient(135deg, #10b981, #059669);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="channel-icon-wrap channel-icon-hotline">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small px-2.5 py-1">
                            08:00 – 23:30
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Hotline Trực Tiếp</h5>
                    <p class="text-muted small mb-3">Tiếp nhận cuộc gọi khẩn cấp về thanh toán, xác nhận đơn hàng hoặc xử lý lỗi gấp.</p>
                    <div class="bg-light p-2.5 rounded-3 mb-4 border d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark font-monospace" id="phoneVal"><?= htmlspecialchars($zaloDisplay) ?></span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" onclick="copyText('phoneVal')" title="Sao chép SĐT">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <div class="mt-auto d-flex flex-column gap-2">
                        <a href="tel:<?= htmlspecialchars($cleanZaloPhone ?: '0772698113') ?>" class="btn btn-success text-white w-100 py-2 rounded-3 fw-semibold shadow-sm">
                            <i class="fa-solid fa-phone me-1"></i> Gọi Điện Ngay
                        </a>
                        <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3 small fw-medium" onclick="copyText('phoneVal')">
                            <i class="fa-regular fa-copy me-1"></i> Sao chép Hotline
                        </button>
                    </div>
                </div>
            </div>

            <!-- Channel 4: Email -->
            <div class="col-md-6 col-xl-3">
                <div class="contact-channel-card p-4 h-100 d-flex flex-column shadow-sm" style="--channel-accent: linear-gradient(135deg, #6366f1, #8b5cf6);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="channel-icon-wrap channel-icon-email">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill small px-2.5 py-1">
                            Hóa đơn & Doanh nghiệp
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Hòm Thư Điện Tử</h5>
                    <p class="text-muted small mb-3">Gửi yêu cầu báo giá doanh nghiệp, hợp đồng dịch vụ, bảo mật hoặc đóng góp ý kiến.</p>
                    <div class="bg-light p-2.5 rounded-3 mb-4 border d-flex align-items-center justify-content-between overflow-hidden">
                        <span class="fw-bold text-dark font-monospace text-truncate me-2 small" id="mailVal" title="<?= htmlspecialchars($systemEmail) ?>"><?= htmlspecialchars($systemEmail) ?></span>
                        <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none flex-shrink-0" onclick="copyText('mailVal')" title="Sao chép Email">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                    <div class="mt-auto d-flex flex-column gap-2">
                        <a href="mailto:<?= htmlspecialchars($systemEmail) ?>" class="btn btn-outline-primary w-100 py-2 rounded-3 fw-semibold">
                            <i class="fa-regular fa-envelope me-1"></i> Gửi Thư Điện Tử
                        </a>
                        <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3 small fw-medium" onclick="copyText('mailVal')">
                            <i class="fa-regular fa-copy me-1"></i> Sao chép Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fast-Track Support Process -->
    <section class="mb-5 fade-in-element">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Quy Trình Nhận Hỗ Trợ Siêu Tốc</h2>
            <p class="text-muted">Chỉ với 3 bước đơn giản để được kỹ thuật viên xử lý và bảo hành ngay lập tức</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="contact-step-card text-center text-md-start">
                    <div class="contact-step-badge">
                        01
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Chuẩn bị thông tin</h5>
                    <p class="text-muted small mb-0 lh-base">
                        Tìm <strong>Mã đơn hàng</strong> (dạng <code>ORD...</code>) hoặc địa chỉ <strong>Email</strong> bạn đã sử dụng khi mua dịch vụ trên website.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-step-card text-center text-md-start">
                    <div class="contact-step-badge">
                        02
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Gửi yêu cầu & ảnh lỗi</h5>
                    <p class="text-muted small mb-0 lh-base">
                        Chụp ảnh màn hình thông báo lỗi (nếu có) trên thiết bị và nhắn tin cho Admin qua Zalo hoặc Telegram để được nhận diện lỗi tức thì.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-step-card text-center text-md-start">
                    <div class="contact-step-badge">
                        03
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Xử lý trong 5 – 15 phút</h5>
                    <p class="text-muted small mb-0 lh-base">
                        Kỹ thuật viên kiểm tra dữ liệu đối soát và thực hiện đổi mới tài khoản 1:1 hoặc hướng dẫn giải quyết sự cố ngay tại chỗ.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Commitments (3 Pillars) -->
    <section class="mb-5 fade-in-element">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border shadow-sm h-100 d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-rotate-left fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Bảo Hành 1 Đổi 1</h6>
                        <p class="text-muted small mb-0">Cam kết đổi tài khoản mới hoặc gia hạn lại toàn bộ chu kỳ nếu tài khoản phát sinh lỗi nhà cung cấp.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border shadow-sm h-100 d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-desktop fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Hỗ Trợ Từ Xa Tận Tình</h6>
                        <p class="text-muted small mb-0">Sẵn sàng kết nối qua UltraViewer hoặc AnyDesk để cài đặt, đăng nhập và giải đáp thắc mắc cho người mới.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-white p-4 rounded-4 border shadow-sm h-100 d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-user-shield fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Bảo Mật Tuyệt Đối</h6>
                        <p class="text-muted small mb-0">Mọi thông tin tài khoản, đơn hàng và trao đổi hỗ trợ đều được bảo mật nghiêm ngặt và riêng tư.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion -->
    <section class="mb-5 fade-in-element">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Câu Hỏi Thường Gặp Khi Cần Hỗ Trợ</h2>
            <p class="text-muted">Một số giải đáp nhanh giúp bạn tiết kiệm thời gian trước khi liên hệ</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion accordion-flush bg-white rounded-4 border p-2 shadow-sm" id="contactFaqAccordion">
                    <!-- FAQ 1 -->
                    <div class="accordion-item contact-faq-item border-0 border-bottom">
                        <h2 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                <i class="fa-solid fa-circle-question text-primary me-2"></i> Kênh nào phản hồi nhanh nhất khi tôi cần bảo hành tài khoản?
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body text-muted small pt-0 pb-3 lh-lg">
                                Kênh <strong>Zalo (<?= htmlspecialchars($zaloDisplay) ?>)</strong> và <strong>Telegram (<?= htmlspecialchars($telegramHandle) ?>)</strong> là hai kênh có kỹ thuật viên trực tuyến phản hồi nhanh nhất (thường dưới 3 - 5 phút). Khi nhắn tin, bạn hãy gửi kèm <strong>Mã đơn hàng</strong> và <strong>ảnh chụp lỗi</strong> để được đổi mới ngay lập tức.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="accordion-item contact-faq-item border-0 border-bottom">
                        <h2 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                <i class="fa-solid fa-circle-question text-primary me-2"></i> Tôi mua hàng vào đêm khuya (sau 23:30) thì đơn hàng có được xử lý không?
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body text-muted small pt-0 pb-3 lh-lg">
                                Hệ thống thanh toán VietQR tự động và bàn giao tài khoản có sẵn trong kho hoạt động <strong>24/7/365</strong>. Quý khách vẫn sẽ nhận được tài khoản ngay sau khi thanh toán thành công. Đối với các yêu cầu cần admin thao tác thủ công gửi sau 23:30, admin sẽ ưu tiên xử lý sớm nhất bắt đầu từ 08:00 sáng hôm sau.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="accordion-item contact-faq-item border-0 border-bottom">
                        <h2 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                <i class="fa-solid fa-circle-question text-primary me-2"></i> Tôi là người mới chưa biết sử dụng công cụ AI thì có được chỉ dẫn không?
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body text-muted small pt-0 pb-3 lh-lg">
                                Hoàn toàn có! Chúng tôi cung cấp video, hình ảnh và hướng dẫn chi tiết từng bước cho từng loại tài khoản (ChatGPT, Midjourney, Canva, GitHub Copilot...). Nếu bạn vẫn gặp khó khăn, chuyên viên có thể hỗ trợ điều khiển máy tính qua UltraViewer hoặc AnyDesk hoàn toàn miễn phí.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="accordion-item contact-faq-item border-0">
                        <h2 class="accordion-header" id="faqHeading4">
                            <button class="accordion-button collapsed fw-bold text-dark py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                <i class="fa-solid fa-circle-question text-primary me-2"></i> Tôi có thể tra cứu tình trạng đơn hàng ở đâu?
                            </button>
                        </h2>
                        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#contactFaqAccordion">
                            <div class="accordion-body text-muted small pt-0 pb-3 lh-lg">
                                Quý khách có thể vào mục <strong>Lịch sử đơn hàng</strong> trong menu tài khoản hoặc bấm vào biểu tượng <strong>Chat trực tuyến</strong> ở góc dưới bên phải màn hình rồi chọn nút <em>"Gửi mã đơn hàng"</em> để tra cứu nhanh thông tin.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Card -->
    <section class="fade-in-element">
        <div class="contact-cta-banner p-4 p-md-5 rounded-4 shadow-lg text-center text-md-start position-relative overflow-hidden"
             style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #312e81 100%) !important; color: #ffffff !important; border: 1px solid rgba(99, 102, 241, 0.3) !important;">
            <div class="row align-items-center gy-4 position-relative" style="z-index: 2;">
                <div class="col-lg-8">
                    <span class="badge rounded-pill px-3 py-1.5 mb-3 d-inline-flex align-items-center gap-1.5"
                          style="background: rgba(99, 102, 241, 0.3) !important; color: #e0e7ff !important; border: 1px solid rgba(165, 180, 252, 0.4) !important; font-size: 0.78rem; font-weight: 600;">
                        <i class="fa-solid fa-headset text-warning"></i> KẾT NỐI TRỰC TIẾP 24/7
                    </span>
                    <h3 class="fw-bold mb-2" style="color: #ffffff !important; font-size: calc(1.35rem + 0.6vw); letter-spacing: -0.3px;">
                        Bạn cần hỗ trợ ngay bây giờ?
                    </h3>
                    <p class="mb-0 fs-6" style="color: #cbd5e1 !important; line-height: 1.6; max-width: 620px;">
                        Nhắn tin trực tiếp qua <strong>Zalo</strong> hoặc nhấp vào biểu tượng <strong>Chat Trực Tuyến</strong> ở góc phải màn hình để được nhân viên tiếp nhận và xử lý yêu cầu tức thì.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-inline-flex flex-column flex-sm-row flex-lg-column gap-2.5 w-100 w-lg-auto justify-content-center">
                        <a href="<?= htmlspecialchars($zaloLink) ?>" target="_blank" rel="noopener noreferrer" 
                           class="btn fw-bold rounded-pill px-4 py-2.5 shadow-sm d-inline-flex align-items-center justify-content-center gap-2"
                           style="background: #ffffff !important; color: #0068ff !important; border: none !important; font-size: 0.95rem;">
                            <i class="fa-solid fa-comment-dots fs-5"></i> Chat Zalo Admin
                        </a>
                        <button type="button" 
                                class="btn fw-semibold rounded-pill px-4 py-2.5 d-inline-flex align-items-center justify-content-center gap-2"
                                style="background: rgba(255, 255, 255, 0.12) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important; font-size: 0.95rem; backdrop-filter: blur(4px);"
                                onclick="document.getElementById('chat-bubble-toggle')?.click();">
                            <i class="fa-solid fa-comments text-warning fs-5"></i> Mở Chat Trực Tuyến
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Scoped styles for Contact Page */
.contact-page-wrapper {
    width: 100%;
}
.contact-hero {
    background: #ffffff !important;
    border: 1px solid var(--border-color, #e2e8f0) !important;
}
.contact-channel-card {
    background: #ffffff !important;
    border: 1px solid var(--border-color, #e2e8f0) !important;
    border-radius: 1.25rem !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative;
    overflow: hidden;
}
.contact-channel-card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08) !important;
    border-color: rgba(99, 102, 241, 0.35) !important;
}
.contact-channel-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--channel-accent, linear-gradient(135deg, #6366f1, #a855f7));
    opacity: 0;
    transition: opacity 0.3s ease;
}
.contact-channel-card:hover::before {
    opacity: 1;
}
.channel-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
}
.channel-icon-zalo {
    background: linear-gradient(135deg, #0068ff 0%, #0091ff 100%) !important;
}
.channel-icon-telegram {
    background: linear-gradient(135deg, #229ED9 0%, #0088cc 100%) !important;
}
.channel-icon-hotline {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
}
.channel-icon-email {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
}
.contact-step-card {
    background: #ffffff !important;
    border: 1px solid var(--border-color, #e2e8f0) !important;
    border-radius: 1.25rem !important;
    padding: 1.75rem 1.5rem !important;
    position: relative;
    height: 100%;
    transition: all 0.3s ease !important;
}
.contact-step-card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06) !important;
    border-color: rgba(99, 102, 241, 0.3) !important;
}
.contact-step-badge {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.1) !important;
    color: #6366f1 !important;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
}
.contact-cta-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 450px;
    height: 450px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, transparent 70%);
    pointer-events: none;
}
.contact-faq-item .accordion-button:not(.collapsed) {
    background-color: rgba(99, 102, 241, 0.06) !important;
    color: #4f46e5 !important;
    box-shadow: none !important;
}
.contact-faq-item .accordion-button:focus {
    box-shadow: none !important;
    border-color: rgba(99, 102, 241, 0.2) !important;
}
</style>
