<?php

use Galjot\Maitena\Neumatico;

require_once "./clases/neumatico.php";

/* altaNeumaticoJSON.php: Se recibe por POST la marca, las medidas y el precio. Invocar al método guardarJSON y
pasarle './archivos/neumaticos.json' cómo parámetro. */

$_marca = isset($_POST["marca"]) ? $_POST["marca"] : NULL;
$_medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;
$_precio = isset($_POST["precio"]) ? $_POST["precio"] : 0;

$unNeumatico = new Neumatico($_marca, $_medidas, $_precio);

$retorno = $unNeumatico->guardarJSON("./archivos/neumaticos.json");

echo $retorno;