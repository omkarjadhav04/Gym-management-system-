<?php
$host = 'localhost';
$db_name = 'gym_management';
$username = 'root';
$password = '';
$conn = null;
$databaseError = null;

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $databaseError = $e->getMessage();
}
?>