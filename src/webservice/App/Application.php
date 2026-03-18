<?php

namespace App;

use App\Controllers\DefaultController;
use Framework\AbstractApplication;
use Framework\Router;

class Application extends AbstractApplication
{
    public function run()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        $this->router = new Router($this);

        $this->router->mapDefault(DefaultController::class, 'error404');
        $this->router->map('GET', '/api/joueurs/{string:nom}/inventaire', \App\Controllers\ApiControlleur::class, 'getInventory');
        $this->router->map('GET', '/api/joueurs/{string:nom}/buildings', \App\Controllers\ApiControlleur::class, 'getBuildings');
        $this->router->map('GET', '/api/joueurs/{string:nom}/building/{string:building}/level', \App\Controllers\ApiControlleur::class, 'getBuildingLevel');

        $this->router->map('POST','/api/joueurs/{string:nom}/inventaire', \App\Controllers\ApiControlleur::class, 'editInventory');
        $this->router->map('POST','/api/joueurs/{string:nom}/building',\App\Controllers\ApiControlleur::class, 'editBuildingLevel');
        $this->router->map('POST','/api/joueurs/{string:nom}/building/harvest',\App\Controllers\ApiControlleur::class,'harvest');
        $this->router->map('POST','/api/joueurs/{string:nom}/building/upgrade',\App\Controllers\ApiControlleur::class,'upgrade');

        $route = $this->router->findRoute();
        $controller = $this->router->getController($route->controller);
        $controller->execute($route->action, $route->foundParams);
    }
}
