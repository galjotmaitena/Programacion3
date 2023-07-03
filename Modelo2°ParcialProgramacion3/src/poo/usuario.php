<?php
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseMW;
use Poo\AccesoDatos;

require_once "accesoDatos.php";
require_once __DIR__ . "/autentificadora.php";

class Usuario
{
    public int $id;
    public string $correo;
    public string $clave;
    public string $nombre;
    public string $apellido;
    public string $foto;
    public string $perfil;

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
        $objetoRetorno->mensaje = "No se pudo traer la lista";
        $objetoRetorno->tabla = "{}";
        $objetoRetorno->status = 424;

		$listaUsuarios = Usuario::traer();

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
                                                        foto AS foto, perfil AS perfil FROM usuarios");

        $consulta->execute();

        $filas = $consulta->fetchAll();

        foreach($filas as $fila)
        {
            $usuario = new Usuario();
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

		if(isset($parametros['usuario']))
        {
            $objeto = json_decode($parametros['usuario']);

            $usuario = Usuario::verificar($objeto);

            if($usuario != null)
            {
                $usuarioData = new Usuario();
                $usuarioData->correo = $usuario->correo;
                $usuarioData->nombre = $usuario->nombre;
                $usuarioData->apellido = $usuario->apellido;
                $usuarioData->perfil = $usuario->perfil;
                $usuarioData->foto = $usuario->foto;

                $data = new stdclass();
                $data->usuario = json_encode($usuarioData);
                $data->alumno = "Galjot Maitena";
                $data->dni_alumno = "44457866";

                $objetoRetorno->exito = true;
                $objetoRetorno->mensaje = "Token creado!";
                $objetoRetorno->jwt = Autentificadora::crearJWT($data, 120000);
                $objetoRetorno->status = 200;
            }
            
        }
  
		$newResponse = $response->withStatus($objetoRetorno->status);
        $newResponse->getBody()->write(json_encode($objetoRetorno));

        return $newResponse->withHeader('Content-Type', 'application/json');	
    }

    public static function verificar($objeto) : Usuario | null | bool
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

    public function verificarJWT(Request $request, Response $response, array $args): Response
    {
        $contenidoAPI = "";
        $obj_respuesta = new stdClass();
        $obj_respuesta->exito = false;
        $obj_respuesta->status = 403;

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $obj = Autentificadora::verificarJWT($token);

            if ($obj->verificado) 
            {
                $obj_respuesta->exito = true;
                $obj_respuesta->status = 200;
            }

            $obj_respuesta->mensaje = $obj;
        }


        $contenidoAPI = json_encode($obj_respuesta);

        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarCorreo($objeto) : bool
    {
        $retorno = false;
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAcceso->retornarConsulta( "SELECT * FROM usuarios WHERE correo = :correo");

        $consulta->bindValue(':correo', $objeto->correo, PDO::PARAM_STR);

        $consulta->execute();
        $usuario = $consulta->fetchObject('Usuario');

        if($usuario != null)
        {
            $retorno = true;
        }

        return $retorno;
    }

}