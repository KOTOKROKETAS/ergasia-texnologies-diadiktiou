<?php
require_once '../../config/db.php';
session_start();

$current = $_SESSION['user_id'] ?? 0;
$target  = (int)($_POST['user_id'] ?? 0);
$redirect= $_POST['redirect'] ?? '/';

if (!$current || !$target) { http_response_code(400); exit; }

$pdo->prepare(
  "DELETE FROM follows WHERE follower_id=? AND followed_id=?"
)->execute([$current, $target]);

header("Location: $redirect");
