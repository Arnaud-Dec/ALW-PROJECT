<?php

namespace App\Controllers;

use Framework\AbstractController; // Import nécessaire
use App\Repositories\SaveRepository;

class ApiControlleur extends AbstractController // Ajout de l'héritage
{

    public function getSaveRepository(){
        $filePathSave = "Data/Saves/";
        $Save = new SaveRepository($filePathSave, "Data/Saves/default.json");
        return $Save;
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

    public function editBuildingLevel()
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