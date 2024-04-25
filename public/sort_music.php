<?php
session_start();
require_once '../includes/config.php'; // Assurez-vous que le chemin est correct.

// Récupérer le critère de tri depuis la requête
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';

$query = "SELECT morceaux_de_musique.*, genres_musicaux.nom AS genre_nom FROM morceaux_de_musique JOIN genres_musicaux ON morceaux_de_musique.genre_id = genres_musicaux.id";

// Ajouter la logique de tri
switch ($sort_order) {
    case 'date_desc':
        $query .= " ORDER BY morceaux_de_musique.date_upload DESC";
        break;
    case 'genre_asc':
        $query .= " ORDER BY genres_musicaux.nom ASC";
        break;
    case 'genre_desc':
        $query .= " ORDER BY genres_musicaux.nom DESC";
        break;
    default:
        $query .= " ORDER BY morceaux_de_musique.date_upload ASC";
        break;
}

$stmt = $pdo->query($query);
$morceaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoyer la réponse JSON
echo json_encode($morceaux);

