<?php
header("Content-Type: application/json");

$host = 'localhost';
$db = 'hr';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Option 1: Retrieve users only (modify if needed):
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll();
        echo json_encode($users);

        // Option 2: Retrieve users with their associated orders (if needed):
        // $stmt = $pdo->query("SELECT u.*, o.* FROM users u LEFT JOIN orders o ON u.user_id = o.user_id");
        // $data = $stmt->fetchAll();
        // echo json_encode($data);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate and Sanitize user input before inserting (recommended)

        $sql = "INSERT INTO users (username, email) VALUES (?, ?)"; // Don't store passwords in plain text
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input['username'], $input['email']]);
        echo json_encode(['message' => 'User added successfully']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>