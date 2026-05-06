<?php
session_start();

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST["submit"])) {

    // INPUT
    $nome = test_input($_POST["Nome"] . " " . $_POST["Cognome"]);
    $telefono = test_input($_POST["Telefono"]);
    $email = test_input($_POST["Email"]);
    $password = $_POST["Password"];
    $passConfirm = $_POST["PassConfirm"];
    $remember = isset($_POST["remember"]);

    // PASSWORD CHECK
    if ($password !== $passConfirm) {
        $error = "Le password non coincidono";
    } else {

        require("pdoConnection.php"); // $pdo

        /* ������ CONTROLLO EMAIL ESISTENTE */
        $check = $pdo->prepare("SELECT id FROM utenti WHERE email = :email");
        $check->execute([
            ":email" => $email
        ]);

        if ($check->fetch()) {
            $error = "Email già registrata";
        } else {

            // HASH PASSWORD
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            /* INSERT */
            $stmt = $pdo->prepare(
                "INSERT INTO utenti (nome, telefono, email, password)
                 VALUES (:nome, :telefono, :email, :password)"
            );

            $ok = $stmt->execute([
                ":nome" => $nome,
                ":telefono" => $telefono,
                ":email" => $email,
                ":password" => $passwordHash
            ]);

            if ($ok) {

                // COOKIE
                if ($remember) {
                    setcookie("email", $email, time() + (86400 * 7), "/");
                    setcookie("telefono", $telefono, time() + (86400 * 7), "/");
                }

                header("Location: login.php");
                exit();

            } else {
                $error = "Errore durante la registrazione";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Registrati | CookIt</title>
    <link rel="stylesheet" type="text/css" href="css/style_login.css">
</head>
<body>

    <div class="reg-container">
        <h2>Nuovo Account</h2>
        <p class="desc">Compila il modulo per accedere a CookIT.</p> <br>

        <form id="regForm" action="" method="post">
            
            <div class="form-section-title"><strong>Dati Personali</strong></div><br>
            
            <div class="input-group">
                <input type="text" id="Nome" name="Nome" placeholder="Nome" required>
            </div>
            <div class="input-group">
                <input type="text" id="Cognome" name="Cognome" placeholder="Cognome" required>
            </div>
            <div class="input-group">
                <input type="tel" id="Telefono" name="Telefono" placeholder="Telefono" required>
            </div>

            <div class="form-section-title"><strong>Sicurezza</strong></div><br>

            <div class="input-group">
                <input type="email" id="Email" name="Email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" id="Password" name="Password" placeholder="Crea password" required>
            </div>
            <div class="input-group">
                <input type="password" id="PassConfirm" name="PassConfirm" placeholder="Conferma password" required>
            </div>

            <button type="submit" name="submit" class="btn-submit">CREA ACCOUNT</button>
        </form>

        <div id="feedback">
            <?php if (!empty($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
        </div>

        <br><a href="login.php" class="login-link">
            Hai già un account? <strong>Accedi qui</strong>
        </a>
    </div>
</body>
</html>