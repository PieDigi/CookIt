<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php"); // blocco accessi diretti
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="../PROFILO.php" method="post">
        <input type="submit" class="torna" value="< Indietro">
    </form>
    preferiti
</body>
</html>
