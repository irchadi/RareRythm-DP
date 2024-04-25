<?php


// Configuration de la base de données
require_once '../includes/config.php';

// Initialiser la session
session_start();
// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit(); 
}

// Récupération des données de l'utilisateur
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$userInfo = $stmt->fetch();

// Récupération des playlists de l'utilisateur
$playlistsStmt = $pdo->prepare("SELECT id, titre FROM playlists WHERE utilisateur_id = (SELECT id FROM users WHERE username = ?)");
$playlistsStmt->execute([$_SESSION['username']]);
$playlists = $playlistsStmt->fetchAll();


// Récupération de l'historique d'écoute
$historyStmt = $pdo->prepare("SELECT track_name, listened_on FROM listening_history WHERE user_id = (SELECT id FROM users WHERE username = ?) ORDER BY listened_on DESC");
$historyStmt->execute([$_SESSION['username']]);
$history = $historyStmt->fetchAll();

//gestion d'erreur
if ($stmt->errorCode() != '00000' || $playlistsStmt->errorCode() != '00000' || $historyStmt->errorCode() != '00000') {
    echo "<p>Erreur lors de la récupération des données.</p>";
}

// Créer playlists
if (isset($_POST['createPlaylist'])) {
    $title = $_POST['title'];
    $description = $_POST['description'] ?? ''; // Opérateur null coalescent pour gérer l'absence de description

if ($user) {
    $stmt = $pdo->prepare("INSERT INTO playlists (titre, description, utilisateur_id) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $user['id']]);
    if ($stmt->rowCount() > 0) {
        echo "Playlist créée avec succès!";
    } else {
        echo "Erreur lors de la création de la playlist.";
    }
} else {
    echo "Utilisateur non trouvé.";
}
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
    <h1>Tableau de Bord</h1>
    <p>Bienvenue, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
    <div class="container">

    <section>
        <h2>Informations Personnelles</h2>
        <p>Email: <?= htmlspecialchars($userInfo['email']) ?></p>
    </section>

    <section>
    <h2>Vos Playlists</h2>
    <?php foreach ($playlists as $playlist): ?>
        <p><?= htmlspecialchars($playlist['titre']) ?>
            <a href="edit_playlist.php?id=<?= $playlist['id'] ?>">Modifier</a>
            <a href="delete_playlist.php?id=<?= $playlist['id'] ?>">Supprimer</a>
        </p>
        <?php endforeach; ?>
    </section>

    <section>
        <form action="create_playlist.php" method="POST">
            <h4>Créer une nouvelle playlist</h4>
            <div>
                <label for="title">Nom de la playlist:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="description">Description (optionnel):</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <button type="submit" name="createPlaylist">Créer Playlist</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="alert <?= isset($error) ? 'alert-danger' : 'alert-success' ?>" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
    </section>

    <section>
        <h2>Historique d'écoute</h2>
        <ul>
            <?php foreach ($history as $track): ?>
                <li><?= htmlspecialchars($track['track_name']) ?> écouté le <?= date("d/m/Y", strtotime($track['listened_on'])) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

</div>
<footer class="bg-light text-center text-lg-start">
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">© 2024 RareRythm - Tous droits réservés</div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


