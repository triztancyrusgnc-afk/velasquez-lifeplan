<?php
session_start();

$Installed = false;
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
    if (isset($pdo) && $pdo instanceof PDO) {
        $Installed = true;
    }
}

// Redirect to setup if not installed (except index.php which IS the setup)
if (!$Installed && basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    header('Location: index.php');
    exit;
}
