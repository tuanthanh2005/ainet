<?php
/**
 * Render the blog body. The admin editor saves sanitized HTML
 * (Upload::sanitizeHtml whitelists tags + strips dangerous attrs),
 * so we can output it directly. For older plaintext rows, we fall back
 * to a lightweight markdown-ish formatter.
 */
function render_blog_body(string $raw): string {
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }

    if (preg_match('/&lt;\s*(p|h[1-6]|ul|ol|li|blockquote|strong|em|br|a|img|span|b|i|u|table|thead|tbody|tr|th|td)\b/i', $raw)) {
        $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // Heuristic: if the content already contains any HTML tag it was saved by
    // the rich editor — render as-is.
    if (preg_match('/<\s*(p|h[1-6]|ul|ol|li|blockquote|strong|em|br|a|img|span|b|i|u|table|thead|tbody|tr|th|td)\b/i', $raw)) {
        $allowed = '<p><br><strong><b><em><i><u><s><h1><h2><h3><h4><ul><ol><li><blockquote><a><img><span><table><thead><tbody><tr><th><td>';
        return strip_tags($raw, $allowed);
    }

    // Plaintext fallback (legacy rows): minimal markdown style
    $raw    = str_replace(["\r\n", "\r"], "\n", $raw);
    $blocks = preg_split("/\n{2,}/", $raw);
    $html   = '';

    foreach ($blocks as $block) {
        $lines = array_map('trim', explode("\n", trim($block)));
        if (count($lines) === 0 || $lines[0] === '') {
            continue;
        }

        if (preg_match('/^##\s+(.+)$/u', $lines[0], $m) && count($lines) === 1) {
            $html .= '<h3 class="text-dark fw-bold mt-5 mb-3" style="letter-spacing: -0.5px;">'
                  .  htmlspecialchars($m[1])
                  .  '</h3>';
            continue;
        }

        $isQuote = true;
        foreach ($lines as $l) {
            if (!preg_match('/^>\s?/', $l)) { $isQuote = false; break; }
        }
        if ($isQuote) {
            $quote = implode(' ', array_map(function ($l) {
                return preg_replace('/^>\s?/', '', $l);
            }, $lines));
            $html .= '<blockquote class="blockquote bg-light border-start border-4 border-dark p-4 my-4 rounded-end fst-italic shadow-sm">'
                  .  htmlspecialchars($quote)
                  .  '</blockquote>';
            continue;
        }

        $isList = true;
        foreach ($lines as $l) {
            if (!preg_match('/^[-*]\s+/', $l)) { $isList = false; break; }
        }
        if ($isList) {
            $html .= '<ul class="mb-4">';
            foreach ($lines as $l) {
                $item = preg_replace('/^[-*]\s+/', '', $l);
                $html .= '<li class="mb-2">' . htmlspecialchars($item) . '</li>';
            }
            $html .= '</ul>';
            continue;
        }

        $html .= '<p>' . nl2br(htmlspecialchars(implode("\n", $lines))) . '</p>';
    }

    return $html;
}

