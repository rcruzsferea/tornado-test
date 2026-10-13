<?php
// Router para el servidor embebido de PHP (php -S).
// Sirve los archivos estaticos existentes (css, js, img) y delega
// el resto al front controller (index.php), igual que hace .htaccess.
if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/' && is_file(__DIR__ . $path)) {
        return false;
    }
}
require __DIR__ . '/index.php';
