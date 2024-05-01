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

    /* un método de instancia toJSON(), que retornará los datos de la instancia (en una cadena con formato JSON). */

    public function toJSON() : string
    {
        return parent::toJSON() . ', "id" : ' . $this->id . ', "pathFoto" : "' . $this->pathFoto . '"';
    }

    #Interfaz IParte1

    /* agregar: agrega, a partir de la instancia actual, un nuevo registro en la tabla neumaticos (id, marca,
    medidas, precio, foto), de la base de datos gomeria_bd. Retorna true, si se pudo agregar, false, caso
    contrario. */

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

    /* traer: este método estático retorna un array de objetos de tipo NeumaticoBD, recuperados de la base de datos. */

    public static function traer() : array
    {
        $neumaticos = array();
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("SELECT * FROM neumaticos");

        $consulta->execute();

        $filas = $consulta->fetchAll();

        foreach($filas as $fila)
        {
            array_push($neumaticos, new NeumaticoBD($fila["marca"], $fila["medidas"], $fila["precio"], $fila["id"], $fila["foto"]));
        }

        return $neumaticos;
    }
    
    #Interfaz IParte2

    /* eliminar: este método estático, elimina de la base de datos el registro coincidente con el id recibido cómo
    parámetro. Retorna true, si se pudo eliminar, false, caso contrario. */

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
    
    /* modificar: Modifica en la base de datos el registro coincidente con la instancia actual (comparar por id).
    Retorna true, si se pudo modificar, false, caso contrario. */

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

        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $retorno = true;
        }

        return $retorno;
    }

    #Interfaz IParte3

    /* existe: retorna true, si la instancia actual está en el array de objetos de tipo NeumaticoBD que recibe como
    parámetro (comparar por marca y medidas). Caso contrario retorna false. */

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

    /* guardarEnArchivo: escribirá en un archivo de texto (./archivos/neumaticosbd_borrados.txt) toda la
    información del neumático más la nueva ubicación de la foto. La foto se moverá al subdirectorio
    “./neumaticosBorrados/”, con el nombre formado por el id punto marca punto 'borrado' punto hora,
    minutos y segundos del borrado (Ejemplo: 688.bridgestone.borrado.105905.jpg).
    Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido. */

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

    #Parte5

    public static function mostrarBorradosJSON() : array 
    {
        $arrayBorrados = array();

        $archivo = fopen("./archivos/neumaticosbd_borrados.txt", "r");

        while(!feof($archivo))
        {
            $contenido = fgets($archivo);

            $neumatico = explode("\n\r", $contenido);
            $neumatico = $neumatico[0];
            $neumatico = trim($neumatico);

            if($neumatico !== "")
            {
                $parseado = json_decode($neumatico);
                $neumaticoB = new NeumaticoBD($parseado->marca, $parseado->medidas, $parseado->precio, $parseado->id, $parseado->pathFoto);
                
                array_push($arrayBorrados, $neumaticoB);
            }
        }

        fclose($archivo);

        return $arrayBorrados;
    }

    public static function mostrarModificados()
    {
        $directorio = "./neumaticosModificados/";
        $imagenes = glob($directorio . "*.jpg");
        
        echo `  <table>
                    <thead>
                        <tr>
                            <td>
                                FOTO
                            </td>
                        </tr>
                    </thead>
                    <tbody>`;

        foreach($imagenes as $imagen)
        {
            echo `<tr><td>`;

            echo '<img src="' . $imagen . '" width="50" height="50"></td></tr>';
        }

        echo `</tbody></table>`;

    }
}