<div class="contact-page py-5">
    <div class="contact-container fade-in-element">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="contact-info-card bg-dark text-white p-5 rounded-4 h-100 shadow-lg">
                    <h3 class="fw-bold mb-4">


                        <?php echo htmlspecialchars($settings['contact_title'] ?? 'Thông tin liên hệ'); ?>
                    </h3>
                    <p class="text-light opacity-75 mb-5">
                        <?php echo htmlspecialchars($settings['contact_desc'] ?? 'Bạn cần hỗ trợ? Đừng ngần ngại liên hệ với chúng tôi qua các kênh sau.'); ?>
                    </p>

                    <div class="d-flex flex-column gap-4">
                        <?php
                        $contactMethods = json_decode($settings['contact_methods'] ?? '[]', true);
                        if (empty($contactMethods)) {
                            $contactMethods = [
                                ['icon' => 'fa-envelope', 'text' => 'tetuongmmovn@gmail.com'],
                                ['icon' => 'fa-telegram', 'text' => '@specademy'],
                                ['icon' => 'fa-phone', 'text' => 'Zalo: 0967037906']
                            ];
                        }
                        foreach ($contactMethods as $method):
                            ?>
                            <div class="d-flex align-items-center gap-3">
                                <div class="contact-icon bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="<?= htmlspecialchars($method['icon']) ?>"></i>
                                </div>
                                <span><?= htmlspecialchars($method['text']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-auto pt-5">
                        <div class="d-flex gap-3">
                            <?php
                            $socialLinks = json_decode($settings['social_links_json'] ?? '[]', true);
                            if (empty($socialLinks)) {
                                $socialLinks = [
                                    ['icon' => 'fa-facebook-f', 'url' => '#'],
                                    ['icon' => 'fa-zalo', 'url' => '#'],
                                    ['icon' => 'fa-tiktok', 'url' => '#']
                                ];
                            }
                            foreach ($socialLinks as $link):
                                ?>
                                <a href="<?= htmlspecialchars($link['url']) ?>"
                                    class="btn btn-outline-light rounded-circle p-0 d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;"><i
                                        class="<?= htmlspecialchars($link['icon']) ?>"></i></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-form-wrapper bg-white p-5 rounded-4 shadow-sm border">
                    <h3 class="fw-bold mb-4 text-dark">Gửi tin nhắn cho chúng tôi</h3>
                    <form id="contactForm">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Họ tên</label>
                                <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" class="form-control" placeholder="name@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Chủ đề</label>
                                <input type="text" class="form-control" placeholder="Tôi cần hỗ trợ về dịch vụ..."
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Nội dung</label>
                                <textarea class="form-control" rows="5" placeholder="Mô tả chi tiết vấn đề của bạn..."
                                    required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-buy w-100 py-3 rounded-3">Gửi yêu cầu ngay</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>