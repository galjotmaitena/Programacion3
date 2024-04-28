<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

$_neumaticoJSON = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;

$objetoJSON = json_decode($_neumaticoJSON, true);
$unNeumatico = new NeumaticoBD($objetoJSON['marca'], $objetoJSON['medidas'], $objetoJSON['precio'], $objetoJSON['id']);

if($unNeumatico->modificar())
{  
    echo '{"exito" : true, "mensaje" : "NeumaticoBD modificado."}';  
}
else
{
    echo '{"exito" : false, "mensaje" : "NeumaticoBD NO modificado."}';
}