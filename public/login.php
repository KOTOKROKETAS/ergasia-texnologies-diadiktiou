<?php
session_start();
require_once '../config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!$username || !$password) {
        $errors[] = "Συμπλήρωσε όλα τα πεδία.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if (!empty($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
            } else {
                header("Location: profile.php");
            }
            exit;
        } else {
            $errors[] = "Λάθος στοιχεία σύνδεσης.";
        }
    }
}
?>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>






<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Σύνδεση Χρήστη</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>



<h1 style="text-align:center">Σύνδεση Χρήστη</h1>

<?php if ($errors): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div style="display:flex; justify-content:center; margin-top:2rem;">
    <form class="grid" style="gap:1rem; max-width:300px" method="POST">
        <label>Username:
            <input type="text" name="username" required>
        </label>
        <label>Κωδικός:
            <input type="password" name="password" required>
        </label>
        <button class="btn" type="submit" style="max-width:100px;">Σύνδεση</button>
    </form>
</div>

</body>
</html>
