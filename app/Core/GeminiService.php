<?php

class GeminiService {
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public static function getApiKey(): string {
        $settings = Setting::getAll();
        $key = trim((string) ($settings['gemini_api_key'] ?? ''));
        if ($key === '') {
            $key = trim((string) (getenv('GEMINI_API_KEY') ?: ''));
        }
        return $key;
    }

    public static function setApiKey(string $key): void {
        Setting::saveAll(['gemini_api_key' => trim($key)]);
    }

    /**
     * Kiểm tra tính hợp lệ và độ trễ của Gemini API Key
     */
    public static function testConnection(?string $apiKey = null): array {
        if ($apiKey === null || trim($apiKey) === '') {
            $apiKey = self::getApiKey();
        }
        $apiKey = trim((string)$apiKey);
        if ($apiKey === '') {
            throw new RuntimeException('Vui lòng nhập Gemini API Key để kiểm tra.');
        }

        $candidates = [
            'gemini-3.1-flash',
            'gemini-3.1-flash-lite',
            'gemini-3.1-pro',
            'gemini-3.8-flash',
            'gemini-3.5-flash',
        ];
        $lastError = '';

        foreach ($candidates as $model) {
            $url = self::API_BASE . urlencode($model) . ':generateContent?key=' . urlencode($apiKey);
            $payload = [
                'contents' => [
                    ['parts' => [['text' => 'Hi, reply with: OK']]]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 10
                ]
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'User-Agent: AiCuaToi-Admin/1.0'
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $start = microtime(true);
            $response = curl_exec($ch);
            $duration = round((microtime(true) - $start) * 1000);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                $lastError = 'Lỗi mạng khi kết nối Google: ' . $err;
                continue;
            }

            $decoded = json_decode((string)$response, true);
            if ($code >= 200 && $code < 300) {
                $reply = trim($decoded['candidates'][0]['content']['parts'][0]['text'] ?? 'OK');
                return [
                    'success' => true,
                    'model' => $model,
                    'latency_ms' => $duration,
                    'reply' => $reply,
                    'message' => 'Kết nối thành công tới Google Gemini AI (' . $model . ')!',
                ];
            }

            $errMsg = $decoded['error']['message'] ?? ('HTTP ' . $code);
            $lastError = $errMsg;
            if (stripos($errMsg, 'API key') !== false || stripos($errMsg, 'PERMISSION_DENIED') !== false) {
                throw new RuntimeException('API Key không hợp lệ hoặc bị từ chối: ' . $errMsg);
            }
        }

        throw new RuntimeException('Không thể kết nối Gemini API: ' . $lastError);
    }

    /**
     * Trả về danh sách model dự phòng theo thứ tự ưu tiên (ưu tiên dòng 3.1)
     */
    public static function resolveModelCandidates(string $model): array {
        $model = strtolower(trim($model));
        if ($model === 'gemini-3.1-flash-lite' || strpos($model, 'lite') !== false) {
            return [
                'gemini-3.1-flash-lite',
                'gemini-3.1-flash',
                'gemini-3.8-flash',
                'gemini-3.5-flash',
            ];
        }

        return [
            'gemini-3.1-flash',
            'gemini-3.1-flash-lite',
            'gemini-3.8-flash',
            'gemini-3.5-flash',
        ];
    }

