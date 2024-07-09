<?php
session_start(); // Inizia la sessione

$libro_id = intval($_POST['libro_id']); // Converte il valore POST di 'libro_id' in un intero
$username = $_SESSION['username']; // Ottiene il nome utente dalla sessione

$connessione = new mysqli('localhost', 'root', '', 'DB_Biblioteca'); // Connessione al database

// Verifica se la connessione al database ha avuto successo
if ($connessione->connect_error) {
    error_log('Connessione al database fallita: ' . $connessione->connect_error); // Log dell'errore
    echo json_encode(['success' => false, 'message' => 'Connessione al database fallita: ' . $connessione->connect_error]);
    http_response_code(500); // Imposta il codice di risposta HTTP a 500 (Errore interno del server)
    exit; // Termina lo script
}

// Ottenere l'ID dell'utente dal nome utente
$sql = "SELECT id FROM utenti WHERE username = ?"; // Query SQL per ottenere l'ID dell'utente
$stmt = $connessione->prepare($sql); // Prepara la query SQL
$stmt->bind_param('s', $username); // Associa il parametro username alla query

// Esegui la query preparata
if ($stmt->execute()) {
    $result = $stmt->get_result(); // Ottiene il risultato della query
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc(); // Ottiene i dati dell'utente
        $utente_id = $row['id']; // Assegna l'ID utente alla variabile $utente_id
    } else {
        error_log('Utente non trovato'); // Log dell'errore
        echo json_encode(['success' => false, 'message' => 'Utente non trovato']);
        http_response_code(404); // Imposta il codice di risposta HTTP a 404 (Non trovato)
        $stmt->close(); // Chiude lo statement
        $connessione->close(); // Chiude la connessione al database
        exit; // Termina lo script
    }
} else {
    error_log('Errore durante la ricerca dell\'utente: ' . $stmt->error); // Log dell'errore
    echo json_encode(['success' => false, 'message' => 'Errore durante la ricerca dell\'utente: ' . $stmt->error]);
    http_response_code(500); // Imposta il codice di risposta HTTP a 500 (Errore interno del server)
    $stmt->close(); // Chiude lo statement
    $connessione->close(); // Chiude la connessione al database
    exit; // Termina lo script
}

$stmt->close(); // Chiude lo statement

$data_prenotazione = date('Y-m-d'); // Data attuale in formato 'Y-m-d'
$scadenza = date('Y-m-d', strtotime('+1 month', strtotime($data_prenotazione))); // Calcola la data di scadenza aggiungendo un mese

// Inserimento dei dettagli della prenotazione nella tabella prenotazioni
$sql_insert = "INSERT INTO prenotazioni (libro_id, utente_id, data_prenotazione, data_scadenza) VALUES (?, ?, ?, ?)";
$stmt_insert = $connessione->prepare($sql_insert); // Prepara la query SQL di inserimento
if ($stmt_insert === false) {
    error_log('Errore nella preparazione della query di inserimento: ' . $connessione->error); // Log dell'errore
    echo json_encode(['success' => false, 'message' => 'Errore nella preparazione della query di inserimento: ' . $connessione->error]);
    http_response_code(500); // Imposta il codice di risposta HTTP a 500 (Errore interno del server)
    exit; // Termina lo script
}

$stmt_insert->bind_param('iiss', $libro_id, $utente_id, $data_prenotazione, $scadenza); // Associa i parametri alla query di inserimento
if ($stmt_insert->execute()) {
    // Aggiornamento dello stato del libro a 'occupato' nella tabella Libri
    $sql_update = "UPDATE Libri SET `stato-libro` = 'occupato' WHERE id = ?;";
    $stmt_update = $connessione->prepare($sql_update); // Prepara la query SQL di aggiornamento

    if ($stmt_update) {
        $stmt_update->bind_param('i', $libro_id); // Associa il parametro alla query di aggiornamento
        if ($stmt_update->execute()) {
            echo json_encode(['success' => true, 'message' => 'Prenotazione effettuata con successo. Per visualizzare le prenotazioni vai nella tua pagina profilo...']);
        } else {
            error_log('Errore durante l\'aggiornamento dello stato del libro: ' . $stmt_update->error); // Log dell'errore
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento dello stato del libro: ' . $stmt_update->error]);
            http_response_code(500); // Imposta il codice di risposta HTTP a 500 (Errore interno del server)
        }
    } else {
        error_log('Errore nella preparazione della query di aggiornamento: ' . $connessione->error); // Log dell'errore
        echo json_encode(['success' => false, 'message' => 'Errore nella preparazione della query di aggiornamento: ' . $connessione->error]);
        http_response_code(500); // Imposta il codice di risposta HTTP a 500 (Errore interno del server)
    }
} else {
    error_log('Errore durante l\'inserimento dei dettagli della prenotazione: ' . $stmt_insert->error); // Log dell'errore
    echo json_encode(['success' => false, 'message' => 'Errore durante l\'inserimento dei dettagli della prenotazione: ' . $stmt_insert->error]);
    http_response_code(500); // Imposta il codice di risposta HTTP a 500 (Errore interno del server)
}

$stmt_insert->close(); // Chiude lo statement di inserimento
if (isset($stmt_update)) {
    $stmt_update->close(); // Chiude lo statement di aggiornamento se è stato creato
}
$connessione->close(); // Chiude la connessione al database
?>