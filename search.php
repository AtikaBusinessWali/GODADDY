<?php
// search.php - Fixed domain availability checker with minimal validation for debugging
session_start();

// Enable error logging for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

include 'db.php';

$domain = $_GET['domain'] ?? '';
if (empty($domain)) {
    error_log("Empty domain input received in search.php at " . date('Y-m-d H:i:s'));
    echo '<script>alert("Please enter a domain name."); window.location.href = "index.php";</script>';
    exit;
}

// Normalize input
$raw_domain = $domain;
$domain = strtolower(trim($domain));
$domain = preg_replace('#^(https?://|www\.)#i', '', $domain);
$domain = rtrim($domain, '/ .');
$domain = preg_replace('/\s+/', '', $domain); // Remove all spaces
$domain = preg_replace('/\.+/', '.', $domain); // Replace multiple dots with single dot
$domain = preg_replace('/[^a-z0-9\-\.]/', '', $domain); // Remove invalid characters

// Add .com if no TLD is provided
if (!strpos($domain, '.')) {
    $domain .= '.com';
}

// Log inputs
error_log("Raw domain: $raw_domain, Sanitized domain: $domain at " . date('Y-m-d H:i:s'));

// Minimal validation for debugging
if (empty($domain) || !preg_match('/\./', $domain)) {
    $error_message = "Invalid domain format. Please include at least a name and extension (e.g., example.com).";
    error_log("Validation failed for domain: $domain, Error: $error_message at " . date('Y-m-d H:i:s'));
    echo '<script>alert("' . addslashes($error_message) . '"); window.location.href = "index.php";</script>';
    exit;
}

// Parse domain name and extension
$parts = explode('.', $domain);
$ext = '.' . array_pop($parts);
$name = implode('.', $parts);
if (empty($name)) {
    error_log("Empty domain name after parsing: $domain at " . date('Y-m-d H:i:s'));
    echo '<script>alert("Domain name cannot be empty. Please include a name before the extension (e.g., example.com)."); window.location.href = "index.php";</script>';
    exit;
}

try {
    // Check availability in database
    $stmt = $pdo->prepare("SELECT * FROM domains WHERE full_domain = :full_domain");
    $stmt->execute(['full_domain' => $domain]);
    $isAvailable = $stmt->rowCount() === 0;
} catch (PDOException $e) {
    error_log("Database error in search.php: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
    echo '<script>alert("An error occurred while checking domain availability. Please try again later."); window.location.href = "index.php";</script>';
    exit;
}

// Generate suggestions if not available
$suggestions = [];
if (!$isAvailable) {
    $baseName = $parts[0];
    $suggestions = [
        $baseName . '-online' . $ext => 11.99,
        $baseName . '-pro' . $ext => 12.99,
        $baseName . '.net' => 13.99,
        $baseName . '.org' => 10.99,
        $baseName . '-site' . $ext => 11.99,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Search Results - <?php echo htmlspecialchars($domain); ?></title>
    <style>
        /* Polished CSS for a professional, GoDaddy-like experience */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            margin: 0;
            padding: 0;
            color: #2d3748;
            line-height: 1.6;
        }
        header {
            background: #007bff;
            color: white;
            padding: 20px 40px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        header h1 {
            margin: 0;
            font-size: 2.3em;
            animation: slideInDown 0.5s ease;
        }
        .back-link {
            color: #ffd700;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: color 0.3s, transform 0.2s;
        }
        .back-link:hover {
            color: #ffffff;
            transform: scale(1.05);
        }
        .results {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            animation: fadeInUp 0.6s ease;
        }
        .available {
            color: #28a745;
            font-size: 1.6em;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 12px;
            border-left: 5px solid #28a745;
            background: #e6ffed;
            border-radius: 5px;
        }
        .not-available {
            color: #dc3545;
            font-size: 1.6em;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 12px;
            border-left: 5px solid #dc3545;
            background: #ffe6e6;
            border-radius: 5px;
        }
        .suggestions h3 {
            color: #007bff;
            font-size: 1.4em;
            margin: 20px 0 15px;
        }
        .suggestions ul {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .suggestions li {
            padding: 15px;
            background: #f1f3f5;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .suggestions li:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        button {
            padding: 12px 24px;
            font-size: 1.1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        .add-to-cart {
            background: #28a745;
            color: white;
        }
        .add-to-cart:hover {
            background: #218838;
            transform: scale(1.05);
        }
        .check-again {
            background: #17a2b8;
            color: white;
        }
        .check-again:hover {
            background: #138496;
            transform: scale(1.05);
        }
        .non-functional {
            background: #ffc107;
            color: #333;
        }
        .non-functional:hover {
            background: #e0a800;
            transform: scale(1.05);
        }
        .pricing {
            color: #555;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #007bff;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .results {
                margin: 20px;
                padding: 20px;
            }
            header h1 {
                font-size: 1.9em;
            }
            .suggestions ul {
                grid-template-columns: 1fr;
            }
            button {
                width: 100%;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Domain Search Results for <?php echo htmlspecialchars($domain); ?></h1>
        <a href="index.php" class="back-link">Back to Home</a>
    </header>
    <div class="results">
        <?php if ($isAvailable): ?>
            <p class="available"><?php echo htmlspecialchars($domain); ?> is available!</p>
            <p class="pricing">Pricing: $9.99/year</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form id="registerForm">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
                    <input type="hidden" name="ext" value="<?php echo htmlspecialchars($ext); ?>">
                    <button type="submit" class="add-to-cart">Add to Cart & Register</button>
                </form>
            <?php else: ?>
                <button onclick="window.location.href='login.php'" class="add-to-cart">Login to Register</button>
            <?php endif; ?>
        <?php else: ?>
            <p class="not-available"><?php echo htmlspecialchars($domain); ?> is not available.</p>
            <div class="suggestions">
                <h3>Try These Alternatives:</h3>
                <ul>
                    <?php foreach ($suggestions as $sug => $price): ?>
                        <li>
                            <span><?php echo htmlspecialchars($sug); ?> - $<?php echo $price; ?>/year</span>
                            <button class="check-again" onclick="window.location.href='search.php?domain=<?php echo urlencode($sug); ?>'">Check</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <button class="non-functional" onclick="alert('Checkout is non-functional in this demo.')">Proceed to Checkout</button>
    </div>
    <footer>&copy; 2025 GoDaddy Clone. All rights reserved.</footer>
    <script>
        // Robust JS for domain registration
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(registerForm);
                try {
                    const response = await fetch('register_domain.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.text();
                    alert(data);
                    if (data.includes('successfully')) {
                        window.location.href = 'dashboard.php';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while registering the domain. Please try again.');
                }
            });
        }
    </script>
</body>
</html>
