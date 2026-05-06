<?php
session_start();
require("pdoConnection.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['ID'];
$errore_qty = "";

// 1. Gestione Aggiunta dalla Dispensa
if (isset($_POST["aggiungi_dispensa"]) && !empty($_POST["ingrediente_dispensa_raw"])) {
    if (!isset($_SESSION['lista_temp'])) $_SESSION['lista_temp'] = [];
    
    $_SESSION['lista_temp'][] = $_POST["ingrediente_dispensa_raw"];
    // È buona norma fare un redirect per evitare il doppio invio al refresh
    header("Location: CREA.php"); 
    exit;
}

// 2. Gestione Aggiunta Nuovo Ingrediente
if (isset($_POST['aggiungi_nuovo']) && !empty($_POST['nome_ingrediente_nuovo'])) {
    if (!isset($_SESSION['lista_temp'])) $_SESSION['lista_temp'] = [];
    
    $nome = $_POST['nome_ingrediente_nuovo'];
    if (!empty($_POST["quantita_nuova"])) {
        $qty = $_POST["quantita_nuova"];
        $_SESSION['lista_temp'][] = "$nome ($qty)";
        header("Location: CREA.php#aggiungi2");
        exit; // Ricordati sempre l'exit dopo header Location
    } else {
        $errore_qty = "Errore: manca la quantità";
    }
}

// Salvataggio filtri (solo se il form viene inviato e non è un reset)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['reset'])) {
    $_SESSION['filtri'] = [
        'tempo' => $_POST['tempo'] ?? '5',
        'calorie' => $_POST['calorie'] ?? 'non specificato',
        'intolleranza' => $_POST['intolleranza'] ?? 'Nessuna'
    ];
}

// 3. Reset Lista
if (isset($_POST['reset'])) {
    unset($_SESSION['lista_temp']);
    unset($_SESSION['filtri']);
    header("Location: CREA.php");
    exit;
}

// Rimuovi ultimo elemento
if(isset($_POST['rimuovi']) && !empty($_SESSION['lista_temp'])){
    array_pop($_SESSION['lista_temp']);
    header("Location: CREA.php#rimU");
    exit;
}

// 4. Invio Finale
if(isset($_POST["genera"])){
    //print_r($_SESSION['lista_temp']); 
    header("Location: ricetteCreate.php");
    exit;
}

// --- RECUPERO DATI DAL DATABASE ---
$tuttiIngredienti = $pdo->query("SELECT * FROM ingredienti ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

$stmUser = $pdo->prepare("
    SELECT i.nome, ipu.quantita 
    FROM ingredientiPerUtenti ipu 
    JOIN ingredienti i ON ipu.ingrediente_id = i.id 
    WHERE ipu.user_id = :uid
");
$stmUser->execute([':uid' => $user_id]);
$dispensaUtente = $stmUser->fetchAll(PDO::FETCH_ASSOC);

$intolleranze = $pdo->query("SELECT * FROM intolleranze ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PietroOnline - Crea Ricetta</title>
    <link rel="stylesheet" href="css/style_crea.css">
</head>
<body>

<div class="main-container">
    <header>
        <h1>Generatore di Ricette</h1>
    </header>

    <form method="POST" action="">
        
        <section class="card filters-area">
            <h2>1. Preferenze</h2>
            <div class="filter-grid">
                <div class="filter-item">
                    <label>Tempo Max (min):</label>
                    <input type="number" name="tempo" placeholder="es. 30" min="2" value="<?= $_SESSION['filtri']['tempo'] ?? '' ?>">
                </div>
                <div class="filter-item">
                    <label>Calorie Max:</label>
                    <input type="number" name="calorie" placeholder="es. 600" value="<?= $_SESSION['filtri']['calorie'] ?? '' ?>">
                </div>
                <div class="filter-item">
                    <label>Intolleranze:</label>
                    <input list="lista-intolleranze" name="intolleranza" placeholder="Scrivi o seleziona..." autocomplete="off" value="<?= isset($_SESSION['filtri']['intolleranza']) ? htmlspecialchars($_SESSION['filtri']['intolleranza']) : '' ?>">
                    <datalist id="lista-intolleranze">
                        <option value="Nessuna"></option>
                        <?php foreach($intolleranze as $int): ?>
                            <option value="<?= $int['nome'] ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
</div>
            </div>
        </section>
<br>
        <div class="grid-layout">
            <section class="card selection-area">
                <h2>2. Ingredienti</h2>
                
                <!-- 1° DATALIST: Dalla tua dispensa -->
                <div class="input-group">
                    <label>Dalla tua Dispensa:</label>
                    <input list="lista-dispensa" name="ingrediente_dispensa_raw" placeholder="Scrivi o seleziona..." autocomplete="off">
                    <datalist id="lista-dispensa">
                        <?php foreach($dispensaUtente as $item): ?>
                            <option value="<?= $item['nome'] ?> (<?= $item['quantita'] ?>)"></option>
                        <?php endforeach; ?>
                    </datalist>
                    <button type="submit" name="aggiungi_dispensa" class="btn-add">Aggiungi</button>
                </div>

                <div class="separator"><span>oppure</span></div>

                <!-- 2° DATALIST: Nuovo ingrediente -->
                <div class="input-group">
                    <label>Nuovo ingrediente:</label>
                    <input list="lista-nuovi" name="nome_ingrediente_nuovo" placeholder="Scrivi o seleziona..." autocomplete="off">
                    <datalist id="lista-nuovi">
                        <?php foreach($tuttiIngredienti as $ing): ?>
                            <option value="<?= $ing['nome'] ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                    
                    <input type="text" name="quantita_nuova" placeholder="Quantità (es. 200g/3 fette/5)">
                    <?php if($errore_qty){ ?>
                        <span class="error-text"><?= $errore_qty ?></span>
                    <?php } ?>
                    <button type="submit" id="aggiungi2" name="aggiungi_nuovo" class="btn-add secondary">Aggiungi</button>
                </div>
            </section>

            <section class="card summary-area">
                <h2>3. Riepilogo</h2>
                <div class="lista-scelti">
                    <?php if(!empty($_SESSION['lista_temp'])): ?>
                        <ul>
                            <?php foreach($_SESSION['lista_temp'] as $item): ?>
                                <li><?= htmlspecialchars($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <button type="submit" name="genera" class="btn-generate">GENERA ORA</button>
                        <button type="submit" id="rimU" name="rimuovi" class="btn-reset">Rimuovi l'ultimo</button>
                        <button type="submit" name="reset" class="btn-reset">Svuota tutto</button>
                    <?php else: ?>
                        <p class="empty-msg">La lista è vuota.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </form>
    <br><br><br>
</div>

<div class="barra">
    <a class="terzoBarra" href="DISPENSA.php"><span class="nav-icon">🥫</span>Dispensa</a>
    <a class="terzoBarra active" href="CREA.php"><span class="nav-icon">➕</span>Crea</a>
    <a class="terzoBarra" href="PROFILO.php"><span class="nav-icon">👤</span>Profilo</a>
</div>

</body>
</html>