<?php
// db.php - Database connection file (unchanged)
$host = 'localhost'; // Assuming localhost, change if needed
$dbname = 'dbeahfjbxqyga2';
$username = 'unkuodtm3putf';
$password = 'htk2glkxl4n4';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
