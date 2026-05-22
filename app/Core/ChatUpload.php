<?php

class ChatUpload {
    private const MAX_BYTES = 10 * 1024 * 1024; // 10 MB

    private const ALLOWED_MIMES = [
        // Images
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        // Docs
        'application/pdf', 'application/zip', 'application/x-zip-compressed',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'text/csv',
        // Misc
        'application/json',
    ];

    /**
     * Storage convention: files are written under the configured public
     * filesystem disk. On Hostinger this should be public_uploads, which maps
     * to public_html.
     *
     * Path returned is relative to the configured disk root, e.g.
     *   "chat/2026/05/abcd_1700000000.jpg"
     */
    private const STORE_SUBDIR = 'chat';

    /**
     * Validate and store a chat upload from $_FILES. Returns metadata or null/throws on error.
     * Throws RuntimeException with user-facing message on failure.
     */
    public static function store(array $file): ?array {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Tệp tải lên bị lỗi.');
        }
        if (($file['size'] ?? 0) <= 0 || $file['size'] > self::MAX_BYTES) {
            throw new RuntimeException('Tệp quá lớn (giới hạn 10MB).');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';
        if (!in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new RuntimeException('Định dạng tệp không được phép.');
        }

        $origName  = (string) ($file['name'] ?? 'file');
        $extension = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'bin';
        if (strlen($extension) > 6) $extension = substr($extension, 0, 6);

        $relativeDir = self::STORE_SUBDIR . '/' . date('Y/m');
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

        return [
            'path' => $relativeDir . '/' . $fname,
            'name' => mb_substr($origName, 0, 255),
            'mime' => $mime,
            'size' => (int) $file['size'],
        ];
    }

    /**
     * Absolute path of an attachment for the chatFile endpoint.
     * Resolves a path inside the configured public upload root.
     */
    public static function absolutePath(string $relative): string {
        $relative = ltrim($relative, '/');
        $primary  = self::publicDiskPath($relative);
        if (is_file($primary)) {
            return $primary;
        }
        return $primary;
    }

    /**
     * Resolve a path inside the configured public disk root.
     * Reads config/filesystems.php so it stays in sync with the rest of the app.
     */
    public static function publicDiskPath(string $relative = ''): string {
        $disk = self::publicDiskConfig();
        $root = $disk['root'] ?? public_path();
        return rtrim($root, '/\\') . ($relative === '' ? '' : DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($relative, '/')));
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
}