    /**
     * Gọi Gemini API tạo toàn bộ thông tin sản phẩm và SEO
     * Hỗ trợ kết hợp tiêu đề, định hướng mô tả của Admin và dữ liệu sẵn có trên form
     */
    /**
     * Gọi Gemini API tạo toàn bộ thông tin sản phẩm và SEO
     * Hỗ trợ kết hợp tiêu đề, định hướng mô tả của Admin và dữ liệu sẵn có trên form
     */
    public static function generateProduct(string $title, string $requestedModel = 'gemini-3.1-flash', ?string $currentCategory = null, string $customPrompt = '', array $existingData = [], int $variantCount = 0): array {
        $apiKey = self::getApiKey();
        if ($apiKey === '') {
            throw new RuntimeException('Chưa có Gemini API Key. Vui lòng bấm vào biểu tượng chìa khóa bên cạnh để nhập API Key từ Google AI Studio (aistudio.google.com).');
        }

        $title = trim($title);
        $customPrompt = trim($customPrompt);
        if ($title === '' && $customPrompt === '') {
            throw new RuntimeException('Vui lòng nhập Tên sản phẩm hoặc Mô tả/Yêu cầu trước khi tạo tự động.');
        }

        // Tự động phân tích xem admin có liệt kê sẵn danh sách các gói trong mô tả không
        $extractedVariants = self::extractVariantsFromText($customPrompt . "\n" . $title);
        $extractedCount = count($extractedVariants);

        $categories = Category::getAll();
        $catDescriptions = [];
        foreach ($categories as $cat) {
            $catDescriptions[] = "- Tên: " . ($cat['name'] ?? '') . ", slug: \"" . ($cat['slug'] ?? '') . "\"";
        }
        $catListText = implode("\n", $catDescriptions);

        $extraContext = [];
        if ($customPrompt !== '') {
            $extraContext[] = "=== YÊU CẦU & ĐỊNH HƯỚNG MÔ TẢ CỦA ADMIN (RẤT QUAN TRỌNG) ===\n" . $customPrompt;
        }

        if (!empty($existingData)) {
            $existingLines = [];
            if (!empty($existingData['price'])) $existingLines[] = "- Giá bán mong muốn: " . number_format((float)$existingData['price']) . " VNĐ";
            if (!empty($existingData['original_price'])) $existingLines[] = "- Giá gốc: " . number_format((float)$existingData['original_price']) . " VNĐ";
            if (!empty($existingData['desc'])) $existingLines[] = "- Ý tưởng mô tả ngắn: " . $existingData['desc'];
            if (!empty($existingData['category_name'])) $existingLines[] = "- Danh mục đang chọn: " . $existingData['category_name'];
            if (!empty($existingLines)) {
                $extraContext[] = "=== DỮ LIỆU ĐANG CÓ TRÊN FORM ===\n" . implode("\n", $existingLines);
            }
        }
        $extraContextText = !empty($extraContext) ? ("\n\n" . implode("\n\n", $extraContext) . "\n") : '';

        $effectiveTitle = $title !== '' ? $title : ('Dịch vụ số / Tài khoản theo yêu cầu: ' . substr($customPrompt, 0, 80));

        if ($extractedCount > 0) {
            $variantInstruction = "3. CÁC GÓI / BIẾN THỂ (\"variants\"): ADMIN ĐÃ LIỆT KÊ RÕ {$extractedCount} GÓI TRONG MÔ TẢ. BẮT BUỘC PHẢI TẠO ĐẦY ĐỦ 100% CẢ {$extractedCount} GÓI NÀY, TUYỆT ĐỐI KHÔNG ĐƯỢC BỎ BỚT HOẶC RÚT GỌN! Chuyển đổi chính xác giá tiền sang số nguyên VNĐ (VD: 1980k -> 1980000, 470k -> 470000). Giá gốc tính cao hơn giá bán 20-35%. Nếu có 'random số lượng' thì gán kho ngẫu nhiên 20-99.";
        } elseif ($variantCount > 0) {
            $variantInstruction = "3. CÁC GÓI / BIẾN THỂ (\"variants\"): Hãy tạo đúng {$variantCount} gói dịch vụ thực tế phù hợp với mô tả và sản phẩm (VD: Gói 1 Tháng, Gói 3 Tháng, Gói 6 Tháng, Gói 1 Năm...), có giá bán, giá gốc hợp lý.";
        } else {
            $variantInstruction = "3. CÁC GÓI / BIẾN THỂ (\"variants\"): Tạo các gói dịch vụ thực tế phù hợp với mô tả (VD: Gói 1 Tháng, Gói 3 Tháng, Gói 1 Năm hoặc Tài khoản cấp sẵn / Nâng cấp chính chủ), có giá bán, giá gốc hợp lý.";
        }

        $prompt = <<<PROMPT
Bạn là chuyên gia marketing thương mại điện tử chuyên về sản phẩm phần mềm, tài khoản AI Premium, dịch vụ số tại Việt Nam (aicuatoi.net).
Hãy tạo nội dung sản phẩm hoàn chỉnh, lôi cuốn, bán chạy, chuẩn phong cách Việt Nam và chuẩn SEO Google dựa trên thông tin sau:
- Tên / Chủ đề sản phẩm: "{$effectiveTitle}"
{$extraContextText}
Danh sách danh mục có sẵn trên website (hãy chọn slug danh mục phù hợp nhất):
{$catListText}

HƯỚNG DẪN XỬ LÝ (QUAN TRỌNG):
1. ĐA DẠNG NỘI DUNG & BÁM SÁT MÔ TẢ ADMIN: Không chỉ rập khuôn theo tiêu đề ngắn, mà hãy phát triển sâu theo mọi ý tưởng, định hướng, ưu đãi, quà tặng, thời hạn, chính sách bảo hành được Admin mô tả trong phần YÊU CẦU & ĐỊNH HƯỚNG.
2. NỘI DUNG BÀI VIẾT ("description"): Viết bằng mã HTML đẹp mắt (dùng <h2>, <h3>, <ul>, <li>, <strong>, bảng tính năng, cam kết bảo hành, lưu ý sử dụng).
{$variantInstruction}
4. ĐIỀU CHỈNH TIÊU ĐỀ ("title"): Nếu tiêu đề ban đầu chưa đủ hấp dẫn hoặc chưa có, hãy tạo một tiêu đề thương mại điện tử thật chuyên nghiệp và thu hút.

Yêu cầu trả về DUY NHẤT một chuỗi JSON hợp lệ (không kèm markdown ```json hay giải thích nào khác) với cấu trúc chính xác sau:
{
  "title": "Tên sản phẩm đầy đủ và hấp dẫn",
  "category_slug": "slug_cua_danh_muc_phu_hop_nhat_trong_danh_sach_tren",
  "price": 150000,
  "original_price": 250000,
  "desc": "Mô tả ngắn gọn khoảng 10-15 từ (VD: Cấp tốc 5 phút, Bảo hành trọn đời, Kích hoạt chính chủ)",
  "card_features": [
    "Dòng tính năng 1 (ngắn gọn dưới 6 từ hiển thị trên card)",
    "Dòng tính năng 2",
    "Dòng tính năng 3",
    "Dòng tính năng 4"
  ],
  "description": "Nội dung bài viết chi tiết bằng mã HTML (hấp dẫn, có thẻ <h2>, <h3>, <ul>, <li>, <strong>, bảng tính năng, quyền lợi và chính sách bảo hành rõ ràng, không dùng <html> hay <body>)",
  "seo_slug": "duong-dan-than-thien-khong-dau-cach-nhau-bang-dau-gach-ngang",
  "seo_title": "Tiêu đề SEO Google từ 50-60 ký tự",
  "seo_description": "Mô tả SEO Google từ 150-160 ký tự súc tích, kích thích click",
  "seo_keywords": "tu khoa 1, tu khoa 2, tu khoa 3, mua tai khoan...",
  "variants": [
    {
      "name": "Tên gói dịch vụ",
      "price": 150000,
      "original_price": 250000,
      "stock": 99,
      "is_upgrade": 0,
      "require_password": 1
    }
  ]
}
PROMPT;

        $candidates = self::resolveModelCandidates($requestedModel);
        $lastError = 'Không thể kết nối đến Gemini API.';

        foreach ($candidates as $modelName) {
            try {
                $rawResult = self::callGeminiApi($modelName, $apiKey, $prompt);
                if (!empty($rawResult)) {
                    // Nếu Admin đã cung cấp danh sách gói cụ thể, ưu tiên bảo toàn 100%
                    if ($extractedCount > 0) {
                        $aiVariants = is_array($rawResult['variants'] ?? null) ? $rawResult['variants'] : [];
                        if (count($aiVariants) < $extractedCount) {
                            $rawResult['variants'] = $extractedVariants;
                        } else {
                            $rawResult['variants'] = $extractedVariants;
                        }
                    }

                    // Tự động đồng bộ giá bán của sản phẩm chính bằng giá của gói rẻ nhất
                    if (!empty($rawResult['variants']) && is_array($rawResult['variants'])) {
                        $prices = array_filter(array_column($rawResult['variants'], 'price'), fn($p) => is_numeric($p) && $p > 0);
                        if (!empty($prices)) {
                            $minPrice = min($prices);
                            if (empty($rawResult['price']) || $rawResult['price'] <= 0 || $rawResult['price'] > $minPrice) {
                                $rawResult['price'] = (int)$minPrice;
                                foreach ($rawResult['variants'] as $v) {
                                    if (($v['price'] ?? 0) == $minPrice && !empty($v['original_price'])) {
                                        $rawResult['original_price'] = (int)$v['original_price'];
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    $rawResult['used_model'] = $modelName;
                    return $rawResult;
                }
            } catch (Throwable $e) {
                $lastError = $e->getMessage();
                // Nếu model 404 (chưa public hoặc deprecated), thử model dự phòng tiếp theo
                if (stripos($lastError, 'not found') !== false || stripos($lastError, '404') !== false) {
                    continue;
                }
                // Nếu API key sai hoặc hết hạn mức thì dừng báo lỗi ngay
                if (stripos($lastError, 'API key') !== false || stripos($lastError, 'PERMISSION_DENIED') !== false || stripos($lastError, 'RESOURCE_EXHAUSTED') !== false) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException($lastError);
    }

    /**
     * Tự động trích xuất các gói dịch vụ (variants) từ mô tả hoặc yêu cầu của Admin
     * Hỗ trợ định dạng: "Tên gói | Giá | Kho" hoặc "Tên gói: Giá" hoặc dạng copy từ Telegram/Zalo
     */
    public static function extractVariantsFromText(string $text): array {
        $hasRandomStock = (bool)preg_match('/random\s*(s[ôố]\s*l[ưư][ơợ]ng|kho|stock)/iu', $text);

        // Chuẩn hóa dòng
        $cleanText = str_replace(["\r\n", "\r"], "\n", $text);
        $rawLines = explode("\n", $cleanText);
        $candidateLines = [];

        foreach ($rawLines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            // Bỏ qua câu chỉ thị như "có thể random số lượng"
            if (preg_match('/^(có\s*thể|random|lưu\s*ý|chú\s*ý|ghi\s*chú|note|yêu\s*cầu)\b/iu', $line)) {
                continue;
            }

            // Nếu trên cùng 1 dòng có nhiều gói phân cách bằng khoảng trắng hoặc icon gói hàng 📦
            if (substr_count($line, '|') >= 3) {
                $subItems = preg_split('/(?<=[📦\d\w\x{221e}…\.\)])\s{2,}(?=[A-ZÀ-Ỹa-z0-9])/u', $line);
                if (count($subItems) > 1) {
                    foreach ($subItems as $sub) {
                        if (trim($sub) !== '') $candidateLines[] = trim($sub);
                    }
                    continue;
                }
            }

            $candidateLines[] = $line;
        }

        $variants = [];

        foreach ($candidateLines as $cand) {
            // Định dạng 1: Phân cách bằng dấu '|' (Ví dụ: "Slot Veo3 Ultra Riêng 25K Credits 1 tháng BHF | 1980k | 📦 ∞")
            if (str_contains($cand, '|')) {
                $parts = array_map('trim', explode('|', $cand));
                if (count($parts) >= 2) {
                    $name = $parts[0];
                    $priceRaw = $parts[1];
                    $stockRaw = $parts[2] ?? '';

                    $price = self::parsePriceNumber($priceRaw);
                    if ($price > 0 && mb_strlen($name) >= 2) {
                        $orig = self::parsePriceNumber($parts[3] ?? '') ?: (int)(ceil(($price * 1.3) / 10000) * 10000);
                        $stock = 99;
                        if ($hasRandomStock) {
                            $stock = rand(20, 95);
                        } elseif (preg_match('/(\d+)/', $stockRaw, $sm)) {
                            $stock = (int)$sm[1];
                        }

                        $isUpgrade = (bool)preg_match('/(nâng\s*cấp|nang\s*cap|chính\s*chủ|chinh\s*chu|upgrade)/iu', $name);

                        $variants[] = [
                            'name' => self::cleanVariantName($name),
                            'price' => $price,
                            'original_price' => $orig,
                            'stock' => $stock,
                            'is_upgrade' => $isUpgrade ? 1 : 0,
                            'require_password' => 1
                        ];
                        continue;
                    }
                }
            }

            // Định dạng 2: Phân cách bằng dấu ':' hoặc '-' (Ví dụ: "- Gói 1 tháng: 150k")
            if (preg_match('/^[-*•]?\s*(.+?)[\s:=-]+([0-9\.,]+\s*(?:k|tr|m|đ|₫|vnđ|vnd)?)\s*(?:[-|]\s*kho\s*(\d+|∞))?/iu', $cand, $m)) {
                $name = trim($m[1], " \t\n\r\0\x0B-:*•");
                $priceRaw = $m[2];
                $stockRaw = $m[3] ?? '';

                $price = self::parsePriceNumber($priceRaw);
                if ($price >= 1000 && mb_strlen($name) >= 2 && !preg_match('/^(giá|giá bán|giá gốc)/iu', $name)) {
                    $orig = (int)(ceil(($price * 1.3) / 10000) * 10000);
                    $stock = $hasRandomStock ? rand(20, 95) : 99;
                    if (is_numeric($stockRaw)) {
                        $stock = (int)$stockRaw;
                    }
                    $isUpgrade = (bool)preg_match('/(nâng\s*cấp|nang\s*cap|chính\s*chủ|chinh\s*chu|upgrade)/iu', $name);

                    $variants[] = [
                        'name' => self::cleanVariantName($name),
                        'price' => $price,
                        'original_price' => $orig,
                        'stock' => $stock,
                        'is_upgrade' => $isUpgrade ? 1 : 0,
                        'require_password' => 1
                    ];
                }
            }
        }

        return $variants;
    }

    private static function parsePriceNumber(string $raw): int {
        $raw = trim($raw);
        if ($raw === '') return 0;

        if (preg_match('/([\d\.,]+)\s*([kmtrđ₫vnđvnd]*)/iu', $raw, $m)) {
            $numPart = $m[1];
            $unit = mb_strtolower(trim($m[2] ?? ''));

            if (str_contains($numPart, '.') || str_contains($numPart, ',')) {
                if (preg_match('/^\d{1,3}(?:[\.,]\d{3})+$/', $numPart)) {
                    $valFloat = (float)str_replace(['.', ','], '', $numPart);
                } else {
                    $valFloat = (float)str_replace(',', '.', $numPart);
                }
            } else {
                $valFloat = (float)$numPart;
            }

            if (str_starts_with($unit, 'k')) {
                return (int)round($valFloat * 1000);
            } elseif (str_starts_with($unit, 'tr') || str_starts_with($unit, 'm')) {
                return (int)round($valFloat * 1000000);
            } else {
                if ($valFloat > 0 && $valFloat < 1000 && ($unit === '' || $unit === 'k')) {
                    return (int)round($valFloat * 1000);
                }
                return (int)round($valFloat);
            }
        }
        return 0;
    }

    private static function cleanVariantName(string $name): string {
        $name = trim($name, " \t\n\r\0\x0B-|:•*");
        $name = preg_replace('/^[-*•\d\.\)]\s*/u', '', $name);
        return trim($name);
    }

    private static function callGeminiApi(string $model, string $apiKey, string $prompt): array {
        $url = self::API_BASE . urlencode($model) . ':generateContent?key=' . urlencode($apiKey);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: AiCuaToi-Admin/1.0'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('Lỗi mạng khi gọi Gemini (' . $model . '): ' . $err);
        }

        $decoded = json_decode((string) $response, true);
        if ($code < 200 || $code >= 300) {
            $msg = $decoded['error']['message'] ?? ('HTTP ' . $code . ': ' . $response);
            throw new RuntimeException('Gemini API (' . $model . ') lỗi: ' . $msg);
        }

        $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if (trim($text) === '') {
            throw new RuntimeException('Gemini API trả về nội dung rỗng.');
        }

        // Loại bỏ markdown ```json ... ``` nếu có
        $text = trim($text);
        if (preg_match('/^```(?:json)?\s*([\s\S]*?)\s*```$/i', $text, $m)) {
            $text = trim($m[1]);
        }

        $data = json_decode($text, true);
        if (!is_array($data)) {
            throw new RuntimeException('Dữ liệu Gemini trả về không phải JSON hợp lệ: ' . substr($text, 0, 150));
        }

        return $data;
    }
}
