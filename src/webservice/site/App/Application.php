<?php

namespace App;

use App\Controllers\DefaultController;
use Framework\AbstractApplication;
use Framework\Router;

class Application extends AbstractApplication
{
    public function run()
    {

        // map all routes to corresponding controllers/actions
        $this->router = new Router($this);
        $this->router->mapDefault(DefaultController::class, 'error404');

        $route = $this->router->findRoute();
        $controller = $this->router->getController($route->controller);
        $controller->execute($route->action, $route->foundParams);
    }
}
