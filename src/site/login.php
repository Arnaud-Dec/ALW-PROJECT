<?php

require_once "Utils/User.php";
require_once "Utils/FileStorage.php";
require_once "Utils/UserRepository.php";



session_start();
$error = null;

$repo = new UserRepository("Data/users.json");
// exemples d'utilisation :
//$user = $repo->get($login);
$users = $repo->getAll();

if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
    $user = $repo->get($_SESSION["id"]);
    if ($user !== null && isset($_SESSION["password_hash"]) && $_SESSION["password_hash"] === $user->password_hash) {
        echo "bienvenue ". $_SESSION["id"];
        exit;
    }
}


if (isset($_POST["username"]) && isset($_POST["password"])) {
    $login = $_POST["username"];
    $Mdp = $_POST["password"];

    $user = $repo->get($login);

    if ($user !== null) {
        if (password_verify($Mdp, $user->password_hash)) {
            echo "Connexion réussie !";
            $_SESSION["password_hash"] = $user->password_hash;
            $_SESSION["id"] = $user->login;
        } else {
            $error = "Mot de passe incorrect";
        }
    } else {
        $error = "Utilisateur non trouvé";
    }
} else {
    $error = "Veuillez remplir le formulaire.";
}

// TODO: gérer ici la connexion lors de la soumission du formulaire


?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="Public/style.css">
</head>
<body>
    <form action="" method="POST" class="loginForm">
        <h2>Connexion</h2>
        <input type="text" name="username" placeholder="Nom d'utilisateur" required autocomplete="off">
        <input type="password" name="password" placeholder="Mot de passe" required autocomplete="off">

        <?php if (empty($error) == false) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>