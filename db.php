<?php
// db.php - Database connection file
$host = 'localhost'; // Assuming localhost; change if different host is needed
$dbname = 'dblnc69bkyquii';
$username = 'uar8kmsrlijda';
$password = 'knczabmoocaw';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
