<?php
require 'config.php';

$token = $_GET["token"] ?? "";

if (!$token) {
  echo "Invalid verification link";
  exit;
}

$stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
$stmt->execute([$token]);

if ($stmt->rowCount() > 0) {
  echo "✅ Your account is verified! You can now log in.";
} else {
  echo "❌ Invalid or expired token.";
}
?>
