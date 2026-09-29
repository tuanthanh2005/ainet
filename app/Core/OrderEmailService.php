<?php

class OrderEmailService {
    /**
     * Khởi tạo SmtpMailer từ cấu hình Database hoặc .env
     */
    private static function getMailer(&$fromEmail, &$fromName): ?SmtpMailer {
        try {
            $settings = Setting::getAll();
        } catch (Throwable $e) {
            $settings = [];
        }

        $host = trim($settings['smtp_host'] ?? (getenv('MAIL_HOST') ?: ''));
        $port = (int)($settings['smtp_port'] ?? (getenv('MAIL_PORT') ?: 465));
        $secure = trim($settings['smtp_secure'] ?? (getenv('MAIL_ENCRYPTION') ?: 'ssl'));
        $user = trim($settings['smtp_user'] ?? (getenv('MAIL_USERNAME') ?: ''));
        $pass = trim($settings['smtp_pass'] ?? (getenv('MAIL_PASSWORD') ?: ''));
        $fromName = trim($settings['smtp_from_name'] ?? (getenv('MAIL_FROM_NAME') ?: (defined('SITENAME') ? SITENAME : 'AI CỦA TÔI')));
        $fromEmail = trim($settings['smtp_from_email'] ?? (getenv('MAIL_FROM_ADDRESS') ?: $user));

        if (empty($host) || empty($user) || empty($pass)) {
            error_log('[OrderEmailService] SMTP chưa được cấu hình đầy đủ (host/user/pass trống).');
            return null;
        }

        require_once APP_ROOT . '/app/Core/SmtpMailer.php';
        return new SmtpMailer($host, $port, $secure, $user, $pass);
    }

