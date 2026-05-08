<div class="row g-5">
    <!-- Cột trái (Sidebar - Sản phẩm HOT ngẫu nhiên) -->
    <div class="col-lg-4 col-md-5 d-none d-md-block">
        <h4 class="fw-bold mb-4 border-bottom pb-3" style="letter-spacing: -0.5px;"><i class="fa-solid fa-fire text-danger me-2"></i>Sản phẩm HOT</h4>
        <div class="row g-4">
            <?php foreach($sidebarProducts as $product): ?>
            <div class="col-12 product-item fade-in-element">
                <div class="card product-card position-relative h-100 shadow-sm" onclick="showProductDetail('<?= htmlspecialchars($product['id']) ?>')" style="cursor: pointer; border-radius: 12px;">
                    <?php if(!empty($product['badge'])): ?>
                        <span class="badge-hot" style="font-size: 0.65rem; padding: 4px 10px; top: 10px; right: 10px;"><?= htmlspecialchars($product['badge']) ?></span>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['category']) ?>" style="height: 180px; border-radius: 12px 12px 0 0;">
                    <div class="card-body p-3">
                        <h4 class="product-title" style="font-size: 1.05rem; line-height: 1.4;"><?= htmlspecialchars($product['title']) ?></h4>
                        <p class="text-muted small mb-2"><i class="fa-solid <?= htmlspecialchars($product['feature_icon']) ?> me-1"></i> <?= htmlspecialchars($product['feature_text']) ?></p>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <span class="product-price fs-5" style="letter-spacing: -0.5px;"><?= htmlspecialchars($product['price']) ?></span>
                            <button class="btn btn-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" onclick="event.stopPropagation(); addToCart('<?= htmlspecialchars($product['category']) ?>')">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Cột phải (Nội dung bài viết Blog) -->
    <div class="col-lg-8 col-md-7">
        <nav aria-label="breadcrumb" class="mb-4 fade-in-element">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none fw-medium">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="index.php" class="text-muted text-decoration-none fw-medium">Tạp Chí</a></li>
                <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Tối Ưu Hóa Trải Nghiệm AI</li>
            </ol>
        </nav>

        <article class="blog-article p-4 p-md-5 bg-white rounded-4 border shadow-sm fade-in-element" style="animation-delay: 0.1s;">
            <div class="text-muted small text-uppercase fw-bold mb-3" style="letter-spacing: 1.5px;">
                <i class="fa-regular fa-calendar me-2"></i> 15 Th05, 2026 &nbsp;&bull;&nbsp; AI TRONG CÔNG VIỆC
            </div>
            
            <h1 class="fw-bolder mb-4 lh-sm" style="font-size: 2.4rem; letter-spacing: -1px; color: var(--pure-black);">Tối Ưu Hóa Trải Nghiệm AI Trong Công Việc Của Bạn</h1>
            
            <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=1200&h=600" alt="Blog cover" class="img-fluid rounded-4 mb-5 w-100 shadow-sm" style="object-fit: cover; max-height: 450px;">
            
            <div class="blog-content-formatted" style="font-size: 1.1rem; line-height: 1.85; color: #444;">
                <p class="lead text-dark fw-medium mb-4" style="font-size: 1.25rem;">
                    Sự can thiệp của AI vào quá trình làm việc đang thay đổi định nghĩa về mọi ngành nghề. Làm thế nào để bạn bắt kịp xu hướng và không bị bỏ lại phía sau? Dưới đây là những bí quyết quan trọng nhất.
                </p>

                <h3 class="text-dark fw-bold mt-5 mb-3" style="letter-spacing: -0.5px;">1. Viết Prompt Giống Như Giao Tiếp Cùng Chuyên Gia</h3>
                <p>Nhiều người sử dụng AI (như ChatGPT hay Claude) giống như một công cụ tìm kiếm truyền thống (Google). Tuy nhiên, sức mạnh thực sự của các mô hình ngôn ngữ lớn (LLM) nằm ở khả năng hiểu ngữ cảnh và giả lập vai trò.</p>
                <ul class="mb-4">
                    <li class="mb-2"><strong class="text-dark">Cách dùng sai:</strong> "Viết code HTML form đăng nhập."</li>
                    <li class="mb-2"><strong class="text-primary">Cách dùng chuẩn:</strong> "Hãy đóng vai một chuyên gia UI/UX Senior. Viết cho tôi một form đăng nhập bằng HTML, CSS, Bootstrap 5 theo phong cách tối giản (minimalist). Yêu cầu code ngắn gọn, responsive trên điện thoại."</li>
                </ul>

                <blockquote class="blockquote bg-light border-start border-4 border-dark p-4 my-5 rounded-end fst-italic shadow-sm">
                    "Trí tuệ nhân tạo sẽ không thay thế con người. Nhưng những người biết sử dụng AI chắc chắn sẽ thay thế những người không biết dùng nó."
                </blockquote>

                <h3 class="text-dark fw-bold mt-5 mb-3" style="letter-spacing: -0.5px;">2. Tích hợp AI Vào Workflow Hàng Ngày</h3>
                <p>Đừng chỉ mở trình duyệt khi cần. Hãy tích hợp thẳng AI vào môi trường làm việc của bạn thông qua các extension hoặc phần mềm bản địa (ví dụ: Github Copilot trong VSCode, ChatGPT Desktop). Điều này giúp tiết kiệm đến 50% thời gian gõ phím và debug mỗi ngày.</p>
                
                <h4 class="text-dark fw-bold mt-4 mb-3 fs-5">Top các công cụ khuyên dùng năm 2026:</h4>
                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <strong class="d-block text-dark mb-1"><i class="fa-solid fa-bolt text-warning me-2"></i> ChatGPT Plus</strong>
                            <span class="small">Bản trả phí cho tốc độ cực nhanh và plugin mạnh mẽ.</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <strong class="d-block text-dark mb-1"><i class="fa-brands fa-github text-dark me-2"></i> Github Copilot</strong>
                            <span class="small">Trợ lý hoàn hảo dành riêng cho Lập trình viên.</span>
                        </div>
                    </div>
                </div>

                <p class="mt-4 pt-4 border-top">
                    Hãy bắt đầu ngay hôm nay để tối đa hóa hiệu suất làm việc của bạn. Đừng quên ghé qua mục Sản Phẩm của <strong class="text-dark">AI CỦA TÔI</strong> để trang bị cho mình những "vũ khí" số sắc bén nhất với chi phí tối ưu nhé!
                </p>
                
                <div class="mt-5 pt-4 d-flex align-items-center justify-content-between border-top">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=000&color=fff" class="rounded-circle" width="45" height="45" alt="Author">
                        <div>
                            <div class="fw-bold text-dark small">Tuan Tran</div>
                            <div class="small text-muted">Admin / Tech Reviewer</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-dark btn-sm rounded-pill px-3"><i class="fa-solid fa-share-nodes me-1"></i> Chia sẻ</button>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
