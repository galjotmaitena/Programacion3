<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* agregarNeumaticoSinFoto.php: Se recibe por POST el parámetro neumático_json (marca, medidas y precio), en
formato de cadena JSON. Se invocará al método agregar.
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido. */

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

