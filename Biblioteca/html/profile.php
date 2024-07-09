<?php
// Inizia la sessione
session_start();
// Se l'utente non è loggato, reindirizzalo alla pagina di login
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.html');
    exit();
}

// Ottieni il tipo di utente, il nome utente e l'email dalla sessione
$tipoUtente = isset($_SESSION['tipo_utente']) ? $_SESSION['tipo_utente'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <!-- Metadati -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/main/visualizza_libro.css">
    <title>Il Mio Profilo - Biblioteca Online</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <!-- Intestazione -->
    <header>
        <h1>Biblioteca Online</h1>
        <!-- Navigazione -->
        <nav>
            <ul>
                <?php
                // Controlla se l'utente è loggato
                if ($_SESSION['logged_in']) {
                    // Se sì, mostra i link appropriati
                    echo "<li><a class='menu' href='index.php'>Home</a></li>";
                    echo "<li><a class='menu' href='../php/logout.php'>Logout</a></li>";
                    // Se l'utente è un SUPERUSER, mostra il link per aggiungere un libro
                    if ($tipoUtente == 'SUPERUSER') {
                        echo "<li><a class='menu' href='ImportaLibri.php'>+ Libro</a></li>";
                    }
                } else {
                    // Se l'utente non è loggato, mostra i link per accedere o registrarsi
                    echo "<li><a class='menu' href='index.php'>Home</a></li>";
                    echo "<li><a class='menu' href='login.html'>Accedi</a></li>";
                    echo "<li><a class='menu' href='register.html'>Registrati</a></li>";
                }
                ?>
                <!-- Mostra il nome utente se l'utente è loggato -->
                <br>
                <?php
                if ($_SESSION['logged_in']) {
                    echo "<li class='userlink'>Benvenuto: <a href='profile.php'>" . $username . "</a>!</li>";
                }
                ?>
            </ul>
        </nav>
    </header>

    <!-- Sezione del profilo -->
    <section class="profile">
        <h2>Il Mio Profilo</h2>
        <!-- Informazioni sul profilo -->
        <div class="profile-info">
            <?php
            // Mostra il nome utente e l'email
            echo "<p><strong>Username: " . $username . "</strong></p>";
            echo "<p><strong>Email: " . $email . "</strong></p>";
            ?>
            <!-- Altre informazioni sul profilo utente possono essere aggiunte qui -->
        </div>
        <?php
        // Se l'utente è un SUPERUSER, mostra il pannello di controllo admin
        if ($tipoUtente == 'SUPERUSER') {
            echo "<h2>Pannello Di Controllo Admin:</h2>";

            // Mostra tutte le prenotazioni
            echo "<h3>Tutte le Prenotazioni:</h3>";
            echo "<table border='1'>";

            // Aggiungi le righe HTML prima di generare la tabella
            echo "<tr><th>ID</th><th>Libro</th><th>Utente</th><th>Data Prenotazione</th><th>Data Scadenza</th><th>Stato Libro</th><th>Data Restituzione</th></tr>";

            // Connettiti al database
            $connessione = new mysqli('localhost', 'root', '', 'DB_Biblioteca');
            if ($connessione->connect_error) {
                die("Connessione fallita: " . $connessione->connect_error);
            }

            // Query per ottenere tutte le prenotazioni
            $sql = "SELECT prenotazioni.id, Libri.titolo, utenti.username, prenotazioni.data_prenotazione, prenotazioni.data_scadenza, prenotazioni.data_restituzione, Libri.`stato-libro`
                    FROM prenotazioni
                    JOIN Libri ON prenotazioni.libro_id = Libri.id 
                    JOIN utenti ON prenotazioni.utente_id = utenti.id;";

            // Esegui la query
            $result = $connessione->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Mostra i dettagli delle prenotazioni in una tabella
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['titolo'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['data_prenotazione'] . "</td>";
                    echo "<td>" . $row['data_scadenza'] . "</td>";
                    echo "<td>" . ($row['stato-libro'] == 'occupato' ? 'Non Disponibile' : 'Disponibile') . "</td>";
                    echo "<td>" . ($row['data_restituzione'] ? $row['data_restituzione'] : 'Non restituito') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Non ci sono prenotazioni.</td></tr>";
            }
            echo "</table>";

            // Mostra solo i libri ancora prestati
            echo "<h3>Libri Ancora Prestati:</h3>";
            $sql_prestati = "SELECT prenotazioni.id, Libri.titolo, utenti.username, prenotazioni.data_prenotazione, prenotazioni.data_restituzione, Libri.`stato-libro`
                             FROM prenotazioni
                             JOIN Libri ON prenotazioni.libro_id = Libri.id 
                             JOIN utenti ON prenotazioni.utente_id = utenti.id
                             WHERE Libri.`stato-libro` = 'occupato';";

            $result_prestati = $connessione->query($sql_prestati);
            if ($result_prestati->num_rows > 0) {
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Libro</th><th>Utente</th><th>Data Prenotazione</th><th>Data Restituzione</th><th>Azione</th></tr>";
                while ($row = $result_prestati->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['titolo'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['data_prenotazione'] . "</td>";
                    echo "<td>" . ($row['data_restituzione'] ? $row['data_restituzione'] : "Non restituito") . "</td>";
                    echo "<td>";
                    if (!$row['data_restituzione']) {
                        echo "<button onclick=\"segnareRestituito(" . $row['id'] . ")\">Segnare come restituito</button>";
                    } else {
                        echo "Già restituito";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Nessun libro ancora prestato.</p>";
            }
            $connessione->close();

        } else if ($tipoUtente == 'UTENTE') {
            echo "<h3>Le mie prenotazioni:</h3>";
            echo "<table border='1'>";

            // Se e' un utente normale vedra' solo la tabella delle sue prenotazioni
            echo "<tr><th>ID</th><th>Libro</th><th>Data Prenotazione</th><th>Data Scadenza</th><th>Stato Libro</th><th>Data restituzione</th></tr>";

            // Connettiti al database
            $connessione = new mysqli('localhost', 'root', '', 'DB_Biblioteca');
            if ($connessione->connect_error) {
                die("Connessione fallita: " . $connessione->connect_error);
            }

            // Ottieni l'ID dell'utente dalla sessione
            $utente_id = $_SESSION['utente_id'];
            // Query per ottenere le prenotazioni dell'utente corrente
            $sql = "SELECT prenotazioni.id, Libri.titolo, prenotazioni.data_prenotazione, prenotazioni.data_scadenza, prenotazioni.data_restituzione, Libri.`stato-libro`
                           FROM prenotazioni
                           JOIN Libri ON prenotazioni.libro_id = Libri.id 
                           WHERE prenotazioni.utente_id = $utente_id;";

            // Esegui la query
            $result = $connessione->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Mostra i dettagli delle prenotazioni dell'utente in una tabella
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['titolo'] . "</td>";
                    echo "<td>" . $row['data_prenotazione'] . "</td>";
                    echo "<td>" . $row['data_scadenza'] . "</td>";
                    echo "<td>" . ($row['stato-libro'] == 'occupato' ? 'Non ancora restituito' : 'Restituito') . "</td>";
                    echo "<td>" . ($row['data_restituzione']  ? $row['data_restituzione'] : 'Non restituito') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Non ci sono prenotazioni.</td></tr>";
            }
            echo "</table>";

            $connessione->close();

        }
        ?>
        <br><br><br><br><br><br><br><br><br><br>
    </section>

    <!-- Script JavaScript -->
    <script>
    // Funzione per segnare un libro come restituito
    function segnareRestituito(prenotazioneId) {
        // Mostra una finestra di conferma all'utente
        if (confirm("Sei sicuro di voler segnare questo libro come restituito?")) {
            // Crea un nuovo oggetto XMLHttpRequest
            const xhr = new XMLHttpRequest();
            // Configura la richiesta: metodo POST verso l'URL specificato
            xhr.open("POST", "../php/segnare_restituito.php", true);
            // Imposta l'header della richiesta per indicare che i dati inviati sono formati
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            // Imposta una funzione per gestire la risposta
            xhr.onreadystatechange = function () {
                // Verifica se la richiesta è completa
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    // Analizza la risposta JSON
                    const response = JSON.parse(xhr.responseText);
                    // Mostra un messaggio di avviso con il messaggio ricevuto dal server
                    alert(response.message);
                    // Se l'operazione ha avuto successo, ricarica la pagina
                    if (response.success) {
                        location.reload();
                    }
                }
            };
            // Invia la richiesta con l'ID della prenotazione
            xhr.send("prenotazione_id=" + prenotazioneId);
        }
    }
</script>


</body>

</html>

