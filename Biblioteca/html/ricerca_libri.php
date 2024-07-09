<?php
// Inizia la sessione se non è già attiva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ottiene la query di ricerca dai parametri GET, se presente
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Connessione al database MySQL
$connessione = new mysqli('localhost', 'root', '', 'DB_Biblioteca');

// Verifica se la connessione al database è avvenuta con successo
if ($connessione->connect_error) {
    die("Connessione fallita: " . $connessione->connect_error);
}

// Esegue una query per cercare libri in base al titolo, autore o descrizione
$sql = "SELECT id, titolo, autore, percorso_immagine FROM Libri WHERE titolo LIKE '%$query%' OR autore LIKE '%$query%' OR descrizione LIKE '%$query%'";
$result = $connessione->query($sql);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risultati della ricerca</title>
    <link rel="stylesheet" href="../css/main/styles.css">
    <link rel="stylesheet" href="../css/main/libri.css">
    <link rel="stylesheet" href="../css/main/search.css">
</head>

<body>
    <br>
    <h1>Biblioteca Online</h1>
    <br>
    <nav>
        <ul>
            <?php
            // Verifica se l'utente è loggato
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                echo "<li><a class='menu' href='index.php'>Home</a></li>";
                echo "<li><a class='menu' href='../php/logout.php'>Logout</a></li>";
                // Mostra il link per importare libri solo se l'utente è un SUPERUSER
                if ($_SESSION['tipo_utente'] == 'SUPERUSER') {
                    echo "<li><a class='menu' href='ImportaLibri.php'>+ Libro</a></li>";
                }
            } else {
                // Se l'utente non è loggato, mostra i link per accedere o registrarsi
                echo "<li><a class='menu' href='index.php'>Home</a></li>";
                echo "<li><a class='menu' href='login.html'>Accedi</a></li>";
                echo "<li><a class='menu' href='register.html'>Registrati</a></li>";
            }
            ?>
            <br>
            <!-- Form di ricerca -->
            <form action="ricerca_libri.php" method="get">
                <div class="input-container">
                    <input type="text" name="query" class="input" placeholder="search..." id="search" required>
                    <span class="icon">
                        <!-- Icona di ricerca -->
                        <svg width="19px" height="19px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path opacity="1" d="M14 5H20" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path opacity="1" d="M14 8H17" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path d="M21 11.5C21 16.75 16.75 21 11.5 21C6.25 21 2 16.5 2 11.5C2 6.25 6.25 2 11.5 2"
                                stroke="#000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path opacity="1" d="M22 22L20 20" stroke="#000" stroke-width="3.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </span>
                </div>
            </form>
            <div id="results"></div>
            <?php
            // Visualizza il nome utente se loggato
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                echo "<li class='userlink'>Benvenuto: <a href='profile.php'>" . $_SESSION['username'] . "</a>!</li>";
            }
            ?>
        </ul>
    </nav>
    <header>
        <!-- Spaziatura per separare l'intestazione dal contenuto -->
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    </header>
    <br><br>
    <h1>Risultati della ricerca per:</h1>
    <?php
    // Mostra i risultati della ricerca
    if ($result->num_rows > 0) {
        echo "<div class='book-category'>";
        echo "<h3 id='genere'>" . htmlspecialchars($query) . "</h3>";
        echo "<div class='scroll-container'>";
        // Itera attraverso i risultati e crea le miniature dei libri
        while ($row = $result->fetch_assoc()) {
            $bookId = $row['id'];
            $imagePath = $row['percorso_immagine'];
            echo "<div class='book-cover'><a href='dettagli_libro.php?id=$bookId'><img src='$imagePath' alt='Book Cover'></a></div>";
        }
        echo "</div></div>";
    } else {
        // Mostra un messaggio se non ci sono risultati
        echo "<p>Nessun risultato trovato.</p>";
    }
    // Chiude la connessione al database
    $connessione->close();
    ?>
    </div>
</body>

</html>