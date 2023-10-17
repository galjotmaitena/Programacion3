<?php
require_once "accesoDatos.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Poo\AccesoDatos;

class Auto
{
    public int $id;
    public string $color;
    public string $marca;
    public int $precio;
    public string $modelo;

    public function agregarUno(Request $request, Response $response, array $args): Response 
	{
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo agregar el auto";
        $objetoRetorno->status = 418;

        if(isset($parametros["auto"]))
        {
            $objetoAuto = json_decode($parametros["auto"]);

            $auto = new Auto();
            $auto->color = $objetoAuto->color;
            $auto->marca = $objetoAuto->marca;
            $auto->precio = $objetoAuto->precio;
            $auto->modelo = $objetoAuto->modelo;

           if($auto->agregar())
            {
                $objetoRetorno->exito = true;
                $objetoRetorno->mensaje = "Auto agregado";
                $objetoRetorno->status = 200;
            }    
        }

        $newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public function agregar() : bool 
    {
        $retorno = false;

        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("INSERT INTO autos (color, marca, precio, modelo) 
                                                    VALUES(:color, :marca, :precio, :modelo)");

        $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':modelo', $this->modelo, PDO::PARAM_STR);

        if($consulta->execute())
        {
            $retorno = true;
        }

        return $retorno;
    }

    public function traerTodos(Request $request, Response $response, array $args): Response 
	{
        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo traer la lista";
        $objetoRetorno->tabla = "{}";
        $objetoRetorno->status = 424;

		$listaUsuarios = Auto::traer();

        if(count($listaUsuarios) > 0)
        {
            $objetoRetorno->exito = true;
            $objetoRetorno->mensaje = "Listado de autos";
            $objetoRetorno->tabla = json_encode($listaUsuarios);
            $objetoRetorno->status = 200;
        }
  
		$newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');	
	}

    public static function traer() : array
    {
        $autos = array();
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("SELECT id, color AS color, marca AS marca, 
                                                        precio AS precio, modelo AS modelo FROM autos");

        $consulta->execute();

        $filas = $consulta->fetchAll();

        foreach($filas as $fila)
        {
            $auto = new Auto();
            $auto->id = $fila[0];
            $auto->color = $fila[1];
            $auto->marca = $fila[2];
            $auto->precio = $fila[3];
            $auto->modelo = $fila[4];

            array_push($autos, $auto);
        }

        return $autos;
    }

   /*(DELETE) Borrado de autos por ID.
    Recibe el ID del auto a ser borrado (id_auto) más el JWT → token (en el header).
    Si el perfil es ‘propietario’ se borrará de la base de datos. Caso contrario, se mostrará el
    mensaje correspondiente (indicando que usuario intentó realizar la acción).
    Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418) */

    

    
}