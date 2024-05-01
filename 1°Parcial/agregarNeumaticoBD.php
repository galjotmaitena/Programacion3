<?php

use Galjot\Maitena\NeumaticoBD;

require_once "./clases/neumaticoBD.php";

/* agregarNeumaticoBD.php: Se recibirán por POST los valores: marca, medidas, precio y la foto para registrar un
neumático en la base de datos.
Verificar la previa existencia del neumático invocando al método existe. Se le pasará como parámetro el array que
retorna el método traer.
Si el neumático ya existe en la base de datos, se retornará un mensaje que indique lo acontecido.
Si el neumático no existe, se invocará al método agregar. La imagen se guardará en “./neumaticos/imagenes/”,
con el nombre formado por el marca punto hora, minutos y segundos del alta (Ejemplo: pirelli.105905.jpg).
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido. */

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