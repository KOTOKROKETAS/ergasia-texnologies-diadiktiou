<?php
require_once '../../config/db.php';
session_start();

$current = $_SESSION['user_id'] ?? 0;
$target  = (int)($_POST['user_id'] ?? 0);
$username= $_POST['username'] ?? '';

if (!$current || $current === $target) {
  http_response_code(400); exit;
}

$stmt = $pdo->prepare(
  "INSERT IGNORE INTO follows (follower_id, followed_id)
   VALUES (?, ?)"
);
$stmt->execute([$current, $target]);

$redirect = $_SERVER['HTTP_REFERER'] ?? '/';
header("Location: $redirect");

