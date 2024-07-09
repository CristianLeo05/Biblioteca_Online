<?php
session_start(); // Avvia la sessione per gestire l'autenticazione dell'utente.

// Verifica se l'utente è un SUPERUSER.
if ($_SESSION['tipo_utente'] != 'SUPERUSER') {
    die(json_encode(['success' => false, 'message' => 'Accesso negato'])); // Se non è un SUPERUSER, interrompe lo script e restituisce un messaggio di errore JSON.
}

// Controlla se è stato inviato l'ID della prenotazione tramite POST.
if (isset($_POST['prenotazione_id'])) {
    // Ottiene l'ID della prenotazione convertendolo in un intero per sicurezza.
    $prenotazioneId = intval($_POST['prenotazione_id']);

    // Crea una nuova connessione al database.
    $connessione = new mysqli('localhost', 'root', '', 'DB_Biblioteca');
    // Verifica se c'è un errore di connessione al database.
    if ($connessione->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Connessione fallita: ' . $connessione->connect_error])); // Se c'è un errore, interrompe lo script e restituisce un messaggio di errore JSON.
    }

    // Aggiorna la data di restituzione della prenotazione nel database.
    $sqlUpdateDataRestituzione = "UPDATE prenotazioni SET data_restituzione = CURRENT_DATE() WHERE id = ?";
    $stmtUpdateDataRestituzione = $connessione->prepare($sqlUpdateDataRestituzione);
    // Verifica se c'è un errore nella preparazione della query.
    if (!$stmtUpdateDataRestituzione) {
        die(json_encode(['success' => false, 'message' => 'Preparazione query fallita: ' . $connessione->error])); // Se c'è un errore, interrompe lo script e restituisce un messaggio di errore JSON.
    }
    $stmtUpdateDataRestituzione->bind_param('i', $prenotazioneId); // Associa l'ID della prenotazione al parametro della query.

    // Esegue la query per aggiornare la data di restituzione.
    if ($stmtUpdateDataRestituzione->execute()) {
        // Se l'aggiornamento della data di restituzione è riuscito, aggiorna lo stato del libro associato.
        $sqlUpdateStatoLibro = "UPDATE Libri SET `stato-libro` = 'DISPONIBILE' WHERE id = (
            SELECT libro_id FROM prenotazioni WHERE id = ?
        )";
        $stmtUpdateStatoLibro = $connessione->prepare($sqlUpdateStatoLibro);
        // Verifica se c'è un errore nella preparazione della query.
        if (!$stmtUpdateStatoLibro) {
            die(json_encode(['success' => false, 'message' => 'Preparazione query fallita: ' . $connessione->error])); // Se c'è un errore, interrompe lo script e restituisce un messaggio di errore JSON.
        }
        $stmtUpdateStatoLibro->bind_param('i', $prenotazioneId); // Associa l'ID della prenotazione al parametro della query.

        // Esegue la query per aggiornare lo stato del libro.
        if ($stmtUpdateStatoLibro->execute()) {
            echo json_encode(['success' => true, 'message' => 'Prenotazione segnata come restituita.']); // Se tutto è andato bene, restituisce un messaggio di successo JSON.
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiornamento dello stato del libro: ' . $stmtUpdateStatoLibro->error]); // Se c'è un errore nell'aggiornamento dello stato del libro, restituisce un messaggio di errore JSON.
        }
        $stmtUpdateStatoLibro->close(); // Chiude l'istruzione preparata per liberare le risorse.
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore nell\'esecuzione della query: ' . $stmtUpdateDataRestituzione->error]); // Se c'è un errore nell'esecuzione della query, restituisce un messaggio di errore JSON.
    }

    $stmtUpdateDataRestituzione->close(); // Chiude l'istruzione preparata per liberare le risorse.
    $connessione->close(); // Chiude la connessione al database per liberare le risorse.
} else {
    echo json_encode(['success' => false, 'message' => 'ID prenotazione non specificato.']); // Se l'ID della prenotazione non è stato specificato, restituisce un messaggio di errore JSON.
}
?>