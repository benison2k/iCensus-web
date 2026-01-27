<?php
// core/init.php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad(); 
} catch (Exception $e) {
    error_log("Dotenv could not be loaded: " . $e->getMessage());
}

session_start();

require_once __DIR__ . '/Csrf.php';
Csrf::generate(); 

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

$base_directory = ''; 

define('BASE_URL', $protocol . '://' . $host . $base_directory);

$current_route = str_replace($base_directory, '', strtok($_SERVER['REQUEST_URI'], '?'));
$current_route = trim($current_route, '/');

$allowed_pages = [
    'login',
    'home',
    '', 
    'verify-otp',
    'resend-otp',
    'password/forgot',
    'password/reset',
    'debug_csrf.php' 
];

$is_allowed_page = in_array($current_route, $allowed_pages);
$is_in_2fa_flow = isset($_SESSION['2fa_required']) && $_SESSION['2fa_required'] === true;

if (!isset($_SESSION['user']) && !$is_allowed_page && !$is_in_2fa_flow) {
    header("Location: " . BASE_URL . "/login");
    exit;
}

$timeout = 1800; 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    $_SESSION = array();
    session_destroy();
    session_start();
    $_SESSION['timeout_message'] = "You have been logged out due to inactivity.";
    header("Location: " . BASE_URL . "/login");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

function checkAuth() {
    if (!isset($_SESSION['user'])) {
        header("Location: " . BASE_URL . "/login");
        exit;
    }
}