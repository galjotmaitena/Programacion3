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

   public function borrarUno(Request $request, Response $response, array $args): Response 
	{		 
        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo eliminar el juguete";
        $objetoRetorno->status = 418;

        if(isset($request->getHeader('token')[0]) && isset($args['id']))
        {
            $token = $request->getHeader('token')[0];
            $id = $args['id'];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            #$jugueteSinEliminar = Juguete::traerJuguete($id);
            #var_dump( $jugueteSinEliminar);

            if($perfilUsuario == "propietario")
            {
                if(Auto::borrar($id))
                {
                    #unlink($jugueteSinEliminar->pathFoto);

                    $objetoRetorno->exito = true;
                    $objetoRetorno->mensaje = "Auto eliminado";
                    $objetoRetorno->status = 200;
                }
                else
                {
                    $objetoRetorno->mensaje = "El auto no se encuentra en el listado";
                }
            }
            else
            {
                $objetoRetorno->mensaje = "No esta autorizado para eliminar autos, debe ser propietario y usted es {$usuarioToken->perfil}";
            }
        }

        $newResponse = $response->withStatus(200, "OK");
		$newResponse->getBody()->write(json_encode($objetoRetorno));	

		return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public static function borrar(int $_id) : bool
	{
        $retorno = false;

	 	$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 

		$consulta = $objetoAccesoDato->RetornarConsulta("DELETE FROM autos WHERE id = :id");	
		$consulta->bindValue(':id', $_id, PDO::PARAM_INT);		

        $consulta->execute();

		if($consulta->rowCount() > 0)
        {
            $retorno = true;
        }

		return $retorno;
	}

    /*(PUT) Modificar los autos por ID.
    Recibe el JSON del auto a ser modificado (auto), el ID (id_auto) y el JWT → token (en el
    header).
    Si el perfil es ‘encargado’ se modificará de la base de datos. Caso contrario, se mostrará
    el mensaje correspondiente (indicando que usuario intentó realizar la acción).
    Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    */

    public function modificarUno(Request $request, Response $response, array $args): Response 
	{
        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo modificar el auto";
        $objetoRetorno->status = 418;

        if(isset($request->getHeader('token')[0]) && isset($request->getHeader('auto')[0]) && isset($request->getHeader('id_auto')[0]))
        {
            $token = $request->getHeader('token')[0];
            $autoJSON = json_decode($request->getHeader('auto')[0]);
            $id = $request->getHeader('id_auto')[0];
            
            //TOKEN
            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            if($perfilUsuario == "encargado")
            {
                #$jugueteSinModificar = Juguete::traerJuguete($juguete->id);
                #var_dump( $jugueteSinModificar);

                $auto = Auto::traerPorId($id);

                if($auto)
                {
                    $auto->color = $autoJSON->color;
                    $auto->marca = $autoJSON->marca;
                    $auto->precio = $autoJSON->precio;
                    $auto->modelo = $autoJSON->modelo;

                    if($auto->modificar())
                    {
                        #unlink($jugueteSinModificar->pathFoto);

                        $objetoRetorno->exito = true;
                        $objetoRetorno->mensaje = "Auto modificado";
                        $objetoRetorno->status = 200;
                    }
                }
                else
                {
                    $objetoRetorno->mensaje = "El auto no se encuentra en el listado";
                }
            }
            else
            {
                $objetoRetorno->mensaje = "No esta autorizado para eliminar juguetes, debe ser encargado y usted es {$usuarioToken->perfil}";
            }
        }
        
        $newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public function modificar() : bool 
    {
        $retorno = false;

        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("UPDATE autos SET color = :color, marca = :marca, 
                                                        precio = :precio, modelo = :modelo WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':precio', (float)$this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':modelo', $this->modelo, PDO::PARAM_STR);

        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $retorno = true;
        }

        return $retorno;
    }

    public static function traerPorId(int $id) : Auto
	{
		$objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("SELECT * FROM autos WHERE id = :id");

        $consulta->bindValue(":id", $id, PDO::PARAM_INT);

        $consulta->execute();

        $juguete = $consulta->fetchObject('Auto');

        return $juguete;
	}
    
}