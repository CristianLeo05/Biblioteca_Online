<?php
session_start(); // Inizia la sessione

// Elimina tutte le variabili di sessione
$_SESSION = array(); // Imposta l'array $_SESSION a vuoto per cancellare tutte le variabili di sessione

// Cancella il cookie di sessione se è impostato l'uso dei cookie per la sessione
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params(); // Ottiene i parametri del cookie di sessione
    setcookie(
        session_name(),
        '',
        time() - 42000, // Imposta il cookie di sessione a una data passata per eliminarlo
        $params["path"], // Specifica il percorso del cookie
        $params["domain"], // Specifica il dominio del cookie
        $params["secure"], // Specifica se il cookie deve essere trasmesso solo su connessioni sicure HTTPS
        $params["httponly"] // Specifica se il cookie è accessibile solo tramite il protocollo HTTP (non Javascript)
    );
}

// Distrugge la sessione
session_destroy(); // Distrugge tutti i dati associati alla sessione corrente

// Reindirizza l'utente alla pagina di login o a un'altra pagina di destinazione
header("Location: ../html/index.php"); // Reindirizza alla pagina principale
exit; // Termina lo script per assicurarsi che non venga eseguito altro codice
?>