<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* eliminarNeumaticoBDFoto.php: Se recibe el parámetro neumatico_json (id, marca, medidas, precio y pathFoto
en formato de cadena JSON) por POST. Se deberá borrar el neumático (invocando al método eliminar).
Si se pudo borrar en la base de datos, invocar al método guardarEnArchivo.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
Si se invoca por GET (sin parámetros), se mostrarán en una tabla (HTML) la información de todos los neumáticos
borrados y sus respectivas imagenes.   */

$_neumaticoJSON = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;


if($_neumaticoJSON !== null)
{
    $objetoJSON = json_decode($_neumaticoJSON, true);
    $unNeumatico = new NeumaticoBD($objetoJSON['marca'], $objetoJSON['medidas'], $objetoJSON['precio'], $objetoJSON['id'], $objetoJSON['pathFoto']);

    if(NeumaticoBD::eliminar($objetoJSON['id']))
    {  
        $objetoRetorno = $unNeumatico->guardarEnArchivo();
        $retorno = json_decode($objetoRetorno, true);

        echo $retorno['mensaje'];  
    }
    else
    {
        echo '{"exito" : false, "mensaje" : "NeumaticoBD NO eliminado."}';
    }
}
else
{
    $ar = fopen("./archivos/neumaticosbd_borrados.json", "r");
    $cadena = "";
    $contenido = "";

    while(!feof($ar))
    {
        $contenido = fgets($ar);

        $obj = json_decode($contenido, true);

       $cadena .= '<tr><td>' . $obj['id'] . '</td><td>' .$obj['marca'] . '</td><td>'. $obj['medidas'] . '</td><td>' . $obj['precio'] . '</td><td>'. $obj['pathFoto'] . '</td><td><img src=' . "./neumaticosBorrados/".  $obj['pathFoto']  . ' width="50" height="50"/></td></td><br></tr>';   
    }

    fclose($ar);

    echo $cadena;
}


