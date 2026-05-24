<?php

/**
 * TelegramService — Gửi thông báo đến Telegram Bot của Admin
 *
 * Sử dụng:
 *   TelegramService::notifyNewOrder($order);
 *   TelegramService::notifyOrderCompleted($order);
 *   TelegramService::notifyNewChatMessage($user, $body, $attachmentName);
 *   TelegramService::sendRaw('Hello *world*!');
 */
class TelegramService {

    /** Lấy settings từ DB và trả về [bot_token, chat_id] */
    private static function getConfig(): array {
        try {
            $settings = Setting::getAll();
        } catch (Throwable $e) {
            self::log("getConfig error: " . $e->getMessage());
            return ['', ''];
        }
        $token  = trim((string) ($settings['telegram_bot_token'] ?? ''));
        $chatId = trim((string) ($settings['telegram_chat_id'] ?? ''));
        return [$token, $chatId];
    }

    /**
     * Ghi log Telegram vào storage/logs/telegram.log
     */
    public static function log(string $message): void {
        try {
            $root = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
            $dir = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $file = $dir . DIRECTORY_SEPARATOR . 'telegram.log';
            @file_put_contents($file, '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n", FILE_APPEND);
        } catch (Throwable $ignored) {}
    }

    /**
     * Gửi raw Markdown message đến admin Telegram.
     * Trả về true nếu thành công, false nếu không cấu hình hoặc lỗi.
     */
    public static function sendRaw(string $text): bool {
        [$token, $chatId] = self::getConfig();
        if ($token === '' || $chatId === '') {
            self::log("sendRaw failed: Token or Chat ID is empty.");
            return false;
        }
        return self::callApi($token, $chatId, $text);
    }

    /**
     * Thông báo đơn hàng MỚI (pending) vừa được tạo.
     */
    public static function notifyNewOrder(array $order): void {
        try {
            $id          = htmlspecialchars_decode($order['id'] ?? '');
            $productName = $order['product_name'] ?? 'Không rõ';
            $variantName = $order['variant_name'] ?? '';
            $amount      = number_format((float) ($order['amount'] ?? 0), 0, ',', '.');
            $qty         = (int) ($order['quantity'] ?? 1);
            $email       = $order['customer_email'] ?? '';
            $phone       = $order['phone'] ?? '—';
            $note        = trim($order['note'] ?? '');
            $time        = date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now'));

            $lines = [
                "🛒 *ĐƠN HÀNG MỚI* — `{$id}`",
                "",
                "📦 *Sản phẩm:* " . self::esc($productName),
                "🎯 *Gói:* " . self::esc($variantName ?: '—') . ($qty > 1 ? " × {$qty}" : ""),
                "💰 *Tổng:* {$amount}đ",
                "",
                "👤 *Khách:* " . self::esc($order['name'] ?? $email),
                "📧 *Email:* " . self::esc($email),
                "📱 *SĐT:* " . self::esc($phone),
            ];

            if ($note !== '') {
                $lines[] = "📝 *Ghi chú:* " . self::esc(mb_substr($note, 0, 200));
            }

            // Nếu là đơn nâng cấp tài khoản
            if (!empty($order['upgrade_email'])) {
                $lines[] = "";
                $lines[] = "🔑 *Tài khoản nâng cấp:* " . self::esc($order['upgrade_email']);
                if (!empty($order['upgrade_pass'])) {
                    $lines[] = "🔒 *Mật khẩu:* " . self::esc($order['upgrade_pass']);
                }
                if (!empty($order['upgrade_link'])) {
                    $lines[] = "🔗 *Liên hệ:* " . self::esc($order['upgrade_link']);
                }
            }

            $lines[] = "";
            $lines[] = "⏰ {$time}";
            $lines[] = "⚠️ _Chờ thanh toán..._";

            self::sendRaw(implode("\n", $lines));
        } catch (Throwable $e) {
            self::log("notifyNewOrder error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     * Thông báo đơn hàng đã HOÀN THÀNH (thanh toán xác nhận).
     */
    public static function notifyOrderCompleted(array $order): void {
        try {
            $id          = $order['id'] ?? '';
            $productName = $order['product_name'] ?? 'Không rõ';
            $variantName = $order['variant_name'] ?? '';
            $amount      = number_format((float) ($order['amount'] ?? 0), 0, ',', '.');
            $qty         = (int) ($order['quantity'] ?? 1);
            $email       = $order['customer_email'] ?? '';
            $phone       = $order['phone'] ?? '—';
            $txId        = $order['transaction_id'] ?? '';
            $time        = date('d/m/Y H:i');

            $status = $order['status'] ?? 'completed';
            $title = $status === 'processing' 
                ? "⏳ *THANH TOÁN THÀNG CÔNG - CHỜ XỬ LÝ* — `{$id}`" 
                : "✅ *THANH TOÁN THÀNH CÔNG* — `{$id}`";

            $lines = [
                $title,
                "",
                "📦 *Sản phẩm:* " . self::esc($productName),
                "🎯 *Gói:* " . self::esc($variantName ?: '—') . ($qty > 1 ? " × {$qty}" : ""),
                "💰 *Số tiền:* {$amount}đ",
                $txId ? "🔖 *Mã GD:* `{$txId}`" : "",
                "",
                "👤 *Khách:* " . self::esc($order['name'] ?? $email),
                "📧 *Email:* " . self::esc($email),
                "📱 *SĐT:* " . self::esc($phone),
            ];

            // Giao hàng tự động
            $delivered = $order['delivered_items'] ?? [];
            if (!empty($delivered)) {
                $lines[] = "";
                $lines[] = "🎁 *Đã giao tự động:* " . count($delivered) . " sản phẩm";
                foreach (array_slice((array) $delivered, 0, 3) as $item) {
                    $lines[] = "  • `" . self::esc(mb_substr((string) $item, 0, 80)) . "`";
                }
                if (count($delivered) > 3) {
                    $lines[] = "  + " . (count($delivered) - 3) . " mục khác...";
                }
            } else {
                $lines[] = "";
                $lines[] = $status === 'processing'
                    ? "⏳ _Đang chờ xử lý thủ công (không có kho)_"
                    : "⏳ _Chưa giao hàng tự động (không có kho)_";
            }

            $lines[] = "";
            $lines[] = "⏰ {$time}";

            // Remove empty lines from tx ID if empty
            $lines = array_filter($lines, fn($l) => $l !== "");
            self::sendRaw(implode("\n", array_values($lines)));
        } catch (Throwable $e) {
            self::log("notifyOrderCompleted error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     * Thông báo khách hàng vừa gửi tin nhắn chat.
     */
    public static function notifyNewChatMessage(array $user, string $body, ?string $attachmentName = null): void {
        try {
            $name  = $user['name'] ?? 'Khách hàng';
            $email = $user['email'] ?? '';
            $time  = date('d/m/Y H:i');

            $lines = [
                "💬 *TIN NHẮN MỚI* từ " . self::esc($name),
                "",
                "📧 " . self::esc($email),
            ];

            if ($body !== '') {
                $preview = mb_substr($body, 0, 300);
                if (mb_strlen($body) > 300) $preview .= '...';
                $lines[] = "💭 \"" . self::esc($preview) . "\"";
            }

            if ($attachmentName) {
                $lines[] = "📎 _Tệp đính kèm: " . self::esc($attachmentName) . "_";
            }

            $lines[] = "";
            $lines[] = "⏰ {$time}";
            $lines[] = "_Trả lời tại trang admin chat_";

            if (!self::sendRaw(implode("\n", $lines))) {
                self::log('Telegram new chat notification failed or is not configured.');
            }
        } catch (Throwable $e) {
            self::log('Telegram new chat notification error: ' . $e->getMessage());
        }
    }

    /**
     * Gửi tin nhắn test đến Telegram để kiểm tra cấu hình.
     * Trả về mảng ['success' => bool, 'message' => string]
     */
    public static function sendTest(): array {
        [$token, $chatId] = self::getConfig();
        if ($token === '') {
            return ['success' => false, 'message' => 'Bot Token chưa được cấu hình.'];
        }
        if ($chatId === '') {
            return ['success' => false, 'message' => 'Chat ID chưa được cấu hình.'];
        }

        $text = "🤖 *Test kết nối thành công!*\n\n✅ Bot Telegram đã được cấu hình đúng và sẵn sàng nhận thông báo từ website.\n\n⏰ " . date('d/m/Y H:i:s');
        $ok = self::callApi($token, $chatId, $text);
        return [
            'success' => $ok,
            'message' => $ok ? 'Gửi test thành công! Kiểm tra Telegram của bạn.' : 'Gửi thất bại. Kiểm tra lại Bot Token và Chat ID (xem logs trong storage/logs/telegram.log).',
        ];
    }

    /**
     * Escape ký tự đặc biệt cho MarkdownV2 của Telegram.
     */
    private static function esc(string $text): string {
        // Escape legacy Markdown special characters: *, _, `
        return str_replace(['*', '_', '`'], ['\*', '\_', '\`'], $text);
    }

    /**
     * Gọi Telegram Bot API sendMessage.
     */
    private static function callApi(string $token, string $chatId, string $text): bool {
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $payload = json_encode([
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ]);

        self::log("Sending to Telegram. Chat ID: {$chatId}, Text: " . str_replace("\n", " ", substr($text, 0, 100)) . "...");

        // Try cURL first if available
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $response = curl_exec($ch);
            if ($response === false) {
                $err = curl_error($ch);
                self::log("Telegram cURL error: " . $err);
                curl_close($ch);
            } else {
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                self::log("Telegram cURL response code: " . $httpCode . ", Response: " . $response);
                $decoded = json_decode($response, true);
                if (($decoded['ok'] ?? false) === true) {
                    return true;
                }
            }
        } else {
            self::log("cURL is NOT available, falling back to file_get_contents.");
        }

        // Fallback to stream context file_get_contents
        $context = stream_context_create([
            'http' => [
                'method'          => 'POST',
                'header'          => "Content-Type: application/json\r\nContent-Length: " . strlen($payload),
                'content'         => $payload,
                'timeout'         => 8,
                'ignore_errors'   => true,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);
        try {
            $response = @file_get_contents($url, false, $context);
            if ($response === false) {
                self::log("Telegram file_get_contents returned false.");
                return false;
            }
            self::log("Telegram file_get_contents response: " . $response);
            $decoded = json_decode($response, true);
            return ($decoded['ok'] ?? false) === true;
        } catch (Throwable $e) {
            self::log("Telegram file_get_contents error: " . $e->getMessage());
            return false;
        }
    }
}
