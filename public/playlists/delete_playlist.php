<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Μη έγκυρο αίτημα.");
}

$playlist_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ελέγχουμε αν η λίστα ανήκει στον χρήστη
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
$stmt->execute([$playlist_id, $user_id]);
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$playlist) {
    die("Δεν έχεις δικαίωμα διαγραφής αυτής της λίστας.");
}

// Διαγραφή της λίστας
$stmt = $pdo->prepare("DELETE FROM playlists WHERE id = ?");
$stmt->execute([$playlist_id]);

// Επιστροφή στη σελίδα με τις λίστες
header("Location: my_playlists.php");
exit;
