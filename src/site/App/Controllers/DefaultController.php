<?php

namespace App\Controllers;

use Framework\AbstractController;
use App\Repositories\UserRepository;
use App\Repositories\GameConfigRepository;
class DefaultController extends AbstractController
{
    public function test()
    {
        echo '<p>Cette page a reçu un paramètre nommé "nombre" et valant "' . $this->parameters['nombre'] . '"</p>
              <p>Contenu complet de <code>$this->parameters</code>:</p>
              <pre>';
        print_r($this->parameters);
    }

    public function error404()
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint non trouvé']);

    }
}
