<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Libro - Biblioteca Online</title>
    <link rel="stylesheet" href="../css/importa_libro/importa_libro.css">
    <link rel="stylesheet" href="../css/importa_libro/upload.css">
</head>

<body>
    <header>
        <h1>Biblioteca Online</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
                <?php
                session_start();
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                    echo "<li class='userlink'>Benvenuto: <a href='profile.php'>" . $_SESSION['username'] . "</a>!</li>";
                }
                ?>
            </ul>
        </nav>
    </header>

    <section class="add-book">
        <h2>Aggiungi Libro</h2>
        <!-- Form per aggiungere un libro -->
        <form action="../php/salva_libro.php" method="post" enctype="multipart/form-data">
            <!-- Input per caricare la copertina -->
            <label class="custum-file-upload" for="file">
                <div class="icon">
                    <!-- Icona SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                        <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                        <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill=""
                                d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z"
                                clip-rule="evenodd" fill-rule="evenodd"></path>
                        </g>
                    </svg>
                </div>
                <div class="text">
                    <span>Clicca per caricare la copertina</span>
                </div>
                <input type="file" name="file" id="file" onchange="previewImage(this)">
            </label>



            <div id="preview"></div> <!-- Questo è il div in cui verrà visualizzata l'anteprima dell'immagine -->

            <!-- Altri campi del form -->
            <label for="titolo">Titolo:</label>
            <input type="text" id="titolo" name="titolo" required><br><br>

            <label for="autore">Autore:</label>
            <input type="text" id="autore" name="autore" required><br><br>

            <label for="genere">Genere:</label>
            <select id="genere" name="genere">
                <option value="fantascienza">Fantascienza</option>
                <option value="fantasy">Fantasy</option>
                <option value="horror">Horror</option>
                <option value="manga">Manga</option>
                <option value="romanzo">Romanzo</option>
            </select>

            <br><br>

            <label for="descrizione">Descrizione:</label><br>
            <textarea id="descrizione" name="descrizione" rows="4" cols="50"
                placeholder="C'era una volta..."></textarea>

            <p>Data di pubblicazione(AAAA-MM-GG):
                <br>
                <select name="anno" id="anno" class="data-select">
                    <?php
                    $annoAttuale = date("Y");
                    for ($i = $annoAttuale; $i >= $annoAttuale - 100; $i--) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>

                <label for="mese">/</label>
                <select name="mese" id="mese" class="data-select">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>

                <label for="giorno">/</label>
                <select name="giorno" id="giorno" class="data-select">
                    <?php
                    for ($i = 1; $i <= 31; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?></select>
            </p>

            <label for="lingua">Lingua:</label>
            <input type="text" id="lingua" name="lingua" required><br><br>

            <label for="quantita_disponibile">Quantità Disponibile:</label>
            <input type="number" id="quantita_disponibile" name="quantita_disponibile" required><br><br>

            <input id="submit" type="submit" value="Aggiungi Libro">
        </form>
    </section>

    <!-- Script JavaScript -->
    <script>
        // Funzione per visualizzare un'anteprima dell'immagine
        function previewImage(input) {
            var preview = document.getElementById('preview');
            preview.innerHTML = ''; // Pulisce l'area di anteprima prima di visualizzare una nuova immagine

            var label = document.querySelector('.custum-file-upload');
            label.style.display = 'none'; // Nasconde l'elemento label

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.width = 200; // Imposta la larghezza dell'immagine a 200 pixel (puoi modificare secondo le tue preferenze)
                    preview.appendChild(img);
                };

                reader.readAsDataURL(input.files[0]); // Legge il file come URL dati
            }
        }
    </script>
</body>

</html>