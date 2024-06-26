<?php
session_start();
require_once '../includes/config.php';  // Assurez-vous que le chemin est correct.

// GESTION CONTACT ET CONFIDENTIALITE  //
// Récupérer les paramètres
$stmt = $pdo->query("SELECT setting_key, setting_value FROM SiteSettings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Récupérer l'ID de l'événement depuis l'URL
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les détails de l'événement depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM evenements WHERE id = :id");
$stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Rediriger si l'événement n'est pas trouvé
if (!$event) {
    header("Location: error.php");  // Rediriger vers une page d'erreur si l'événement n'existe pas
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos favoris - RareRythm</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">
        <img src="images/RareRythm logo/logo-transparent-png.png" alt="Logo RareRythm" style="width: 175px;">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="genres.php">Genres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="favorites.php">Favoris</a>
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

    <main>
    <div class="container mt-4">
        <h1>Vos morceaux favoris</h1>
        <?php if ($favorites): ?>
            <div class="row">
                <?php foreach ($favorites as $favorite): ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <img class="bd-placeholder-img card-img-top" width="100%" height="225" src="path/to/album/art/<?= htmlspecialchars($favorite['image_path']) ?>" alt="<?= htmlspecialchars($favorite['titre']) ?>">
                            <div class="card-body">
                                <p class="card-text"><?= htmlspecialchars($favorite['titre']) ?> - <?= htmlspecialchars($favorite['artiste']) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?= htmlspecialchars($favorite['genre_name']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Vous n'avez aucun morceau favori pour le moment.</p>
        <?php endif; ?>
    </div>
    </main>

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