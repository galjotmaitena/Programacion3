<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

$_neumaticoJSON = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;

if($_neumaticoJSON != NULL)
{
    $objetoJSON = json_decode($_neumaticoJSON, true);
    $neumaticoBD = new NeumaticoBD($objetoJSON['marca'], $objetoJSON['medidas'], $objetoJSON['precio']);

    if($neumaticoBD->agregar())
    {
        echo '{"exito" : true, "mensaje" : "NeumaticoBD agregado."}';
    }
    else
    {
        echo '{"exito" : false, "mensaje" : "NeumaticoBD NO agregado."}';
    }
}
else
{
    echo 'No se ingresaron datos';
}

