<!DOCTYPE html>
<html lang="it">

<head>
    <!-- Definizione dei metadati -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Titolo della pagina -->
    <title>Biblioteca Online</title>
    <!-- Collegamento ai file CSS -->
    <link rel="stylesheet" href="../css/main/libri.css">
    <link rel="stylesheet" href="../css/main/search.css">
    <link rel="stylesheet" href="../css/main/styles.css">
</head>

<body>
    <br>
    <h1>Biblioteca Online</h1>
    <br>
    <!-- Barra di navigazione -->
    <nav>
        <ul>
            <?php
            // Inizio della sessione
            session_start();
            // Controllo se l'utente è loggato
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                // Se loggato, mostra i link appropriati
                echo "<li><a class='menu' href='index.php'>Home</a></li>";
                echo "<li><a class='menu' href='../php/logout.php'>Logout</a></li>";
                // Se l'utente è un superuser, mostra il link per aggiungere un libro
                if ($_SESSION['tipo_utente'] == 'SUPERUSER') {
                    echo "<li><a class='menu' href='ImportaLibri.php'>+ Libro</a></li>";
                }
            } else {
                // Se non loggato, mostra i link per il login e la registrazione
                echo "<li><a class='menu' href='index.php'>Home</a></li>";
                echo "<li><a class='menu' href='login.html'>Accedi</a></li>";
                echo "<li><a class='menu' href='register.html'>Registrati</a></li>";
            }
            ?>
            <!-- Form per la ricerca di libri -->
            <br>
            <form action="ricerca_libri.php" method="get">
                <div class="input-container">
                    <input type="text" name="query" class="input" placeholder="search..." id="search" required>
                    <span class="icon">
                        <svg width="19px" height="19px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <!-- Icona di ricerca -->
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
            <!-- Contenitore per i risultati della ricerca -->
            <div id="results"></div>
            <?php
            // Se l'utente è loggato, mostra il suo nome utente
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                echo "<li class='userlink'>Benvenuto: <a href='profile.php'>" . $_SESSION['username'] . "</a>!</li>";
            }
            ?>
        </ul>
    </nav>
    <!-- Intestazione principale -->
    <header>
        <br><br><br><br><br>
        <h2>Benvenuto nella Biblioteca Online</h2>
        <br><br><br><br><br><br><br>
        <div class="presentazione">
            <div class="colonna">
                <h3>Introduzione</h3>
                <p>La tua biblioteca online di libri, eBook,
                    audiolibri e riviste su qualsiasi argomento
                    immaginabile. Esplora un mondo infinito di
                    conoscenza e inizia subito a leggere creando
                    un account gratuito oggi stesso e ottenendo
                    accesso immediato a migliaia di titoli.</p>
            </div>
            <div class="colonna">
                <h3>Prendi, Leggi, Riconsegna, e Ancora!</h3>
                <p>Dopo aver fatto l'accesso potrai prenotare il
                    libro che piu' ti piace direttamente dal
                    nostro sito web!
                    Avrai a disposizione 30 giorni per leggerlo e
                    riconsegnarlo.</p>
            </div>
            <div class="colonna">
                <h3>Di tutto e di piu!</h3>
                <p>In questa biblioteca abbiamo libri di tutti
                    i generi, perditi anche tu nel mondo della
                    lettura...
                </p>
            </div>
            <br><br><br><br><br><br>
    </header>

    <?php
    // Se l'utente è loggato, mostra i libri per categoria
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        // Array dei generi di libri
        $generi = ['fantascienza', 'fantasy', 'horror', 'manga', 'romanzo'];
        // Connessione al database
        $connessione = new mysqli('localhost', 'root', '', 'DB_Biblioteca');

        if ($connessione->connect_error) {
            die("Connessione fallita: " . $connessione->connect_error);
        }

        // Iterazione attraverso i generi
        foreach ($generi as $genere) {
            // Query per selezionare i libri di un determinato genere
            $sql = "SELECT id, percorso_immagine FROM Libri WHERE genere='$genere'";
            $result = $connessione->query($sql);

            if ($result->num_rows > 0) {
                echo "<div
                class='book-category'>";
                // Intestazione della categoria
                echo "<h3 id='genere'>" . ucfirst($genere) . "</h3>";
                // Contenitore per la visualizzazione dei libri
                echo "<div class='scroll-container'>";
                // Iterazione attraverso i risultati della query
                while ($row = $result->fetch_assoc()) {
                    $bookId = $row['id'];
                    $imagePath = $row['percorso_immagine'];
                    // Visualizzazione dell'immagine del libro con link ai dettagli
                    echo "<div class='book-cover'><a href='dettagli_libro.php?id=$bookId'><img src='$imagePath' alt='$genere book'></a></div>";
                }
                echo "</div></div>";
            }
        }

        // Chiusura della connessione al database
        $connessione->close();
    } else {
        // Se l'utente non è loggato, mostra il messaggio di accesso
        echo '<p>Per esplorare le categorie di libri, <a href="login.html">accedi</a> o <a href="register.html">registrati</a>.</p>';
        echo "<br><br><br>";
    }
    ?>
</body>

</html>