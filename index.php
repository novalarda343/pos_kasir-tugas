<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/config/database.php';
require __DIR__ . '/app/helpers.php';
require __DIR__ . '/app/actions.php';
require __DIR__ . '/app/layout.php';

handle_request();

$page = current_page();
$allowedPages = ['dashboard', 'pos', 'products', 'categories', 'stock', 'sales', 'receipt'];

if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    $page = 'dashboard';
    flash('Halaman tidak ditemukan.', 'error');
}

require __DIR__ . '/pages/' . $page . '.php';
