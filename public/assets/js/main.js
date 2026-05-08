let cartTotal = 0;

function addToCart(productName) {
    cartTotal++;
    document.getElementById('cart-count').innerText = cartTotal;

    // Hiệu ứng Toast Minimalist Trắng Đen
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        background: '#111',
        color: '#fff',
        iconColor: '#fff',
        customClass: { popup: 'rounded-4 shadow-lg' },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'success',
        title: `Đã thêm ${productName}`
    });
}

function buyNow(productName) {
    Swal.fire({
        title: 'Xác nhận đơn hàng',
        html: `<p class="text-muted">Bạn đang tiến hành thanh toán cho:<br><strong class="text-dark fs-5">${productName}</strong></p>`,
        icon: 'none',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#e5e7eb',
        confirmButtonText: 'Tiến hành thanh toán',
        cancelButtonText: '<span style="color:#000">Hủy bỏ</span>',
        customClass: { confirmButton: 'rounded-pill px-4 py-2', cancelButton: 'rounded-pill px-4 py-2', popup: 'rounded-4' },
        backdrop: `rgba(0,0,0,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Đơn hàng thành công',
                text: `Hệ thống sẽ gửi ${productName} tới email của bạn.`,
                icon: 'success',
                confirmButtonColor: '#000',
                customClass: { confirmButton: 'rounded-pill px-5 py-2', popup: 'rounded-4' }
            });
        }
    });
}

function switchTab(tabName, event) {
    document.getElementById('products-section').style.display = 'none';
    document.getElementById('blog-section').style.display = 'none';
    document.getElementById('detail-section').style.display = 'none'; // Đóng trang detail nếu đang mở

    if (tabName === 'products') {
        document.getElementById('products-section').style.display = 'block';
    } else if (tabName === 'blog') {
        document.getElementById('blog-section').style.display = 'block';
    }

    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.tab-btn').classList.add('active');
}

const products = {
    'chatgpt-plus': {
        title: 'Tài khoản ChatGPT Plus (1 Tháng)',
        category: 'ChatGPT',
        price: '450.000đ',
        image: 'https://images.unsplash.com/photo-1675271591211-126ad94e4958?auto=format&fit=crop&q=80&w=800&h=500',
        description: 'Nâng cấp lên ChatGPT Plus và tận hưởng truy cập không giới hạn vào AI tiên tiến nhất, phản hồi siêu tốc độ và truy cập vào các tính năng mới ngay lập tức.',
        options: ['Gói Cấp Tốc (Bảo hành 1 Ngày)', 'Gói Tiêu Chuẩn (Bảo hành 7 Ngày)', 'Gói Premium (Bảo hành 30 Ngày)']
    },
    'youtube-premium': {
        title: 'YouTube Premium (Mail Chính Chủ)',
        category: 'YouTube',
        price: '250.000đ',
        image: 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&q=80&w=800&h=500',
        description: 'Trải nghiệm xem video không quảng cáo, phát trong nền và tải xuống dễ dàng. Nâng cấp trực tiếp trên email cá nhân của bạn, tuyệt đối an toàn.',
        options: ['Bảo hành trọn đời (Gói Cá nhân)', 'Bảo hành 1 Năm (Gói Gia đình)']
    },
    'github-copilot': {
        title: 'Github Copilot (Gói Dev 1 Năm)',
        category: 'GitHub',
        price: '150.000đ',
        image: 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&q=80&w=800&h=500',
        description: 'Trợ lý lập trình AI đẳng cấp. Tự động đề xuất code, tiết kiệm hàng giờ gõ phím. Hỗ trợ mọi ngôn ngữ lập trình phổ biến nhất hiện nay.',
        options: ['Gói Sinh Viên (Bảo hành 6 tháng)', 'Gói Chuyên Nghiệp (Bảo hành 1 Năm)']
    },
    'netflix-premium': {
        title: 'Netflix Premium 4K (1 Tháng)',
        category: 'Netflix',
        price: '85.000đ',
        image: 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&q=80&w=800&h=500',
        description: 'Đắm chìm vào thế giới điện ảnh với chất lượng Ultra HD 4K sắc nét nhất. Profile riêng tư được bảo mật bằng mã PIN.',
        options: ['Profile Tiêu Chuẩn (Bảo hành 1 Tháng)', 'Profile Cao Cấp 4K (Bảo hành 3 Tháng)']
    }
};

let currentProductId = null;

function showProductDetail(productId) {
    const product = products[productId];
    if (!product) return;

    currentProductId = productId;

    document.getElementById('detail-image').src = product.image;
    document.getElementById('detail-category').textContent = product.category;
    document.getElementById('detail-title').textContent = product.title;
    document.getElementById('detail-price').textContent = product.price;
    document.getElementById('detail-desc').textContent = product.description;

    const optionsHTML = product.options.map((option, index) => 
        `<div class="option-item ${index === 0 ? 'selected' : ''}" onclick="selectOption(this)">
            <div class="d-flex align-items-center">
                <div style="width: 16px; height: 16px; border: 1px solid #000; border-radius: 50%; margin-right: 12px; display: flex; align-items: center; justify-content: center;">
                    <div class="radio-dot" style="width: 8px; height: 8px; background: ${index === 0 ? '#000' : 'transparent'}; border-radius: 50%;"></div>
                </div>
                <span>${option}</span>
            </div>
        </div>`
    ).join('');
    document.getElementById('product-options').innerHTML = optionsHTML;

    document.getElementById('products-section').style.display = 'none';
    document.getElementById('blog-section').style.display = 'none';
    document.getElementById('detail-section').style.display = 'block';
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goBackToProducts(event) {
    event.preventDefault();
    document.getElementById('detail-section').style.display = 'none';
    document.getElementById('products-section').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selectOption(element) {
    document.querySelectorAll('.option-item').forEach(item => {
        item.classList.remove('selected');
        item.querySelector('.radio-dot').style.background = 'transparent';
    });
    element.classList.add('selected');
    element.querySelector('.radio-dot').style.background = '#000';
}

function addToCartFromDetail() {
    if (products[currentProductId]) addToCart(products[currentProductId].title);
}

function showCheckout() {
    if (products[currentProductId]) buyNow(products[currentProductId].title);
}

function filterProducts(category, event) {
    const allProducts = document.querySelectorAll('.product-item');
    const allButtons = document.querySelectorAll('.cat-pill');

    if (allButtons.length > 0 && event && event.target) {
        allButtons.forEach(btn => btn.classList.remove('active'));
        const btn = event.target.closest('.cat-pill');
        if (btn) btn.classList.add('active');
    }

    allProducts.forEach((product, index) => {
        if (category === 'all' || product.dataset.category === category) {
            product.style.display = 'block';
            // Reset animation for fresh load feel
            product.style.animation = 'none';
            product.offsetHeight; // trigger reflow
            product.style.animation = `fadeIn 0.5s ease forwards ${index * 0.1}s`;
        } else {
            product.style.display = 'none';
        }
    });
}

// Scroll to Top Logic
document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopBtn = document.getElementById('btnScrollToTop');
    
    if (scrollToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        });

        scrollToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
