<?php
// logout.php - Logout
session_start();
session_destroy();
echo '<script>window.location.href = "index.php";</script>';
exit;
?>
