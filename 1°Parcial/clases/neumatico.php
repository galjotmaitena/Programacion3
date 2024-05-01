<?php

namespace Galjot\Maitena;
class Neumatico
{
    protected string $marca;
    protected string $medidas;
    protected float $precio;

    /* Un constructor (que inicialice los atributos), un método de instancia toJSON(), que retornará los datos de la
    instancia (en una cadena con formato JSON). */

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

    /* Método de instancia guardarJSON($path), que agregará al neumático en el path recibido por parámetro.
    Retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido. */

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

    /* Método de clase traerJSON($path), que retornará un array de objetos de tipo neumático (recuperados del path). */

    public static function traerJSON(string $path) : array
    {
        $arrayRetorno = array();
        $cadenaJSON = "";

        $archivo = fopen($path, "r");

        while(!feof($archivo))
        {
            $cadenaJSON = fgets($archivo);
            $neumaticoJSON = explode("\n\r", $cadenaJSON);
            $neumaticoJSON = $neumaticoJSON[0];
            $neumaticoJSON = trim($neumaticoJSON);

            if($neumaticoJSON !== "")
            {
                $parseado = json_decode($neumaticoJSON);
                $unNeumatico = new Neumatico($parseado->marca, $parseado->medidas, $parseado->precio);
                array_push($arrayRetorno, $unNeumatico);
            }
        }

        fclose($archivo);

        return $arrayRetorno;
    }

    /* Método de clase verificarNeumaticoJSON($neumatico), que recorrerá el array obtenido del método traerJSON y
    retornará un JSON que contendrá: existe(bool) y mensaje(string).
    Nota: Si el neumático está registrado (comparar por marca y medidas), retornará true y el mensaje indicará la
    sumatoria de precios de aquellos neumáticos registrados con la misma marca y las mismas medidas del
    neumático recibido por parámetro. Caso contrario, retornará false, y en el mensaje se informará de lo acontecido.  */

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