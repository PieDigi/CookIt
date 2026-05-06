<?php
session_start();

// 1. Controllo se ci sono ingredienti, altrimenti rimando indietro
if (empty($_SESSION['lista_temp'])) {
    header("Location: CREA.php");
    exit();
}

// 2. Preparazione degli ingredienti in una stringa

$lista_testo = implode(",", $_SESSION['lista_temp']);

// 3. Configurazione API (Sostituisci con la tua CHIAVE REALE)
$api_key = 'LA_TUA_CHIAVE_API_QUI'; 

$tempo = $_SESSION['filtri']['tempo'];
$calorie = $_SESSION['filtri']['calorie'];
$intolleranze = $_SESSION['filtri']['intolleranze'];

// Il messaggio che istruisce l'IA
$prompt ="Agisci come un assistente culinario intelligente specializzato nella creazione di ricette personalizzate.

    Devi generare una ricetta utilizzando principalmente questi ingredienti con le rispettive quantità disponibili nella dispensa dell'utente:

    $lista_testo

    REGOLE TASSATIVE DA RISPETTARE RIGOROSAMENTE:
    1. TEMPO: Il tempo totale di preparazione e cottura NON DEVE ASSOLUTAMENTE superare: $tempo. Scegli preparazioni veloci se il tempo è ridotto.
    2. CALORIE: Il totale calorico deve avvicinarsi il più possibile a: $calorie.
    3. INTOLLERANZE: È severamente vietato includere ingredienti o derivati non compatibili con: $intolleranze. Questa è una direttiva di sicurezza alimentare.
    4. PERSONE: Le dosi devono essere calcolate esattamente per 1 persona.

    REGOLE PER LE QUANTITÀ DEGLI INGREDIENTI (MOLTO IMPORTANTE):
    Per permettere al sistema di moltiplicare dinamicamente le porzioni in base al numero di persone, devi isolare SOLO il valore numerico della quantità all'interno di un tag <span class=\"qty\" data-base=\"NUMERO\">NUMERO</span>. 
    Esempi corretti:
    - <span class=\"qty\" data-base=\"150\">150</span> g di farina
    - <span class=\"qty\" data-base=\"2\">2</span> uova
    - <span class=\"qty\" data-base=\"0.5\">0.5</span> bicchiere di latte

    Obiettivo:
    Creare una ricetta realistica, semplice da preparare, coerente con gli ingredienti disponibili e ottimizzata per ridurre gli sprechi.

    IMPORTANTE:
    La risposta deve essere restituita direttamente in HTML pulito e ben strutturato, senza markdown (non inserire ```html), senza blocchi di codice e senza testo esterno.

    Formato HTML richiesto e Struttura di Esempio:

    <h1>Nome Ricetta</h1>

    <p>Breve descrizione della ricetta.</p>

    <p><strong>Tempo di preparazione:</strong> $tempo (o inferiore)</p>

    <p><strong>Calorie stimate:</strong> $calorie</p>

    <h2>Ingredienti</h2>
    <ul>
    <li><span class=\"qty\" data-base=\"100\">100</span> g di Ingrediente 1</li>
    <li><span class=\"qty\" data-base=\"1\">1</span> cucchiaio di Ingrediente 2</li>
    </ul>

    <h2>Procedimento</h2>
    <ol>
    <li>Step 1</li>
    <li>Step 2</li>
    </ol>

    <p><strong>Consiglio:</strong> eventuale suggerimento finale.</p>

    Non scrivere assolutamente nulla al di fuori di questo HTML.";

require_once '../cose/config.php';
$apiKey = OPENAI_API_KEY;

$url = "https://api.openai.com/v1/chat/completions";

//Payload
$data = [
    "model" => "gpt-4o-mini",
    "messages" => [
        [
            "role" => "system",
            "content" => "Sei uno chef AI esperto nella creazione di ricette."
        ],
        [
            "role" => "user",
            "content" => $prompt
        ]
    ],
    "temperature" => 0.7 //--> la creatività
];

// cURL
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //torna come stringa
curl_setopt($ch, CURLOPT_POST, true); // usa il POST
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json", //dico "sta arrivando un JSON"
    "Authorization: Bearer " . $apiKey // mi autentico con la mia api key
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // trasformo il payload $data in JSON
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch); //richiesta


// Controllo errori
if (curl_errno($ch)) {
    echo "Errore cURL: " . curl_error($ch);
    exit;
}

curl_close($ch);

// Decodifica risposta
$result = json_decode($response, true);

// Recupera HTML generato
$htmlRicetta = $result['choices'][0]['message']['content'] ?? "Errore nella generazione della ricetta";
?>

<script src="js/js_ricette.js"></script>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le tue Ricette - PietroOnline</title>
    <link rel="stylesheet" href="css/style_ricetteCreate.css">
    <link rel="stylesheet" href="css/style_crea.css">
</head>
<body>

<div class="main-container">
    <header>
        <h1>Le tue Ricette AI</h1>
        <p>Ecco cosa puoi preparare con i tuoi ingredienti!</p>
    </header>

    <a href="CREA.php" class="btn-back">&larr; Torna agli ingredienti</a>
    <br>
    <div class="recipe-results">
        <?= $htmlRicetta /*$prompt */ ?>
    </div>

    <div class="moltiplicatore">
        <strong>👥 Porzioni:</strong>
        <button class="moltiBtn" id="btnMeno">-</button>
        <span id="display">1</span>
        <button class="moltiBtn" id="btnPiu">+</button>
    </div>
</div>

<div class="barra">
    <a class="terzoBarra" href="DISPENSA.php"><span class="nav-icon">🥫</span>Dispensa</a>
    <a class="terzoBarra active" href="CREA.php"><span class="nav-icon">➕</span>Crea</a>
    <a class="terzoBarra" href="PROFILO.php"><span class="nav-icon">👤</span>Profilo</a>
</div>

</body> 
</html>