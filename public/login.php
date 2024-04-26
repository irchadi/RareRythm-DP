<?php
session_start();
require_once '../includes/config.php';  // Connexion BDD

$message = '';

try {
    $pdo->beginTransaction();
    $users = $pdo->query("SELECT id, password FROM users");
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");

    while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();
    }

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erreur lors de la mise à jour des mots de passe : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $message = "Nom d'utilisateur introuvable.";
    } elseif (!password_verify($password, $user['password'])) {
        $message = "Le mot de passe est incorrect.";
    } else {
        $_SESSION['username'] = $user['username'];
        header("Location: user_dashboard.php");
        exit;
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
                    <a class="nav-link" href="#">Genres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Favoris</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Évènements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Partager ma musique</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary" href="user_dashboard.php" role="button">Connexion</a>
                </li>
            </ul>
        </div>
    </nav>
<form class="box" action="" method="post" name="login">
    <h1 class="box-logo box-title"><a href="#">RareRythm</a></h1>
    <h1 class="box-title">Connexion</h1>
    <input type="text" class="box-input" name="username" placeholder="Nom d'utilisateur" required>
    <input type="password" class="box-input" name="password" placeholder="Mot de passe" required>
    <input type="submit" value="Connexion" name="submit" class="box-button">
    <p class="box-register">Vous êtes nouveau ici? <a href="register.php">S'inscrire</a></p>
    <?php if (!empty($message)) { ?>
        <p class="errorMessage"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>
</form>
</body>
</html>




