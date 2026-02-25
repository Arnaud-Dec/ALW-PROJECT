<?php

namespace App\Controllers;

use Framework\AbstractController; // Import nécessaire
use App\Repositories\SaveRepository;

class ApiControlleur extends AbstractController // Ajout de l'héritage
{
    public function getInventory() {
        $nom = $this->parameters['nom'];

        $filePathSave = "Data/Saves/";
        $Save = new SaveRepository($filePathSave, "Data/Saves/default.json");
        $inventory = $Save->getInventory($nom);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($inventory);
    }
}