<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basit kimlik doğrulama fonksiyonları
function isLoggedIn()
{
    return isset($_SESSION['user']);
}

function isAdmin()
{
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

function requireAuth()
{
    if (!isLoggedIn()) {
        header("Location: /login.php");
        exit;
    }
}

function requireAdmin()
{
    requireAuth();
    if (!isAdmin()) {
        header("Location: /login.php");
        exit;
    }
}

function getUser()
{
    return isLoggedIn() ? $_SESSION['user'] : null;
}