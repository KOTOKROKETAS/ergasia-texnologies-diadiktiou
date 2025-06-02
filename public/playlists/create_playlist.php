<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'playlists/create_playlist.php';
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    if (!$title) {
        $errors[] = "Ο τίτλος της λίστας είναι υποχρεωτικός.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO playlists (user_id, title, is_public) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $is_public])) {
            $success = true;
        } else {
            $errors[] = "Σφάλμα κατά την αποθήκευση της λίστας.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Δημιουργία Λίστας</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>



<h1>Δημιουργία Λίστας</h1>

<?php if ($success): ?>
    <p style="color: green;">Η λίστα δημιουργήθηκε επιτυχώς!</p>
<?php endif; ?>

<?php if ($errors): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <label>Τίτλος Λίστας:
        <input type="text" name="title" required style="max-width:300px">
    </label>
    <label>
        <input type="checkbox" name="is_public" checked> Δημόσια λίστα
    </label>
    <form>
    <button type="submit" class="btn" style="margin-left: 15px; background-color:rgba(240, 108, 0, 0.85)">Δημιουργία</button>
        </form>

</form>


<form action="/index.php" method="get" style="text-align: left; margin-top: 15px;">
        <button type="submit" class="btn">Αρχική</button>
    </form>


</body>
</html>
