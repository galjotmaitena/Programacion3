<?php

use Galjot\Maitena\Neumatico;

require_once "./clases/neumatico.php";

$_marca = isset($_POST["marca"]) ? $_POST["marca"] : NULL;
$_medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;

if($_medidas != NULL && $_marca != NULL)
{
    $unNeumatico = new Neumatico($_marca, $_medidas);

    $retorno = $unNeumatico->verificarNeumaticoJSON($unNeumatico);

    echo $retorno;
}
else
{
    echo 'Faltaron ingresar datos';
}

