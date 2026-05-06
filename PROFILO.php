<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}
$email = $_SESSION['email'];
require("pdoConnection.php");
$stm = $pdo->prepare("SELECT * FROM utenti WHERE email = :email");
$stm->bindValue(":email", $email);
$stm->execute();
$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    $nome = $row['nome'];
    $_SESSION['nome'] = $nome;
    $telefono = $row['telefono'];
    $id = $row['ID'];
    $_SESSION['ID'] = $id;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <title>Profilo CookIt di <?php echo htmlspecialchars($nome); ?></title>
  <link rel="stylesheet" href="css/style_profilo.css">
</head>
<body>

  <div class="header">
    <img src="fote/CookIt_logo.png" alt="CookIt Logo" class="logo">
    <div class="app-name">Cook<span>It</span></div>
  </div>

  <div class="profile-card">
    <div class="avatar-ring">
      <?php 
      $percorsoFile = "fote/img".$nome.".png"; 
      if (file_exists($percorsoFile)) {
          $versione = filemtime($percorsoFile);
          $srcImmagine = $percorsoFile . "?v=" . $versione;
      } else {
          $srcImmagine = "fote/noFoto.png"; 
      }
      ?>
      <img src="<?php echo $srcImmagine; ?>" alt="foto_profilo">
    </div>

    <div class="profile-name">Ciao, <?php echo htmlspecialchars($nome); ?>!</div>

    <div class="profile-info">
      <div class="info-row">
        <div class="info-icon">✉️</div>
        <div>
          <div class="info-label">Email</div>
          <?php echo htmlspecialchars($email); ?>
        </div>
      </div>
      <div class="info-row">
        <div class="info-icon">📞</div>
        <div>
          <div class="info-label">Telefono</div>
          <?php echo htmlspecialchars($telefono); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="menu">
    <a class="menu-item" href="profilo/impostazioni.php">
      <div class="menu-icon green">⚙️</div>
      Impostazioni
      <span class="menu-arrow">›</span>
    </a>
    <a class="menu-item" href="profilo/preferiti.php">
      <div class="menu-icon brown">❤️</div>
      Preferiti
      <span class="menu-arrow">›</span>
    </a>
    <form class="menu-form" action="login.php" method="post">
      <button type="submit" name="logout" class="menu-item">
        <div class="menu-icon red">🚪</div>
        <span class="logout-text">Esci dall'account</span>
        <span class="menu-arrow" style="color:#A94442">›</span>
      </button>
    </form>
  </div>

  <div class="barra">
    <a class="terzoBarra" href="DISPENSA.php">
      <span class="nav-icon">🥫</span>Dispensa
    </a>
    <a class="terzoBarra" href="CREA.php">
      <span class="nav-icon">➕</span>Crea
    </a>
    <a class="terzoBarra active" href="PROFILO.php">
      <span class="nav-icon">👤</span>Profilo
    </a>
  </div>

</body>
</html>