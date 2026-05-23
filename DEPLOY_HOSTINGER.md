# Triển khai trên Hostinger

Hostinger yêu cầu mọi tệp **public** phải nằm trong `public_html/` và **không được** đặt code app trực tiếp tại đó (dòng `DO_NOT_UPLOAD_HERE` ở thư mục gốc đã cảnh báo).

## Cấu trúc đề xuất trên hosting

```
/home/<user>/
├── aicuatoi/                      # ← code app (private, KHÔNG ai vào URL được)
│   ├── app/
│   ├── config/
│   ├── database/                  # ← Chứa thư mục migrations/
│   ├── routes/  (nếu còn)
│   └── ... (mọi thứ trừ thư mục public/)
│
└── public_html/                   # ← document root (public, /)
    ├── index.php                  # copy nguyên từ public/index.php của project
    ├── .htaccess                  # copy nguyên từ public/.htaccess
    ├── assets/                    # copy public/assets/* của project
    │   ├── css/
    │   └── js/
    ├── blogs/                     # hình ảnh upload công khai
    └── chat/                      # file đính kèm chat, truy cập qua ACL
```

## Các bước thực hiện

### 1. Upload code

- Tải toàn bộ project lên `~/aicuatoi/`, **trừ thư mục `public/`**.
- Copy nội dung thư mục `public/` của project sang `~/public_html/`:
  - `public/index.php` → `public_html/index.php`
  - `public/.htaccess` → `public_html/.htaccess`
  - `public/assets/css/*` → `public_html/assets/css/`
  - `public/assets/js/*` → `public_html/assets/js/`

`index.php` đã được cấu hình tự động dò `~/aicuatoi/config/config.php`. Không cần chỉnh sửa bằng tay.

### 2. Cấu hình `.env`

Trong `~/aicuatoi/.env` đặt cấu hình tương ứng với server:

```env
APP_NAME="AI CỦA TÔI"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://aicuatoi.net
APP_KEY=base64:uniquekeyforsecurity12345

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<db_name_hostinger>
DB_USERNAME=<db_user>
DB_PASSWORD=<db_pass>

SESSION_LIFETIME_DAYS=365
FILESYSTEM_DISK=public_uploads
```

### 3. Chạy migration

Vào hPanel → Advanced → Cron Jobs → Manual run, hoặc qua SSH để chạy:

```bash
cd ~/aicuatoi
php artisan migrate
```

### 4. Tạo thư mục upload

```bash
mkdir -p ~/public_html/blogs ~/public_html/chat
```

Với cấu hình `FILESYSTEM_DISK=public_uploads`, hệ thống sẽ tự động ghi các file upload vào `~/public_html/<folder>` và link lưu trong database sẽ tương đương `APP_URL/<folder>/filename`.

### 5. Bảo mật bổ sung

- File `~/aicuatoi/.env` chứa dữ liệu nhạy cảm và **không** nằm trong `public_html` nên không thể truy cập từ web.
- Thêm vào `~/aicuatoi/.htaccess` để tăng cường bảo mật:
  ```htaccess
  Order deny,allow
  Deny from all
  ```

### 6. SePay Webhook URL

Sau khi deploy, hãy copy Webhook URL hiển thị trong mục Admin Settings trên website của bạn và cấu hình trên dashboard SePay để hệ thống tự động nhận diện thanh toán.

## Khi cập nhật code mới

1. Copy nội dung `public/` vào `public_html/` (ghi đè).
2. Copy phần còn lại vào `aicuatoi/` (trừ folder `public/`).
3. Tăng giá trị phiên bản trong `config/config.php` để trình duyệt người dùng tự động tải lại các file CSS/JS mới.
4. Chạy migration mới nếu có bổ sung bảng dữ liệu:
   ```bash
   php artisan migrate
   ```

## Cấu trúc lúc dev cục bộ (Windows / php -S)

Chạy dev server cục bộ bằng lệnh:
```bash
php -S 127.0.0.1:8000 -t public public/router.php
```
