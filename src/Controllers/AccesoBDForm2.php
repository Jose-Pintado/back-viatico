<?php  namespace App\Controllers;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use App\Verificaciones\Verificacion;

class AccesoBDForm2 {
    protected $container;
    public function __construct (ContainerInterface $c)  //esto es un constructor de clase
    {  
        $this -> container = $c;
    }
    public function existeForm($tabla,$idform, $id){
        $conexion = $this->container->get('bd');
        $respuesta = 0;
        $sql="SELECT * FROM ".$tabla." WHERE id_".$idform."='".$id."'";
        
        try {
            $query = $conexion->prepare($sql);
            //$query->bindParam(':tabla', $tabla,PDO::PARAM_STR); 
            //$query->bindParam(':id', $id,PDO::PARAM_STR); 
            $query->execute();
            if($query->rowCount()>0) $respuesta = 1;
        }  catch (PDOException $e){
            print "Error!!!!".$e->getMessage()."<br>";
            die();
         }
        
        return $respuesta;
    }
   
    public function guardarForm2($datos){
        $conexion = $this->container->get('bd');
        $f = new Verificacion();
        $fechainicio = $datos->form2_fecha_salida;
        $fechafinal = $datos->form2_fecha_retorno;
        if ($this->existeForm("form2","form2",  $datos->id_form1)==0)
        {
            $respuesta = $f->verificaFechaFinSemanaFestivo($fechainicio, $fechafinal);
        if ($respuesta == 1 ) 
        $resolucion = true;
        else $resolucion = false;

        $uuid = Uuid::uuid6();
        $id = $uuid->toString();
        $tramos = $datos->tramos;
        
        $fecha_elaboracion = date('Y-m-d');
        $lugar = "LA PAZ";
         $idform1=$datos->id_form1;       
        $sql1="
        INSERT INTO form2 ( 
            id_form2,
            id_form1,
            form2_lugar,
            form2_fecha_elaboracion,
            tipo_cambio,
            resolucion,
            form2_fecha_salida,
            form2_hora_salida,
            form2_fecha_retorno,
            form2_hora_retorno,
            justificacion
            ) 
           
            VALUES ( 
            :id_form2,
            :id_form1,
            :form2_lugar,
            :form2_fecha_elaboracion,
            :tipo_cambio,
            :resolucion,
            :form2_fecha_salida,
            :form2_hora_salida,
            :form2_fecha_retorno,
            :form2_hora_retorno,
            :justificacion
              );
                ";
                $query = $conexion->prepare($sql1);
                $query->bindParam(':id_form2', $id,PDO::PARAM_STR); 
                $query->bindParam(':id_form1', $datos->id_form1,PDO::PARAM_STR);
                $query->bindParam(':form2_lugar', $lugar,PDO::PARAM_STR); 
                $query->bindParam(':form2_fecha_elaboracion', $fecha_elaboracion,PDO::PARAM_STR);
                $query->bindParam(':tipo_cambio', $datos->tipo_cambio,PDO::PARAM_STR);
                $query->bindParam(':resolucion', $resolucion,PDO::PARAM_BOOL);
                $query->bindParam(':form2_fecha_salida', $datos->form2_fecha_salida,PDO::PARAM_STR); 
                $query->bindParam(':form2_hora_salida', $datos->form2_hora_salida,PDO::PARAM_STR); 
                $query->bindParam(':form2_fecha_retorno', $datos->form2_fecha_retorno,PDO::PARAM_STR); 
                $query->bindParam(':form2_hora_retorno', $datos->form2_hora_retorno,PDO::PARAM_STR); 
                $query->bindParam(':justificacion', $datos->justificacion,PDO::PARAM_STR); 
                

         try {
            $query->execute();

            $datos = $query->fetch(PDO::FETCH_NUM);
          
            foreach($tramos as $tramo)
            {
                 $sqlt="
                 INSERT INTO form2_tramos ( 
                    id_tramos_form2,
                    id_form2,
                    nro_tramo,
                    tramo_fecha_salida,
                    tramo_hora_salida,
                    pais_salida,
                    dpto_salida,
                    municipio_salida,
                    poblacion_salida,
                    pais_destino,
                    dpto_destino,
                    municipio_destino,
                    poblacion_destino,
                    tipo_transporte
                    )
                 VALUES (
                    :id_tramos_form2,
                    :id_form2,
                    :nro_tramo,
                    :tramo_fecha_salida,
                    :tramo_hora_salida,
                    :pais_salida,
                    :dpto_salida,
                    :municipio_salida,
                    :poblacion_salida,
                    :pais_destino,
                    :dpto_destino,
                    :municipio_destino,
                    :poblacion_destino,
                    :tipo_transporte
                 );
                 ";
                $queryt = $conexion->prepare($sqlt);
                $uuidt = Uuid::uuid6();
                $idt = $uuidt->toString();
                
                    $queryt->bindParam(':id_tramos_form2',$idt, PDO::PARAM_STR);
                    $queryt->bindParam(':id_form2',$id,PDO::PARAM_STR);
                    $queryt->bindParam(':nro_tramo',$tramo->nro_tramo,PDO::PARAM_INT);
                    $queryt->bindParam(':tramo_fecha_salida',$tramo->tramo_fecha_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':tramo_hora_salida',$tramo->tramo_hora_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':pais_salida',$tramo->pais_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':dpto_salida',$tramo->dpto_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':municipio_salida',$tramo->municipio_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':poblacion_salida',$tramo->poblacion_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':pais_destino',$tramo->pais_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':dpto_destino',$tramo->dpto_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':municipio_destino',$tramo->municipio_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':poblacion_destino',$tramo->poblacion_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':tipo_transporte',$tramo->tipo_transporte,PDO::PARAM_STR);

                $queryt->execute();
                $tramos = $queryt->fetch(PDO::FETCH_NUM);
            }
            $sql="UPDATE form1 SET cambio_ruta_fecha=true WHERE id_form1=:idform1";
            $query = $conexion->prepare($sql);
            $query->bindValue(':idform1',$idform1, PDO::PARAM_STR);
            $query->execute();

            $queryt = null;
            $query = null;
            $consulta = null;
            $conexion = null;
            return array('success' => true,'message' => 'Formulario registrado', 'id' => $id);
            } catch (Exception $e) {
            return array('success' => false,'message' => 'Error insertar Formulario 2');
           }  
        }else {
            return array('success' => false,'message' => 'Formulario 2 ya existe ');
        }
        
          
    }
/*--------------------EDITAR FORMULARIO---------------------------- */
    public function editForm2($datos){
        $conexion = $this->container->get('bd');

        $f = new Verificacion();
        if ($this->existeForm("form2","form2",  $datos->id_form2)==1)
        {
            try {
                $fechainicio = $datos->form2_fecha_salida;
                $fechafinal = $datos->form2_fecha_retorno;
                $respuesta = $f->verificaFechaFinSemanaFestivo($fechainicio, $fechafinal);
                if ($respuesta == 1 ) 
                $resolucion = true;
                else $resolucion = false;
                
                $tramos = $datos->tramos;
                                 
                $sqluf="
                UPDATE form2 SET  
                    tipo_cambio =:tipo_cambio, 
                    form2_fecha_salida =:form2_fecha_salida, 
                    form2_hora_salida=:form2_hora_salida, 
                    form2_fecha_retorno =:form2_fecha_retorno, 
                    form2_hora_retorno =:form2_hora_retorno,
                    justificacion =:justificacion 
                   
                    WHERE id_form2=:id_form2;
                    ";
                    $query = $conexion->prepare($sqluf);
                    $query->bindParam(':id_form2', $datos->id_form2,PDO::PARAM_STR); 
                    $query->bindParam(':tipo_cambio', $datos->tipo_cambio,PDO::PARAM_STR); 
                    $query->bindParam(':form2_fecha_salida', $datos->form2_fecha_salida,PDO::PARAM_STR);
                    $query->bindParam(':form2_hora_salida', $datos->form2_hora_salida,PDO::PARAM_STR);
                    $query->bindParam(':form2_fecha_retorno', $datos->form2_fecha_retorno,PDO::PARAM_STR);
                    $query->bindParam(':form2_hora_retorno', $datos->form2_hora_retorno,PDO::PARAM_STR);
                    $query->bindParam(':justificacion', $datos->justificacion,PDO::PARAM_STR);
                    
                    $query->execute();
                    $idf=$datos->id_form2;
                    $datos = $query->fetch(PDO::FETCH_NUM);

                    $sqld="
                    DELETE FROM form2_tramos WHERE id_form2 ='".$idf."';";
                    $queryd = $conexion->prepare($sqld);
                    //$queryd->bindValue(':id_form1', $datos->id_form1,PDO::PARAM_STR); 
                    $queryd->execute();
                    
                    foreach($tramos as $tramo)
            {
                 $sqlt="
                 INSERT INTO form2_tramos ( 
                    id_tramos_form2,
                    id_form2,
                    nro_tramo,
                    tramo_fecha_salida,
                    tramo_hora_salida,
                    pais_salida,
                    dpto_salida,
                    municipio_salida,
                    poblacion_salida,
                    pais_destino,
                    dpto_destino,
                    municipio_destino,
                    poblacion_destino,
                    tipo_transporte
                    )
                 VALUES (
                    :id_tramos_form2,
                    :id_form2,
                    :nro_tramo,
                    :tramo_fecha_salida,
                    :tramo_hora_salida,
                    :pais_salida,
                    :dpto_salida,
                    :municipio_salida,
                    :poblacion_salida,
                    :pais_destino,
                    :dpto_destino,
                    :municipio_destino,
                    :poblacion_destino,
                    :tipo_transporte
                 );
                 ";
                $queryt = $conexion->prepare($sqlt);
                $uuidt = Uuid::uuid6();
                $idt = $uuidt->toString();
                
                    $queryt->bindParam(':id_tramos_form2',$idt, PDO::PARAM_STR);
                    $queryt->bindParam(':id_form2',$idf,PDO::PARAM_STR);
                    $queryt->bindParam(':nro_tramo',$tramo->nro_tramo,PDO::PARAM_INT);
                    $queryt->bindParam(':tramo_fecha_salida',$tramo->tramo_fecha_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':tramo_hora_salida',$tramo->tramo_hora_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':pais_salida',$tramo->pais_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':dpto_salida',$tramo->dpto_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':municipio_salida',$tramo->municipio_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':poblacion_salida',$tramo->poblacion_salida,PDO::PARAM_STR);
                    $queryt->bindParam(':pais_destino',$tramo->pais_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':dpto_destino',$tramo->dpto_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':municipio_destino',$tramo->municipio_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':poblacion_destino',$tramo->poblacion_destino,PDO::PARAM_STR);
                    $queryt->bindParam(':tipo_transporte',$tramo->tipo_transporte,PDO::PARAM_STR);

                $queryt->execute();
                $tramos = $queryt->fetch(PDO::FETCH_NUM);
            }
                    $queryt = null;
                    $query = null;
                    $consulta = null;
                    $conexion = null;
                    return array('success' => true,'message' => 'Formulario Modificado');
                    } catch (Exception $e) {
                    return array('success' => false,'message' => 'Error insertar Formulario 2');
                   }  
        }
        else
        {
            return array('success' => false,'message' => 'Formulario 2 inexistente');
        }
        
          
    } 
    
