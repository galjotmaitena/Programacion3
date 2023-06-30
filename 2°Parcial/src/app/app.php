<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../poo/usuario.php";
require_once __DIR__ . "/../poo/juguete.php";
require_once __DIR__ . "/../poo/MW.php";

use Firebase\JWT\JWT;

$app = AppFactory::create();

$app->get('/', Usuario::class . ':traerTodos');

$app->post('/', \Juguete::class . ':agregarUno');
$app->get('/juguetes', Juguete::class . ':traerTodos');

$app->post('/login', \Usuario::class . ':login')
->add(\MW::class . ':ValidarCorreoYClaveExistentes')    //2
->add(\MW::class . '::ValidarParametrosVacios');        //1

$app->get('/login', Usuario::class . ':verificarJWT');

$app->group('/toys', function (RouteCollectorProxy $grupo) 
{   
    $grupo->delete('/{id}', \Juguete::class . ':borrarUno');
    $grupo->post('/', \Juguete::class . ':modificarUno')
    ->add(\MW::class . ':ValidarToken');
});

$app->group('/tablas', function (RouteCollectorProxy $grupo) 
{   
    $grupo->get('/usuarios', Usuario::class . ':traerTodos')
    ->add(\MW::class . '::ListarTablaUsuariosGet');
    $grupo->post('/usuarios', \Usuario::class . ':traerTodos')
    ->add(\MW::class . '::ListarTablaUsuariosPost')
    ->add(\MW::class . ':ValidarToken');

    $grupo->get('/juguetes', Juguete::class . ':traerTodos')
    ->add(\MW::class . ':ListarTablaJuguetesGet');
});

$app->post('/usuarios', \Usuario::class . ':agregarUno') //NO FUNCIONAN LOS MW
->add(\MW::class . ':ValidarCorreoNoExistente')             
->add(\MW::class . '::ValidarParametrosVacios')
->add(\MW::class . ':ValidarToken');            

try 
{
   $app->run();
}
catch (Exception $e) 
{
    die(json_encode(array("status" => "failed", "message" => "This action is not allowed")));
}