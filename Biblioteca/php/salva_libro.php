<?php
// Abilita la visualizzazione degli errori
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titolo = $_POST["titolo"];
    $autore = $_POST["autore"];
    $genere = $_POST["genere"];
    $descrizione = $_POST["descrizione"];
    $data_pubb = $_POST["anno"] . "-" . $_POST["mese"] . "-" . $_POST["giorno"];
    $lingua = $_POST["lingua"];
    $n_disponibili = $_POST["quantita_disponibile"];

    $path = salva_copertina($genere); // Richiama la funzione per salvare la copertina nel progetto e ritorna il path dell'immagine

    $conn = new mysqli('localhost', 'root', '', 'DB_Biblioteca');

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Usa le dichiarazioni preparate per evitare l'SQL injection
    $stmt = $conn->prepare("INSERT INTO Libri (titolo, autore, genere, descrizione, anno_pubblicazione, lingua, quantita_disponibile, percorso_immagine) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $titolo, $autore, $genere, $descrizione, $data_pubb, $lingua, $n_disponibili, $path);

    if ($stmt->execute()) {
        echo "Libro salvato con successo<br>";
    } else {
        echo "Errore: " . $stmt->error . "<br>";
    }

    $stmt->close();
    $conn->close();
}

function salva_copertina($genere) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Percorso di destinazione per il file caricato
        $uploadDir = '../copertine/' . $genere . '/';
        
        // Crea la directory se non esiste
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Nome del file sul server
        $fileName = basename($_FILES['file']['name']);
        
        // Percorso completo del file sul server
        $uploadFile = $uploadDir . $fileName;
        
        // Sposta il file caricato nella directory di destinazione
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            return $uploadFile;
        } else {
            echo "Si è verificato un errore durante il caricamento del file.<br>";
            return null;
        }
    } else {
        echo "Si è verificato un errore durante il caricamento del file.<br>";
        return null;
    }
}
?>
