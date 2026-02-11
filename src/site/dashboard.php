<?php
require_once "Utils/GameConfigRepository.php";
require_once "Utils/FileStorage.php";

session_start();


if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    http_response_code(401);
    exit();
}

$filePath = "Data/Config/game_config.json";

if (!file_exists($filePath)) {
    die("Fichier introuvable : " . $filePath);
}

$GameConf = new GameConfigRepository($filePath);



$products = $GameConf->getProducts();
$buildings = $GameConf->getBuildings();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Maquette Ferme Manager</title>
    <link rel="stylesheet" href="Public/style.css">

    <!-- Intégration du JS (Partie 2.1) -->
    <!-- <script src="Public/JS/FermeEngine.js" defer></script> -->
    <!-- <script src="Public/JS/main.js" defer></script> -->
</head>

<body>
<h1>Ferme Manager</h1>

<section id="inventory">
    <h2>Inventaire</h2>
    <?php
    foreach ($products as $idProduit => $infosProduit) {
    ?>

    <article id="product-<?php echo $idProduit; ?>">
        <h3>
            <?php echo $infosProduit->icon; ?>
            <?php echo $infosProduit->display; ?>
        </h3>
        <div>Stock : <output class="stock">0</output></div>
    </article>

    <?php
    }
    ?>
</section>

<?php if (empty($error) == false) { ?>
    <div class="error"><?php echo $error; ?></div>
<?php } ?>

<hr>

<section id="buildings">
    <h2>Bâtiments</h2>

    <?php
    foreach ($buildings as $idBuilding => $infosBuilding) {
    ?>

    <article id="building-<?php echo $idBuilding; ?>">
        <h3><?php echo $infosBuilding->display; ?> (Niv. <output class="level">1</output>)</h3>

        <button class="harvest"><?php echo $infosBuilding->action; ?></button>

        <?php if(isset($infosBuilding->cost )){
            $costId = $infosBuilding->cost;
            $iconCost = $products->$costId->icon;
            ?>
        <button class="upgrade">
            Améliorer <br>
            Coût : <output class="cost-<?php echo $idBuilding; ?>">10 <?php echo $iconCost ?></output>
        </button>
        <?php
        }
        ?>
    </article>

    <?php
    }
    ?>
</section>
</body>

</html>