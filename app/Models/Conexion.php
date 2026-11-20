<?php

declare(strict_types=1);

namespace Api\Models;

use PDO;
use PDOException;

class Conexion
{
    private $conBD;

    public function __construct()
    {
        //Recuperamos los datos secretos de las variables de entorno o usamos valores por defecto para desarrollo local si no se han configurado las variables de entorno.
        $DB_HOST=$_ENV['DB_HOST'] ?? "localhost";
        $DB_USER=$_ENV['DB_USER'] ?? "root";
        $DB_PASS=$_ENV['DB_PASS'] ??  "";
        $DB_NAME=$_ENV['DB_NAME'] ?? "EjemploAPI";
        $DB_PORT=$_ENV['DB_PORT'] ?? "3306";

        $cadenaConexion="mysql:host=".$DB_HOST.";port=".$DB_PORT.";dbname=".$DB_NAME.";charset=utf8";

        try
        {
            $this->conBD=new PDO($cadenaConexion,$DB_USER,$DB_PASS);
            $this->conBD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Conexion Exitosa";
        }catch(PDOException $e)
        {
            header('Content-type: application/json');
            $this->conBD="Error de conexion";
            http_response_code(500);
            $json=array();
            $json["Estado"]="Error";
            //$json["Mensaje"]=" No se puede conectar con la base de datos en este momento. Intente más tarde."." Detalle: ".$e->getMessage();
            $mensajeError=$e->getMessage();
            if(str_contains($mensajeError,'Connection refused') or str_contains($mensajeError,'refused it')) 
            { 
                //Servidor MySQL apagado
                $json["Mensaje"]=" No se puede establecer conexión con el servidor de base de datos.";
            } 
            else if(str_contains($mensajeError,'Unknown database')) 
            { 
                //Base de datos no existe 
                $json["Mensaje"]="La base de datos configurada no existe."; 
            }  
            else if(str_contains($mensajeError,'getaddrinfo')) 
            {
                //Host incorrecto
                $json["Mensaje"]="No se encontró el servidor de base de datos."; 
            }  
            else 
            { 
                //Error general    
                $json["Mensaje"]="Error interno de conexión con la base de datos.";
            }
            $json["data"]=[];
            echo json_encode($json);
            exit;
        }
    }

    public function getConexion()
    {
        return $this->conBD;
    }
}