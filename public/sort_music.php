<?php
require_once '../includes/config.php'; // Connexion à la base de données

// Récupérer le critère de tri depuis l'URL
$sort = $_GET['sort'] ?? 'date_asc';

// Déterminer l'ordre SQL basé sur le critère de tri
switch ($sort) {
    case 'date_asc':
        $orderBy = 'date_upload ASC';
        break;
    case 'date_desc':
        $orderBy = 'date_upload DESC';
        break;
    case 'genre_asc':
        $orderBy = 'genre_id ASC'; // Assurez-vous que cette colonne existe et est correcte
        break;
    case 'genre_desc':
        $orderBy = 'genre_id DESC';
        break;
    default:
        $orderBy = 'date_upload ASC';
}

// Construire et exécuter la requête de tri
$query = "SELECT * FROM morceaux_de_musique ORDER BY {$orderBy}";
$result = $pdo->query($query);
$morceaux = $result->fetchAll(PDO::FETCH_ASSOC);

// Convertir les résultats en JSON
echo json_encode($morceaux);
