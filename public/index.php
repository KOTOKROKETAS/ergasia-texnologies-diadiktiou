<?php
session_start();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Αρχική - Streaming App</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>


<body>
    <h1 style="text-align:center">Καλώς ήρθατε στην Πλατφόρμα Ροής Περιεχομένου!</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="container card mt-3">
        <p>Γεια σου, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
        <nav>
            <ul>
                <div class="grid grid-2 mt-3">
                <form action="/profile.php" method="get">
                    <button class="btn" >Το προφίλ μου</button>
                </form>
                <form action="/playlists/my_playlists.php" method="get">
                     <button class="btn" >Οι λίστες μου</button>
                </form>
                <form action="/playlists/create_playlist.php" method="get">
                    <button class="btn" >Δημιουργία λίστας</button>
                </form>
                <form action="/logout.php" method="get">
                    <button class="btn" style="background-color: red;">Αποσύνδεση</button>
                </form>
            </ul>
        </nav>
    <?php else: ?>
        <nav>
            <ul>
                <li><a href="login.php">Σύνδεση</a></li>
                <li><a href="register.php">Εγγραφή</a></li>
                <li><a href="about.html">Σχετικά</a></li>
                <li><a href="help.html">Βοήθεια</a></li>
            </ul>
        </nav>
    <?php endif; ?>
    
</body>
</html>
