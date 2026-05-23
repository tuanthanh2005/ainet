# AI CỦA TÔI - MVC Store

Hệ thống bán hàng dịch vụ số tối giản, chuẩn kiến trúc MVC (Model-View-Controller).

## Cấu trúc thư mục
- `/assets`: Chứa CSS, JS, Hình ảnh.
- `/config`: Cấu hình database và hệ thống.
- `/controllers`: Xử lý logic nghiệp vụ.
- `/core`: Các lớp cốt lõi (Database, Base Controller, Base Model).
- `/data`: Chứa dữ liệu JSON (Settings, Products tạm thời).
- `/models`: Tương tác với dữ liệu.
- `/views`: Giao diện người dùng và Admin.
- `index.php`: Entry point duy nhất của ứng dụng.

## Cài đặt và Deployment
1. Cấu hình database trong `config/config.php`.
2. Đảm bảo server hỗ trợ `mod_rewrite` (Apache) để sử dụng `.htaccess`.
3. Truy cập `/` để xem website.
4. Truy cập `/?action=adminDashboard` để quản trị.

## Tính năng
- Quản lý sản phẩm.
- Quản lý cài đặt website (Banner, Social, Footer).
- Giao diện Mobile-First native app style.
- Tối ưu SEO & Performance.
