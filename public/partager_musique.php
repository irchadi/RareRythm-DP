<?php
session_start();
require_once '../includes/config.php'; 

// Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$message = ''; // Pour afficher des messages à l'utilisateur

// Gérer la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fichier'])) {
    $titre = $_POST['titre'];
    $artiste = $_POST['artiste'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $file = $_FILES['fichier'];

    // Chemin du dossier où les fichiers seront sauvegardés
    $uploadDir = '../public/musique/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Gestion du fichier reçu
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $valid_extensions = array("mp3", "wav", "ogg");
    $newFileName = uniqid() . '.' . $fileExtension; // Nom de fichier unique pour éviter les conflits
    $dest_path = $uploadDir . $newFileName;

    // Vérifiez si le fichier est une extension valide
    if (in_array($fileExtension, $valid_extensions)) {
        if ($file['error'] === 0) {
            if ($file['size'] < 10000000) { // Limite de taille de fichier à 10MB
                if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                    // Insertion des données dans la base de données
                    $stmt = $pdo->prepare("INSERT INTO morceaux_de_musique (titre, artiste, genre_id, fichier_audio, description) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$titre, $artiste, $genre, $newFileName, $description])) {
                        $message = "Musique partagée avec succès!";
                    } else {
                        $message = "Erreur lors de l'enregistrement dans la base de données.";
                    }
                } else {
                    $message = "Erreur lors du téléchargement du fichier.";
                }
            } else {
                $message = "Le fichier est trop grand.";
            }
        } else {
            $message = "Erreur lors du téléchargement du fichier.";
        }
    } else {
        $message = "Type de fichier non autorisé. Seuls les formats mp3, wav, et ogg sont acceptés.";
    }
}

// Récupérer la liste des genres pour le formulaire
$genreStmt = $pdo->query("SELECT id, nom FROM genres_musicaux");
$genres = $genreStmt->fetchAll();


// GESTION CONTACT ET CONFIDENTIALITE  //
// Récupérer les paramètres
$stmt = $pdo->query("SELECT setting_key, setting_value FROM SiteSettings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partager votre musique - RareRythm</title>
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
    <div class="container">
        <h1>Partager votre musique</h1>
        <p><?= htmlspecialchars($message) ?></p>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
            </div>
            <div class="mb-3">
                <label for="artiste" class="form-label">Artiste</label>
                <input type="text" class="form-control" id="artiste" name="artiste" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <select class="form-control" id="genre" name="genre" required>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?= $genre['id'] ?>"><?= htmlspecialchars($genre['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="fichier" class="form-label">Fichier audio</label>
                <input type="file" class="form-control" id="fichier" name="fichier" required>
            </div>
            <button type="submit" class="btn btn-primary">Partager</button>
        </form>
    </div>
    </main>

    <footer class="bg-light text-center text-lg-start fixed-bottom">
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