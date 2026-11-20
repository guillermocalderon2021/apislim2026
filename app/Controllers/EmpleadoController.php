<?php

declare(strict_types=1);

namespace Api\Controllers;

use Api\Models\EmpleadoModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EmpleadoController{

    private $OBJEmpleadoModel;

    public function __construct(){
        $this->OBJEmpleadoModel = new EmpleadoModel();
    }

    public function VerTodos(Request $request, Response $response, array $args){
        $listaEmpleados=$this->OBJEmpleadoModel->VerEmpleados();
        $json=[];
        if($listaEmpleados!=null){
            $codigoHTTP=200;
            $json["Estado"]="Exito";
            $json["Mensaje"]="Empleados recuperados...";
            $json["data"]=$listaEmpleados;
            $response->getBody()->write(json_encode($json));
        }
        else{
            $codigoHTTP=404;
            $json["Estado"]="Error";
            $json["Mensaje"]="No hay empleados";
            $json["data"]=[];
            $response->getBody()->write(json_encode($json));
        }
        return $response->withHeader('Content-type','application/json')->withStatus($codigoHTTP);
    }

        public function RecuperarEmpleado(Request $request, Response $response, array $args){
        $codigoEmp=$args['id'];
        $empleado=$this->OBJEmpleadoModel->RecuperarEmpleado($codigoEmp);
        $json=[];
        if($empleado!=null){
            $codigoHTTP=200;
            $json["Estado"]="Exito";
            $json["Mensaje"]="Empleados $codigoEmp recuperado...";
            $json["data"]=$empleado;
            $response->getBody()->write(json_encode($json));
        }
        else{
            $codigoHTTP=404;
            $json["Estado"]="Error";
            $json["Mensaje"]="Empleado $codigoEmp no encontrado";
            $json["data"]=[];
            $response->getBody()->write(json_encode($json));
        }
        return $response->withHeader('Content-type','application/json')->withStatus($codigoHTTP);
    }

      public static function CheckParametros($requiredParams, Request $request, Response $response)
    {
        $error=false;
        $errorParams='';
        $requestParams=$request->getParsedBody();
        foreach($requiredParams as $param)
        {
            if(!isset($requestParams[$param]) || strlen($requestParams[$param])<=0 || $requestParams[$param]=='NaN')
            {
                $error=true;
                $errorParams=$errorParams.$param.' ';
            }
        }
        if ($error)
        {
            $json=array();
            $json["Estado"]="Error";
            $json["Mensaje"]="Faltan parametros: ".$errorParams." todos los datos del empleado son obligatorios.";
            $json["data"]=[];
            $response->getBody()->write(json_encode($json));
        }
        return $error;
    }

    public function CrearEmpleado(Request $request, Response $response, array $args){

        if(!EmpleadoController::CheckParametros(array('CodigoEmp','NombreEmp','EdadEmp', 'SueldoEmp'),$request, $response))
        {

                $datosRecibidos=$request->getParsedBody();
                $codigoEmp=$datosRecibidos['CodigoEmp'];
                $nombreEmp=$datosRecibidos['NombreEmp'];
                $edadEmp=(int)$datosRecibidos['EdadEmp'];
                $sueldoEmp=(float)$datosRecibidos['SueldoEmp'];

                $EmpleadoDB=new EmpleadoModel();
                $resultadoInsert=$EmpleadoDB->insertarUsuario($codigoEmp,$nombreEmp,$edadEmp,$sueldoEmp);

                $json=array();
                if ($resultadoInsert==true)
                {
                    $codigoHTTP=201;
                    $json["Estado"]="Exito";
                    $json["Mensaje"]="Empleado $codigoEmp registrado con exito.";
                    $json["data"]=[];
                    $response->getBody()->write(json_encode($json));
                }
                else
                {
                    $codigoHTTP=202;
                    $json["Estado"]="Error";
                    $json["Mensaje"]="No se pudo insertar el empleado $codigoEmp codigo ya existe.";
                    $json["data"]=[];
                    $response->getBody()->write(json_encode($json));

                }
            }
            else
            {
                $codigoHTTP=202;
            }
            return $response
                ->withHeader('Content-type','application/json')
                ->withStatus($codigoHTTP);

        }


    public function ActualizarEmpleado(Request $request, Response $response, array $args){
        $contentType = $request->getHeaderLine('Content-Type');
        if ($contentType === 'application/json') {
            $datosRecibidos = json_decode($request->getBody()->getContents(), true);
        } else {
            $datosRecibidos = $request->getParsedBody();
        }

        if(!EmpleadoController::CheckParametros(array('NombreEmp','EdadEmp', 'SueldoEmp'),$request,$response))
       {
            //Recuperamos el parametro
            $codigoEmp=$args['id'];
            //recuperamos el resto de datos a actualizar del cuerpo de la peticion
            $nombreEmp=$datosRecibidos['NombreEmp'];
            $edadEmp=(int)$datosRecibidos['EdadEmp'];
            $sueldoEmp=(float)$datosRecibidos['SueldoEmp'];

            $EmpleadoDB=new EmpleadoModel();
            $resultadoUpdate=$EmpleadoDB->actualizarEmpleado($codigoEmp,$nombreEmp,$edadEmp,$sueldoEmp);

            $json=array();
            if ($resultadoUpdate==true)
            {
                $codigoHTTP=200;
                $json["Estado"]="Exito";
                $json["Mensaje"]="Empleado $codigoEmp actualizado con exito.";
                $json["data"]=[];
                $response->getBody()->write(json_encode($json));
            }
            else
            {
                $codigoHTTP=202;
                $json["Estado"]="Error";
                $json["Mensaje"]="No se pudo actualizar el empleado $codigoEmp no existe o no ha sido modificado ningun dato.";
                $json["data"]=[];
                $response->getBody()->write(json_encode($json));

            }
        }
        else
        {
            $codigoHTTP=400;
        }
        return $response
            ->withHeader('Content-type','application/json')
            ->withStatus($codigoHTTP);
          }

    public function BorrarEmpleado(Request $request, Response $response, array $args){
        //Recuperamos el parametro
        $codigoEmp=$args['id'];
        $EmpleadoDB=new EmpleadoModel();
        $resultadoDelete=$EmpleadoDB->eliminarEmpleado($codigoEmp);
        $json=array();
        if ($resultadoDelete==true)
        {
            $codigoHTTP=200;
            $json["Estado"]="Exito";
            $json["Mensaje"]="Empleado $codigoEmp eliminado con exito.";
            $json["data"]=[];
            $response->getBody()->write(json_encode($json));
        }
        else
        {
            $codigoHTTP=404;
            $json["Estado"]="Error";
            $json["Mensaje"]="No se pudo eliminar al empleado $codigoEmp, o no existe.";
            $json["data"]=[];
            $response->getBody()->write(json_encode($json));

        }
        return $response
            ->withHeader('Content-type','application/json')
            ->withStatus($codigoHTTP);
    }


}