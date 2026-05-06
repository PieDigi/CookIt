<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // blocco accessi diretti
    exit;
}

?>

DISPENSA

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_dispensa.css">
    <title>Document</title>
</head>
<body>
    <div class="barra">
        <a class="terzoBarra active" href="DISPENSA.php"><span class="nav-icon">🥫</span>Dispensa</a>
        <a class="terzoBarra" href="CREA.php"><span class="nav-icon">➕</span>Crea</a>
        <a class="terzoBarra" href="PROFILO.php"><span class="nav-icon">👤</span>Profilo</a>
    </div>
</body>
</html>