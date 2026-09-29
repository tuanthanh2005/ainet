<?php

class Captcha {
    private const SESSION_KEY = 'register_captcha_code';
    private const SESSION_TIME_KEY = 'register_captcha_time';
    private const TTL = 600; // 10 minutes

    /**
     * Characters used for captcha generation.
     * Excludes ambiguous characters (0, O, 1, I, L, B, 8).
     */
    private const CHARS = '2345679ACDEFGHJKMNPRTUVWXYZ';

    /**
     * Generate a new captcha code and save to session.
     */
    public static function generate(int $length = 5): string {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $charsLen = strlen(self::CHARS);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= self::CHARS[random_int(0, $charsLen - 1)];
        }

        $_SESSION[self::SESSION_KEY] = $code;
        $_SESSION[self::SESSION_TIME_KEY] = time();

        return $code;
    }

    /**
     * Verify captcha input against the session code.
     * Clears the session code on verification to prevent replay/brute-force attacks.
     */
    public static function verify(?string $input): bool {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $expected = $_SESSION[self::SESSION_KEY] ?? '';
        $time = (int)($_SESSION[self::SESSION_TIME_KEY] ?? 0);

        // Always clear after an attempt to prevent replay attacks
        self::clear();

        if ($expected === '' || ($time > 0 && (time() - $time) > self::TTL)) {
            return false;
        }

        if (empty($input) || !is_string($input)) {
            return false;
        }

        return hash_equals(strtoupper($expected), strtoupper(trim($input)));
    }

    /**
     * Clear captcha from session.
     */
    public static function clear(): void {
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::SESSION_TIME_KEY]);
    }

    /**
     * Render captcha image (PNG if GD is enabled, SVG fallback).
     */
    public static function render(): void {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $code = self::generate(5);
        $width = 130;
        $height = 42;

        if (function_exists('imagecreatetruecolor')) {
            self::renderGdPng($code, $width, $height);
        } else {
            self::renderSvg($code, $width, $height);
        }
        exit;
    }

    /**
     * Render using GD TrueColor PNG.
     */
    private static function renderGdPng(string $code, int $width, int $height): void {
        $im = imagecreatetruecolor($width, $height);

        // Background
        $bg = imagecolorallocate($im, 248, 250, 252);
        imagefilledrectangle($im, 0, 0, $width, $height, $bg);

        // Border
        $border = imagecolorallocate($im, 226, 232, 240);
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $border);

        // Noise dots
        for ($i = 0; $i < 45; $i++) {
            $dotCol = imagecolorallocate($im, rand(200, 230), rand(200, 230), rand(200, 230));
            imagesetpixel($im, rand(1, $width - 2), rand(1, $height - 2), $dotCol);
        }

        // Noise lines
        for ($i = 0; $i < 3; $i++) {
            $lineCol = imagecolorallocate($im, rand(190, 225), rand(190, 225), rand(190, 225));
            imageline($im, rand(0, 30), rand(0, $height), rand($width - 30, $width), rand(0, $height), $lineCol);
        }

        $palette = [
            [15, 23, 42],    // Slate 900
            [29, 78, 216],   // Blue 700
            [109, 40, 217],  // Purple 700
            [4, 120, 87],    // Emerald 700
            [185, 28, 28],   // Red 700
            [194, 65, 12],   // Orange 700
            [14, 116, 144]   // Cyan 700
        ];

        $charWidth = 10;
        $charHeight = 15;
        $scale = 2.0;
        $targetW = (int)($charWidth * $scale);
        $targetH = (int)($charHeight * $scale);
        $spacing = 4;

        $totalTextWidth = strlen($code) * ($targetW + $spacing) - $spacing;
        $startX = (int)(($width - $totalTextWidth) / 2);

        imagealphablending($im, true);

        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            $colorArr = $palette[array_rand($palette)];

            $charIm = imagecreatetruecolor($charWidth, $charHeight);
            imagealphablending($charIm, false);
            imagesavealpha($charIm, true);
            $transparent = imagecolorallocatealpha($charIm, 255, 255, 255, 127);
            imagefill($charIm, 0, 0, $transparent);

            $tc = imagecolorallocate($charIm, $colorArr[0], $colorArr[1], $colorArr[2]);
            imagestring($charIm, 5, 0, 0, $char, $tc);
            imagestring($charIm, 5, 1, 0, $char, $tc); // bold

            $destX = $startX + $i * ($targetW + $spacing);
            $destY = (int)(($height - $targetH) / 2) + rand(-2, 2);

            imagecopyresampled($im, $charIm, $destX, $destY, 0, 0, $targetW, $targetH, $charWidth, $charHeight);
            imagedestroy($charIm);
        }

        // Foreground curve to deter OCR
        $curveCol = imagecolorallocate($im, rand(160, 200), rand(160, 200), rand(180, 220));
        imagearc($im, (int)($width / 2), (int)($height / 2), $width - 12, $height - 10, 0, 360, $curveCol);

        if (!headers_sent()) {
            header('Content-Type: image/png');
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        imagepng($im);
        imagedestroy($im);
    }

    /**
     * Fallback SVG renderer if GD is not present.
     */
    private static function renderSvg(string $code, int $width, int $height): void {
        if (!headers_sent()) {
            header('Content-Type: image/svg+xml');
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        $escaped = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
        echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <rect width="100%" height="100%" fill="#f8fafc" stroke="#e2e8f0"/>
  <line x1="10" y1="10" x2="120" y2="35" stroke="#cbd5e1" stroke-width="1.5"/>
  <line x1="15" y1="32" x2="115" y2="12" stroke="#e2e8f0" stroke-width="1.5"/>
  <text x="50%" y="62%" font-family="monospace, monospace" font-size="22" font-weight="bold" fill="#1e293b" letter-spacing="6" text-anchor="middle" dominant-baseline="middle">{$escaped}</text>
</svg>
SVG;
    }
}
