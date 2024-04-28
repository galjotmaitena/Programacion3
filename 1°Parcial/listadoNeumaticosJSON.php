<?php

use Galjot\Maitena\Neumatico;

require_once "./clases/neumatico.php";

$arrayNeumaticos = Neumatico::traerJSON("./archivos/neumaticos.json");
//var_dump($arrayNeumaticos);
foreach($arrayNeumaticos as $neumatico)
{
    echo $neumatico->toJSON() . "\n";
}