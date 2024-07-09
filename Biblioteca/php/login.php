<?php
session_start(); // Inizia la sessione

// Controlla se il metodo della richiesta è POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera i dati inviati dal form
    $input1 = $_POST['username']; // input1 può essere username o email
    $password = hash('sha256', $_POST['password']); // Hash della password utilizzando SHA-256

    // Connessione al database MySQL
    $conn = new mysqli('localhost', 'root', '', 'DB_Biblioteca');

    // Verifica se la connessione al database è avvenuta con successo
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Costruisce la query SQL in base al tipo di input (email o username)
    if (strpos($input1, '@') !== false) {
        // Se $input1 contiene '@', è considerato un'email
        $query = "SELECT * FROM utenti WHERE email = ? AND password = ?";
    } else {
        // Altrimenti, $input1 è considerato un nome utente
        $query = "SELECT * FROM utenti WHERE username = ? AND password = ?";
    }

    // Prepara e esegue la query SQL
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $input1, $password); // Associa i parametri alla query
    $stmt->execute(); // Esegue la query
    $result = $stmt->get_result(); // Ottiene il risultato della query

    // Verifica se è stato trovato un utente
    if ($result->num_rows > 0) {
        // Se l'utente esiste, recupera i suoi dati
        $row = $result->fetch_assoc();
        $_SESSION['logged_in'] = true; // Imposta la variabile di sessione 'logged_in' a true
        $_SESSION['username'] = $row['username']; // Salva il nome utente nella sessione
        $_SESSION['utente_id'] = $row['id']; // Salva l'ID utente nella sessione
        $_SESSION['tipo_utente'] = $row['tipo_utente']; // Salva il tipo di utente nella sessione
        $_SESSION['email'] = $row['email']; // Salva l'email nella sessione
        $stmt->close(); // Chiude lo statement
        $conn->close(); // Chiude la connessione al database
        header("Location: /Biblioteca/html/index.php"); // Reindirizza alla pagina principale
        exit(); // Termina lo script
    } else {
        // Se l'utente non esiste, chiude lo statement e la connessione e reindirizza alla pagina di login
        $stmt->close(); // Chiude lo statement
        $conn->close(); // Chiude la connessione al database
        header("Location: /Biblioteca/html/login.html"); // Reindirizza alla pagina di login
        exit(); // Termina lo script
    }
}
?>
