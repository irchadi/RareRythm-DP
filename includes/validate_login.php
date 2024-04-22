<?php
session_start();

// Configuration de la base de données
require_once '../includes/config.php';

$error_message = ''; // Initialiser le message d'erreur

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier que les champs ne sont pas vides
    if (!empty($username) && !empty($password)) {
        // Préparation de la requête SQL pour vérifier l'identifiant
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Après la tentative de récupération de l'utilisateur
        if ($user) {
            // Après la tentative de vérification du mot de passe
            if (password_verify($password, $user['password'])) {
                // Utilisateur authentifié
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirection vers le tableau de bord utilisateur
                header('Location: ../public/user_dashboard.php');
                exit;
            } else {
                // Mot de passe incorrect
                $error_message = 'Échec de la vérification du mot de passe pour : ' . $username;
                error_log($error_message);
            }
        } else {
            // Identifiant incorrect
            $error_message = 'Aucun utilisateur trouvé pour : ' . $username;
            error_log($error_message);
        }
    } else {
        $error_message = 'Veuillez entrer un nom d’utilisateur et un mot de passe.';
    }

    if (!empty($error_message)) {
        // Si l'authentification échoue, rediriger avec un message d'erreur
        header('Location: ../public/login.php?error=' . urlencode($error_message));
        exit;
    }
} else {
    // Si le formulaire n'est pas soumis correctement, rediriger vers la page de connexion
    header('Location: ../public/login.php');
    exit;
}
?>
