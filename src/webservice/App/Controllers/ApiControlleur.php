<?php

namespace App\Controllers;

use Framework\AbstractController; // Import nécessaire
use App\Repositories\SaveRepository;

class ApiControlleur extends AbstractController // Ajout de l'héritage
{
    public function getInventory() {

        $nom = $this->parameters['nom'];

        $Save = $this->getSaveRepository();

        $inventory = $Save->getInventory($nom);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($inventory);
    }

    public function getBuildings(){

        $nom = $this->parameters['nom'];
        $Save = $this->getSaveRepository();

        $Buildings = $Save->getBuildings($nom);
        http_response_code(200);
        echo json_encode($Buildings);
    }

    public function getSaveRepository(){
        $filePathSave = "Data/Saves/";
        $Save = new SaveRepository($filePathSave, "Data/Saves/default.json");
        return $Save;
    }



}