    public function eliminarForm_2($idForm){
        
        $conexion = $this->container->get('bd');
        $f = new Verificacion();
        if ($this->existeForm("form2","form2",  $idForm)==1)
         
        {
            try{$idform1=0;
                $sqls="SELECT id_form1 FROM form2  WHERE id_form2=:idform";
                $querys = $conexion->prepare($sqls);
                $querys->bindParam(':idform',$idform, PDO::PARAM_STR);
                $querys->execute();
                $results = $querys -> fetchAll(PDO::FETCH_OBJ);
                if($querys -> rowCount() > 0){    
                    foreach($results as $result) {
                        $idform1= $results->id_form1;
                    }
                }
                echo "id form1: ", $idform1;
                $sqld="
                    DELETE FROM form2_tramos WHERE id_form2 ='".$idForm."';";
                $queryd = $conexion->prepare($sqld);
                $queryd->execute();

                $sql1="
                     DELETE FROM form2 WHERE id_form2='".$idForm."'
                      ";
                $querydf = $conexion->prepare($sql1);
                $querydf->execute();
                
                $sql="UPDATE form1 SET cambio_ruta_fecha=false WHERE id_form1=:idform1";
                $query = $conexion->prepare($sql);
                $query->bindParam(':idform1',$idform1, PDO::PARAM_STR);
                $query->execute();

                $query = null;
                $queryd = null;
                $querys = null;
                $querydf = null;
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
    public function verFormulario2($idForm){
        $conexion = $this->container->get('bd');
        if ($this->existeForm("form2","form1", $idForm)==1)
        {
           try {
                $sql = "
                SELECT *
                FROM form1 
                WHERE id_form1 = :idForm ;";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(':idForm',$idForm,PDO::PARAM_STR);
                $consulta->execute();
                $datos = [];
                 while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
                 {                    
                        foreach ($reg as $clave => $valor){
                            $datos[$clave]=$valor;
                        }
                    }

                $sql = "
                SELECT *
                FROM form1_tramos 
                WHERE id_form1 = :idForm ;";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(':idForm',$idForm,PDO::PARAM_STR);
                $consulta->execute();
                //$datos = [];
                $i=0;
                while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
                    {    $i+=1;               
                        foreach ($reg as $clave => $valor){
                             $datos["tramosForm1"][$i][$clave]=$valor;
                         }
                    } 
            
                $sw=0;
                $id_form2=0;
                $sql = "
                SELECT *
                FROM form2 
                WHERE id_form1 = :idForm ;";
                $consulta = $conexion->prepare($sql);
                $consulta->bindParam(':idForm',$idForm,PDO::PARAM_STR);
                $consulta->execute();
                //$datos = [];
                while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
                    { foreach ($reg as $clave => $valor){
                             $datos[$clave]=$valor;
                            //echo $clave;
                             if($sw == 0 && $clave =='id_form2') { 
                                
                                $id_form2= $valor; $sw=1;}
                                
                         }
                    }
                

                $sql = "
                SELECT *
                FROM form2_tramos 
                WHERE id_form2 = :id_form2 ;";
                $consulta = $conexion->prepare($sql);
                $consulta->bindValue(':id_form2',$id_form2,PDO::PARAM_STR);
                $consulta->execute();
                //$datos = [];
                $i=0;
                while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
                    {    $i+=1;               
                        foreach ($reg as $clave => $valor){
                                $datos["tramosForm2"][$i][$clave]=$valor;
                            }
                    } 
                } 
                    catch (Exception $e) {
                         return array('success' => false,'message' => 'Error en ver informacion');
                    }
                    $consulta = null;
                    $conexion = null;
                    return $datos;

        } else {
            return array('success' => false,'message' => 'Formulario 2 no encontrado');
        }
        
        
      
                
    }  
        /*--------------------------------------*/
   /*
   public function verFormulario2($idForm,$s){
        $conexion = $this->container->get('bd');
        if ($s==0){
             $sql = "
            SELECT *
            FROM form2 
            WHERE id_form1 = :idForm ;";
        }else {
            $sql = "
            SELECT *
            FROM form2_tramos 
            WHERE id_form2 = :idForm ;";
        }
       try {
                   $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':idForm',$idForm,PDO::PARAM_STR);
        $consulta->execute();
        $datos = [];
        if($consulta->rowCount()>0){
            $i = 0;
            while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
            {
                $i++;
                foreach ($reg as $clave => $valor){
                    $datos[$i][$clave]=$valor;
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
   */
}
