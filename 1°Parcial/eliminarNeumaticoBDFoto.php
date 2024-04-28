<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

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


