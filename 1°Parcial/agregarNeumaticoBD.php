<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

$_marca = isset($_POST["marca"]) ? $_POST["marca"] : NULL;
$_medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;
$_precio = isset($_POST["precio"]) ? $_POST["precio"] : 0;
$_fotoDestino = "./neumaticos/imagenes/" . $_marca . "." . date("His") . "." .pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);

$unNeumatico = new NeumaticoBD($_marca, $_medidas, $_precio, 0, $_fotoDestino);

if($unNeumatico->existe(NeumaticoBD::traer()))
{
    echo "El neumatico ya existe en la DB";
}
else
{
    $extension = pathinfo($_fotoDestino, PATHINFO_EXTENSION);
    
    if(!(file_exists($_fotoDestino)) && ($extension == "jpg" || $extension == "jpeg" ||  $extension == "png"))
    {
        if(move_uploaded_file($_FILES["foto"]["tmp_name"], $_fotoDestino)) 
        {
            if($unNeumatico->agregar())
            {
                echo '{"exito" : true, "mensaje" : "Neumatico agregado"}';
            }
            else
            {
                echo '{"exito" : false, "mensaje" : "Neumatico NO agregado"}';
            }
        }
    }
    
}