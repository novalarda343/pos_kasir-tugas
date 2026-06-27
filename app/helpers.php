<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah(int|float|string|null $value): string
{
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
}

function redirect(string $page, array $params = []): never
{
    $query = http_build_query(array_merge(['page' => $page], $params));
    header('Location: index.php?' . $query);
    exit;
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function current_page(): string
{
    return $_GET['page'] ?? 'pos';
}

function is_active(string $page): string
{
    return current_page() === $page ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/[0.12] dark:text-brand-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5';
}

function post_value(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $default;
}
