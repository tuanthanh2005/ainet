<?php

class Csrf {
    private const SESSION_KEY = '_csrf_token';
    private const FIELD_NAME = 'csrf_token';
    private const HEADER_NAME = 'HTTP_X_CSRF_TOKEN';

    public static function token(): string {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string {
        return '<input type="hidden" name="' . self::FIELD_NAME . '" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function fieldName(): string {
        return self::FIELD_NAME;
    }

    public static function validate(): bool {
        $expected = $_SESSION[self::SESSION_KEY] ?? '';
        if ($expected === '') {
            return false;
        }

        $provided = $_POST[self::FIELD_NAME] ?? $_SERVER[self::HEADER_NAME] ?? '';

        return is_string($provided) && hash_equals($expected, $provided);
    }

    public static function rotate(): void {
        unset($_SESSION[self::SESSION_KEY]);
        self::token();
    }
}
