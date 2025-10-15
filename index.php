<?php
// index.php - Updated homepage with robust domain search validation
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoDaddy Clone - Find Your Domain</title>
    <style>
        /* Amazing, real-looking CSS - Professional, clean, and responsive */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
            animation: fadeIn 1s ease-in;
        }
        .nav-links {
            margin-top: 10px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.1em;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #ffd700;
        }
        .search-container {
            max-width: 800px;
            margin: 50px auto;
            text-align: center;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        #domainInput {
            width: 60%;
            padding: 15px;
            font-size: 1.2em;
            border: 2px solid #007bff;
            border-radius: 5px 0 0 5px;
            outline: none;
            transition: border-color 0.3s;
        }
        #domainInput:focus {
            border-color: #0056b3;
        }
        button[type="submit"] {
            padding: 15px 30px;
            font-size: 1.2em;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #218838;
        }
        .extensions {
            margin: 20px 0;
        }
        .extensions span {
            background-color: #e9ecef;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .extensions span:hover {
            background-color: #007bff;
            color: white;
        }
        .promotions {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
        }
        .promotions h2 {
            color: #007bff;
        }
        .promotions ul {
            list-style: none;
            padding: 0;
        }
        .promotions li {
            margin: 10px 0;
            font-size: 1.1em;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #007bff;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
            padding: 10px;
            background: #ffe6e6;
            border-radius: 5px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @media (max-width: 768px) {
            .search-bar {
                flex-direction: column;
            }
            #domainInput {
                width: 100%;
                border-radius: 5px;
                margin-bottom: 10px;
            }
            button[type="submit"] {
                border-radius: 5px;
            }
            header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>GoDaddy Clone</h1>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="search-container">
        <h2>Find Your Perfect Domain</h2>
        <form id="searchForm" class="search-bar" action="search.php" method="GET">
            <input type="text" id="domainInput" name="domain" placeholder="Enter domain name (e.g., example.com)" required>
            <button type="submit">Search</button>
        </form>
        <div id="errorMessage" class="error-message"></div>
        <div class="extensions">
            Popular Extensions: 
            <span onclick="appendExtension('.com')">.com</span>
            <span onclick="appendExtension('.net')">.net</span>
            <span onclick="appendExtension('.org')">.org</span>
            <span onclick="appendExtension('.io')">.io</span>
            <span onclick="appendExtension('.app')">.app</span>
        </div>
    </div>
    <div class="promotions">
        <h2>Current Promotions</h2>
        <ul>
            <li>.com domains for $9.99/year</li>
            <li>.net domains for $11.99/year</li>
            <li>Free domain with hosting plan</li>
        </ul>
    </div>
    <footer>&copy; 2025 GoDaddy Clone. All rights reserved.</footer>
    <script>
        // JS for form validation and submission
        function appendExtension(ext) {
            const input = document.getElementById('domainInput');
            let value = input.value.trim().replace(/\.[a-z]{2,}$/i, '');
            input.value = value + ext;
        }

        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('domainInput');
            let domain = input.value.trim();
            const errorMessage = document.getElementById('errorMessage');

            // Clean input
            domain = domain.toLowerCase()
                .replace(/^(https?:\/\/|www\.)/i, '')
                .replace(/[\/ ]+$/, '')
                .replace(/\s+/g, '')
                .replace(/\.+/g, '.')
                .replace(/[^a-z0-9\-\.]/g, '');

            // Add .com if no TLD
            if (!domain.includes('.')) {
                domain += '.com';
            }

            // Log for debugging
            console.log('Raw input:', input.value, 'Sanitized:', domain, 'Time:', new Date().toISOString());

            // Minimal validation
            if (empty(domain) || !domain.includes('.')) {
                errorMessage.textContent = 'Invalid domain format. Please include at least a name and extension (e.g., example.com).';
                errorMessage.style.display = 'block';
                console.log('Validation failed:', domain, 'Time:', new Date().toISOString());
                return;
            }

            errorMessage.style.display = 'none';
            // Update input value and submit form
            input.value = domain;
            this.submit();
        });

        // Polyfill for PHP's empty() function
        function empty(mixed_var) {
            return (mixed_var === "" || mixed_var === null || mixed_var === undefined);
        }
    </script>
</body>
</html>
