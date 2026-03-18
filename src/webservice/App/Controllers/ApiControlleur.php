<?php

namespace App\Controllers;

use Framework\AbstractController; // Import nécessaire
use App\Repositories\SaveRepository;

class ApiControlleur extends AbstractController // Ajout de l'héritage
{

    public function getSaveRepository(): SaveRepository{
        $filePathSave = "Data/Saves/";
        return new SaveRepository($filePathSave, "Data/Saves/default.json");
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

            // ressource en fonction du batiment
            $mapping = [
                "champ_ble"   => "ble",
                "moulin"      => "farine",
                "boulangerie" => "pain"
            ];

            if (isset($mapping[$building])){
                $product = $mapping[$building];
                $nbProduct = $Save->getProduct($nom , $product);

                $nbProduct++;
                $Save->setProduct($nom,$product,$nbProduct);

                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    "status" => "success",
                    "added" => 1,
                    "inventory" => $Save->getInventory($nom)
                ]);
            }else{
                http_response_code(400);
                echo json_encode(["error" => "Bâtiment inconnu"]);
            }
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