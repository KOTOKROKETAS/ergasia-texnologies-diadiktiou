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

$entry_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// Φέρνουμε το entry και τη λίστα του
$stmt = $pdo->prepare("SELECT e.*, p.user_id AS playlist_owner
                       FROM playlist_entries e
                       JOIN playlists p ON e.playlist_id = p.id
                       WHERE e.id = ?");
$stmt->execute([$entry_id]);
$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entry) {
    die("Το βίντεο δεν βρέθηκε.");
}

// Ο χρήστης μπορεί να διαγράψει αν:
// - Το πρόσθεσε ο ίδιος, ή
// - Είναι ο ιδιοκτήτης της λίστας
if ($entry['added_by'] != $user_id && $entry['playlist_owner'] != $user_id) {
    die("Δεν έχεις δικαίωμα να διαγράψεις αυτό το βίντεο.");
}

$playlist_id = $entry['playlist_id'];

// Διαγραφή
$stmt = $pdo->prepare("DELETE FROM playlist_entries WHERE id = ?");
$stmt->execute([$entry_id]);

header("Location: view_playlist.php?id=" . $playlist_id);
exit;
