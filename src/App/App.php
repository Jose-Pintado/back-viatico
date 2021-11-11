<?php


use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

$cont_aux = new \DI\Container; //configuramos con el container que esta php-di que se instalo
AppFactory::setContainer($cont_aux);

$app = AppFactory::create();
$app->addErrorMiddleware(true,true,true);//para atrapar los errores
//para desarrollo mantener todo en true, pero cuando este en linea cambiar a false, true, true. para que no muestre los errores

$container = $app->getContainer(); //container ya esta listo para ser usado

require __DIR__ . "/Routes.php";
require __DIR__ . "/Conexion.php";


$app->run();