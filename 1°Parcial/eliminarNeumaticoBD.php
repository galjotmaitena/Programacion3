<?php

use Galjot\Maitena\Neumatico;
use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

$_neumaticoJSON = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;

$objetoJSON = json_decode($_neumaticoJSON, true);
$unNeumatico = new Neumatico($objetoJSON['marca'], $objetoJSON['medidas'], $objetoJSON['precio']);

if(NeumaticoBD::eliminar($objetoJSON['id']))
{
    if($unNeumatico->guardarJSON("./archivos/neumaticos_eliminados.json"))
    {
        echo '{"exito" : true, "mensaje" : "NeumaticoBD eliminado."}';
    }
}
else
{
    echo '{"exito" : false, "mensaje" : "NeumaticoBD NO eliminado."}';
}