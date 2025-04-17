<?php
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"]);
$email = trim($data["email"]);
$password = $data["password"];

if (!$username || !$email || !$password) {
  http_response_code(400);
  echo "Missing fields";
  exit;
}

$token = bin2hex(random_bytes(32));
$hash = password_hash($password, PASSWORD_BCRYPT);

// Check if user exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);

if ($stmt->rowCount() > 0) {
  echo "User already exists";
  exit;
}

// Insert user
$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, verification_token) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $email, $hash, $token]);

// Send verification email
$subject = "Verify your TypeTurtle account";
$verifyURL = "https://ts944.brighton.domains/TypeTurtle/backend/verify.php?token=$token";
$body = "Welcome to TypeTurtle!\n\nClick to verify your account:\n$verifyURL";

mail($email, $subject, $body, "From: TypeTurtle <noreply@ts944.brighton.domains>");

echo "Registration successful! Please check your email to verify.";
?>
