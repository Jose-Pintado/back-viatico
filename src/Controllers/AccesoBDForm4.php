<?php  namespace App\Controllers;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use App\Verificaciones\Verificacion;

class AccesoBDForm4 {
    protected $container;
    public function __construct (ContainerInterface $c)  //esto es un constructor de clase
    {  
        $this -> container = $c ;
    }
    public function existeForm($tabla, $idForm, $id){
        $conexion = $this->container->get('bd');
        $respuesta = 0;
        $sql="SELECT * FROM ".$tabla." WHERE id_".$idForm."='".$id."'";
        
        try {
            $query = $conexion->prepare($sql);
            //$query->bindParam(':tabla', $tabla,PDO::PARAM_STR); 
            //$query->bindParam(':id', $id,PDO::PARAM_STR); 
            $query->execute();
            if($query->rowCount()>0) $respuesta = 1;
        }  catch (PDOException $e){
            $respuesta = 0;
            
         }
        
        return $respuesta;
    }
   
    public function guardarForm4($datos){
        $conexion = $this->container->get('bd');
       
        $uuid = Uuid::uuid6();
        $id = $uuid->toString();
    if ($this->existeForm("form1","form1", $datos->id_form1)==1)
    {
        if ($this->existeForm("form4","form4", $datos->id_form1)==0)
        {
            $fecha_elaboracion = date('Y-m-d');
        $sql1="
        INSERT INTO form4 ( 
            id_form4,
            id_form1,
            form4_fecha_elaboracion,
            form4_hora_salida,
            form4_hora_retorno,
            objetivo_viaje,
            desarrollo,
            conclusiones
            ) 
        VALUES ( 
            :id_form4,
            :id_form1,
            :form4_fecha_elaboracion,
            :form4_hora_salida,
            :form4_hora_retorno,
            :objetivo_viaje,
            :desarrollo,
            :conclusiones
              );
                ";
                $query = $conexion->prepare($sql1);
                $query->bindParam(':id_form4', $id,PDO::PARAM_STR); 
                $query->bindParam(':id_form1', $datos->id_form1,PDO::PARAM_STR);
                $query->bindParam(':form4_fecha_elaboracion', $datos->form4_fecha_elaboracion,PDO::PARAM_STR); 
                $query->bindParam(':form4_hora_salida', $datos->form4_hora_salida,PDO::PARAM_STR);
                $query->bindParam(':form4_hora_retorno', $datos->form4_hora_retorno,PDO::PARAM_STR);
                $query->bindParam(':objetivo_viaje', $datos->objetivo_viaje,PDO::PARAM_STR);
                $query->bindParam(':desarrollo', $datos->desarrollo,PDO::PARAM_STR); 
                $query->bindParam(':conclusiones', $datos->conclusiones,PDO::PARAM_STR); 
                

         try {
            $query->execute();

            $datos = $query->fetch(PDO::FETCH_NUM);
          
            $query = null;
            $consulta = null;
            $conexion = null;
            return array('success' => true,'message' => 'Formulario registrado', 'id' => $id);
            } catch (Exception $e) {
            return array('success' => false,'message' => 'Error insertar Formulario 4');
           }  
        }
        else {
            return array('success' => false,'message' => 'Ya existe formulario 4');
        }
    }
    else {
        return array('success' => false,'message' => 'No existe formulario 1');
    }
        
          
    }
/*--------------------EDITAR FORMULARIO---------------------------- */
    public function editForm4($datos){
        $conexion = $this->container->get('bd');

        $f = new Verificacion();
        if ($this->existeForm("form4","form4", $datos->id_form4)==1)
        {
            try {
                                
                $sqluf="
                UPDATE form4 SET  
                     
                    form4_hora_salida =:form4_hora_salida, 
                    form4_hora_retorno=:form4_hora_retorno, 
                    objetivo_viaje =:objetivo_viaje, 
                    desarrollo =:desarrollo,
                    conclusiones =:conclusiones 
                   
                    WHERE id_form4=:id_form4;
                    ";
                    $query = $conexion->prepare($sqluf);
                    $query->bindParam(':id_form4', $datos->id_form4,PDO::PARAM_STR);
                    $query->bindParam(':form4_hora_salida', $datos->form4_hora_salida,PDO::PARAM_STR);
                    $query->bindParam(':form4_hora_retorno', $datos->form4_hora_retorno,PDO::PARAM_STR);
                    $query->bindParam(':objetivo_viaje', $datos->objetivo_viaje,PDO::PARAM_STR);
                    $query->bindParam(':desarrollo', $datos->desarrollo,PDO::PARAM_STR); 
                    $query->bindParam(':conclusiones', $datos->conclusiones,PDO::PARAM_STR); 

                    $query->execute();
                    
                    $datos = $query->fetch(PDO::FETCH_NUM);
                
                   
                    $query = null;
                    $conexion = null;
                    return array('success' => true,'message' => 'Formulario Modificado');
                    } catch (Exception $e) {
                    return array('success' => false,'message' => 'Error insertar Formulario 2');
                   }  
        }
        else
        {
            return array('success' => false,'message' => 'Formulario 4 inexistente');
        }
        
          
    } 
    
