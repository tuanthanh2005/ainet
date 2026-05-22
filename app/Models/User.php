<?php

class User extends Model {
    public static function findByEmail($email) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, name, email, role, status, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function findWithPasswordById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function create($name, $email, $password, $role = 'user') {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, :status)'
        );

        return $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => $role,
            'status'   => 'active'
        ]);
    }

    public static function updatePassword($id, $password) {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE users SET password = :password WHERE id = :id');
        return $stmt->execute([
            'id'       => $id,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    // ── Google OAuth ────────────────────────────────────────────

    /** Tìm user theo Google ID */
    public static function findByGoogleId(string $googleId): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE google_id = :google_id LIMIT 1');
        $stmt->execute(['google_id' => $googleId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Tạo tài khoản mới từ Google (không cần mật khẩu).
     * Trả về user array sau khi tạo xong.
     */
    public static function createFromGoogle(string $name, string $email, string $googleId, string $avatar = ''): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO users (name, email, password, google_id, avatar, role, status)
             VALUES (:name, :email, :password, :google_id, :avatar, :role, :status)'
        );
        $ok = $stmt->execute([
            'name'      => $name,
            'email'     => $email,
            'password'  => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // random, unusable
            'google_id' => $googleId,
            'avatar'    => $avatar,
            'role'      => 'user',
            'status'    => 'active',
        ]);
        if (!$ok) return null;
        return self::findByEmail($email);
    }

    /**
     * Liên kết Google ID vào tài khoản email đã tồn tại.
     */
    public static function linkGoogleId(int $userId, string $googleId, string $avatar = ''): void {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE users SET google_id = :gid, avatar = COALESCE(NULLIF(avatar,""), :avatar) WHERE id = :id');
        $stmt->execute(['gid' => $googleId, 'avatar' => $avatar, 'id' => $userId]);
    }
}

