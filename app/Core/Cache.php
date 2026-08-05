<?php

/**
 * Optional in-memory cache. It never writes to the filesystem, so it also
 * works on shared hosts where application storage is read-only.
 */
class Cache {
    private static array $requestCache = [];

    private static function apcuAvailable(): bool {
        return function_exists('apcu_fetch')
            && filter_var((string) ini_get('apc.enabled'), FILTER_VALIDATE_BOOLEAN);
    }

    public static function remember(string $key, int $ttl, callable $callback) {
        if (array_key_exists($key, self::$requestCache)) {
            return self::$requestCache[$key];
        }

        if ($ttl > 0 && self::apcuAvailable()) {
            $success = false;
            $value = apcu_fetch('ainet:' . $key, $success);
            if ($success) {
                return self::$requestCache[$key] = $value;
            }
        }

        $value = $callback();
        self::$requestCache[$key] = $value;
        if ($ttl > 0 && self::apcuAvailable()) {
            apcu_store('ainet:' . $key, $value, $ttl);
        }
        return $value;
    }

    public static function forget(string $key): void {
        unset(self::$requestCache[$key]);
        if (self::apcuAvailable()) {
            apcu_delete('ainet:' . $key);
        }
    }
}
