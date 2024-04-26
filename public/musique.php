<?php
session_start();
require_once '../includes/config.php';

// Récupérer le genre sélectionné s'il est passé en paramètre
$genreId = isset($_GET['genre']) ? intval($_GET['genre']) : null;

// Initialiser la requête de base pour récupérer les morceaux
$query = "SELECT morceaux_de_musique.*, genres_musicaux.nom AS genre_nom 
          FROM morceaux_de_musique 
          JOIN genres_musicaux ON morceaux_de_musique.genre_id = genres_musicaux.id";

// Ajouter une condition si un genre a été sélectionné
if ($genreId !== null) {
    $query .= " WHERE morceaux_de_musique.genre_id = :genreId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':genreId', $genreId, PDO::PARAM_INT);
    $stmt->execute();
    $morceaux = $stmt->fetchAll();
} else {
    // Si aucun genre n'est sélectionné, récupérer tous les morceaux
    $stmt = $pdo->query($query);
    $morceaux = $stmt->fetchAll();
}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Musiques - RareRythm</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
    <img src="images/RareRythm logo/logo-transparent-png.png" alt="Logo RareRythm" style="width: 175px;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="genres.php">Genres</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="musique.php">Musiques</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="events.php">Évènements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="partager_musique.php">Partager ma musique</a>
            </li>
            <li class="nav-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <a class="btn btn-primary" href="user_dashboard.php" role="button">
                        <?= htmlspecialchars($_SESSION['username']) ?>
                    </a>
                    <!-- Lien supplémentaire pour l'administrateur -->
                    <?php if ($_SESSION['username'] === 'admin'): ?>
                        <a class="btn btn-warning" href="admin_dashboard.php" role="button">Admin Dashboard</a>
                    <?php endif; ?>
                    <a class="btn btn-secondary" href="logout.php" role="button">Déconnexion</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="login.php" role="button">Connexion</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <h1>Liste des Musiques</h1>
    
    <!-- Menu déroulant pour le tri -->
    <select id="sort" class="form-control mb-3">
        <option value="date_asc">Plus récents d'abord</option>
        <option value="date_desc">Plus anciens d'abord</option>
        <option value="genre_asc">Genre (A-Z)</option>
        <option value="genre_desc">Genre (Z-A)</option>
    </select>
    <button id="reset-sort" class="btn btn-outline-secondary mb-3">Réinitialiser le tri</button>
   
    
    <!-- Conteneur pour la liste de musiques -->
    <div id="music-list" class="container">
    <?php foreach ($morceaux as $morceau): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($morceau['titre']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($morceau['description']) ?></p>
            <audio controls>
                <source src="musique/<?= htmlspecialchars($morceau['fichier_audio']) ?>" type="audio/mpeg">
                Votre navigateur ne supporte pas l'élément audio.
            </audio>
            <span class="badge bg-secondary genre-badge"><?= htmlspecialchars($morceau['genre_nom']) ?></span>
        </div>
    </div>
<?php endforeach; ?>

    </div>
</div>

<script src="js/script.js"></script>
    <script>
        // Script pour réinitialiser le tri
        document.getElementById('reset-sort').addEventListener('click', function() {
            window.location.href = 'musique.php'; // Simple redirection pour réinitialiser
        });
    </script>

    
</body>
</html>
