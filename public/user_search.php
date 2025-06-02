<?php
/* ------------------ business-logic (μένει ίδιο) ------------------ */
require_once '../config/db.php';

$found  = null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q = trim($_POST['username'] ?? '');
    $stmt = $pdo->prepare(
        "SELECT username FROM users
         WHERE username = ? AND is_public = 1"
    );
    $stmt->execute([$q]);
    if ($found) {
  // φέρε id & isFollowing
  $stmt=$pdo->prepare("SELECT id FROM users WHERE username=?");
  $stmt->execute([$found]);
  $targetId=$stmt->fetchColumn();

  $isFollowing=false;
  if(isset($_SESSION['user_id'])){
     $q=$pdo->prepare("SELECT 1 FROM follows WHERE follower_id=? AND followed_id=?");
     $q->execute([$_SESSION['user_id'],$targetId]);
     $isFollowing=(bool)$q->fetchColumn();
  }
}

    $found = $stmt->fetchColumn();

    if ($found) {
        header("Location: /profile_public.php?u=" . $found);
        exit;
    } else {
        $error = "Δεν βρέθηκε δημόσιο προφίλ με αυτό το όνομα.";
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <title>Αναζήτηση χρήστη</title>

    <!-- ✅  σύνδεση με το global stylesheet -->
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>



<!-- κύριο περιεχόμενο -->
<div class="container fade-in">

    <h1>Αναζήτηση χρήστη</h1>

    <?php if ($error): ?>
        <p class="text-muted"><?= $error ?></p>
    <?php endif; ?>

    <!-- φόρμα αναζήτησης -->
    <form class="form-vertical" method="POST">
        <label>
            <input
                class="w-250"           
                type="text"
                name="username"
                placeholder="username"
                required
                style="max-width:300px"
            >
        </label>

        <button class="btn" type="submit" style="margin-left: 15px;">Εύρεση</button>
    </form>

    <!-- κουμπιά πλοήγησης: space-between στις άκρες -->
    <div class="row-between mt-3">
        <a href="/index.php" class="btn" style="margine-top: 15px;">Αρχική</a>

    </div>

</div><!-- /.container -->

</body>
</html>
