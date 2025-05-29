<?php
require_once '../config/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Απλός έλεγχος για κενά πεδία
    if (!$firstName || !$lastName || !$username || !$email || !$password) {
        $errors[] = "Όλα τα πεδία είναι υποχρεωτικά.";
    } else {
        // Έλεγχος για διπλό username/email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Το username ή το email χρησιμοποιείται ήδη.";
        } else {
            // Εισαγωγή χρήστη
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash)
                                   VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$firstName, $lastName, $username, $email, $hash])) {
                $success = true;
            } else {
                $errors[] = "Σφάλμα κατά την εγγραφή.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Εγγραφή Χρήστη</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>



<body style="text-align:center">
    <h1>Εγγραφή Χρήστη</h1>

    <?php if ($success): ?>
        <p>Εγγραφήκατε επιτυχώς! <a href="login.php">Συνδεθείτε</a></p>
    <?php else: ?>
        <?php if ($errors): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" style="display:flex; justify-content:center; margin-top:2rem;">
            <label>Όνομα: <input type="text" name="first_name" required></label><br>
        </form>
        <form method="POST" style="display:flex; justify-content:center; margin-top:15px;">
            <label>Επώνυμο: <input type="text" name="last_name" required></label><br>
                </form>
        <form method="POST" style="display:flex; justify-content:center; margin-top:15px;">
            <label>Username: <input type="text" name="username" required></label><br>
                </form>
        <form method="POST" style="display:flex; justify-content:center; margin-top:15px;">
            <label>Email: <input type="email" name="email" required></label><br>
                </form>
        <form method="POST" style="display:flex; justify-content:center; margin-top:15px;">
            <label>Κωδικός: <input type="password" name="password" required></label><br>
                </form>
        <form method="POST" style="display:flex; justify-content:center; margin-top:2rem;">    
           <button type="submit" class="btn" style="background-color: white; color: black;">Εγγραφή</button>
        </form>
    <?php endif; ?>
    <form action="/index.php" method="get">
    <button type="submit" class="btn" style="margin-top: 15px;">Αρχική</button>
</form>
</body>
</html>
