// Wacht tot de hele pagina geladen is voordat we handlers toevoegen
document.addEventListener('DOMContentLoaded', () => {
    // Zoek alle formulieren met een data-confirm attribuut (bijv. delete-knop)
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        // Elke submit wordt onderschept om een bevestigingspop-up te tonen
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm') || 'Weet je het zeker?';
            // Annuleer de submit als de gebruiker op "Annuleren" klikt
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
