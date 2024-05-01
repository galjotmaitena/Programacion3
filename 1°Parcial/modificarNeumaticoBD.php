<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* modificarNeumaticoBD.php: Se recibirán por POST los siguientes valores: neumatico_json (id, marca, medidas y
precio, en formato de cadena JSON) para modificar un neumático en la base de datos. Invocar al método
modificar.
Nota: El valor del id, será el id del neumático 'original', mientras que el resto de los valores serán los del neumático
a ser modificado.
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido. */

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