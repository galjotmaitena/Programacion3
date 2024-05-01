<?php

use Galjot\Maitena\Neumatico;

require_once "./clases/neumatico.php";

/* listadoNeumaticosJSON.php: (GET) Se mostrará el listado de todos los neumáticos en formato JSON (traerJSON).
Pasarle './archivos/neumaticos.json' cómo parámetro. */

$arrayNeumaticos = array();

foreach(Neumatico::traerJSON("./archivos/neumaticos.json") as $neumatico)
{
    $objStd = new stdClass();
    $objStd->marca = $neumatico->getMarca();
    $objStd->medidas = $neumatico->getMedidas();
    $objStd->precio = $neumatico->getPrecio();

    array_push($arrayNeumaticos, $objStd);
}

echo json_encode($arrayNeumaticos);