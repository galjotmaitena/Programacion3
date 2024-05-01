<?php

use Galjot\Maitena\Neumatico;
use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* eliminarNeumaticoBD.php: Recibe el parámetro neumatico_json (id, marca, medidas y precio, en formato de
cadena JSON) por POST y se deberá borrar el neumático (invocando al método eliminar).
Si se pudo borrar en la base de datos, invocar al método guardarJSON y pasarle cómo parámetro el valor
'./archivos/neumaticos_eliminados.json'.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido. */

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