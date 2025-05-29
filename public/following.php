<?php
require_once '../config/db.php';
session_start();
if(!isset($_SESSION['user_id'])) { header('Location: /login.php'); exit; }

$stmt=$pdo->prepare(
 "SELECT u.id, u.username
  FROM follows f
  JOIN users u ON u.id=f.followed_id
  WHERE f.follower_id=?"
);
$stmt->execute([$_SESSION['user_id']]);
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html><html lang="el"><head>
  <meta charset="utf-8"><title>Ακολουθείς</title>
  <link rel="stylesheet" href="/assets/css/styles.css">
</head><body>

<div class="container fade-in">
  <h1 style="text-align:center">Ακολουθείς</h1>

  <?php if(!$rows): ?>
     <p class="text-muted">Δεν ακολουθείς κανέναν ακόμη.</p>
  <?php else: ?>
     <ul class="grid grid-2">
       <?php foreach($rows as $row): ?>
         <li class="card">
           <a href="/profile_public.php?u=<?= urlencode($row['username']) ?>">
             @<?= htmlspecialchars($row['username']) ?>
           </a>
           <form action="/actions/unfollow.php" method="post" style="margin-top:0.5rem">
             <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
             <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
             <button class="btn btn-outline btn-sm">✖︎ Unfollow</button>
           </form>
         </li>
       <?php endforeach; ?>
     </ul>
  <?php endif; ?>
</div>
  <form action="/profile.php" method="get" style="text-align:center">
    <button class="btn" >Το προφίλ μου</button>
</form>
</body>
</html>