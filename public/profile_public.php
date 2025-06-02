<?php
require_once __DIR__ . '/../config/db.php';
session_start();

// ------------------------- FETCH USER -------------------------
$username = $_GET['u'] ?? '';
if ($username === '') {
    http_response_code(400);
    exit('Λείπει όνομα χρήστη.');
}

$stmt = $pdo->prepare(
    "SELECT id, username, first_name, last_name
     FROM users
     WHERE username = ? AND is_public = 1
     LIMIT 1"
);
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    http_response_code(404);
    exit('Προφίλ μη διαθέσιμο');
}

// ------------------------- PUBLIC PLAYLISTS -------------------------
$stmt = $pdo->prepare(
    "SELECT id, title
       FROM playlists
       WHERE user_id = ? AND is_public = 1
       ORDER BY created_at DESC"
);
$stmt->execute([$user['id']]);
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ------------------------- FOLLOW STATS -------------------------
$stmt = $pdo->prepare(
    "SELECT
        (SELECT COUNT(*) FROM follows WHERE follower_id = ?)  AS following_cnt,
        (SELECT COUNT(*) FROM follows WHERE followed_id = ?) AS followers_cnt"
);
$stmt->execute([$user['id'], $user['id']]);
[$followingCnt, $followersCnt] = $stmt->fetch(PDO::FETCH_NUM);

// ------------------------- FOLLOW STATE -------------------------
$currentId  = $_SESSION['user_id'] ?? null;
$isFollowing = false;
if ($currentId && $currentId !== $user['id']) {
    $q = $pdo->prepare(
        "SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ? LIMIT 1"
    );
    $q->execute([$currentId, $user['id']]);
    $isFollowing = (bool) $q->fetchColumn();
}
?>




<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Προφίλ – @<?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>


<div class="container fade-in">

    <h1>@<?= htmlspecialchars($user['username']) ?></h1>

    <!-- Followers / Following & Follow‑button -->
    <div class="row-between mt-2">
        <div>
            <strong>Ακολουθεί</strong>
            <span class="stat-badge"><?= $followingCnt ?></span><br>
            <strong>Ακολουθείται</strong>
            <span class="stat-badge"><?= $followersCnt ?></span>
        </div>

        <?php if ($currentId && $currentId !== $user['id']): ?>
            <form action="/actions/<?= $isFollowing ? 'unfollow' : 'follow' ?>.php" method="post">
                <input type="hidden" name="user_id"  value="<?= $user['id'] ?>">
                <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                <button class="btn <?= $isFollowing ? 'btn-outline' : '' ?>">
                    <?= $isFollowing ? '✖︎ Unfollow' : '➕ Follow' ?>
                </button>
            </form>
        <?php elseif ($currentId === $user['id']): ?>
            <a href="/following.php" class="btn btn-outline">👥 Ακολουθείς</a>
        <?php endif; ?>
    </div>

    <!-- Public playlists -->
    <h2 class="mt-3">Δημόσιες λίστες</h2>

    <?php if (!$lists): ?>
        <p class="text-muted">Δεν υπάρχουν λίστες.</p>
    <?php else: ?>
        <ul class="grid grid-2 mt-2">
            <?php foreach ($lists as $pl): ?>
                <li class="card">
                    <a href="/playlists/view_playlist.php?id=<?= $pl['id'] ?>">
                        <?= htmlspecialchars($pl['title']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
  
  <form action="/index.php" method="get" style="text-align: left">
        <button type="submit" class="btn">Αρχική</button>
    </form>

</div>



</body>
</html>