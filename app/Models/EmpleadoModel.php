<?php

declare(strict_types=1);

namespace Api\Models;

use Api\Models\Conexion;
use PDO;
use PDOException;

class EmpleadoModel extends Conexion{

    private $conexion;

    public function __construct()
    {
        $this->conexion=new Conexion();
        $this->conexion=$this->conexion->getConexion();
    }

    public function insertarUsuario($codigoEmp,$nombreEmp,int $edadEmp,float $sueldoEmp)
    {
        try
        {
            $sql="INSERT INTO Empleados(CodigoEmp, NombreEmp, EdadEmp, SueldoEmp) VALUES(?,?,?,?)";
            $arregloParametros=array($codigoEmp,$nombreEmp,$edadEmp,$sueldoEmp);
            $insert=$this->conexion->prepare($sql);
            $ResultadoInsert=$insert->execute($arregloParametros);
            return $ResultadoInsert;
        }
        catch(PDOException $e)
        {
            $ResultadoInsert=0;
            return $ResultadoInsert;
        }
    }

    public function VerEmpleados()
    {
        $sql="SELECT * FROM Empleados ORDER BY CodigoEmp ASC";
        $execute=$this->conexion->query($sql);
        $resultado=$execute->fetchall(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public function RecuperarEmpleado($codigoEmp)
    {
        $sql="SELECT * FROM Empleados WHERE CodigoEmp=?";
        $arregloParametros=array($codigoEmp);
        $query=$this->conexion->prepare($sql);
        $query->execute($arregloParametros);
        $resultado=$query->fetchall(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public function actualizarEmpleado(string $codigoEmp,string $nombreEmp, int $edadEmp, float $sueldoEmp)
    {
        $sql="UPDATE Empleados SET NombreEmp=?, EdadEmp=?, SueldoEmp=? WHERE CodigoEmp=?";
        $arregloParametros=array($nombreEmp,$edadEmp,$sueldoEmp,$codigoEmp);
        $update=$this->conexion->prepare($sql);
        $resultado=$update->execute($arregloParametros);
        $filasAfectadas=$update->rowCount();
        return ($resultado and $filasAfectadas>0);
        
    }

    public function eliminarEmpleado(string $codigoEmp)
    {
        $sql="DELETE FROM Empleados WHERE CodigoEmp=?";
        $arregloParametros=array($codigoEmp);
        $delete=$this->conexion->prepare($sql);
        $resultado=$delete->execute($arregloParametros);
        $filasAfectadas=$delete->rowCount();
        return ($resultado and $filasAfectadas>0);
        
    }    


    
}