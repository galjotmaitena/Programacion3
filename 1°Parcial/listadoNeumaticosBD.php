<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

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
    foreach($arrayNeumaticos as $neumatico)
    {
        echo $neumatico->toJSON() . "\n";
    }
}
