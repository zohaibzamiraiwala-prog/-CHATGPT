<?php
// logout.php - Unchanged from previous
// Logout script with JS redirect
 
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging out...</title>
</head>
<body>
    <script>window.location.href = 'login.php';</script>
</body>
</html>
