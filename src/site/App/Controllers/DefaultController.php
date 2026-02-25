<?php

namespace App\Controllers;

use Framework\AbstractController;
use App\Repositories\UserRepository;
use App\Repositories\GameConfigRepository;
class DefaultController extends AbstractController
{
    public function getTwig()
    {
        $loader = new \Twig\Loader\FilesystemLoader('App/Templates');
        $twig = new \Twig\Environment($loader);

        $twig->addGlobal('id', $_SESSION['id'] ?? null);

        return $twig;
    }

    public function login()
    {
        var_dump("mdp Beer4Life");

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
        //---

        $twig = $this->getTwig();


        $userId = $_SESSION['id'] ?? null;

        echo $twig->render('login.html.twig', [
            'id'    => $userId,
            'error' => $error
        ]);
    }

    public function dashboard()
    {

        if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
            http_response_code(401);
            exit();
        }

        $filePath = "Data/Config/game_config.json";

        if (!file_exists($filePath)) {
            die("Fichier introuvable : " . $filePath);
        }

        $GameConf = new GameConfigRepository($filePath);

        $products = (array) $GameConf->getProducts();
        $buildings = (array) $GameConf->getBuildings();

        //---

        $twig = $this->getTwig();

        echo $twig->render('dashboard.html.twig', [
            'id' => $_SESSION['id'],
            'products' => $products,
            'buildings' => $buildings
        ]);
    }

    public function index()
    {

        $twig = $this->getTwig();

        $data = "Bonjour le monde !";
        $this->app->view()->setParam('pageTitle', $data);

        echo $twig->render('homepage.html.twig', [
            'message' => $data,
            'parameters' => $this->parameters
        ]);
    }

    public function test()
    {
        echo '<p>Cette page a reçu un paramètre nommé "nombre" et valant "' . $this->parameters['nombre'] . '"</p>
              <p>Contenu complet de <code>$this->parameters</code>:</p>
              <pre>';
        print_r($this->parameters);
    }

    public function error404()
    {
        http_response_code(404);
        echo $this->getTwig()->render('404.html.twig');

    }
}
