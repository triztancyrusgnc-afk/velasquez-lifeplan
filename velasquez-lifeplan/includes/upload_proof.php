<?php
// Handles "Upload Proof" submissions from the client dashboard.
ob_start();
session_start();
require_once __DIR__ . '/../config.php';

try {
    $pdo = getDB();
    $pdo->query('SELECT 1');
} catch (Throwable $e) {
    header('Location: ../Step1.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['installment_id'])) {
    header('Location: ../pages/dashboard.php#payments');
    exit;
}

$installmentId = (int)$_POST['installment_id'];

// Make sure this installment belongs to the logged-in user
$stmt = $pdo->prepare("SELECT ps.* FROM payment_schedule ps JOIN subscriptions s ON ps.subscription_id = s.id WHERE ps.id = ? AND s.user_id = ?");
$stmt->execute([$installmentId, $_SESSION['user_id']]);
$installment = $stmt->fetch();

if (!$installment) {
    header('Location: ../pages/dashboard.php#payments');
    exit;
}

if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../pages/dashboard.php#payments');
    exit;
}

$uploadDir = __DIR__ . '/../uploads/proofs/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$ext = strtolower(pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
if (!in_array($ext, $allowed, true)) {
    header('Location: ../pages/dashboard.php#payments');
    exit;
}

$filename = 'proof_' . $installmentId . '_' . time() . '.' . $ext;
if (!move_uploaded_file($_FILES['proof_image']['tmp_name'], $uploadDir . $filename)) {
    header('Location: ../pages/dashboard.php#payments');
    exit;
}

$relativePath = 'uploads/proofs/' . $filename;
$reference = trim($_POST['reference_number'] ?? '');
$notes = trim($_POST['notes'] ?? '');

// Record the payment for review
$stmt = $pdo->prepare("INSERT INTO payments (subscription_id, installment_id, amount, payment_method, reference_number, proof_image, status, notes) VALUES (?, ?, ?, 'gcash', ?, ?, 'pending', ?)");
$stmt->execute([
    $installment['subscription_id'],
    $installmentId,
    $installment['amount'],
    $reference,
    $relativePath,
    $notes,
]);

// Attach the proof to the schedule row
$stmt = $pdo->prepare("UPDATE payment_schedule SET proof_image = ? WHERE id = ?");
$stmt->execute([$relativePath, $installmentId]);

header('Location: ../pages/dashboard.php#payments');
exit;
