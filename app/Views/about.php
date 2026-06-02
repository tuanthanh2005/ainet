<div class="about-page py-5">
    <div class="about-container fade-in-element">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4" style="font-size: 2.5rem; letter-spacing: -1px;"><?php echo $settings['about_title'] ?? 'Chào mừng đến với <span class="text-primary">AI CỦA TÔI</span>'; ?></h2>
                <p class="lead text-muted mb-4"><?php echo htmlspecialchars($settings['about_desc'] ?? 'Chúng tôi là đơn vị hàng đầu cung cấp các giải pháp tài khoản Premium và dịch vụ AI cao cấp tại Việt Nam.'); ?></p>
                <div class="row g-4 mb-5">
                    <?php 
                    $aboutFeatures = json_decode($settings['about_features'] ?? '[]', true);
                    if (empty($aboutFeatures)) {
                        $aboutFeatures = [
                            ['icon' => 'fa-bolt', 'color' => 'text-warning', 'title' => 'Nhanh chóng', 'desc' => 'Kích hoạt trong 5 phút'],
                            ['icon' => 'fa-shield-check', 'color' => 'text-success', 'title' => 'Bảo mật', 'desc' => 'Cam kết an toàn 100%']
                        ];
                    }
                    foreach ($aboutFeatures as $feature): 
                    ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="feature-icon-sm bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="<?= htmlspecialchars($feature['icon']) ?> <?= htmlspecialchars($feature['color']) ?>"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($feature['title']) ?></h6>
                                <p class="small text-muted mb-0"><?= htmlspecialchars($feature['desc']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?php echo url(); ?>" class="btn btn-buy px-5 py-3 rounded-pill">Khám phá dịch vụ ngay</a>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="<?php echo htmlspecialchars($settings['about_image'] ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=1000'); ?>" class="img-fluid rounded-4 shadow-lg" loading="lazy" decoding="async" alt="AI Của Tôi">
                    <div class="position-absolute bottom-0 start-0 m-4 p-4 bg-white rounded-3 shadow-sm d-none d-md-block border border-light">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-1 fw-bold text-dark"><?php echo htmlspecialchars($settings['about_stat_value'] ?? '50K+'); ?></div>
                            <div class="small fw-medium text-muted"><?php echo nl2br(htmlspecialchars($settings['about_stat_label'] ?? "Khách hàng tin dùng\ntrên toàn quốc")); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
