<?php

/**
 * Generic file uploader for assets that can be served publicly.
 * Files land under the configured public filesystem disk and become reachable
 * through that disk's configured URL.
 *
 * Use ChatUpload for chat attachments (private, served via ACL endpoint).
 */
class Upload {
    private const MAX_BYTES = 10 * 1024 * 1024; // 10 MB

    public const IMAGE_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
    ];

    /**
     * Store an uploaded file. Returns a public URL (absolute) and meta.
     * @param array $file         Element from $_FILES
     * @param string $subdir      Logical subdir e.g. "blogs", "products"
     * @param array|null $allowed Whitelist of MIME types (default: images)
     * @return array{url:string,path:string,name:string,mime:string,size:int}
     */
    public static function store(array $file, string $subdir, ?array $allowed = null): array {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new RuntimeException('Chưa chọn tệp.');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Tệp tải lên bị lỗi.');
        }
        if (($file['size'] ?? 0) <= 0 || $file['size'] > self::MAX_BYTES) {
            throw new RuntimeException('Tệp quá lớn (giới hạn 10MB).');
        }

        $allowed = $allowed ?: self::IMAGE_MIMES;
        $finfo   = new finfo(FILEINFO_MIME_TYPE);
        $mime    = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';
        if (!in_array($mime, $allowed, true)) {
            throw new RuntimeException('Định dạng tệp không được phép.');
        }

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: self::extFromMime($mime);
        if (strlen($extension) > 6) $extension = substr($extension, 0, 6);

        $relativeDir = trim($subdir, '/') . '/' . date('Y/m');
        $absoluteDir = self::publicDiskPath($relativeDir);
        if (!is_dir($absoluteDir)) {
            @mkdir($absoluteDir, 0775, true);
        }
        if (!is_dir($absoluteDir) || !is_writable($absoluteDir)) {
            throw new RuntimeException('Không thể ghi tệp lên server.');
        }

        $fname = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $dest  = $absoluteDir . DIRECTORY_SEPARATOR . $fname;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Lưu tệp thất bại.');
        }

        $relative = $relativeDir . '/' . $fname;
        return [
            'url'  => self::publicDiskUrl($relative),
            'path' => $relative,
            'name' => mb_substr((string) ($file['name'] ?? $fname), 0, 255),
            'mime' => $mime,
            'size' => (int) $file['size'],
        ];
    }

    public static function publicDiskPath(string $relative = ''): string {
        $disk = self::publicDiskConfig();
        $root = $disk['root'] ?? public_path();
        return rtrim($root, '/\\') . ($relative === '' ? '' : DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($relative, '/')));
    }

    public static function publicDiskUrl(string $relative = ''): string {
        $disk = self::publicDiskConfig();
        $base = $disk['url'] ?? rtrim(URLROOT, '/');
        return rtrim($base, '/') . ($relative === '' ? '' : '/' . ltrim($relative, '/'));
    }

    private static function publicDiskConfig(): array {
        static $disk = null;
        if ($disk === null) {
            $config = @include base_path('config/filesystems.php');
            $diskName = is_array($config) ? ($config['default'] ?? 'public') : 'public';
            if ($diskName === 'local') {
                $diskName = 'public';
            }
            $disk = is_array($config) && !empty($config['disks'][$diskName])
                ? $config['disks'][$diskName]
                : ($config['disks']['public'] ?? ['root' => public_path()]);
        }
        return $disk;
    }

    private static function extFromMime(string $mime): string {
        return [
            'image/jpeg'   => 'jpg',
            'image/png'    => 'png',
            'image/gif'    => 'gif',
            'image/webp'   => 'webp',
            'image/svg+xml'=> 'svg',
        ][$mime] ?? 'bin';
    }

    /**
     * Sanitize HTML coming from a rich text editor: only keep a whitelist of
     * tags and strip dangerous attributes (event handlers, javascript: URLs).
     */
    public static function sanitizeHtml(string $html): string {
        $allowed = '<p><br><strong><b><em><i><u><s><h1><h2><h3><h4><ul><ol><li><blockquote><a><img><span>';
        $clean = strip_tags($html, $allowed);
        // Remove any inline event handlers
        $clean = preg_replace('/\son[a-z]+\s*=\s*"[^"]*"/i', '', $clean);
        $clean = preg_replace("/\son[a-z]+\s*=\s*'[^']*'/i", '', $clean);
        // Strip javascript:/data: schemes from href/src
        $clean = preg_replace('/(href|src)\s*=\s*"\s*(javascript|data):[^"]*"/i', '$1=""', $clean);
        $clean = preg_replace("/(href|src)\s*=\s*'\s*(javascript|data):[^']*'/i", "$1=''", $clean);
        // style attribute can be used for tracking pixels / xss
        $clean = preg_replace('/\sstyle\s*=\s*"[^"]*"/i', '', $clean);
        $clean = preg_replace("/\sstyle\s*=\s*'[^']*'/i", '', $clean);
        return trim($clean);
    }
}
