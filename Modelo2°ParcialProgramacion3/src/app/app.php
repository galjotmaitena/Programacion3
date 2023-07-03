<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../poo/usuario.php";
require_once __DIR__ . "/../poo/auto.php";
require_once __DIR__ . "/../poo/MW.php";

use Firebase\JWT\JWT;
//modelo_SP_progra
$app = AppFactory::create();

$app->post('/usuarios', \Usuario::class . ':agregarUno')
->add(\MW::class . ':ValidarCorreoNoExistente')
->add(\MW::class . '::ValidarParametrosVacios')
->add(\MW::class . ':ValidarCorreoYClave');   //1 - 2 - 4
$app->get('/', Usuario::class . ':traerTodos')
->add(\MW::class . ':ListarUsuariosSiEsEncargado')
->add(\MW::class . ':ListarUsuariosSiEsEmpleado')
->add(\MW::class . '::ListarUsuariosSiEsPropietario');

$app->post('/', \Auto::class . ':agregarUno')
->add(\MW::class . ':ValidarPrecioYColor');              //5
$app->get('/autos', Auto::class . ':traerTodos');

$app->post('/login', \Usuario::class . ':login')
->add(\MW::class . ':ValidarCorreoYClaveExistentes')
->add(\MW::class . '::ValidarParametrosVacios')
->add(\MW::class . ':ValidarCorreoYClave');           //1 - 2 - 3
$app->get('/login', Usuario::class . ':verificarJWT');

$app->delete('/{id}', \Auto::class . ':borrarUno')
->add(\MW::class . '::ValidarPropietario')
->add(\MW::class . ':ValidarToken');
$app->put('/', \Auto::class . ':modificarUno')
->add(\MW::class . ':ValidarEncargado')
->add(\MW::class . ':ValidarToken');

$app->group('/autos', function (RouteCollectorProxy $grupo) 
{
    $grupo->get('/', \Auto::class . ':traerTodos')
    ->add(\MW::class . ':ListarAutosSiEsEncargado')
    ->add(\MW::class . ':ListarAutosSiEsEmpleado')
    ->add(\MW::class . '::ListarAutosSiEsPropietario');
});

try 
{
   $app->run();
}
catch (Exception $e) 
{
    die(json_encode(array("status" => "failed", "message" => "This action is not allowed")));
}