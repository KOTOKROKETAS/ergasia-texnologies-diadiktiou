<?php
session_start();
require_once '../../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Μη έγκυρο αίτημα.");
}

$playlist_id = (int) $_GET['id'];

// Φέρνουμε τα στοιχεία της λίστας
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ?");
$stmt->execute([$playlist_id]);
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$playlist) {
    die("Η λίστα δεν βρέθηκε.");
}

// Έλεγχος πρόσβασης
$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $playlist['user_id'];

if (!$playlist['is_public'] && !$is_owner) {
    die("Δεν έχεις πρόσβαση σε αυτήν τη λίστα.");
}

// Φέρνουμε τα βίντεο της λίστας
$stmt = $pdo->prepare("SELECT e.*, u.username FROM playlist_entries e
                       JOIN users u ON e.added_by = u.id
                       WHERE e.playlist_id = ? ORDER BY added_at DESC");
$stmt->execute([$playlist_id]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Λίστα: <?= htmlspecialchars($playlist['title']) ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<h1>Λίστα: <?= htmlspecialchars($playlist['title']) ?></h1>

<?php if (count($entries) === 0): ?>
    <p>Δεν υπάρχουν βίντεο σε αυτήν τη λίστα.</p>
<?php else: ?>
    <?php foreach ($entries as $entry): ?>
        <div style="margin-bottom: 30px;">
            <p><strong><?= htmlspecialchars($entry['video_title']) ?></strong> από <?= htmlspecialchars($entry['username']) ?></p>
            <iframe width="560" height="315"
                    src="https://www.youtube.com/embed/<?= htmlspecialchars($entry['youtube_id']) ?>"
                    frameborder="0" allowfullscreen></iframe>
            <p style="font-size: 0.9em;">Προστέθηκε: <?= $entry['added_at'] ?></p>

            <?php if (isset($_SESSION['user_id']) &&
                      ($_SESSION['user_id'] == $entry['added_by'] || $is_owner)): ?>
                <p>
                    <?php if (isset($_SESSION['user_id']) &&
          ($_SESSION['user_id'] == $entry['added_by'] || $is_owner)): ?>
    <div>
        <form action="delete_video.php" method="get" onsubmit="return confirm('Να διαγραφεί αυτό το βίντεο;');" style="display:inline;">
            <input type="hidden" name="id" value="<?= $entry['id'] ?>">
            <button type="submit" class="btn" style="background-color: red; color: white;">🗑️ Διαγραφή</button>
        </form>
    </div>
<?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <p>
    <form action="my_playlists.php" method="get">
    <button type="submit" class="btn" style="margin-top: 15px;">Λίστες μου</button>
</form>
</p>

<?php endif; ?>

</body>
</html>
