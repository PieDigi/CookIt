<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}
$id = $_SESSION["ID"];
$vista = 'menu';
if (isset($_POST['foto']))     $vista = 'foto';
if (isset($_POST['nome']))     $vista = 'nome';
if (isset($_POST['telefono'])) $vista = 'telefono';

$vista_corrente=$_POST["vista_corrente"];
if(isset($_POST['torna'])){
    if($vista_corrente!="menu"){
       	header("Location: impostazioni.php");
    }else{
    	header("Location: ../PROFILO.php");
    }
    exit;
}
$nome = $_SESSION['nome'];

if (isset($_POST['salvaFoto'])) {
    if (isset($_FILES['scegli']) && $_FILES['scegli']['error'] == 0) {
        $percorsoFile = "../fote/img".$nome.".png";
        move_uploaded_file($_FILES['scegli']['tmp_name'], $percorsoFile);
        header("Location: ../PROFILO.php");
        exit;
    }
}
if(isset($_POST["salvaNome"])){
    if(isset($_POST["nuovoNome"])){
        $nuovoNome = $_POST["nuovoNome"];
        require("../pdoConnection.php");
        $stm = $pdo->prepare("UPDATE utenti SET nome=:nome WHERE ID=:id");
        $stm->bindValue(":nome", $nuovoNome);
        $stm->bindValue(":id", $id);
        $stm->execute();
        rename("../fote/img".$nome.".png","../fote/img".$nuovoNome.".png");
        $_SESSION['nome'] = $nuovoNome;
    }
}

if(isset($_POST["salvaTel"])){
    if(isset($_POST["nuovoTel"])){
        $nuovoTel = $_POST["nuovoTel"];
        require("../pdoConnection.php");
        $stm = $pdo->prepare("UPDATE utenti SET telefono=:telefono WHERE ID=:id");
        $stm->bindValue(":telefono", $nuovoTel);
        $stm->bindValue(":id", $id);
        $stm->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impostazioni</title>
    <link rel="stylesheet" type="text/css" href="../css/style_impostazioni.css">
   </head>
   <body>
    <div class="container">
    <!-- BOTTONE INDIETRO -->
    <div class="top-bar">
        <form action="" method="post">
        	<input type="hidden" name="vista_corrente" value="<?= $vista ?>">
    		<input type="submit" class="torna" name="torna" value="‹ Indietro">
        </form>
        <h2><?= $vista === 'menu' ? 'Impostazioni' : ($vista === 'foto' ? 'Foto Profilo' : ($vista === 'nome' ? 'Nome e Cognome' : 'Telefono')) ?></h2>
    </div>

    <?php if ($vista === 'menu'): ?>

        <!-- MENU IMPOSTAZIONI -->
        <div class="menu">
            <form action="" method="post">
                <input type="submit" class="button" name="foto" value="Modifica Foto Profilo">
                <input type="submit" class="button" name="nome" value="Modifica Nome e Cognome">
                <input type="submit" class="button" name="telefono" value="Modifica Numero di Telefono">
            </form>
        </div>

    <?php elseif ($vista === 'foto'): ?>

        <!-- CARD MODIFICA FOTO -->
        <form action="" method="post" enctype="multipart/form-data">
            <div class="card">
                <div class="preview">
                    <img id="anteprima" src="../fote/noFoto.png">
                </div>
                <input type="file" name="scegli" accept="image/*" onchange="previewImage(event)">
                <input type="submit" name="salvaFoto" value="Salva">
            </div>
        </form>
        <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById("anteprima").src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
        </script>

    <?php elseif ($vista === 'nome'): ?>

        <!-- CARD MODIFICA NOME  -->
        <form action="" method="post" >
            <div class="card">
                <input type="text" name="nuovoNome" placeholder="Cambia nome">
                <input type="submit" name="salvaNome" value="Salva">
            </div>
        </form>
        
    <?php elseif ($vista === 'telefono'): ?>

        <!-- CARD MODIFICA TELEFONO  -->
        <form action="" method="post">
            <div class="card">
                <input type="text" name="nuovoTel" placeholder="Cambia numero di telefono">
                <input type="submit" name="salvaTel" value="Salva">
            </div>
        </form>

    <?php endif; ?>

</div>
</body>
</html>