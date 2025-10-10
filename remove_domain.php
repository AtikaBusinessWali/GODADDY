<?php
// remove_domain.php - Handle domain removal
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo 'Unauthorized.';
    exit;
}

$id = $_POST['id'] ?? 0;
if ($id <= 0) {
    echo 'Invalid ID.';
    exit;
}

$stmt = $pdo->prepare("DELETE FROM domains WHERE id = :id AND user_id = :user_id");
try {
    $stmt->execute(['id' => $id, 'user_id' => $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        echo 'Domain removed successfully!';
    } else {
        echo 'Domain not found or unauthorized.';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
