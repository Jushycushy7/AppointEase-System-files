<?php
header('Content-Type: application/json');
$host = 'localhost';
$db   = 'clinic_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

// Get form data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$email = $_POST['email'] ?? '';

if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO staff (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashed_password, $email]);
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} catch (\PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(["status" => "error", "message" => "Username or email already exists."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Something went wrong."]);
    }
}
?>