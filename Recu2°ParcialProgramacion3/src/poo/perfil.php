<?php
namespace Galjot\Maitena;
use Firebase\JWT\JWT;
use Galjot\Maitena\AccesoDatos as AccesoDatos;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseMW;
use stdClass;

require_once "accesoDatos.php";
require_once __DIR__ . "/autentificadora.php";

class Perfil
{
    public int $id;
    public string $descripecion;
    public int $estado;

    public function agregarUno(Request $request, Response $response, array $args): Response 
	{
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo agregar el perfil";
        $objetoRetorno->status = 418;

        if(isset($parametros["perfil"]))
        {
            $perfilJSON = json_decode($parametros["perfil"]);

            $perfil = new Perfil();
            $perfil->descripecion = $perfilJSON->descripcion;
            $perfil->estado = $perfilJSON->estado;

           if($perfil->agregar())
            {
                $objetoRetorno->exito = true;
                $objetoRetorno->mensaje = "Perfil agregado";
                $objetoRetorno->status = 200;
            }    
        }

        $newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public function agregar() : bool | int
    {
        $retorno = false;

        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("INSERT INTO perfiles(descripcion, estado)" . "VALUES(:descripcion, :estado)");

        $consulta->bindValue(':descripcion', $this->descripecion, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        if($consulta->execute())
        {
            $retorno = $objetoAcceso->retornarUltimoIdInsertado();
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

		$listaUsuarios = Perfil::traer();

        if(count($listaUsuarios) > 0)
        {
            $objetoRetorno->exito = true;
            $objetoRetorno->mensaje = "Listado de perfiles";
            $objetoRetorno->tabla = json_encode($listaUsuarios);
            $objetoRetorno->status = 200;
        }
  
		$newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');	
	}

    public static function traer() : array
    {
        $perfiles = array();
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("SELECT id, descripcion AS descripcion, estado AS estado  FROM perfiles");

        $consulta->execute();

        $filas = $consulta->fetchAll();

        foreach($filas as $fila)
        {
            $perfil = new Perfil();
            $perfil->id = $fila[0];
            $perfil->descripecion = $fila[1];
            $perfil->estado = (int)$fila[2];

            array_push($perfiles, $perfil);
        }

        return $perfiles;
    }

    public function borrarUno(Request $request, Response $response, array $args): Response 
	{		 
        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo eliminar el perfil";
        $objetoRetorno->status = 418;

        if(isset($request->getHeader('token')[0]) && isset($args['id']))
        {
            $token = $request->getHeader('token')[0];
            $id = $args['id'];

            if(Autentificadora::verificarJWT($token))
            {
                if(Perfil::borrar($id))
                {
                    $objetoRetorno->exito = true;
                    $objetoRetorno->mensaje = "Perfil eliminado";
                    $objetoRetorno->status = 200;
                }
                else
                {
                    $objetoRetorno->mensaje = "El perfil no se encuentra en el listado";
                }
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

		$consulta = $objetoAccesoDato->RetornarConsulta("DELETE FROM perfiles WHERE id = :id");	
		$consulta->bindValue(':id', $_id, PDO::PARAM_INT);		

        $consulta->execute();

		if($consulta->rowCount() > 0)
        {
            $retorno = true;
        }

		return $retorno;
	}

    public function modificarUno(Request $request, Response $response, array $args): Response 
	{
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo modificar el perfil";
        $objetoRetorno->status = 418;

        if(isset($request->getHeader('token')[0]) && isset($request->getHeader('perfil')[0]))
        {
            $token = $request->getHeader('token')[0];
            $objetoJSON = json_decode($request->getHeader('perfil')[0]);

            $perfil = new Perfil();
            $perfil->id = $objetoJSON->id;
            $perfil->descripecion = $objetoJSON->descripcion;
            $perfil->estado = (int)$objetoJSON->estado;

            if(Autentificadora::verificarJWT($token))
            {
                if($perfil->modificar())
                {
                    $objetoRetorno->exito = true;
                    $objetoRetorno->mensaje = "Perfil modificado";
                    $objetoRetorno->status = 200;
                }
                else
                {
                    $objetoRetorno->mensaje = "El perfil no se encuentra en el listado";
                }
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

        $consulta = $objetoAcceso->retornarConsulta("UPDATE perfiles SET descripcion = :descripcion, estado = :estado WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $this->descripecion, PDO::PARAM_STR);
        $consulta->bindValue(':estado', (int)$this->estado, PDO::PARAM_STR);

        $consulta->execute();

        if($consulta->rowCount() > 0)
        {
            $retorno = true;
        }

        return $retorno;
    }

    
}