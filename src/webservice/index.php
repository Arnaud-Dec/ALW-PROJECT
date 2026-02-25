<?php

declare(strict_types=1);

use App\Application;

ini_set('display_errors', 'On');

require_once('Framework/autoloader.php');


//print "index";

// start the application
$app = new Application();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

$app->run();