<?php
session_start();
require_once '../includes/config.php';  // Assurez-vous que le chemin est correct.

// GESTION CONTACT ET CONFIDENTIALITE  //
// Récupérer les paramètres
$stmt = $pdo->query("SELECT setting_key, setting_value FROM SiteSettings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// GENRES
try {
    // Les genres et leurs images
    $stmt = $pdo->query("SELECT id, nom, image FROM Genres_musicaux");
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des genres : " . $e->getMessage();
    $genres = []; 
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RareRythm - Accueil</title>
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
                            <?= htmlspecialchars($_SESSION['username']); ?> (Tableau de bord)
                        </a>
                        <a class="btn btn-secondary" href="logout.php" role="button">Déconnexion</a>
                    <?php else: ?>
                        <a class="btn btn-primary" href="login.php" role="button">Connexion</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Section Genres -->
    <div class="container genres-container">
        <h2>Genres</h2>
        <div class="row">
            <?php foreach ($genres as $genre): ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <a href="musique.php?genre=<?= $genre['id'] ?>">
                        <img src="<?= htmlspecialchars($genre['image']) ?>" alt="<?= htmlspecialchars($genre['nom']) ?>" class="bd-placeholder-img card-img-top" width="100%" height="225">
                    </a>
                    <div class="card-body">
                        <p class="card-text"><?= htmlspecialchars($genre['nom']) ?></p>
                    </div>
                </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

    <footer class="bg-light text-center text-lg-start">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Contactez-nous</h5>
                <p>Email : <a href="mailto:<?= htmlspecialchars($settings['contact_email']) ?>"><?= htmlspecialchars($settings['contact_email']) ?></a></p>
            </div>
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Politique de confidentialité</h5>
                <a href="privacy_policy.php">Lire notre politique</a>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2024 RareRythm - Tous droits réservés
    </div>
</footer>
</body>
</html>