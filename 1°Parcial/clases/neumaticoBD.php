<?php

namespace Galjot\Maitena;
require_once "./clases/accesoDatos.php";
require_once "./clases/neumatico.php";
require_once "./clases/IParte1.php";
require_once "./clases/IParte2.php";
require_once "./clases/IParte3.php";
require_once "./clases/IParte4.php";

use IParte1;
use IParte2;
use IParte3;
use IParte4;
use Poo\AccesoDatos;

use PDO;
use PDOException;

class NeumaticoBD extends Neumatico implements IParte1, IParte2, IParte3, IParte4
{
    protected int $id;
    protected string $pathFoto;

    function __construct(string $_marca, string $_medidas, float $_precio = 0, int $_id = 0, string $_pathFoto = "")
    {
        parent::__construct($_marca, $_medidas, $_precio);
        $this->id = $_id;
        $this->pathFoto = $_pathFoto;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getPathFoto() : string
    {
        return $this->pathFoto;
    }

    public function toJSON() : string
    {
        return parent::toJSON() . ', "id" : ' . $this->id . ', "pathFoto" : "' . $this->pathFoto . '"';
    }

    #Interfaz IParte1
    public function agregar() : bool
    {
        $retorno = false;

        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("INSERT INTO neumaticos(marca, medidas, precio, foto)" 
                                                    . "VALUES(:marca, :medidas, :precio, :foto)");

        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':medidas', $this->medidas, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);

        if($consulta->execute())
        {
            $retorno = true;
        }

        return $retorno;
    }

    public static function traer() : array
    {
        $neumaticos = array();
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("SELECT id, marca AS marca, medidas AS medidas, 
                                                        precio AS precio, foto AS foto FROM neumaticos");

        $consulta->execute();

        $filas = $consulta->fetchAll();

        foreach($filas as $fila)
        {
            array_push($neumaticos, new NeumaticoBD($fila[1], $fila[2], $fila[3], $fila[0], (string)$fila[4]));
        }

        return $neumaticos;
    }
    
    #Interfaz IParte2
    public static function eliminar(int $id) : bool
    {
        $retorno = false;
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("DELETE FROM neumaticos WHERE id = :id");

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        $consulta->execute();
 
        if($consulta->rowCount() > 0)
        {
            $retorno = true;
        }

        return $retorno;
    }

    public function modificar() : bool
    {
        $retorno = false;
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("UPDATE neumaticos SET marca = :marca, medidas = :medidas, 
                                                    precio = :precio, foto = :foto WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':medidas', $this->medidas, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);

        $filasAfectadas = $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $retorno = true;
        }

        return $retorno;
    }

    #Interfaz IParte3
    public function existe(array $neumaticos) : bool
    {
        foreach($neumaticos as $neumatico)
        {
            if($neumatico->marca == $this->marca && $neumatico->medidas == $this->medidas)
            {
                return true;
            }
        }

        return false;
    }

    #Interfaz IParte4
    public function guardarEnArchivo(): string
    {
        $nuevoDestino = "./neumaticosBorrados/" . (string)$this->id . "." . $this->marca . ".borrado." . date("His") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION);
        $retorno = '{"exito" : false, "mensaje" : "Neumatico NO escrito"}';

       if(rename($this->pathFoto, $nuevoDestino))
        {
            $objetoJSON = "{" . $this->toJSON() . "}\r\n";

            $archivo = fopen("./archivos/neumaticosbd_borrados.txt", "a");
            $cantidad = fwrite($archivo, $objetoJSON);

            if($cantidad > 0)
            {
                $retorno = '{"exito" : true, "mensaje" : "Neumatico escrito"}';
            }

            fclose($archivo);       
        }
            
        return $retorno;
    }
}