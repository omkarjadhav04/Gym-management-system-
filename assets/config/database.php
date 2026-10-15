<?php
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'gym_management';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$conn = null;
$databaseError = null;

try {
    $connectionOptions = [];
    if (getenv('DB_HOST')) {
        $connectionOptions[PDO::MYSQL_ATTR_SSL_CA] = __DIR__ . '/../certs/isrgrootx1.pem';
        $connectionOptions[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }

    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4",
        $username,
        $password,
        $connectionOptions
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $databaseError = $e->getMessage();
}
?>