<?php

namespace App\Controllers;

use Framework\AbstractController; // Import nécessaire
use App\Repositories\SaveRepository;
use App\Repositories\GameConfigRepository;

class ApiControlleur extends AbstractController // Ajout de l'héritage
{

    public function getSaveRepository(): SaveRepository{
        $filePathSave = "Data/Saves/";
        return new SaveRepository($filePathSave, "Data/Saves/default.json");
    }
    public function getGameConfigRepository() : GameConfigRepository
    {
        $filePathConf = "Data/Config/game_config.json";
        return new GameConfigRepository($filePathConf);
    }

    /// INVENTORY
    public function getInventory() {

        $nom = $this->parameters['nom'];

        $Save = $this->getSaveRepository();

        $inventory = $Save->getInventory($nom);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($inventory);
    }


    public function editInventory()
    {
        $nom = $this->parameters['nom'];

        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if($data && isset($data->product) && isset($data->quantity)){
            $produit = $data->product;
            $quantite = $data->quantity;

            $Save = $this->getSaveRepository();
            $Save->setProduct($nom,$produit,$quantite);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(["status" => "success"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Données incomplètes"]);
        }
    }

    ///BUILDINGS
    public function getBuildings(){

        $nom = $this->parameters['nom'];

        $Save = $this->getSaveRepository();

        $Buildings = $Save->getBuildings($nom);
        http_response_code(200);
        echo json_encode($Buildings);
    }

    public function getBuildingLevel()
    {
        $nom = $this->parameters['nom'];
        $buildingName = $this->parameters['building'];
        $Save = $this->getSaveRepository();

        $Level = $Save->getLevel($nom, $buildingName);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($Level);
    }

    public function harvest()
    {
        $nom = $this->parameters['nom'];

        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if ($data && isset($data->building)) {
            $building = $data->building;
            $Save = $this->getSaveRepository();
            
            $gameConfig = $this->getGameConfigRepository();
            $buildingConfig = $gameConfig->getBuilding($building);
            
            if (!$buildingConfig) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(["error" => "Bâtiment inconnu"]);
                return;
            }

            $product = $buildingConfig->production ?? null;
            if (!$product) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(["error" => "Bâtiment ne produit rien"]);
                return;
            }

            // Vérifier si le bâtiment a besoin d'ingrédients
            $costResource = $buildingConfig->cost ?? null;
            
            if ($costResource) {
                // Le bâtiment consomme des ingrédients
                $currentStock = $Save->getProduct($nom, $costResource);
                if ($currentStock < 1) {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode([
                        "status" => "error", 
                        "message" => "Pas assez d'ingrédients",
                        "needed" => $costResource,
                        "inventory" => $currentStock
                    ]);
                    return;
                }
                
                // Consommer l'ingrédient
                $Save->setProduct($nom, $costResource, $currentStock - 1);
            }

            // Produire le résultat
            $nbProduct = $Save->getProduct($nom, $product);
            $nbProduct++;
            $Save->setProduct($nom, $product, $nbProduct);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "success",
                "added" => 1,
                "consumed" => $costResource ? [$costResource => 1] : [],
                "inventory" => $Save->getInventory($nom)
            ]);
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Données manquantes"]);
        }
    }

    public function upgrade()
    {
        $nom = $this->parameters['nom'];
        $data = json_decode(file_get_contents('php://input'));

        if (!$data || !isset($data->building)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Bâtiment manquant dans la requête"]);
            return;
        }

        $building = $data->building;

        $gameConfig = $this->getGameConfigRepository();
        $buildingConfig = $gameConfig->getBuilding($building);
        
        if (!$buildingConfig) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Bâtiment inconnu"]);
            return;
        }

        // Pour l'upgrade, on utilise ce que produit le bâtiment
        $product = $buildingConfig->production ?? null;
        
        if (!$product) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(["error" => "Bâtiment impossible à améliorer"]);
            return;
        }

        $Save = $this->getSaveRepository();
        $level = $Save->getLevel($nom, $building);

        $gameConfig = $this->getGameConfigRepository();
        $cost = $gameConfig->getUpgradeCost($building, $level + 1);

        $nbProduct = $Save->getProduct($nom, $product);

        header('Content-Type: application/json');

        if ($nbProduct >= $cost) {
            $Save->setLevel($nom, $building, $level + 1);
            $Save->setProduct($nom, $product, $nbProduct - $cost);

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "newLevel" => $level + 1,
                "cost" => $cost,
                "inventoryNow" => $nbProduct - $cost,
                "product" => $product
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Pas assez de ressources",
                "cost" => $cost,
                "inventory" => $nbProduct,
                "product" => $product
            ]);
        }
    }

    public function editBuildingLevel():void
    {
        $nom = $this->parameters['nom'];
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if($data && isset($data->level) && isset($data->building)) {
            $Save = $this->getSaveRepository();
            $Save->setLevel($nom, $data->building, (int)$data->level);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(["status" => "success"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Données incomplètes"]);
        }
    }
}