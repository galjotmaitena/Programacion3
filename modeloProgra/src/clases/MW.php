<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once "accesoDatos.php";
require_once __DIR__ . "/autentificadora.php";

class MW
{
    /*1.- (método de instancia) Verifique que estén “seteados” el correo y la clave.
    Si no existe alguno de los dos (o los dos) retorne un JSON con el mensaje de error
    correspondiente (y status 403).
    Si existen, pasar al siguiente Middleware que verifique que: */

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
    
    /*2.- (método de clase) Si alguno está vacío (o los dos) retorne un JSON con el mensaje de error
    correspondiente (y status 409).
    Caso contrario, pasar al siguiente Middleware. */

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
                        $mensajeError.= "correo";
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

    /*3.- (método de instancia) Verificar que el correo y clave existan en la base de datos. Si NO
    existen, retornar un JSON con el mensaje de error correspondiente (y status 403).
    Caso contrario, acceder al verbo de la API.*/

    public function ValidarCorreoYClaveExistentes(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";

        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "El correo y la clave no existen";
        $objetoRetorno->status = 403;

        if(isset($parametros['usuario']))
        {
            $objetoUsuario = json_decode($parametros['usuario']);

            if($objetoUsuario)
            {
                if(usuario::verificar($objetoUsuario) != null)
                {
                    $response = $handler->handle($request);
                    $contenidoAPI = (string) $response->getBody();
                    $api_respuesta = json_decode($contenidoAPI);
                    $objetoRetorno->status = $api_respuesta->status;
                }
            }

            $response = new ResponseMW();
            $response = $response->withStatus($objetoRetorno->status);
            $response->getBody()->write($contenidoAPI);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    /*4.- (método de clase) Verificar que el correo no exista en la base de datos. Si EXISTE, retornar
    un JSON con el mensaje de error correspondiente (y status 403).
    Caso contrario, acceder al verbo de la API. */

    public function ValidarCorreo(Request $request, RequestHandler $handler): ResponseMW
    {
        $contenidoAPI = "";

        $parametros = $request->getParsedBody();

        $objetoRetorno = new stdclass();
        $objetoRetorno->mensaje = "El correo ya existe";
        $objetoRetorno->status = 403;

        if(isset($parametros['usuario']))
        {
            $objetoUsuario = json_decode($parametros['usuario']);

            if($objetoUsuario)
            {
                $usuario = usuario::verificar($objetoUsuario);
                
                if($usuario == null)
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
    }

    /*5.- (método de instancia) Verificar que el precio posea un rango de entre 50.000 y 600.000 y
    que el color no sea ‘azul’. Si no pasa la validación alguno de los dos (o los dos) retorne un JSON
    con el mensaje de error correspondiente (y status 409).
    Caso contrario, acceder al verbo de la API. */

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
}