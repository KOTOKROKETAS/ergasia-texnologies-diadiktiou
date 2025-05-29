<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
             $stmt = $pdo->prepare(
                    "UPDATE users SET first_name=?, last_name=?, is_public=? WHERE id=?"
                    );
    if (!$firstName || !$lastName) {
        $errors[] = "Τα πεδία δεν μπορεί να είναι κενά.";
    } else {
        $stmt->execute([$firstName, $lastName, $isPublic, $user_id]);

        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?");
        if ($stmt->execute([$firstName, $lastName, $user_id])) {
            $success = true;
        } else {
            $errors[] = "Σφάλμα κατά την αποθήκευση.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    session_destroy();
    header("Location: register.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    
    <meta charset="UTF-8">
    <title>Το προφίλ μου</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<h2 class="container" style="display:flex; gap:55rem;">
    <form action="/index.php" method="get" style="text-align: left">
        <button type="submit" class="btn">Αρχική</button>
    </form>

    <form action="/user_search.php" method="get" style="text-align: right;">
        <button type="submit" class="btn" style="background:purple;">🔍 Χρήστης</button>
    </form>
</h2>


<h1 style="text-align:center">Το Προφίλ μου</h1>


<?php if ($success): ?>
    <p style="color: green;">Τα στοιχεία ενημερώθηκαν με επιτυχία.</p>
<?php endif; ?>

<?php if ($errors): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" style="text-align:center">
    <label>Όνομα:
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" style="max-width:220px"  required>
    </label>
        </form>
<form method="POST" style="text-align:center; margin-top: 15px;"> 
    <label>Επώνυμο:
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" style="max-width:220px"  required>
    </label>
        </form>
<form method="POST" style="text-align:center">
        <label>
  <input type="checkbox" name="is_public"
         <?= $user['is_public'] ? 'checked' : '' ?>>
  Δημόσιο προφίλ
</label>

    <p style="text-align:center"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p style="text-align:center"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <button type="submit" name="update" class="btn" style="background-color: green; color: white;">Αποθήκευση</button>

</form>



<div class="button-group" style="display:flex; justify-content:center; margin-top:2rem;">   
    <form method="POST" onsubmit="return confirm('Είσαι σίγουρος ότι θέλεις να διαγράψεις τον λογαριασμό σου; Αυτή η ενέργεια δεν αναιρείται.')">
    <button type="submit" name="delete" class="btn"
        style="background-color:hsla(0, 69.20%, 25.50%, 0.77); color: white; margin-top: 15px;">
    Διαγραφή λογαριασμού
        </button>
    </form>
</div>



<form action="/logout.php" method="get">
    <button class="btn" style="background-color: red;">Αποσύνδεση</button>
</form>
</body>
</html>
