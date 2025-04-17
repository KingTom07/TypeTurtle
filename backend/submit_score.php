
<?php
require 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$wpm = $data['wpm'];
$accuracy = $data['accuracy'];
$game_mode = $data['game_mode'] ?? 'default';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get user ID from username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("INSERT INTO scores (user_id, game_mode, wpm, accuracy, played_at)
                               VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user['id'], $game_mode, $wpm, $accuracy]);

        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'User not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
