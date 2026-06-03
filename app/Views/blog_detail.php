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

    // Heuristic: if the content already contains any HTML tag it was saved by
    // the rich editor — render as-is.
    if (preg_match('/<\s*(p|h[1-6]|ul|ol|li|blockquote|strong|em|br|a|img|span|b|i|u)\b/i', $raw)) {
        return $raw;
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
<div class="row g-5">
    <!-- Sidebar -->
    <div class="col-lg-4 col-md-5 d-none d-md-block">
        <h4 class="fw-bold mb-4 border-bottom pb-3" style="letter-spacing: -0.5px;">
            <i class="fa-solid fa-fire text-danger me-2"></i>Sản phẩm HOT
        </h4>
        <div class="row g-4">
            <?php foreach (($sidebarProducts ?? []) as $product): ?>
                <div class="col-12 product-item fade-in-element">
                    <a href="<?= Url::product($product) ?>"
                       class="text-decoration-none text-reset">
                        <div class="card product-card position-relative h-100 shadow-sm" style="border-radius: 12px;">
                            <?php if (!empty($product['badge'])): ?>
                                <span class="badge-hot" style="font-size: 0.65rem; padding: 4px 10px; top: 10px; right: 10px;">
                                    <?= htmlspecialchars($product['badge']) ?>
                                </span>
                            <?php endif; ?>
                            <img src="<?= htmlspecialchars(image_url($product['image'] ?? '')) ?>" class="card-img-top"
                                 alt="<?= htmlspecialchars($product['title'] ?? '') ?>"
                                 loading="lazy" decoding="async"
                                 style="height: 180px; object-fit: cover; border-radius: 12px 12px 0 0;">
                            <div class="card-body p-3">
                                <h4 class="product-title" style="font-size: 1.05rem; line-height: 1.4;">
                                    <?= htmlspecialchars($product['title'] ?? '') ?>
                                </h4>
                                <p class="text-muted small mb-2">
                                    <i class="fa-solid <?= htmlspecialchars($product['feature_icon'] ?? 'fa-circle-check') ?> me-1"></i>
                                    <?= htmlspecialchars($product['feature_text'] ?? '') ?>
                                </p>
                                <div class="mt-3">
                                    <span class="product-price fs-5" style="letter-spacing: -0.5px;">
                                        <?= number_format($product['price'] ?? 0, 0, ',', '.') ?>đ
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Article -->
    <div class="col-lg-8 col-md-7">
        <nav aria-label="breadcrumb" class="mb-4 fade-in-element">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= url() ?>" class="text-muted text-decoration-none fw-medium">Trang chủ</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= url() ?>" class="text-muted text-decoration-none fw-medium">Tạp Chí</a>
                </li>
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">
                    <?= htmlspecialchars(mb_strimwidth($blogTitle, 0, 60, '…')) ?>
                </li>
            </ol>
        </nav>

        <?php if (!$hasBlog): ?>
            <div class="bg-white rounded-4 border shadow-sm p-5 text-center">
                <i class="fa-regular fa-newspaper fs-1 text-muted opacity-25 mb-3 d-block"></i>
                <h2 class="fw-bold mb-2">Bài viết không tồn tại</h2>
                <p class="text-muted mb-4">Bài viết bạn đang tìm có thể đã bị gỡ hoặc không còn hiệu lực.</p>
                <a href="<?= url() ?>" class="btn btn-dark px-4 rounded-pill">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại trang chủ
                </a>
            </div>
        <?php else: ?>
            <article class="blog-article p-4 p-md-5 bg-white rounded-4 border shadow-sm fade-in-element"
                     style="animation-delay: 0.1s;">
                <?php if ($blogDate): ?>
                    <div class="text-muted small text-uppercase fw-bold mb-3" style="letter-spacing: 1.5px;">
                        <i class="fa-regular fa-calendar me-2"></i><?= htmlspecialchars($blogDate) ?>
                    </div>
                <?php endif; ?>

                <h1 class="fw-bolder mb-4 lh-sm"
                    style="font-size: 2.4rem; letter-spacing: -1px; color: var(--pure-black);">
                    <?= htmlspecialchars($blogTitle) ?>
                </h1>

                <?php if ($blogImage): ?>
                    <img src="<?= htmlspecialchars(image_url($blogImage)) ?>"
                         alt="<?= htmlspecialchars($blogTitle) ?>"
                         class="img-fluid rounded-4 mb-5 w-100 shadow-sm"
                         loading="eager" fetchpriority="high" decoding="async"
                         style="object-fit: cover; max-height: 450px;">
                <?php endif; ?>

                <div class="blog-content-formatted" style="font-size: 1.1rem; line-height: 1.85; color: #444;">
                    <?php if ($blogDesc !== ''): ?>
                        <?= render_blog_body($blogDesc) ?>
                    <?php else: ?>
                        <p class="text-muted fst-italic">Bài viết đang được cập nhật nội dung.</p>
                    <?php endif; ?>
                </div>

                <div class="mt-5 pt-4 d-flex align-items-center justify-content-between border-top">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=000&color=fff"
                             class="rounded-circle" width="45" height="45" loading="lazy" decoding="async" alt="Author">
                        <div>
                            <div class="fw-bold text-dark small">AI CỦA TÔI</div>
                            <div class="small text-muted">Editorial Team</div>
                        </div>
                    </div>
                    <a href="<?= url() ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-arrow-left me-1"></i>Trở về
                    </a>
                </div>
            </article>
        <?php endif; ?>
    </div>
</div>
