<?php
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseMW;
use Poo\AccesoDatos;

require_once "accesoDatos.php";
require_once __DIR__ . "/autentificadora.php";

class usuario
{
    public int $id;
    public string $correo;
    public string $clave;
    public string $nombre;
    public string $apellido;
    public string $perfil;
    public string $foto;

    public function agregarUno(Request $request, Response $response, array $args): Response 
	{
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo agregar el usuario";
        $objetoRetorno->status = 418;

        if(isset($parametros["usuario"]))
        {
            $objetoUsuario = json_decode($parametros["usuario"]);

            $usuario = new usuario();
            $usuario->correo = $objetoUsuario->correo;
            $usuario->clave = $objetoUsuario->clave;
            $usuario->nombre = $objetoUsuario->nombre;
            $usuario->apellido = $objetoUsuario->apellido;
            $usuario->perfil = $objetoUsuario->perfil;
            $usuario->foto = "";

           $idUsuarioAgregado = $usuario->agregar();
           $usuario->id = $idUsuarioAgregado;

            if($idUsuarioAgregado)
            {
                //Subir archivo
                $archivos = $request->getUploadedFiles();
                $destino = "./src/fotos/";

                $nombreAnterior = $archivos['foto']->getClientFilename();
                $extension = explode(".", $nombreAnterior);

                $extension = array_reverse($extension);

                $foto = $destino . $usuario->correo . "_" . $idUsuarioAgregado . "." . $extension[0];
                $archivos['foto']->moveTo("." . $foto);

                $usuario->foto = $foto;

                if($usuario->modificar())
                {
                    $objetoRetorno->exito = true;
                    $objetoRetorno->mensaje = "Usuario agregado";
                    $objetoRetorno->status = 200;
                }
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

        $consulta = $objetoAcceso->retornarConsulta("INSERT INTO usuarios(correo, clave, nombre, apellido, perfil, foto)" 
                                                    . "VALUES(:correo, :clave, :nombre, :apellido, :perfil, :foto)");

        $consulta->bindValue(':correo', $this->correo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);

        if($consulta->execute())
        {
            $retorno = $objetoAcceso->retornarUltimoIdInsertado();
        }

        return $retorno;
    }

    public function modificar() : bool
    {
        $retorno = false;
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("UPDATE usuarios SET correo = :correo, clave = :clave, 
                                                    nombre = :nombre, apellido = :apellido, 
                                                    perfil = :perfil, foto = :foto WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':correo', $this->correo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);

        $filasAfectadas = $consulta->execute();

        if($filasAfectadas != false && $filasAfectadas > 0)
        {
            $retorno = true;
        }

        return $retorno;
    }

    public function traerTodos(Request $request, Response $response, array $args): Response 
	{
        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo agregar el usuario";
        $objetoRetorno->tabla = "{}";
        $objetoRetorno->status = 424;

		$listaUsuarios = usuario::traer();

        if(count($listaUsuarios) > 0)
        {
            $objetoRetorno->exito = true;
            $objetoRetorno->mensaje = "Listado de usuarios";
            $objetoRetorno->tabla = json_encode($listaUsuarios);
            $objetoRetorno->status = 200;
        }
  
		$newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');	
	}

    public static function traer() : array
    {
        $usuarios = array();
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta("SELECT id, correo AS correo, clave AS clave, 
                                                        nombre AS nombre, apellido AS apellido, 
                                                        perfil AS perfil, foto AS foto FROM usuarios");

        $consulta->execute();

        $filas = $consulta->fetchAll();

        foreach($filas as $fila)
        {
            $usuario = new usuario();
            $usuario->id = $fila[0];
            $usuario->correo = $fila[1];
            $usuario->clave = $fila[2];
            $usuario->nombre = $fila[3];
            $usuario->apellido = $fila[4];
            $usuario->perfil = $fila[5];
            $usuario->foto = $fila[6];

            array_push($usuarios, $usuario);
        }

        return $usuarios;
    }

    public function login(Request $request, Response $response, array $args): Response
    {
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "No se pudo encontrar el usuario";
        $objetoRetorno->status = 424;

		if(isset($parametros['user']))
        {
            $objeto = json_decode($parametros['user']);

            $usuario = usuario::verificar($objeto);

            if($usuario != null)
            {
                $data = new usuario();
                $data->correo = $usuario->correo;
                $data->clave = $usuario->clave;
                $data->nombre = $usuario->nombre;
                $data->apellido = $usuario->apellido;
                $data->perfil = $usuario->perfil;
                $data->foto = $usuario->foto;

                $objetoRetorno->exito = true;
                $objetoRetorno->mensaje = "Listado de usuarios";
                $objetoRetorno->jwt = Autentificadora::crearJWT($data, 12000);
                $objetoRetorno->status = 200;
            }
            
        }
  
		$newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');	
    }

    public static function verificar($objeto) : usuario | null | bool
    {
        $usuario = null;
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta( "SELECT * FROM usuarios WHERE correo = :correo AND clave = :clave");

        $consulta->bindValue(':correo', $objeto->correo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $objeto->clave, PDO::PARAM_STR);

        if($consulta->execute())
        {
            $usuario = $consulta->fetchObject('Usuario');
        }

        return $usuario;
    }

    /*(GET) Se envía el JWT → token (en el header) y se verifica. En caso exitoso, retorna un JSON
    con mensaje y status 200. Caso contrario, retorna un JSON con mensaje y status 403. */
   
    public function verificarJWT(Request $request, Response $response, array $args): Response
    {
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "Token invalido";
        $objetoRetorno->tabla = "{}";
        $objetoRetorno->status = 403;

		if(isset($parametros['token']))
        {
            $objeto = json_decode($parametros['token']);

            $usuario = usuario::verificar($objeto);

            if($usuario != null)
            {
                $data = new usuario();
                $data->correo = $usuario->correo;
                $data->clave = $usuario->clave;
                $data->nombre = $usuario->nombre;
                $data->apellido = $usuario->apellido;
                $data->perfil = $usuario->perfil;
                $data->foto = $usuario->foto;

                $objetoRetorno->mensaje = "Token valido!";
                $objetoRetorno->jwt = Autentificadora::crearJWT($data, 12000);
                $objetoRetorno->status = 200;
            }
            
        }
  
		$newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');	
    }
}