<?php
session_start();

// Καθαρισμός όλων των session μεταβλητών
$_SESSION = [];

// Καταστροφή του session
session_destroy();

// Ανακατεύθυνση στη σελίδα login
header("Location: login.php");
exit;
