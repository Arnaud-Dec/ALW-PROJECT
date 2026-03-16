<?php

namespace App;

use App\Controllers\DefaultController;
use Framework\AbstractApplication;
use Framework\Router;

class Application extends AbstractApplication
{
    public function run()
    {

        $this->router = new Router($this);

        $this->router->mapDefault(DefaultController::class, 'error404');
        $this->router->map('GET', '/api/joueurs/{string:nom}/inventaire', \App\Controllers\ApiControlleur::class, 'getInventory');
        $this->router->map('GET', '/api/joueurs/{string:nom}/buildings', \App\Controllers\ApiControlleur::class, 'getBuildings');
        $this->router->map('GET', '/api/joueurs/{string:nom}/building/{string:building}/level', \App\Controllers\ApiControlleur::class, 'getBuildingLevel');

        $this->router->map('POST','/api/joueurs/{string:nom}/inventaire', \App\Controllers\ApiControlleur::class, 'editInventory');
        $this->router->map('POST','/api/joueurs/{string:nom}/building',\App\Controllers\ApiControlleur::class, 'editBuildingLevel');

        $route = $this->router->findRoute();
        $controller = $this->router->getController($route->controller);
        $controller->execute($route->action, $route->foundParams);
    }
}
