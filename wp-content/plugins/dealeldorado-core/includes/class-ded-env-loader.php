<?php
defined('ABSPATH') || exit;

/**
 * Charge les variables depuis le fichier .env à la racine WordPress.
 * Met chaque variable dans $_ENV et putenv().
 */
class DED_Env_Loader {

    public static function load(string $path): void {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            // Skip comments
            if (str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);
            // Remove surrounding quotes
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            if ($name) {
                $_ENV[$name] = $value;
                putenv("$name=$value");
            }
        }
    }

    public static function get(string $key, string $default = ''): string {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
