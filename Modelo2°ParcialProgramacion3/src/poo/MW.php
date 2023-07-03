<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once "accesoDatos.php";
//require_once "usuario.php";
require_once __DIR__ . "/autentificadora.php";

class MW
{
    public function ValidarCorreoYClave(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";

        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "No ingreso ni el correo ni la clave";
        $objetoRetorno->status = 403;

        if(isset($parametros['usuario']))
        {
            $objetoUsuario = json_decode($parametros['usuario']);

            if($objetoUsuario)
            {
                if(isset($objetoUsuario->correo) && isset($objetoUsuario->clave))
                {
                    $response = $handler->handle($request);
                    $contenidoAPI = (string) $response->getBody();
                    $api_respuesta = json_decode($contenidoAPI);
                    $objetoRetorno->status = $api_respuesta->status;
                }
                else
                {
                    $mensajeError = "Falto el campo: ";

                    if(!isset($objetoUsuario->correo))
                    {
                        $mensajeError.= "correo";
                    }

                    if(!isset($objetoUsuario->clave))
                    {
                        $mensajeError.= "clave";
                    }

                    $objetoRetorno->mensaje = $mensajeError;
                    $contenidoAPI = json_encode($objetoRetorno);
                }
            }

            $response = new ResponseMW();
            $response = $response->withStatus($objetoRetorno->status);
            $response->getBody()->write($contenidoAPI);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ValidarParametrosVacios(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";

        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "Hay un parametro vacio";
        $objetoRetorno->status = 409;

        if(isset($parametros['usuario']))
        {
            $objetoUsuario = json_decode($parametros['usuario']);

            if($objetoUsuario)
            {
                if($objetoUsuario->correo != "" && $objetoUsuario->clave != "")
                {
                    $response = $handler->handle($request);
                    $contenidoAPI = (string) $response->getBody();
                    $api_respuesta = json_decode($contenidoAPI);
                    $objetoRetorno->status = $api_respuesta->status;
                }
                else
                {
                    $mensajeError = "Parametros vacios: ";

                    if($objetoUsuario->correo == "")
                    {
                        $mensajeError.= "correo - ";
                    }

                    if($objetoUsuario->clave == "")
                    {
                        $mensajeError.= "clave";
                    }

                    $objetoRetorno->mensaje = $mensajeError;
                    $contenidoAPI = json_encode($objetoRetorno);
                }
            }

            $response = new ResponseMW();
            $response = $response->withStatus($objetoRetorno->status);
            $response->getBody()->write($contenidoAPI);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function ValidarCorreoYClaveExistentes(Request $request, RequestHandler $handler): ResponseMW
    {
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "El correo y la clave no existen";
        $objetoRetorno->status = 403;
        $objetoUsuario = null;

        if(isset($parametros['usuario']))
        {
            $objetoUsuario = json_decode($parametros['usuario']);

            if(Usuario::verificar($objetoUsuario))
            {
                $response = $handler->handle($request);
                $contenidoAPI = (string) $response->getBody();
                $api_respuesta = json_decode($contenidoAPI);
                $objetoRetorno->status = $api_respuesta->status;
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
            } 
        }

        $response = new ResponseMW();
        $response = $response->withStatus($objetoRetorno->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarCorreoNoExistente(Request $request, RequestHandler $handler): ResponseMW
    {
        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "El correo ya existen";
        $objetoRetorno->status = 403;
        $objetoUsuario = null;

        if(isset($parametros['usuario']))
        {
            $objetoUsuario = json_decode($parametros['usuario']);

            if(!Usuario::verificarCorreo($objetoUsuario))
            {
                $response = $handler->handle($request);
                $contenidoAPI = (string) $response->getBody();
                $api_respuesta = json_decode($contenidoAPI);
                $objetoRetorno->status = $api_respuesta->status;
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
            } 
        }

        $response = new ResponseMW();
        $response = $response->withStatus($objetoRetorno->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarPrecioYColor(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";

        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "El precio y el color no son validos";
        $objetoRetorno->status = 409;

        if(isset($parametros['auto']))
        {
            $objetoAuto = json_decode($parametros['auto']);

            if($objetoAuto)
            {
                if($objetoAuto->precio >= 50000 && $objetoAuto->precio <= 600000 && $objetoAuto->color != "azul")
                {
                    $response = $handler->handle($request);
                    $contenidoAPI = (string) $response->getBody();
                    $api_respuesta = json_decode($contenidoAPI);
                    $objetoRetorno->status = $api_respuesta->status;
                }
                else
                {
                    $mensajeError = "Parametros no permitidos: ";

                    if($objetoAuto->color == "azul")
                    {
                        $mensajeError.= "color";
                    }

                    if($objetoAuto->precio < 50000 || $objetoAuto->precio > 600000)
                    {
                        $mensajeError.= "precio";
                    }

                    $objetoRetorno->mensaje = $mensajeError;
                    $contenidoAPI = json_encode($objetoRetorno);
                }
            }

            $response = new ResponseMW();
            $response = $response->withStatus($objetoRetorno->status);
            $response->getBody()->write($contenidoAPI);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function ValidarToken(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->status = 403;
        $objetoRetorno->mensaje = "Token invalido!";

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $obj = Autentificadora::verificarJWT($token);

            if ($obj->verificado) 
            {
                $response = $handler->handle($request);
                $contenidoAPI = (string) $response->getBody();
                $api_respuesta = json_decode($contenidoAPI);
                $objetoRetorno->status = $api_respuesta->status;
                $objetoRetorno->exito = true;
                $objetoRetorno->status = 200;
                $objetoRetorno->mensaje = "Token valido!";
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
            }

            $objetoRetorno->mensaje = $obj;
        }

        $response = new ResponseMW();
        $response = $response->withStatus($objetoRetorno->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarPropietario(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es propietario";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            if ($perfilUsuario == "propietario") 
            {
                $response = $handler->handle($request);
                $contenidoAPI = (string) $response->getBody();
                $api_respuesta = json_decode($contenidoAPI);
                $objetoRetorno->status = $api_respuesta->status;
                $objetoRetorno->mensaje = "El usuario es propietario";
                $objetoRetorno->status = 200;
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus($objetoRetorno->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarEncargado(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es encargado";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            if ($perfilUsuario == "encargado") 
            {
                $response = $handler->handle($request);
                $contenidoAPI = (string) $response->getBody();
                $api_respuesta = json_decode($contenidoAPI);
                $objetoRetorno->status = $api_respuesta->status;
                $objetoRetorno->mensaje = "El usuario es encargado";
                $objetoRetorno->status = 200;
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus($objetoRetorno->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }
    
    //LISTADOS

    public function ListarAutosSiEsEncargado(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es encargado";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();

            if ($perfilUsuario == "encargado") 
            {
                $autos = Auto::traer();

                foreach ($autos as $auto) 
                {
                    unset($auto->id);
                }

                $contenidoAPI = json_encode($autos);
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
                $objetoRetorno->mensaje = "El usuario no es encargado";
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus(200);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');

     
    }

    public function ListarAutosSiEsEmpleado(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es empleado";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();

            if ($perfilUsuario == "empleado") 
            {
                //$api_respuesta = json_decode($contenidoAPI);
                //$array_autos = json_decode($api_respuesta->dato);

                $autos = Auto::traer();
                $colores = [];

                foreach ($autos as $auto) 
                {
                    array_push($colores, $auto->color);
                }

                $cantColores = array_count_values($colores);

                $objetoRetorno->mensaje = "Hay " . count($cantColores) . " colores distintos en el listado de autos.";
                $objetoRetorno->colores = $cantColores;
                $objetoRetorno->exito = true;

                $contenidoAPI = json_encode($objetoRetorno);
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus(200);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ListarAutosSiEsPropietario(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es propietario";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0]) && isset($request->getHeader("id_auto")[0])) 
        {
            $token = $request->getHeader("token")[0];
            $id = $request->getHeader("id_auto")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();

            if ($perfilUsuario == "propietario") 
            {
                $autos = Auto::traer();

                if($id != null)
                {
                    foreach ($autos as $auto) 
                    {
                        if($id == $auto->id)
                        {
                            $autos = $auto;
                            break;
                        }
                    }
                }
                
                $contenidoAPI = json_encode($autos);
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus(200);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarUsuariosSiEsEncargado(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es encargado";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();

            if ($perfilUsuario == "encargado") 
            {
                $usuarios = Usuario::traer();

                foreach ($usuarios as $usuario) 
                {
                    unset($usuario->id);
                    unset($usuario->clave);
                }

                $contenidoAPI = json_encode($usuarios);
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
                $objetoRetorno->mensaje = "El usuario no es encargado";
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus(200);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');

     
    }

    public function ListarUsuariosSiEsEmpleado(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es empleado";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();

            if ($perfilUsuario == "empleado") 
            {
                $usuarios = Usuario::traer();

                foreach ($usuarios as $usuario) 
                {
                    unset($usuario->id);
                    unset($usuario->clave);
                    unset($usuario->correo);
                    unset($usuario->foto);
                }

                $contenidoAPI = json_encode($usuarios);
            }
            else
            {
                $contenidoAPI = json_encode($objetoRetorno);
                $objetoRetorno->mensaje = "El usuario no es empleado";
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus(200);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');

     
    }

    public static function ListarUsuariosSiEsPropietario(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";
        $objetoRetorno = new stdClass();
        $objetoRetorno->exito = false;
        $objetoRetorno->mensaje = "El usuario no es propietario";
        $objetoRetorno->status = 409;

        if (isset($request->getHeader("token")[0]) && isset($request->getHeader("apellido")[0])) 
        {
            $token = $request->getHeader("token")[0];
            $apellido = $request->getHeader("apellido")[0];

            $datosToken = Autentificadora::obtenerPayLoad($token);
            $usuarioToken = json_decode($datosToken->payload->data->usuario);
            $perfilUsuario = $usuarioToken->perfil;

            $response = $handler->handle($request);
            $contenidoAPI = (string) $response->getBody();

            if ($perfilUsuario == "propietario") 
            {
                $usuarios = Usuario::traer();

                $apellidosIguales = [];
                $todosLosApellidos = [];

                if($apellido != NULL)
                {
                    foreach($usuarios as $usuario)
                    {
                        if($usuario->apellido == $apellido)
                        {
                            array_push($apellidosIguales, $usuario);
                        }
                    }

                    if(count($apellidosIguales) == 0)
                    {
                        $cantidad = 0;
                    }
                    else
                    {
                        $cantidad = count($apellidosIguales);
                    }
                    
                    $contenidoAPI = "La cantidad de apellidos iguales es : {$cantidad} - {$apellido}";
                } 
                else 
                {
                    foreach($usuarios as $usuario)
                    {
                        array_push($todosLosApellidos, $usuario->apellido);
                    }

                    $todosLosApellidos = array_count_values($todosLosApellidos);
                    $contenidoAPI = json_encode($todosLosApellidos);
                }         
            }
        }

        $response = new ResponseMW();
        $response = $response->withStatus(200);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }
}