    /**
     * Gửi email thông báo đơn hàng đang xử lý đến khách hàng
     *
     * @param array|string $order Dữ liệu đơn hàng hoặc Mã đơn hàng
     * @return bool
     */
    public static function sendOrderProcessingEmail($order): bool {
        if (is_string($order)) {
            $order = Order::getById($order);
        }
        if (!$order || empty($order['customer_email'])) {
            return false;
        }

        $toEmail = trim($order['customer_email']);
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $fromEmail = '';
        $fromName = '';
        $mailer = self::getMailer($fromEmail, $fromName);
        if (!$mailer) {
            return false;
        }

        $orderId = htmlspecialchars($order['id'] ?? '');
        $productName = htmlspecialchars($order['product_name'] ?? 'Sản phẩm');
        $variantName = htmlspecialchars($order['variant_name'] ?? 'Mặc định');
        $quantity = (int)($order['quantity'] ?? 1);
        $amount = number_format((float)($order['amount'] ?? 0), 0, ',', '.') . 'đ';
        $orderHistoryUrl = url('index.php?action=orderHistory');
        $zaloPhone = '0772698113';
        $zaloUrl = 'https://zalo.me/0772698113';
        $telegramUrl = 'https://t.me/specademy';
        $siteName = defined('SITENAME') ? SITENAME : 'AI CỦA TÔI';
        $subject = "[{$siteName}] Đơn hàng #{$order['id']} đang được xử lý";

        $bodyHtml = "
        <!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$subject}</title>
            <style>
                body { margin: 0; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b; line-height: 1.6; }
                .email-card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
                .email-header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
                .email-header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
                .email-header p { margin: 6px 0 0; font-size: 14px; opacity: 0.9; }
                .email-body { padding: 32px 28px; }
                .badge-status { display: inline-block; background: #e0f2fe; color: #0284c7; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 13px; margin-bottom: 20px; }
                .order-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0; }
                .order-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #edf2f7; font-size: 14px; }
                .order-row:last-child { border-bottom: none; }
                .order-label { color: #64748b; }
                .order-value { font-weight: 600; color: #0f172a; text-align: right; }
                .info-banner { background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 16px; margin: 24px 0; font-size: 14px; color: #1e40af; }
                .btn-group { text-align: center; margin: 30px 0 10px; }
                .btn { display: inline-block; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; text-decoration: none; margin: 6px; }
                .btn-primary { background: #2563eb; color: #ffffff !important; }
                .btn-secondary { background: #0284c7; color: #ffffff !important; }
                .email-footer { background: #f8fafc; padding: 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
            </style>
        </head>
        <body>
            <div class='email-card'>
                <div class='email-header'>
                    <h1>{$siteName}</h1>
                    <p>Hệ thống cung cấp phần mềm & dịch vụ AI chính hãng</p>
                </div>
                <div class='email-body'>
                    <div style='text-align: center;'>
                        <span class='badge-status'>⏳ ĐƠN HÀNG ĐANG XỬ LÝ</span>
                    </div>
                    <p>Xin chào quý khách <strong>{$toEmail}</strong>,</p>
                    <p>Cảm ơn bạn đã đặt hàng tại <strong>{$siteName}</strong>! Giao dịch của bạn đã được xác nhận thanh toán thành công và hiện đang được đội ngũ quản trị viên tiến hành xử lý/bàn giao.</p>
                    
                    <div class='order-box'>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr style='border-bottom: 1px solid #e2e8f0;'>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Mã đơn hàng:</td>
                                <td style='padding: 8px 0; font-weight: 700; color: #0f172a; font-family: monospace; font-size: 15px; text-align: right;'>#{$orderId}</td>
                            </tr>
                            <tr style='border-bottom: 1px solid #e2e8f0;'>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Sản phẩm:</td>
                                <td style='padding: 8px 0; font-weight: 600; color: #0f172a; font-size: 14px; text-align: right;'>{$productName}</td>
                            </tr>
                            <tr style='border-bottom: 1px solid #e2e8f0;'>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Gói / Phân loại:</td>
                                <td style='padding: 8px 0; font-weight: 600; color: #0f172a; font-size: 14px; text-align: right;'>{$variantName} (x{$quantity})</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Tổng thanh toán:</td>
                                <td style='padding: 8px 0; font-weight: 800; color: #2563eb; font-size: 16px; text-align: right;'>{$amount}</td>
                            </tr>
                        </table>
                    </div>

                    <div class='info-banner'>
                        <strong>⏱ Thời gian xử lý:</strong> Thông thường từ <strong>5 - 15 phút</strong>.<br>
                        Ngay khi hoàn tất bàn giao tài khoản/key, hệ thống sẽ tự động gửi email thông báo chi tiết đến bạn.
                    </div>

                    <p style='font-size: 14px; color: #475569;'>
                        Nếu cần hỗ trợ gấp hoặc kích hoạt ngay, bạn có thể sao chép mã đơn <strong>#{$orderId}</strong> và nhắn tin cho Admin qua <strong>Zalo {$zaloPhone} (ưu tiên)</strong> hoặc Telegram.
                    </p>

                    <div class='btn-group'>
                        <a href='{$zaloUrl}' class='btn' style='background: #0068ff; color: #ffffff !important;' target='_blank'>💬 Zalo Admin: {$zaloPhone} (Ưu tiên)</a>
                        <a href='{$orderHistoryUrl}' class='btn btn-primary' target='_blank'>Xem trong Lịch sử đơn hàng</a>
                        <a href='{$telegramUrl}' class='btn btn-secondary' target='_blank'>Telegram Admin</a>
                    </div>
                </div>
                <div class='email-footer'>
                    &copy; " . date('Y') . " {$siteName}. Mọi quyền được bảo lưu.<br>
                    Hỗ trợ Zalo: <a href='{$zaloUrl}' style='color: #0068ff; font-weight: bold;'>{$zaloPhone}</a> (Ưu tiên) | Telegram: <a href='{$telegramUrl}' style='color: #2563eb;'>@specademy</a> | Website: <a href='" . url() . "' style='color: #2563eb;'>" . SITENAME . "</a>
                </div>
            </div>
        </body>
        </html>";

        try {
            return $mailer->send($fromEmail, $fromName, $toEmail, $subject, $bodyHtml);
        } catch (Throwable $e) {
            error_log('[OrderEmailService] Gửi email đơn hàng đang xử lý thất bại: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email thông báo đơn hàng đã hoàn tất & bàn giao tài khoản
     *
     * @param array|string $order Dữ liệu đơn hàng hoặc Mã đơn hàng
     * @param array $deliveredItems Danh sách tài khoản / key đã bàn giao (nếu có)
     * @return bool
     */
    public static function sendOrderCompletedEmail($order, array $deliveredItems = []): bool {
        if (is_string($order)) {
            $order = Order::getById($order);
        }
        if (!$order || empty($order['customer_email'])) {
            return false;
        }

        $toEmail = trim($order['customer_email']);
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Lấy danh sách tài khoản đã bàn giao từ đơn hàng nếu chưa được truyền vào
        if (empty($deliveredItems) && !empty($order['delivered_items'])) {
            $deliveredItems = is_array($order['delivered_items']) 
                ? $order['delivered_items'] 
                : json_decode((string)$order['delivered_items'], true);
        }
        if (!is_array($deliveredItems)) {
            $deliveredItems = [];
        }

        $fromEmail = '';
        $fromName = '';
        $mailer = self::getMailer($fromEmail, $fromName);
        if (!$mailer) {
            return false;
        }

        $orderId = htmlspecialchars($order['id'] ?? '');
        $productName = htmlspecialchars($order['product_name'] ?? 'Sản phẩm');
        $variantName = htmlspecialchars($order['variant_name'] ?? 'Mặc định');
        $quantity = (int)($order['quantity'] ?? 1);
        $amount = number_format((float)($order['amount'] ?? 0), 0, ',', '.') . 'đ';
        $successUrl = url('index.php?action=success&id=' . urlencode($order['id']));
        $orderHistoryUrl = url('index.php?action=orderHistory');
        $zaloPhone = '0772698113';
        $zaloUrl = 'https://zalo.me/0772698113';
        $telegramUrl = 'https://t.me/specademy';
        $siteName = defined('SITENAME') ? SITENAME : 'AI CỦA TÔI';
        $subject = "[{$siteName}] Bàn giao đơn hàng #{$order['id']} - Hoàn tất thành công";

        // Tạo khối tài khoản đã bàn giao (nếu có)
        $deliveryBlockHtml = '';
        if (!empty($deliveredItems)) {
            $itemsFormatted = '';
            foreach ($deliveredItems as $idx => $item) {
                $num = $idx + 1;
                $itemClean = htmlspecialchars($item);
                $itemsFormatted .= "<div style='padding: 6px 0; border-bottom: 1px dashed #cbd5e1; font-family: monospace; font-size: 14px;'><strong>#{$num}:</strong> {$itemClean}</div>";
            }
            $deliveryBlockHtml = "
            <div style='background: #f8fafc; border: 1px solid #cbd5e1; border-left: 5px solid #10b981; border-radius: 12px; padding: 20px; margin: 24px 0;'>
                <div style='font-weight: 700; color: #0f172a; margin-bottom: 12px; font-size: 15px; display: flex; align-items: center;'>
                    🔑 THÔNG TIN TÀI KHOẢN / KEY KÍCH HOẠT ĐÃ BÀN GIAO:
                </div>
                <div style='background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px;'>
                    {$itemsFormatted}
                </div>
                <p style='font-size: 12px; color: #64748b; margin: 10px 0 0;'>* Bạn có thể sao chép thông tin tài khoản bên trên để đăng nhập sử dụng ngay.</p>
            </div>
            ";
        }

        // Tạo thông tin tài khoản nâng cấp (nếu có)
        $upgradeBlockHtml = '';
        if (!empty($order['upgrade_email'])) {
            $upEmail = htmlspecialchars($order['upgrade_email']);
            $upgradeBlockHtml = "
            <div style='background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin: 16px 0; font-size: 13px; color: #166534;'>
                <strong>Tài khoản nâng cấp chính chủ:</strong> {$upEmail} (Đã được kích hoạt gói).
            </div>
            ";
        }

        $bodyHtml = "
        <!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$subject}</title>
            <style>
                body { margin: 0; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b; line-height: 1.6; }
                .email-card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
                .email-header { background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
                .email-header h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
                .email-header p { margin: 6px 0 0; font-size: 14px; opacity: 0.9; }
                .email-body { padding: 32px 28px; }
                .badge-status { display: inline-block; background: #dcfce7; color: #15803d; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 13px; margin-bottom: 20px; }
                .order-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0; }
                .btn-group { text-align: center; margin: 30px 0 10px; }
                .btn { display: inline-block; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; text-decoration: none; margin: 6px; }
                .btn-review { background: #f59e0b; color: #ffffff !important; }
                .btn-history { background: #0f172a; color: #ffffff !important; }
                .email-footer { background: #f8fafc; padding: 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
            </style>
        </head>
        <body>
            <div class='email-card'>
                <div class='email-header'>
                    <h1>{$siteName}</h1>
                    <p>Đơn hàng của bạn đã hoàn tất!</p>
                </div>
                <div class='email-body'>
                    <div style='text-align: center;'>
                        <span class='badge-status'>✅ ĐÃ HOÀN TẤT & BÀN GIAO</span>
                    </div>
                    <p>Xin chào quý khách <strong>{$toEmail}</strong>,</p>
                    <p>Đơn hàng <strong>#{$orderId}</strong> của bạn đã được đội ngũ Admin của <strong>{$siteName}</strong> xử lý và bàn giao thành công!</p>
                    
                    {$deliveryBlockHtml}
                    {$upgradeBlockHtml}

                    <div class='order-box'>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr style='border-bottom: 1px solid #e2e8f0;'>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Mã đơn hàng:</td>
                                <td style='padding: 8px 0; font-weight: 700; color: #0f172a; font-family: monospace; font-size: 15px; text-align: right;'>#{$orderId}</td>
                            </tr>
                            <tr style='border-bottom: 1px solid #e2e8f0;'>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Sản phẩm:</td>
                                <td style='padding: 8px 0; font-weight: 600; color: #0f172a; font-size: 14px; text-align: right;'>{$productName}</td>
                            </tr>
                            <tr style='border-bottom: 1px solid #e2e8f0;'>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Gói / Phân loại:</td>
                                <td style='padding: 8px 0; font-weight: 600; color: #0f172a; font-size: 14px; text-align: right;'>{$variantName} (x{$quantity})</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Tổng thanh toán:</td>
                                <td style='padding: 8px 0; font-weight: 800; color: #059669; font-size: 16px; text-align: right;'>{$amount}</td>
                            </tr>
                        </table>
                    </div>

                    <div style='background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 14px; font-size: 13px; color: #92400e; margin: 20px 0;'>
                        <strong>💡 Lưu ý sử dụng:</strong> Vui lòng đổi mật khẩu nếu bạn mua tài khoản riêng, hoặc tuân thủ hướng dẫn sử dụng đi kèm. Mọi đơn hàng tại {$siteName} đều được bảo hành theo đúng cam kết của gói dịch vụ.
                    </div>

                    <div class='btn-group'>
                        <a href='{$successUrl}' class='btn btn-review' target='_blank'>⭐ Đánh giá sản phẩm</a>
                        <a href='{$orderHistoryUrl}' class='btn btn-history' target='_blank'>Xem lịch sử đơn hàng</a>
                    </div>
                </div>
                <div class='email-footer'>
                    &copy; " . date('Y') . " {$siteName}. Cảm ơn bạn đã đồng hành cùng chúng tôi!<br>
                    Hỗ trợ Zalo: <a href='{$zaloUrl}' style='color: #0068ff; font-weight: bold;'>{$zaloPhone}</a> (Ưu tiên) | Telegram: <a href='{$telegramUrl}' style='color: #2563eb;'>@specademy</a> | Website: <a href='" . url() . "' style='color: #2563eb;'>" . SITENAME . "</a>
                </div>
            </div>
        </body>
        </html>";

        try {
            return $mailer->send($fromEmail, $fromName, $toEmail, $subject, $bodyHtml);
        } catch (Throwable $e) {
            error_log('[OrderEmailService] Gửi email đơn hàng hoàn tất thất bại: ' . $e->getMessage());
            return false;
        }
    }
}
