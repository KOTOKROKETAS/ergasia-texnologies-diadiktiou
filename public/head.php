<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <title><?= $page_title ?? 'My App'; ?></title>
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
