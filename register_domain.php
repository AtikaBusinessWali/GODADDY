<?php
// register_domain.php - Handle domain registration (called via JS fetch)
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo 'Please login to register domains.';
    exit;
}

$name = $_POST['name'] ?? '';
$ext = $_POST['ext'] ?? '';

if (empty($name) || empty($ext)) {
    echo 'Invalid domain.';
    exit;
}

$full_domain = $name . $ext;
$reg_date = date('Y-m-d');
$exp_date = date('Y-m-d', strtotime('+1 year'));

// Check if available again
$stmt = $pdo->prepare("SELECT * FROM domains WHERE full_domain = :full_domain");
$stmt->execute(['full_domain' => $full_domain]);
if ($stmt->rowCount() > 0) {
    echo 'Domain no longer available.';
    exit;
}

// Register
$stmt = $pdo->prepare("INSERT INTO domains (user_id, domain_name, extension, registration_date, expiration_date) VALUES (:user_id, :name, :ext, :reg_date, :exp_date)");
try {
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'name' => $name,
        'ext' => $ext,
        'reg_date' => $reg_date,
        'exp_date' => $exp_date
    ]);
    echo 'Domain registered successfully!';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
