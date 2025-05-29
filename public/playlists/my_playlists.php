<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'playlists/my_playlists.php';
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM playlists WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Οι Λίστες μου</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>


<h1 style="text-align:center">Οι Λίστες μου</h1>

<?php if (count($playlists) === 0): ?>
    <p>Δεν έχεις δημιουργήσει ακόμη λίστες.</p>
<?php else: ?>
    <ul>
        <?php foreach ($playlists as $playlist): ?>
            <li>
                <strong><?= htmlspecialchars($playlist['title']) ?></strong>
                (<?= $playlist['is_public'] ? 'Δημόσια' : 'Ιδιωτική' ?>)
                |
                <a href="view_playlist.php?id=<?= $playlist['id'] ?>">Προβολή</a>
                |
                <a href="add_video.php?playlist_id=<?= $playlist['id'] ?>">Προσθήκη Βίντεο</a>
                <!-- Προαιρετικά:
                | <a href="edit_playlist.php?id=<?= $playlist['id'] ?>">Επεξεργασία</a>
                | <a href="delete_playlist.php?id=<?= $playlist['id'] ?>" onclick="return confirm('Σίγουρα;')">Διαγραφή</a>
                -->
                | <a href="delete_playlist.php?id=<?= $playlist['id'] ?>" onclick="return confirm('Θέλεις σίγουρα να διαγράψεις αυτή τη λίστα;')">Διαγραφή</a>
                
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="/index.php" method="get">
    <button type="submit" class="btn" style="margin-left: 15px;">Αρχική</button>
</form>


</body>
</html>
