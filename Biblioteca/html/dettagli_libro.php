<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Definizione dei metadati -->
    <meta charset="UTF-8">
    <!-- Collegamento al file CSS -->
    <link rel="stylesheet" href="../css/main/visualizza_libro.css">
    <!-- Meta tag per la visualizzazione su dispositivi mobili -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Libro</title>
</head>

<body>
    <header>
        <h1>Biblioteca Online</h1>
        <!-- Barra di navigazione -->
        <nav>
            <ul>
                <!-- Link alla homepage e al logout -->
                <li><a href="index.php">Home</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
                <!-- Verifica se l'utente è loggato e mostra il nome utente -->
                <?php
                session_start();
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    echo "<li class='userlink'>Benvenuto: <a href='profile.php'>" . $_SESSION['username'] . "</a>!</li>";
                }
                ?>
            </ul>
        </nav>
    </header>

    <br><br><br>

    <?php
    // Connessione al database
    $connessione = new mysqli('localhost', 'root', '', 'DB_Biblioteca');

    if ($connessione->connect_error) {
        die("Connessione fallita: " . $connessione->connect_error);
    }

    if (isset($_GET['id'])) {
        // Ottieni l'ID del libro dalla query string
        $bookId = intval($_GET['id']);
        // Query per selezionare le informazioni del libro
        $sql = "SELECT * FROM Libri WHERE id=$bookId";
        $result = $connessione->query($sql);

        if ($result->num_rows > 0) {
            // Estrai i dati del libro
            $row = $result->fetch_assoc();
            // Mostra l'immagine del libro
            echo "<div style='text-align: center; padding: 20px;'>";
            echo "<img src='" . $row['percorso_immagine'] . "' alt='Copertina del libro' class='book-cover'>";
            echo "</div>";
            // Mostra le informazioni del libro
            echo "<div style='padding: 20px; text-align: center; font-family: Arial, sans-serif; line-height: 1.6; background-color: #483e3e91; border-radius: 8px;'>";
            echo "<h1 style='font-family: Arial, sans-serif;'>" . $row['titolo'] . "</h1>";
            echo "<p style='font-family: Arial, sans-serif;'><strong>Genere:</strong> " . $row['genere'] . "</p>";
            echo "<p style='font-family: Arial, sans-serif;'><strong>Autore:</strong> " . $row['autore'] . "</p>";
            echo "</div>";
            // Mostra la descrizione del libro
            echo "<div style='padding: 20px; text-align: left; font-family: Arial, sans-serif; line-height: 1.6; background-color: #483e3e91; border-radius: 8px;'>";
            echo "<h2>Descrizione</h2>";
            echo "<p>" . nl2br($row['descrizione']) . "</p>";
            // Verifica lo stato del libro
            if ($row['stato-libro'] == 'occupato') {
                echo "<p id='stato-libro'>Stato Libro: <span style='color: red;'>NON DISPONIBILE</span></p>";
                // Disabilita il pulsante di prenotazione se il libro non è disponibile
                echo "<script>document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('prenota-btn').disabled = true;
                        });</script>";
            } else {
                echo "<p id='stato-libro'>Stato Libro: <span style='color: green;'>DISPONIBILE</span></p>";
            }
            echo "</div>";
            // Imposta un campo nascosto per l'ID del libro
            echo "<input type='hidden' id='libro_id' value='$bookId'>";
        } else {
            echo "<p>Libro non trovato.</p>";
        }
    } else {
        echo "<p>ID del libro non specificato.</p>";
    }

    // Chiudi la connessione al database
    $connessione->close();
    ?>
    <br><br><br><br><br>
    <!-- Pulsante per prenotare il libro -->
    <button id="prenota-btn" onclick="prenotaLibro()">Prenota questo libro</button>
    <br><br><br><br><br>

    <!-- Script JavaScript per gestire la prenotazione del libro -->
    <script>
        function prenotaLibro() {
            // Ottieni l'ID del libro
            const libroId = document.getElementById('libro_id').value;
            // Effettua una richiesta POST per prenotare il libro
            fetch('../php/prenota_libro.php', {
                method: 'POST',
                headers: {
                    'Content-Type':'application/x-www-form-urlencoded'
                },
                // Corpo della richiesta con l'ID del libro
                body: `libro_id=${libroId}`
            })
                .then(response => {
                    // Gestione della risposta della richiesta
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    // Manipolazione dei dati della risposta
                    if (data.success) {
                        // Visualizzazione di un messaggio di successo
                        alert(data.message);
                        // Aggiorna lo stato del libro in tempo reale
                        const statoLibro = document.getElementById('stato-libro');
                        statoLibro.innerHTML = "Stato Libro: <span style='color: red;'>NON DISPONIBILE</span>";
                        document.getElementById('prenota-btn').disabled = true;
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    // Gestione degli errori
                    console.error('Errore:', error);
                    alert('Errore nella prenotazione: ' + error.message);
                });
        }
    </script>
</body>

</html>