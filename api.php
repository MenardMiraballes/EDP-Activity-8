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
    $action = isset($_GET['action']) ? $_GET['action'] : null;  // Handle missing action

    if ($action === 'get_users') {
      // Retrieve users
      $stmt = $pdo->query("SELECT * FROM users");
      $users = $stmt->fetchAll();
      echo json_encode($users);
    } elseif ($action === 'get_orders') {
      // Retrieve orders (consider adding filtering if needed)
      $stmt = $pdo->query("SELECT * FROM orders");

      // Print the query results for debugging (temporary):
      $orders = $stmt->fetchAll();
      echo "Orders retrieved: " . count($orders) . "\n"; // Check if any orders are retrieved
      echo json_encode($orders);
    } else {
      // Default action (optional): If no action is specified, you can provide a default behavior here
      // For example, you could return a help message explaining valid actions.
      echo json_encode(['message' => 'Valid actions: get_users, get_orders']);
    }
  } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    if ($action === 'create_user') {
      $input = json_decode(file_get_contents('php://input'), true);

      // Validate and Sanitize user input before inserting (recommended)

      $sql = "INSERT INTO users (username, email) VALUES (?, ?)"; // Don't store passwords in plain text
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$input['username'], $input['email']]);
      echo json_encode(['message' => 'User added successfully']);
    } elseif ($action === 'create_order') {
      // New functionality for creating orders
      $input = json_decode(file_get_contents('php://input'), true);

      // Construct SQL query to insert data into orders table
      $sql = "INSERT INTO orders (user_id, product_name, price) VALUES (?, ?, ?)"; // Replace placeholders with actual columns and values
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$input['user_id'], $input['product_name'], $input['price']]);

      // Return success message or error response
      echo json_encode(['message' => 'Order created successfully']);
    } else {
      // Invalid action
      echo json_encode(['error' => 'Invalid action parameter']);
    }
  }
} catch(PDOException $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>