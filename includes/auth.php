<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = '1234';

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function is_valid_csrf_token($token): bool
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function require_admin(): void
{
    if (is_admin_logged_in()) {
        return;
    }

    $basePath = basename(dirname($_SERVER['SCRIPT_NAME'])) === 'pages' ? '../' : '';
    header('Location: ' . $basePath . 'login.php');
    exit;
}