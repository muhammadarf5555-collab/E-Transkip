<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

@set_time_limit(180);

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Pastikan file statis di dalam folder public langsung dilayani oleh PHP built-in server
$publicPath = __DIR__ . '/public';
$filePath = $publicPath . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false; // Mengembalikan false membuat server PHP melayani file statis secara langsung
}

require_once $publicPath . '/index.php';
