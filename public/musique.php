<?php
// Imaginons que cette page est music_list.php
session_start();
require_once '../includes/config.php'; // Assurez-vous que le chemin est correct.

// Récupérer tous les morceaux de musique
$stmt = $pdo->query("SELECT * FROM morceaux_de_musique");
$morceaux = $stmt->fetchAll();

// Récupérer le morceaux du morceau de musique
$stmt = $pdo->query("SELECT morceaux_de_musique.*, genres_musicaux.nom AS genre_nom FROM morceaux_de_musique JOIN genres_musicaux ON morceaux_de_musique.genre_id = genres_musicaux.id");
$morceaux = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Musiques - RareRythm</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
   
    
    <!-- Conteneur pour la liste de musiques -->
    <div id="music-list" class="container">
    <?php foreach ($morceaux as $morceau): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($morceau['titre']) ?></h5>
                <!-- Afficher la pastille avec le genre -->
                <p class="card-text"><?= htmlspecialchars($morceau['description']) ?></p>
                <audio controls>
                    <source src="musique/<?= htmlspecialchars($morceau['fichier_audio']) ?>" type="audio/mpeg">
                    Votre navigateur ne supporte pas l'élément audio.
                </audio>
                <span class="badge bg-secondary genre-badge" data-genre="<?= htmlspecialchars($morceau['genre_nom']) ?>">
                <?= htmlspecialchars($morceau['genre_nom']) ?></span>

            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>

<script>
document.getElementById('sort').addEventListener('change', function() {
    fetch('sort_music.php?sort=' + this.value)
    .then(response => response.json())
    .then(morceaux => {
        const container = document.getElementById('music-list');
        container.innerHTML = ''; // Vider le conteneur
        morceaux.forEach(morceau => {
            // Créer la carte pour chaque morceau
            const card = `<div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">${morceau.titre}</h5>
                    <p class="card-text">${morceau.description}</p>
                    <audio controls>
                        <source src="musique/${morceau.fichier_audio}" type="audio/mpeg">
                        Votre navigateur ne supporte pas l'élément audio.
                    </audio>
                </div>
            </div>`;
            container.innerHTML += card; // Ajouter la carte au conteneur
        });
    });
});

document.querySelectorAll('.genre-badge').forEach(badge => {
  badge.addEventListener('click', function() {
    const genre = this.getAttribute('data-genre');
    document.querySelectorAll('.card').forEach(card => {
      const cardGenre = card.querySelector('.genre-badge').getAttribute('data-genre');
      if (cardGenre === genre || genre === 'Tous') {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});
</script>

    
</body>
</html>
