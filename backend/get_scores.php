
<?php
require 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['user'])) {
        $username = $_GET['user'];
        $stmt = $pdo->prepare("
            SELECT u.username, s.game_mode, s.wpm, s.accuracy, s.played_at
            FROM scores s
            JOIN users u ON s.user_id = u.id
            WHERE u.username = ?
            ORDER BY s.wpm DESC
            LIMIT 20
        ");
        $stmt->execute([$username]);
    } else {
        $stmt = $pdo->query("
            SELECT u.username, s.game_mode, s.wpm, s.accuracy, s.played_at
            FROM scores s
            JOIN users u ON s.user_id = u.id
            ORDER BY s.wpm DESC
            LIMIT 20
        ");
    }

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($results);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
