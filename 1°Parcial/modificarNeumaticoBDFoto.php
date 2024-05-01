<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* modificarNeumaticoBDFoto.php: Se recibirán por POST los siguientes valores: neumatico_json (id, marca,
medidas y precio, en formato de cadena JSON) y la foto (para modificar un neumático en la base de datos).
Invocar al método modificar.
Nota: El valor del id, será el id del neumático 'original', mientras que el resto de los valores serán los del neumático
a ser modificado.
Si se pudo modificar en la base de datos, la foto original del registro modificado se moverá al subdirectorio
“./neumaticosModificados/”, con el nombre formado por el id punto marca punto 'modificado' punto hora,
minutos y segundos de la modificación (Ejemplo: 987.fateo.modificado.105905.jpg).
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido. */

$_neumaticoJSON = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;
$foto = isset($_FILES["foto"])? $_FILES["foto"] : NULL;

$objetoJSON = json_decode($_neumaticoJSON, true);

$nuevoDestino = "./neumaticosModificados/" . $objetoJSON['id'] . "." . $objetoJSON['marca'] . ".modificado." . date("His") . "." . pathinfo($foto['name'], PATHINFO_EXTENSION);

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