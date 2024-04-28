<?php

use Galjot\Maitena\Neumatico;

require_once "./clases/neumatico.php";

$_marca = isset($_POST["marca"]) ? $_POST["marca"] : NULL;
$_medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;
$_precio = isset($_POST["precio"]) ? $_POST["precio"] : 0;

$unNeumatico = new Neumatico($_marca, $_medidas, $_precio);

$retorno = $unNeumatico->guardarJSON("./archivos/neumaticos.json");
//$objetoRetornado = json_decode($retorno, true);

//echo $objetoRetornado['mensaje'];

echo $retorno;