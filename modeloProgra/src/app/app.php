<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../clases/usuario.php";
require_once __DIR__ . "/../clases/auto.php";
require_once __DIR__ . "/../clases/MW.php";

use Firebase\JWT\JWT;

$app = AppFactory::create();

$app->post('/usuarios', \usuario::class . ':agregarUno')
->add(\MW::class . ':ValidarCorreoYClave')
->add(\MW::class . ':ValidarParametrosVacios')
->add(\MW::class . ':ValidarCorreo');

$app->get('/', usuario::class . ':traerTodos');

$app->post('/', \Auto::class . ':agregarUno');
$app->get('/autos', Auto::class . ':traerTodos');

$app->post('/login', \usuario::class . ':login');


$app->run();