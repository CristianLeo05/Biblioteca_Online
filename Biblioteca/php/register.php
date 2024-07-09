<html>
<?php
// Controlla se il metodo della richiesta è POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupera i dati inviati tramite il form
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    // Hash della password utilizzando SHA-256
    $password = hash('sha256', $_POST['password']);

    // Connessione al database MySQL
    $conn = new mysqli('localhost', 'root', '', 'DB_Biblioteca');

    // Controlla se la connessione ha avuto successo
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error); // Termina lo script in caso di errore di connessione
    }

    // Controlla se l'email è già presente nel database
    $query_controllo = "SELECT * FROM utenti WHERE email = '$email'";
    $result = $conn->query($query_controllo);

    if ($result->num_rows > 0) {
        // Se esiste già un record con lo stesso indirizzo email, reindirizza l'utente alla pagina di login con un messaggio
        $messaggio = urlencode("L'email è già stata utilizzata. Effettua l'accesso con il tuo account.");
        header("Location: /Biblioteca/html/login.html?messaggio=$messaggio");
        exit(); // Assicura che lo script termini dopo il reindirizzamento
    } else {
        // Se l'email non è presente nel database, esegui l'inserimento
        $query_inserimento = "INSERT INTO utenti (nome, cognome, email, username, password)
                                VALUES
                                ('$nome', '$cognome', '$email', '$username', '$password')";
        if ($conn->query($query_inserimento) === TRUE) {
            echo "Registrazione completata con successo!";
            echo "<input type='submit' value='Torna alla pagina principale'>";
        } else {
            // Messaggio di errore in caso di fallimento dell'inserimento
            echo "Errore nell'inserimento dei dati: " . $conn->error;
        }
    }

    // Chiude la connessione al database
    $conn->close();
}
?>
<!-- Form per tornare alla pagina principale -->
<form action="/Biblioteca/html/index.php">
    <input type="submit" value="Torna alla pagina principale">
</form>

</html>