    public function eliminarForm_4($idForm){
        
        $conexion = $this->container->get('bd');
        $f = new Verificacion();
        if ($this->existeForm("form4","form4", $idForm)==1)
         
        {
            try{
               
                $sql1="DELETE FROM form4 WHERE id_form4='".$idForm."'
                      ";
                $query = $conexion->prepare($sql1);
                $query->execute();
              
                $query = null;
                
                $conexion = null;
            return array('success' => true, 'message' => 'Formulario eliminado');
            }
            catch (Exception $e) {
                return array('success' => false,'message' => 'Error eliminar formulario');
            }
            
        }else
        {
            return array('error' => false, 'message' => 'Formulario No existe');
        }
       
    }
    public function verFormulario4($idForm){
        $conexion = $this->container->get('bd');
        $sql = "
            SELECT *
            FROM form4 
            WHERE id_form1 = :idForm ;";
        
       try {
        $consulta = $conexion->prepare($sql);
        $consulta->bindValue(':idForm',$idForm,PDO::PARAM_STR);
        $consulta->execute();
        $datos = [];
        if($consulta->rowCount()>0){
            $i = 0;
            while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
            {
                //$i++;
                foreach ($reg as $clave => $valor){
                    $datos[$clave]=$valor;
                }
            }
        $sql = "
        SELECT objeto_viaje
        FROM form1 
        WHERE id_form1 = :idForm ;";
        $consulta = $conexion->prepare($sql);
        $consulta->bindValue(':idForm',$idForm,PDO::PARAM_STR);
        $consulta->execute();
            while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
            {
            //$i++;
                foreach ($reg as $clave => $valor){
                $datos[$clave]=$valor;
                }
            }
        
        if ($this->existeForm("form2","form1", $idForm)==1)
        {
            $sql = "
            SELECT ft2.id_form2, ft2.nro_tramo, ft2.tramo_fecha_salida,
            ft2.tramo_hora_salida,  ft2.pais_salida, ft2.dpto_salida,
            ft2.municipio_salida,   ft2.poblacion_salida, ft2.pais_destino,
            ft2.dpto_destino,  ft2.municipio_destino,  ft2.poblacion_destino,
            ft2.tipo_transporte
            FROM form2_tramos ft2, form1 f1, form2 f2
            WHERE f1.id_form1 = :idForm and
                  f2.id_form1 = f1.id_form1 and 
                  f2.id_form2 = ft2.id_form2;";
            
        }else {
            $sql = "
            SELECT *
            FROM form1_tramos
            WHERE id_form1 = :idForm ;";
        }
        $consulta = $conexion->prepare($sql);
            $consulta->bindValue(':idForm',$idForm,PDO::PARAM_STR);
            $consulta->execute();
            $i = 0;
                while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
                {
                $i++;
                    foreach ($reg as $clave => $valor){
                    $datos["tramo"][$i][$clave]=$valor;
                 
                }
                }
        }
        else {
            $consulta = null;
            $conexion = null;
            return array('success' => false,'message' => 'No se encontro formulario');
        }

     } 
        catch (Exception $e) {
            return array('success' => false,'message' => 'Error en ver informacion');
        }
        $consulta = null;
        $conexion = null;
        return $datos;
                
    }  
        /*--------------------------------------*/
   
}
