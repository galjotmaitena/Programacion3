<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* mostrarBorradosJSON.php: Muestra todo lo registrado en el archivo “neumaticos_eliminados.json”. Para ello,
agregar un método estático (en NeumaticoBD), llamado mostrarBorradosJSON. */

$arrayNeumaticosBorrados = array();

foreach(NeumaticoBD::mostrarBorradosJSON() as $neumatico)
{
    $objStd = new stdClass();
    $objStd->id = $neumatico->getId();
    $objStd->marca = $neumatico->getMarca();
    $objStd->medidas = $neumatico->getMedidas();
    $objStd->precio = $neumatico->getPrecio();
    $objStd->pathFoto = $neumatico->getPathFoto();

    array_push($arrayNeumaticosBorrados, $objStd);
}

echo json_encode($arrayNeumaticosBorrados);