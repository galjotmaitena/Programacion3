<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../poo/usuario.php";
require_once __DIR__ . "/../poo/perfil.php";
//require_once __DIR__ . "/../poo/MW.php";

use Firebase\JWT\JWT;
use Galjot\Maitena\Usuario;
//prog_3_recu
$app = AppFactory::create();

$app->post('/usuarios', \Galjot\Maitena\Usuario::class . ':agregarUno');
$app->get('/', Usuario::class . ':traerTodos');

$app->post('/', \Galjot\Maitena\Perfil::class . ':agregarUno');
$app->get('/perfil', \Galjot\Maitena\Perfil::class . ':traerTodos');

$app->post('/login', \Galjot\Maitena\Usuario::class . ':login');          
$app->get('/login', Usuario::class . ':verificarJWT');

$app->group('/perfiles', function (RouteCollectorProxy $grupo) 
{
    $grupo->delete('/{id}', \Galjot\Maitena\Perfil::class . ':borrarUno');
    $grupo->put('/', \Galjot\Maitena\Perfil::class . ':modificarUno');
});

$app->group('/usuarios', function (RouteCollectorProxy $grupo) 
{
    $grupo->delete('/{id}', \Galjot\Maitena\Usuario::class . ':borrarUno');
    $grupo->post('/', \Galjot\Maitena\Usuario::class . ':modificarUno');
});


try 
{
   $app->run();
}
catch (Exception $e) 
{
    die(json_encode(array("status" => "failed", "message" => "This action is not allowed")));
}