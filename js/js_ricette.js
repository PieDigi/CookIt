document.addEventListener('DOMContentLoaded', function() {
    // 1. Seleziono i bottoni e il display delle porzioni
    const btnMinus = document.getElementById('btnMeno');
    const btnPlus = document.getElementById('btnPiu');
    const display = document.getElementById('display');
    
    // Partiamo sempre da 1 porzione
    let currentServings = 1;

    // 2. Funzione che aggiorna tutti gli ingredienti
    function updateIngredients() {
        // Prendo tutti gli elementi con classe 'qty' generati dall'IA
        const qtyElements = document.querySelectorAll('.qty');
        
        qtyElements.forEach(function(el) {
            // Leggo il valore base (per 1 persona)
            const baseValue = parseFloat(el.getAttribute('data-base'));
            
            if (!isNaN(baseValue)) {
                // Moltiplico per il numero attuale di porzioni
                let newValue = baseValue * currentServings;
                
                // Arrotondo a massimo 2 decimali (es. 1.5 invece di 1.5000001)
                newValue = Math.round(newValue * 100) / 100;
                
                // Scrivo il nuovo valore nell'HTML
                el.textContent = newValue;
            }
        });
    }

    // 3. Aggiungo le azioni ai bottoni (solo se esistono nella pagina)
    if (btnMeno && btnPiu && display) {
        
        // Tasto MENO
        btnMeno.addEventListener('click', function() {
            if (currentServings > 1) { // Non si può scendere sotto 1
                currentServings--;
                display.textContent = currentServings;
                updateIngredients();
            }
        });

        // Tasto PIÙ
        btnPiu.addEventListener('click', function() {
            if (currentServings < 20) { // Metto un limite massimo a 20 per sicurezza
                currentServings++;
                display.textContent = currentServings;
                updateIngredients();
            }
        });
    }
});