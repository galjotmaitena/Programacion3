<?php

use Galjot\Maitena\Neumatico;
use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* listadoNeumaticosBD.php: (GET) Se mostrará el listado completo de los neumáticos (obtenidos de la base de
datos) en una tabla (HTML con cabecera). Invocar al método traer.
Nota: Si se recibe el parámetro tabla con el valor mostrar, retornará los datos en una tabla (HTML con cabecera),
preparar la tabla para que muestre la imagen, si es que la tiene.
Si el parámetro no es pasado o no contiene el valor mostrar, retornará el array de objetos con formato JSON. */

$tabla = isset($_GET["tabla"]) ? $_GET["tabla"] : NULL;

$arrayNeumaticos = NeumaticoBD::traer();

if($tabla == "mostrar")
{
    $tabla = "<table><tr><td>ID</td><td>MARCA</td><td>MEDIDAS</td><td>PRECIO</td><td>PATH_FOTO</td></tr>";

    foreach($arrayNeumaticos as $neumatico)
    {
        $tabla .= "<tr><td>{$neumatico->getId()}</td><td>{$neumatico->getMarca()}</td><td>{$neumatico->getMedidas()}
                    </td><td>{$neumatico->getPrecio()}</td><td>{$neumatico->getPathFoto()}</td></tr>";    
    }

    $tabla .= "</table>";
                
    echo $tabla;
}
else
{
    $arrayRetorno = array();

    foreach($arrayNeumaticos as $neumatico)
    {
        $objStd = new stdClass();
        $objStd->marca = $neumatico->getMarca();
        $objStd->medidas = $neumatico->getMedidas();
        $objStd->precio = $neumatico->getPrecio();
        $objStd->id = $neumatico->getId();
        $objStd->pathFoto = $neumatico->getPathFoto();

        array_push($arrayRetorno, $objStd);
    }

    echo json_encode($arrayRetorno);
}
