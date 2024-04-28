<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

$cadenaJSON = isset($_POST["obj_neumatico"]) ? $_POST["obj_neumatico"] : NULL;

$objetoJSON = json_decode($cadenaJSON, true);
$unNeumatico = new NeumaticoBD($objetoJSON['marca'], $objetoJSON['medidas']);

if($unNeumatico->existe(NeumaticoBD::traer()))
{
    echo $unNeumatico->toJSON();
}
else
{
    echo "{}";
}