<div class="contact-page py-5">
    <div class="contact-container fade-in-element">
        <div class="row gy-5 gx-4 gx-lg-5">
            <div class="col-lg-5">
                <div class="contact-info-card bg-dark text-white p-4 p-md-5 rounded-4 h-100 shadow-lg">
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
                        <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <i class="fa-regular fa-clock text-warning mt-1" style="font-size: 0.9rem;"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 text-white" style="font-size: 0.85rem;">Thời gian hỗ trợ</h6>
                                    <p class="mb-0 text-light opacity-75" style="font-size: 0.75rem; line-height: 1.4;">Phục vụ liên tục từ 08:00 đến 23:30 hàng ngày (kể cả Thứ 7, Chủ Nhật và ngày lễ).</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-2">
                                <i class="fa-solid fa-circle-info text-info mt-1" style="font-size: 0.9rem;"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 text-white" style="font-size: 0.85rem;">Hướng dẫn hỗ trợ nhanh</h6>
                                    <p class="mb-0 text-light opacity-75" style="font-size: 0.75rem; line-height: 1.4;">Khi liên hệ, vui lòng gửi kèm <strong>Mã đơn hàng</strong> hoặc <strong>Email đăng ký</strong> để admin kiểm tra và xử lý ngay lập tức.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contact-form-wrapper bg-white p-4 p-md-5 rounded-4 shadow-sm border">
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