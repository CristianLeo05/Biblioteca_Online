<?php
// Connessione al database
$conn = new mysqli('localhost', 'root', '', 'DB_Biblioteca');

// Controllo se la connessione ha avuto successo
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Ottieni il valore della query dalla richiesta GET
$query = $_GET['query'];

// Costruisci la query SQL per cercare libri basati sul titolo, autore o genere
$sql = "SELECT * FROM Libri WHERE titolo LIKE '%$query%' OR autore LIKE '%$query%' OR genere LIKE '%$query%'";

// Esegui la query
$result = $conn->query($sql);

// Array per memorizzare i libri trovati
$books = array();

// Verifica se ci sono risultati
if ($result->num_rows > 0) {
    // Scansiona tutti i risultati e memorizzali nell'array $books
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// Converti l'array dei libri in formato JSON e stampalo
echo json_encode($books);

// Chiudi la connessione al database
$conn->close();
?>