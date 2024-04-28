<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

$_neumaticoJSON = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;
$foto = isset($_FILES["foto"])? $_FILES["foto"] : NULL;

$nuevoDestino = "./neumaticosModificados/" . $objetoJSON['id'] . "." . $objetoJSON['marca'] . ".modificado." . date("His") . "." . pathinfo($foto['name'], PATHINFO_EXTENSION);

$objetoJSON = json_decode($_neumaticoJSON, true);
$unNeumatico = new NeumaticoBD($objetoJSON['marca'], $objetoJSON['medidas'], $objetoJSON['precio'], (int)$objetoJSON['id'], $nuevoDestino);

if($unNeumatico->modificar())
{  
    if(rename($foto['tmp_name'], $nuevoDestino))
    {
        echo '{"exito" : true, "mensaje" : "NeumaticoBD modificado."}';
    } 
}
else
{
    echo '{"exito" : false, "mensaje" : "NeumaticoBD NO modificado."}';
}