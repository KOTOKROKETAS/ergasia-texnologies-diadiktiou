<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'playlists/add_video.php';
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['playlist_id']) || !is_numeric($_GET['playlist_id'])) {
    die("Μη έγκυρο αίτημα.");
}

$playlist_id = (int) $_GET['playlist_id'];
$user_id = $_SESSION['user_id'];

// Έλεγχος αν η λίστα ανήκει στον χρήστη
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlist_id, $user_id]);
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$playlist) {
    die("Δεν έχεις δικαίωμα να επεξεργαστείς αυτήν τη λίστα.");
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $youtube_id = trim($_POST['youtube_id']);

    if (!$title || !$youtube_id) {
        $errors[] = "Συμπλήρωσε όλα τα πεδία.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO playlist_entries (playlist_id, video_title, youtube_id, added_by) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$playlist_id, $title, $youtube_id, $user_id])) {
            $success = true;
        } else {
            $errors[] = "Σφάλμα κατά την προσθήκη βίντεο.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Προσθήκη Βίντεο</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<h1>Προσθήκη Βίντεο στη Λίστα: <?= htmlspecialchars($playlist['title']) ?></h1>

<?php if ($success): ?>
    <p style="color: green;">Το βίντεο προστέθηκε επιτυχώς!</p>
<?php endif; ?>

<?php if ($errors): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <label>Τίτλος Βίντεο:
        <input type="text" style="max-width:350px;" name="title" required>
    </label>
    <label style="margin-left: 15px;">YouTube Video ID:
        <input type="text" style="max-width:350px;" name="youtube_id" required placeholder="π.χ. dQw4w9WgXcQ">
    </label>
    <button type="submit" class="btn" style="margin-left: 15px;">Προσθήκη</button>
</form>

<form action="my_playlists.php" method="get">
    <button type="submit" class="btn" style="margin-top: 15px;">Λίστες μου</button>
</form>

</body>
</html>
