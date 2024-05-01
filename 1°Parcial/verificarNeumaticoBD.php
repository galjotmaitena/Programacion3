<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* verificarNeumaticoBD.php: Se recibe por POST el parámetro obj_neumatico, que será una cadena JSON (marca y
medidas), si coincide con algún registro de la base de datos (invocar al método traer) retornará los datos del
objeto (invocar al toJSON). Caso contrario, un JSON vacío ({}). */

$cadenaJSON = isset($_POST["obj_neumatico"]) ? $_POST["obj_neumatico"] : NULL;

$objetoJSON = json_decode($cadenaJSON, true);
$unNeumatico = new NeumaticoBD($objetoJSON['marca'], $objetoJSON['medidas']);

if($unNeumatico->existe(NeumaticoBD::traer()))
{
    foreach(NeumaticoBD::traer() as $neumatico)
    {
        if($objetoJSON["marca"] === $neumatico->getMarca() && $objetoJSON["medidas"] === $neumatico->getMedidas())
        {
            echo $neumatico->toJSON();
        }
    }
}
else
{
    echo "{}";
}