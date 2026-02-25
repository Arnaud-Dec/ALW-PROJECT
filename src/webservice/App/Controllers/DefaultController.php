<?php

namespace App\Controllers;

use Framework\AbstractController;
use App\Repositories\UserRepository;
use App\Repositories\GameConfigRepository;
class DefaultController extends AbstractController
{

    public function error404()
    {
        http_response_code(404);
        echo $this->getTwig()->render('404.html.twig');

    }
}
