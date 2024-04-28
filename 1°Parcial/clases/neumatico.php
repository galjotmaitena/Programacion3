<?php

namespace Galjot\Maitena;
class Neumatico
{
    protected string $marca;
    protected string $medidas;
    protected float $precio;

    function __construct(string $_marca, string $_medidas, float $_precio = 0)
    {
        $this->marca = $_marca;
        $this->medidas = $_medidas;
        $this->precio = $_precio;
    }

    public function getMarca() : string
    {
        return $this->marca;
    }

    public function getMedidas() : string
    {
        return $this->medidas;
    }

    public function getPrecio() : float
    {
        return $this->precio;
    }

    public function toJSON() : string
    {
        return '"marca" : "' . $this->marca . '", "medidas" : "' . $this->medidas . '", "precio" : '. $this->precio;
    }

    public function guardarJSON(string $path) : string
    {
        $objetoJSON = "{" . $this->toJSON() . "}\r\n";

        $exito = false;
        $mensaje = "Neumatico NO agregado";

        $archivo = fopen($path, "a");
        $cantidad = fwrite($archivo, $objetoJSON);

        if($cantidad > 0)
        {
            $exito = true;
            $mensaje = "Neumatico agregado";
        }

        fclose($archivo);

        return '{"exito" : ' . $exito . ', "mensaje" : "' . $mensaje . '"}';
    }

    public static function traerJSON(string $path) : array
    {
        $arrayRetorno = array();
        $cadenaJSON = "";

        $archivo = fopen($path, "r");

        while(!feof($archivo))
        {
            $linea = fgets($archivo);
            $cadenaJSON .= $linea;

            $neumatico = json_decode($linea, true);
			if(isset($neumatico))
			{
				$new = new Neumatico($neumatico['marca'], $neumatico['medidas'], $neumatico['precio']);
				array_push($arrayRetorno,$new);
			}
        }

        fclose($archivo);

        /*$neumaticos = json_decode($cadenaJSON, true);
        var_dump($neumaticos);

        foreach($neumaticos as $neumatico)
        {
            array_push($arrayRetorno, new Neumatico($neumatico['marca'], $neumatico['medidas'], $neumatico['precio']));
        }*/

        return $arrayRetorno;
    }

    public static function verificarNeumaticoJSON(Neumatico $unNeumatico) : string
    {
        $neumaticos = $unNeumatico->traerJSON("./archivos/neumaticos.json");
        $sumaPrecios = 0;

        $exito = "false";
        $mensaje = "No se encontraron neumaticos con la misma marca y medidas";

        foreach($neumaticos as $neumatico)
        {
            if($neumatico->marca == $unNeumatico->marca && $neumatico->medidas == $unNeumatico->medidas)
            {
                $sumaPrecios += $neumatico->precio;
            }
        }

        if($sumaPrecios > 0)
        {
            $exito = "true";
            $mensaje = "Sumatoria precios=" . $sumaPrecios;
        }

        return '{"exito" : ' . $exito . ', "mensaje" : "' . $mensaje . '"}';    
    }
}