$hasBlog = !empty($blog);
$blogTitle = $hasBlog ? ($blog['title'] ?? '') : 'Bài viết không tồn tại';
$blogImage = $hasBlog ? ($blog['image'] ?? '') : '';
$blogDate  = ($hasBlog && !empty($blog['created_at'])) ? date('d/m/Y', strtotime($blog['created_at'])) : '';
$blogDesc  = $hasBlog ? (($blog['content'] ?? '') ?: ($blog['description'] ?? '')) : '';
?>
<div class="blog-detail-container pb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4 fade-in-element">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="<?= url() ?>" class="text-muted text-decoration-none fw-medium">
                    <i class="fa-solid fa-house me-1"></i>Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= Url::blogs() ?>" class="text-muted text-decoration-none fw-medium">Tạp Chí</a>
            </li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                <?= htmlspecialchars(mb_strimwidth($blogTitle, 0, 70, '…')) ?>
            </li>
        </ol>
    </nav>

    <?php if (!$hasBlog): ?>
        <div class="bg-white rounded-4 border shadow-sm p-5 text-center fade-in-element">
            <i class="fa-regular fa-newspaper fs-1 text-muted opacity-25 mb-3 d-block"></i>
            <h2 class="fw-bold mb-2">Bài viết không tồn tại</h2>
            <p class="text-muted mb-4">Bài viết bạn đang tìm có thể đã bị gỡ hoặc không còn hiệu lực.</p>
            <a href="<?= Url::blogs() ?>" class="btn btn-dark px-4 py-2 rounded-pill me-2">
                <i class="fa-solid fa-arrow-left me-2"></i>Xem tạp chí
            </a>
            <a href="<?= url() ?>" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                Trang chủ
            </a>
        </div>
    <?php else: ?>
        <!-- Main Article -->
        <article class="blog-article p-4 p-md-5 bg-white rounded-4 border shadow-sm fade-in-element mb-5"
                 style="animation-delay: 0.1s;">
            <?php if ($blogDate): ?>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                        <i class="fa-regular fa-calendar-days text-primary me-2"></i><?= htmlspecialchars($blogDate) ?>
                    </span>
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-medium" style="font-size: 0.8rem;">
                        <i class="fa-regular fa-clock me-1"></i>3 phút đọc
                    </span>
                </div>
            <?php endif; ?>

            <h1 class="blog-main-title fw-bolder mb-4 lh-base text-dark"
                style="font-size: calc(1.75rem + 0.8vw); letter-spacing: -0.5px;">
                <?= htmlspecialchars($blogTitle) ?>
            </h1>

            <?php if ($blogImage): ?>
                <div class="blog-featured-image-wrapper mb-5 text-center">
                    <img src="<?= htmlspecialchars(image_url($blogImage)) ?>"
                         alt="<?= htmlspecialchars($blogTitle) ?>"
                         class="img-fluid rounded-4 shadow-sm w-100"
                         loading="eager" fetchpriority="high" decoding="async"
                         style="object-fit: cover; max-height: 520px; width: 100%;">
                </div>
            <?php endif; ?>

            <div class="blog-content-formatted">
                <?php if ($blogDesc !== ''): ?>
                    <?= render_blog_body($blogDesc) ?>
                <?php else: ?>
                    <p class="text-muted fst-italic">Bài viết đang được cập nhật nội dung.</p>
                <?php endif; ?>
            </div>

            <!-- Author & Navigation Footer -->
            <div class="mt-5 pt-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 border-top">
                <div class="d-flex align-items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=000&color=fff"
                         class="rounded-circle shadow-sm" width="46" height="46" loading="lazy" decoding="async" alt="Author">
                    <div>
                        <div class="fw-bold text-dark">AI CỦA TÔI</div>
                        <div class="small text-muted">Ban biên tập thông tin công nghệ</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= Url::blogs() ?>" class="btn btn-outline-dark rounded-pill px-3 py-2 btn-sm fw-semibold">
                        <i class="fa-solid fa-newspaper me-1"></i>Xem thêm bài viết
                    </a>
                    <a href="<?= url() ?>" class="btn btn-dark rounded-pill px-3 py-2 btn-sm fw-semibold">
                        <i class="fa-solid fa-house me-1"></i>Trang chủ
                    </a>
                </div>
            </div>
        </article>

        <!-- Recommended / HOT Products Section -->
        <?php if (!empty($sidebarProducts)): ?>
            <div class="recommended-products-section fade-in-element mt-4">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.35rem; letter-spacing: -0.5px;">
                        <i class="fa-solid fa-fire text-danger me-2"></i>Sản phẩm nổi bật gợi ý
                    </h3>
                    <a href="<?= Url::products() ?>" class="text-decoration-none text-muted small fw-semibold">
                        Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row g-4">
                    <?php foreach ($sidebarProducts as $product): ?>
                        <div class="col-12 col-md-4 product-item">
                            <a href="<?= Url::product($product) ?>" class="text-decoration-none text-reset h-100 d-block">
                                <div class="card product-card position-relative h-100 shadow-sm border-0"
                                     style="border-radius: 16px; transition: transform 0.2s, box-shadow 0.2s;">
                                    <?php if (!empty($product['badge'])): ?>
                                        <span class="badge-hot" style="font-size: 0.7rem; padding: 5px 12px; top: 12px; right: 12px;">
                                            <?= htmlspecialchars($product['badge']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>"
                                         class="card-img-top"
                                         alt="<?= htmlspecialchars($product['title'] ?? '') ?>"
                                         loading="lazy" decoding="async"
                                         style="height: 180px; object-fit: cover; border-radius: 16px 16px 0 0;">
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <h4 class="product-title fw-bold mb-2 text-dark" style="font-size: 1rem; line-height: 1.4;">
                                                <?= htmlspecialchars($product['title'] ?? '') ?>
                                            </h4>
                                            <?php if (!empty($product['feature_text'])): ?>
                                                <p class="text-muted small mb-3">
                                                    <i class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> text-success me-1"></i>
                                                    <?= htmlspecialchars($product['feature_text']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                            <span class="product-price fw-bold text-dark fs-5">
                                                <?= number_format((float) ($product['price'] ?? 0), 0, ',', '.') ?>đ
                                            </span>
                                            <span class="btn btn-sm btn-dark rounded-pill px-3 py-1 fw-medium" style="font-size: 0.8rem;">
                                                Chi tiết
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.blog-detail-container {
    width: 100%;
}
.blog-article {
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
}
.blog-content-formatted {
    color: #334155;
    font-size: 1.05rem;
    line-height: 1.85;
    word-break: break-word;
}
.blog-content-formatted p {
    margin: 0 0 1.25rem;
    text-align: justify;
    text-justify: inter-word;
}
.blog-content-formatted h1,
.blog-content-formatted h2,
.blog-content-formatted h3,
.blog-content-formatted h4 {
    color: #0f172a;
    font-weight: 750;
    line-height: 1.4;
    margin: 2rem 0 0.85rem;
    letter-spacing: -0.3px;
}
.blog-content-formatted h1 { font-size: 1.6rem; }
.blog-content-formatted h2 { font-size: 1.35rem; }
.blog-content-formatted h3 { font-size: 1.2rem; }
.blog-content-formatted h4 { font-size: 1.1rem; }
.blog-content-formatted strong,
.blog-content-formatted b {
    font-weight: 700;
    color: #0f172a;
}
.blog-content-formatted ul,
.blog-content-formatted ol {
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
    line-height: 1.85;
}
.blog-content-formatted li {
    margin-bottom: 0.45rem;
    text-align: justify;
    text-justify: inter-word;
}
.blog-content-formatted blockquote {
    background: #f8fafc;
    border-left: 4px solid #0f172a;
    padding: 1.1rem 1.5rem;
    margin: 1.75rem 0;
    border-radius: 0 12px 12px 0;
    font-style: italic;
    color: #475569;
}
.blog-content-formatted table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
}
.blog-content-formatted th,
.blog-content-formatted td {
    border: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
    vertical-align: top;
}
.blog-content-formatted th {
    color: #0f172a;
    background: #f8fafc;
    font-weight: 700;
}
.blog-content-formatted img {
    max-width: 100%;
    height: auto;
    border-radius: 14px;
    margin: 1.5rem auto;
    display: block;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
}
</style>
