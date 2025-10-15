<?php
// dashboard.php - User dashboard for managing domains
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM domains WHERE user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$domains = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manage Domains</title>
    <style>
        /* Amazing CSS */
        body { font-family: 'Arial', sans-serif; background: #f8f9fa; margin: 0; padding: 0; color: #333; }
        header { background: #007bff; color: white; padding: 20px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .dashboard { max-width: 1000px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        button { padding: 8px 16px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        .renew { background: #28a745; color: white; }
        .renew:hover { background: #218838; }
        .transfer { background: #ffc107; color: #333; }
        .transfer:hover { background: #e0a800; }
        .remove { background: #dc3545; color: white; }
        .remove:hover { background: #c82333; }
        footer { text-align: center; padding: 20px; background: #007bff; color: white; position: fixed; width: 100%; bottom: 0; }
        @media (max-width: 768px) { table { font-size: 0.9em; } button { width: 100%; margin: 5px 0; } }
    </style>
</head>
<body>
    <header>
        <h1>Your Domain Dashboard</h1>
        <a href="index.php" style="color: white; text-decoration: none;">Back to Home</a> | <a href="logout.php" style="color: white; text-decoration: none;">Logout</a>
    </header>
    <div class="dashboard">
        <h2>Registered Domains</h2>
        <?php if (empty($domains)): ?>
            <p>No domains registered yet. <a href="index.php">Search for one!</a></p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Domain</th>
                    <th>Registration Date</th>
                    <th>Expiration Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($domains as $dom): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dom['full_domain']); ?></td>
                        <td><?php echo htmlspecialchars($dom['registration_date']); ?></td>
                        <td><?php echo htmlspecialchars($dom['expiration_date']); ?></td>
                        <td><?php echo htmlspecialchars($dom['status']); ?></td>
                        <td>
                            <button class="renew" onclick="alert('Renewal is non-functional in this demo.')">Renew</button>
                            <button class="transfer" onclick="alert('Transfer is non-functional in this demo.')">Transfer</button>
                            <button class="remove" onclick="removeDomain(<?php echo $dom['id']; ?>)">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    <footer>&copy; 2025 GoDaddy Clone. All rights reserved.</footer>
    <script>
        // JS for removing domain
        function removeDomain(id) {
            if (confirm('Are you sure you want to remove this domain?')) {
                fetch('remove_domain.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                }).then(response => response.text())
                .then(data => {
                    alert(data);
                    window.location.reload();
                }).catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>
