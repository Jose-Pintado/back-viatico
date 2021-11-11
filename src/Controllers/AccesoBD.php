<?php  namespace App\Controllers;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use App\Verificaciones\Verificacion;

class AccesoBD {
    protected $container;
    public function __construct (ContainerInterface $c)  //esto es un constructor de clase
    {  
        $this -> container = $c;
    }
    public function existeForm($tabla, $id){
        $conexion = $this->container->get('bd');
        $respuesta = 0;
        $sql="SELECT * FROM ".$tabla." WHERE id_".$tabla."='".$id."'";
        
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
    public function destinoViajeFrontera($tramos)
    {   $s = 0; $t=0; $respuesta=0;
        // 1= frontera, 2= la paz, 3= inter departamental
        foreach($tramos as $tramo)
            {   $tramo_dpto=strtoupper($tramo->dpto_destino);
                
                if ($tramo_dpto != 'LA PAZ' )
                {  try {
                    $conexion = $this->container->get('bd');
                    $poblacion = $tramo->poblacion_destino;
                    $sql="SELECT * FROM frontera WHERE poblacion=:poblacion and departamento=:tramo_dpto";
                    $query = $conexion->prepare($sql);
                    $query->bindParam(':poblacion', $poblacion,PDO::PARAM_STR); 
                    $query->bindParam(':tramo_dpto', $tramo_dpto,PDO::PARAM_STR); 
                    $query->execute();
                    
                    if($query->rowCount()>0) {$respuesta = 1;} 
                    $query = null;
                    $conexion = null;
                         } catch (Exception $e) {
                    $respuesta=0;
                    }
                    
                }
                else {
                    $s+=1;
                }
                   
                $t+=1;
            }
        if ($s==$t)
            $respuesta=2;
            elseif($respuesta==0)
             $respuesta=3;
            
        return $respuesta;
    }
    public function guardarForm1($datos){
        $conexion = $this->container->get('bd');
        $f = new Verificacion();
        $fechainicio = $datos->fecha_salida;
        $fechafinal = $datos->fecha_retorno;
        $respuesta = $f->verificaFechaFinSemanaFestivo($fechainicio, $fechafinal);
        if ($respuesta == 1 ) 
        $resolucion = true;
        else $resolucion = false;

        $uuid = Uuid::uuid6();
        $id = $uuid->toString();
        $cite = uniqid();
        $tramos = $datos->tramos;
        $destino_viaje_frontera = $datos->destino_viaje_frontera;
        $destino_viaje_inter_dptal = $datos->destino_viaje_inter_dptal;
        if ($datos->destino_viaje_frontera == true){
            $r=$this->destinoViajeFrontera($tramos);
             // 1= frontera, 2= la paz, 3= inter departamental
            if ( $r == 1)
                 $destino_viaje_frontera = true;
                 
            if ( $r == 2 || $r == 3)
                 {$destino_viaje_frontera = false;
                  $destino_viaje_inter_dptal =true;
                 }
            
            }  
        
        $estado = "BOR";
        $codigo_poa = $cite;
        $codigo_presupuesto = $cite;
        $fecha_elaboracion = date('Y-m-d');
        $lugar = "LA PAZ";
        $nro_resolucion="";
        
        $sql1="
        INSERT INTO form1 ( 
            id_form1, 
            cite, 
            codigo_poa, 
            codigo_presupuesto, 
            estado, 
            resolucion,
            nro_resolucion, 
            id_persona, 
            nombre, 
            apellidos, 
            ci, 
            telefono, 
            unidad_org, 
            cargo, 
            lugar, 
            fecha_elaboracion, 
            adjunto_form110, 
            descargo_form110,
            fuente_financiamiento, 
            destino_viaje_intra_dptal, 
            destino_viaje_inter_dptal, 
            destino_viaje_frontera, 
            destino_viaje_internacional, 
            gastos_financiados, 
            otros_gastos_pasajes, 
            otros_gastos_viaticos, 
            otros_gastos_hospedaje, 
            otros_gastos_alimentacion, 
            otros_gastos_observaciones, 
            objeto_viaje, 
            antecedente, 
            cronograma, 
            resultados, 
            fecha_salida, 
            hora_salida, 
            fecha_retorno, 
            hora_retorno
            ) 
           
            VALUES ( 
            :id_form1, 
            :cite, 
            :codigo_poa, 
            :codigo_presupuesto, 
            :estado, 
            :resolucion,
            :nro_resolucion, 
            :id_persona, 
            :nombre, 
            :apellidos, 
            :ci, 
            :telefono, 
            :unidad_org, 
            :cargo, 
            :lugar, 
            :fecha_elaboracion, 
            :adjunto_form110, 
            :descargo_form110,
            :fuente_financiamiento, 
            :destino_viaje_intra_dptal, 
            :destino_viaje_inter_dptal, 
            :destino_viaje_frontera, 
            :destino_viaje_internacional, 
            :gastos_financiados, 
            :otros_gastos_pasajes, 
            :otros_gastos_viaticos, 
            :otros_gastos_hospedaje, 
            :otros_gastos_alimentacion, 
            :otros_gastos_observaciones, 
            :objeto_viaje,
            :antecedente, 
            :cronograma, 
            :resultados, 
            :fecha_salida, 
            :hora_salida, 
            :fecha_retorno, 
            :hora_retorno
              );
                ";
                $query = $conexion->prepare($sql1);
                $query->bindParam(':id_form1', $id,PDO::PARAM_STR); 
                $query->bindParam(':cite', $cite,PDO::PARAM_STR);
                $query->bindParam(':codigo_poa', $codigo_poa,PDO::PARAM_STR); 
                $query->bindParam(':codigo_presupuesto', $codigo_presupuesto,PDO::PARAM_STR);
                $query->bindParam(':estado', $estado,PDO::PARAM_STR);
                $query->bindParam(':resolucion', $resolucion,PDO::PARAM_BOOL); 
                $query->bindParam(':nro_resolucion', $nro_resolucion,PDO::PARAM_STR); 
                $query->bindParam(':id_persona', $datos->id_persona,PDO::PARAM_STR); 
                $query->bindParam(':nombre', $datos->nombre,PDO::PARAM_STR); 
                $query->bindParam(':apellidos', $datos-> apellidos,PDO::PARAM_STR) ;
                $query->bindParam(':ci', $datos->ci,PDO::PARAM_STR); 
                $query->bindParam(':telefono', $datos->telefono,PDO::PARAM_STR); 
                $query->bindParam(':unidad_org', $datos->unidad_org,PDO::PARAM_STR); 
                $query->bindParam(':cargo', $datos->cargo,PDO::PARAM_STR); 
                $query->bindParam(':lugar', $lugar,PDO::PARAM_STR);
                $query->bindParam(':fecha_elaboracion', $fecha_elaboracion,PDO::PARAM_STR); 
                $query->bindParam(':adjunto_form110', $datos->adjunto_form110,PDO::PARAM_BOOL); 
                $query->bindParam(':descargo_form110', $datos->descargo_form110,PDO::PARAM_INT); 
                $query->bindParam(':fuente_financiamiento', $datos->fuente_financiamiento,PDO::PARAM_INT); 
                $query->bindParam(':destino_viaje_intra_dptal', $datos->destino_viaje_intra_dptal,PDO::PARAM_BOOL);
                $query->bindParam(':destino_viaje_inter_dptal', $destino_viaje_inter_dptal,PDO::PARAM_BOOL);
                $query->bindParam(':destino_viaje_frontera', $destino_viaje_frontera,PDO::PARAM_BOOL);
                $query->bindParam(':destino_viaje_internacional', $datos->destino_viaje_internacional,PDO::PARAM_BOOL);
                $query->bindParam(':gastos_financiados', $datos->gastos_financiados,PDO::PARAM_BOOL);
                $query->bindParam(':otros_gastos_pasajes', $datos->otros_gastos_pasajes,PDO::PARAM_BOOL);
                $query->bindParam(':otros_gastos_viaticos', $datos->otros_gastos_viaticos,PDO::PARAM_BOOL);
                $query->bindParam(':otros_gastos_hospedaje', $datos->otros_gastos_hospedaje ,PDO::PARAM_BOOL);
                $query->bindParam(':otros_gastos_alimentacion', $datos->otros_gastos_alimentacion,PDO::PARAM_BOOL);
                $query->bindParam(':otros_gastos_observaciones', $datos->otros_gastos_observaciones,PDO::PARAM_STR); 
                $query->bindParam(':objeto_viaje', $datos->objeto_viaje,PDO::PARAM_STR);
                $query->bindParam(':antecedente', $datos->antecedente,PDO::PARAM_STR);
                $query->bindParam(':cronograma', $datos->cronograma,PDO::PARAM_STR);
                $query->bindParam(':resultados', $datos->resultados,PDO::PARAM_STR);
                $query->bindParam(':fecha_salida', $datos->fecha_salida,PDO::PARAM_STR);
                $query->bindParam(':hora_salida', $datos->hora_salida,PDO::PARAM_STR);
                $query->bindParam(':fecha_retorno', $datos->fecha_retorno,PDO::PARAM_STR);
                $query->bindParam(':hora_retorno', $datos->hora_retorno,PDO::PARAM_STR)
             
                ;
                

         try {
            $query->execute();

            $datos = $query->fetch(PDO::FETCH_NUM);
          
            foreach($tramos as $tramo)
            {
                 $sqlt="
                 INSERT INTO form1_tramos ( 
                    id_tramos_form1,
                    id_form1,
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
                    :id_tramos_form1,
                    :id_form1,
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
                
                    $queryt->bindParam(':id_tramos_form1',$idt, PDO::PARAM_STR);
                    $queryt->bindParam(':id_form1',$id,PDO::PARAM_STR);
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
            return array('success' => true,'message' => 'Formulario registrado', 'id' => $id);
            } catch (Exception $e) {
            return array('success' => false,'message' => 'Error insertar Formulario 1');
           }  
          
    }
     
/*--------------------EDITAR FORMULARIO---------------------------- */
    public function editForm1($datos){
        $conexion = $this->container->get('bd');

        $f = new Verificacion();
        if ($this->existeForm("form1", $datos->id_form1)==1)
        {
            try {
                $fechainicio = $datos->fecha_salida;
                $fechafinal = $datos->fecha_retorno;
                $respuesta = $f->verificaFechaFinSemanaFestivo($fechainicio, $fechafinal);
                if ($respuesta == 1 ) 
                $resolucion = true;
                else $resolucion = false;
                
                $tramos = $datos->tramos;
                $destino_viaje_frontera = $datos->destino_viaje_frontera;
                $destino_viaje_inter_dptal = $datos->destino_viaje_inter_dptal;
                if ($datos->destino_viaje_frontera == true){
                    $r=$this->destinoViajeFrontera($tramos);
                     // 1= frontera, 2= la paz, 3= inter departamental
                    if ( $r == 1)
                         $destino_viaje_frontera = true;
                         
                    if ( $r == 2 || $r == 3)
                         {$destino_viaje_frontera = false;
                          $destino_viaje_inter_dptal =true;
                         }
                    }  
                  
                $sqluf="
                UPDATE form1 SET  
                    adjunto_form110 =:adjunto_form110, 
                    descargo_form110=:descargo_form110,
                    destino_viaje_intra_dptal =:destino_viaje_intra_dptal, 
                    destino_viaje_inter_dptal =:destino_viaje_inter_dptal, 
                    destino_viaje_frontera =:destino_viaje_frontera, 
                    destino_viaje_internacional =:destino_viaje_internacional, 
                    gastos_financiados=:gastos_financiados, 
                    otros_gastos_pasajes =:otros_gastos_pasajes, 
                    otros_gastos_viaticos =:otros_gastos_viaticos, 
                    otros_gastos_hospedaje =:otros_gastos_hospedaje, 
                    otros_gastos_alimentacion =:otros_gastos_alimentacion, 
                    otros_gastos_observaciones =:otros_gastos_observaciones, 
                    objeto_viaje =:objeto_viaje, 
                    antecedente =:antecedente, 
                    cronograma=:cronograma, 
                    resultados =:resultados, 
                    fecha_salida =:fecha_salida, 
                    hora_salida =:hora_salida, 
                    fecha_retorno =:fecha_retorno, 
                    hora_retorno =:hora_retorno

                    WHERE id_form1=:id_form1;
                    ";
                        $query = $conexion->prepare($sqluf);
                        $query->bindParam(':id_form1', $datos->id_form1,PDO::PARAM_STR); 
                        $query->bindParam(':adjunto_form110', $datos->adjunto_form110,PDO::PARAM_INT); 
                        $query->bindParam(':descargo_form110', $datos->descargo_form110,PDO::PARAM_INT);
                        $query->bindParam(':destino_viaje_intra_dptal', $datos->destino_viaje_intra_dptal,PDO::PARAM_BOOL);
                        $query->bindParam(':destino_viaje_inter_dptal', $destino_viaje_inter_dptal,PDO::PARAM_BOOL);
                        $query->bindParam(':destino_viaje_frontera', $destino_viaje_frontera,PDO::PARAM_BOOL);
                        $query->bindParam(':destino_viaje_internacional', $datos->destino_viaje_internacional,PDO::PARAM_BOOL);
                        $query->bindParam(':gastos_financiados', $datos->gastos_financiados,PDO::PARAM_BOOL);
                        $query->bindParam(':otros_gastos_pasajes', $datos->otros_gastos_pasajes,PDO::PARAM_BOOL);
                        $query->bindParam(':otros_gastos_viaticos', $datos->otros_gastos_viaticos,PDO::PARAM_BOOL);
                        $query->bindParam(':otros_gastos_hospedaje', $datos->otros_gastos_hospedaje ,PDO::PARAM_BOOL);
                        $query->bindParam(':otros_gastos_alimentacion', $datos->otros_gastos_alimentacion,PDO::PARAM_BOOL);
                        $query->bindParam(':otros_gastos_observaciones', $datos->otros_gastos_observaciones,PDO::PARAM_STR); 
                        $query->bindParam(':objeto_viaje', $datos->objeto_viaje,PDO::PARAM_STR);
                        $query->bindParam(':antecedente', $datos->antecedente,PDO::PARAM_STR);
                        $query->bindParam(':cronograma', $datos->cronograma,PDO::PARAM_STR);
                        $query->bindParam(':resultados', $datos->resultados,PDO::PARAM_STR);
                        $query->bindParam(':fecha_salida', $datos->fecha_salida,PDO::PARAM_STR);
                        $query->bindParam(':hora_salida', $datos->hora_salida,PDO::PARAM_STR);
                        $query->bindParam(':fecha_retorno', $datos->fecha_retorno,PDO::PARAM_STR);
                        $query->bindParam(':hora_retorno', $datos->hora_retorno,PDO::PARAM_STR)
                     
                        ;
                        
        
                 
                    $query->execute();
                    $idf=$datos->id_form1;
                    $datos = $query->fetch(PDO::FETCH_NUM);

                    $sqld="
                    DELETE FROM form1_tramos WHERE id_form1 ='".$idf."';";
                    $queryd = $conexion->prepare($sqld);
                    //$queryd->bindValue(':id_form1', $datos->id_form1,PDO::PARAM_STR); 
                    $queryd->execute();
                    
                    foreach($tramos as $tramo)
                    {
                         $sqlt="
                         INSERT INTO form1_tramos ( 
                            id_tramos_form1,
                            id_form1,
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
                            :id_tramos_form1,
                            :id_form1,
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
                        
                            $queryt->bindParam(':id_tramos_form1',$idt, PDO::PARAM_STR);
                            $queryt->bindParam(':id_form1',$idf,PDO::PARAM_STR);
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
                    return array('success' => false,'message' => 'Error insertar Formulario 1');
                   }  
        }
        else
        {
            return array('success' => false,'message' => 'Formulario 1 inexistente');
        }
        
          
    } 
    
    public function eliminarForm_1($idForm){
        
        $conexion = $this->container->get('bd');
       if ($this->existeForm("form1", $idForm)==1)
        {
            try{
                $sqld="
                    DELETE FROM form1_tramos WHERE id_form1 ='".$idForm."';";
                $queryd = $conexion->prepare($sqld);
                $queryd->execute();

                $sql1="DELETE FROM form1 WHERE id_form1='".$idForm."'
                      ";
                $query = $conexion->prepare($sql1);
                $query->execute();
              
                $query = null;
                $queryd = null;
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
    public function verFormularioImprimir($idForm){
        $conexion = $this->container->get('bd');
        if ($this->existeForm("form1", $idForm)==1)
        {try {
            $datos = [];
            $sql = "
            SELECT *
            FROM form1 
            WHERE id_form1 = :idForm ;";
            $consulta = $conexion->prepare($sql);
            $consulta->bindParam(':idForm',$idForm,PDO::PARAM_STR);
            $consulta->execute();
             
            if($consulta->rowCount()>0){
               $reg = $consulta->fetch(PDO::FETCH_ASSOC);
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
            
            if($consulta->rowCount()>0){
                $i=0;
              while($reg = $consulta->fetch(PDO::FETCH_ASSOC)) {
                  $i++;
                foreach ($reg as $clave => $valor){
                    $datos["tramos"][$i] [$clave]=$valor;
                }
              }
               
            }
          } 
        catch (Exception $e) {
            return array('success' => false,'message' => 'Error en ver informacion' ,'data' => null);
        } 
        $consulta = null;
        $conexion = null;
        return array('success' => true, 'message' => 'Se registro correctamente', 'data' => $datos);
        }else
        {
            return array('success' => false,'message' => 'Formulario no registrado' ,'data' => null);
        }
            
    }   
    public function verFormulario1($idForm,$s){
        $conexion = $this->container->get('bd');
        if ($s==0){
             $sql = "
            SELECT *
            FROM form1 
            WHERE id_form1 = :idForm ;";
        }else {
            $sql = "
            SELECT *
            FROM form1_tramos 
            WHERE id_form1 = :idForm ;";
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
     } 
        catch (Exception $e) {
        }
        return array('success' => false,'message' => 'Error en ver informacion' ,'data'=> null);
        $consulta = null;
        $conexion = null;
        return array('success' => true, 'message' => 'Se registro correctamente', 'data' => $datos);
                
    }  
    public function cambioEstado($idForm,$estado){
        switch (strtolower($estado)) {
            case 'REVISADO': $est='REV';break;
            case 'GENERADO': $est='GEN';break;
            case 'ACEPTADO': $est='ACE';break;
            case 'OBSERVADO': $est='OBS';break;
            case 'RECHAZADO': $est='RECH';break;
            case 'ARCHIVAR': $est='ARCH';break;
            default:$est='BOR';break;
                
        }
        $conexion = $this->container->get('bd');
        $f = new Verificacion();
        if ($this->existeForm("form1", $idForm)==1)
        { try{
                $sql="
                    UPDATE form1 SET  estado=:estado WHERE id_form1 =:idForm;";
                $query = $conexion->prepare($sql);
                $query->bindValue(':idForm',$idForm,PDO::PARAM_STR);
                $query->bindValue(':estado',$est,PDO::PARAM_STR);
                $query->execute();
                $query = null;
                $queryd = null;
                $conexion = null;
                return array('success' => true, 'message' => 'Estado modificado');
            }
            catch (Exception $e) {
                return array('success' => false,'message' => 'Error cambiar estado');
            }
        }else
        {
            return array('error' => false, 'message' => 'Formulario No existe');
        }
    }
    public function observarFormulario($datos){
        
        if ($this->existeForm("form1", $datos->id_form1)==1)
        {   try {
                $conexion = $this->container->get('bd');
                $sql="UPDATE form1 SET observacion=:obs,estado=:estado WHERE id_form1=:id_form1";
                $query = $conexion->prepare($sql);
                $query->bindParam(':id_form1', $datos->id_form1,PDO::PARAM_STR);
                $query->bindParam(':obs', $datos->observacion,PDO::PARAM_STR); 
                $query->bindParam(':estado', $datos->estado,PDO::PARAM_STR); 
                $query->execute();
                $datos = $query->fetch(PDO::FETCH_NUM);
                $query = null;
                $consulta = null;
                $conexion = null;
            return array('success' => true,'message' => 'Formulario Modificado');
            } catch (Exception $e) {
                $query = null;
                $consulta = null;
                $conexion = null;
                return array('success' => false,'message' => 'Error insertar Formulario 1');
            }
        }else {
            return array('success' => false,'message' => 'Formulario no existente');
        }
    }
    public function listarForm1Solicitante($indice,$limit,$idSolicitante){
        $conexion = $this->container->get('bd');
        $sql = "
        SELECT *
        FROM form1 
        WHERE id_persona = :idSolicitante
        ORDER BY fecha_elaboracion DESC LIMIT :lim OFFSET :ind ;";
       
        $consulta = $conexion->prepare($sql);
        $consulta->bindValue(':idSolicitante',$idSolicitante,PDO::PARAM_STR);
        $consulta->bindParam(':ind',$ind,PDO::PARAM_INT);
        $consulta->bindParam(':lim',$lim,PDO::PARAM_INT);
        
        $consulta->execute();
        $datos = [];
       $resgistro= [];
        if($consulta->rowCount()>0){
            $i = 0;
            while ($reg = $consulta->fetch(PDO::FETCH_ASSOC))
            {
                $i++;
                  foreach ($reg as $clave => $valor){
                   $resgistro[$clave] =$valor;
                }
                $datos[] = $resgistro;
            }
            $consulta = null;
            $conexion = null;
            //return $datos;
            return array('success' => true, 'message' => 'Se registro correctamente', 'data' => $datos);
        }
        else {
            return array('error' => false, 'message' => 'No hay formularios registrados', 'data' =>null );
        }
    }


    public function listarFormularios($body){
        $conexion = $this->container->get('bd');
        $id = $body['id'];
        //echo $inicio, $cantidad,$idForm,$filtro;


        if (!isset($body['inicio'], $body['cantidad'])) {
            return array('success' => false, 'message' => 'Datos no valido');
        }
        try {
            $inicio = (int) $body['inicio'];
            $cantidad = (int) $body['cantidad'];
            } catch (Exception $exc) {
            return array('success' => false, 'message' => 'Datos no valido');
            }
        $filtro = '';
         if (isset($body['filtro'])) {
            if (trim($body['filtro']) != '' && $body['filtro'] != null && $body['filtro'] != "undefined" ) {
                 $filtro = strtoupper('%' . $body['filtro'] . '%');
            }
        }
        $estado = '';
        if (isset($body['estado'])) {
            if (trim($body['estado']) != '' && $body['estado'] != null) {
                $estado = $body['estado'];
            }
        }
       
        $id = '';
        if (isset($body['id'])) {
            if (trim($body['id']) != '' && $body['id'] != null) {
                $id = $body['id'];
            }
        }
    try {
        $sql = "
        SELECT 
        id_form1,
        cite,
        codigo_presupuesto,
        codigo_poa,
        estado,
        resolucion,
        nro_resolucion,
        id_persona,
        fecha_elaboracion,
        objeto_viaje,
        antecedente,
        cronograma,
        resultados,
        fecha_salida,
        fecha_retorno

         FROM form1 WHERE id_persona=:id";
        //LISTA CON FILTRO CON LIMIT
        $sqlLO = "SELECT count(id_form1) as cantidad
        FROM form1
        WHERE id_persona=:id ";

        if ($filtro != ''){
            $sql = $sql . " AND (
                upper(objeto_viaje) like :filtro OR 
                upper(antecedente) like :filtro OR 
                upper(cronograma) like :filtro OR 
                upper(resultados) like :filtro OR 
                to_char( fecha_elaboracion, 'DD/MM/YYYY') like :filtro OR
                to_char( fecha_salida, 'DD/MM/YYYY') like :filtro
                ) ";
            $sqlLO = $sqlLO . " AND (
                upper(objeto_viaje) like :filtro OR 
                upper(antecedente) like :filtro OR 
                upper(cronograma) like :filtro OR 
                upper(resultados) like :filtro OR 
                to_char( fecha_elaboracion, 'DD/MM/YYYY') like :filtro OR
                to_char( fecha_salida, 'DD/MM/YYYY') like :filtro
                ) ";
        }
        if ($estado != '') {
            $sql = $sql . " AND estado=:estado";
            $sqlLO = $sqlLO . " AND estado=:estado";
        }
        $sql = $sql . ' ORDER BY fecha_elaboracion DESC ';
        $sql = $sql . ' LIMIT :cantidad OFFSET :inicio ';

        $res = $conexion->prepare($sql);
        $res->bindParam(':id', $id, PDO::PARAM_STR);
        if ($filtro != '') {
            $res->bindParam(':filtro', $filtro, PDO::PARAM_STR);
        }
        if ($estado != '') {
            $res->bindParam(':estado', $estado, PDO::PARAM_STR);
        }
  
        $res->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $res->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    
        $res->execute();

        $resLO = $conexion->prepare($sqlLO);  
        $resLO->bindParam(':id', $id, PDO::PARAM_STR);
        if ($estado != '') {
            $resLO->bindParam(':estado', $estado, PDO::PARAM_STR);
        }  
        if ($filtro != '') {
            $resLO->bindParam(':filtro', $filtro, PDO::PARAM_STR);
        }
        $resLO->execute();
        $resLO = $resLO->fetchAll(PDO::FETCH_ASSOC);
        $resLO = $resLO[0];
        $total = $resLO['cantidad']; 
    } catch (Exception $ex) {
        return array('success' => false, 'message' => 'Error al imgresar los datos');
    }

    $datos = [];
    $resgistro= [];
     if($res->rowCount()>0){
         $i = 0;
        // $res = $res->fetchAll(PDO::FETCH_ASSOC)
         while ($reg = $res->fetch(PDO::FETCH_ASSOC))
         {
             $i++;
               foreach ($reg as $clave => $valor){
                $resgistro[$clave] =$valor;
             }
             $datos[] = $resgistro;
         }
         $res = null;
         $resLO = null;
         $conexion = null;
         //return $datos;
         $tabla = array('total' => $total, 'listado' => $datos);
         return array('success' => true, 'message' => 'Listado de registros', 'data' => $tabla);
     }
     else {
         return array('error' => false, 'message' => 'No hay formularios registrados', 'data' =>null );
     }
      
    /*--------------------------------------*/
      
  }

 public function listarParametros($body){
        $conexion = $this->container->get('bd');
        $id = $body['id'];
        $ignorar='';
        //echo $inicio, $cantidad,$idForm,$filtro;
        //if (isset($body['ignorar']) $ignorar= $body['ignorar'];

        if (!isset($body['inicio'], $body['cantidad'])) {
            return array('success' => false, 'message' => 'Datos no valido');
        }
        try {
            $inicio = (int) $body['inicio'];
            $cantidad = (int) $body['cantidad'];
            } catch (Exception $exc) {
            return array('success' => false, 'message' => 'Datos no valido');
            }
        $filtro = '';
         if (isset($body['filtro'])) {
            if (trim($body['filtro']) != '' && $body['filtro'] != null) {
                 $filtro = strtoupper('%' . $body['filtro'] . '%');
            }
        }
        $estado = '';
        if (isset($body['estado'])) {
            if (trim($body['estado']) != '' && $body['estado'] != null) {
                $estado = $body['estado'];
            }
        }
       
        $id = '';
        if (isset($body['id'])) {
            if (trim($body['id']) != '' && $body['id'] != null) {
                $id = $body['id'];
            }
        }
    try {
        $sql = "
        SELECT 
        id_form1,
        cite,
        codigo_presupuesto,
        codigo_poa,
        estado,
        resolucion,
        nro_resolucion,
        id_persona,
        fecha_elaboracion,
        objeto_viaje,
        antecedente,
        cronograma,
        resultados,
        fecha_salida,
        fecha_retorno

         FROM form1 WHERE id_persona=:id";
        //LISTA CON FILTRO CON LIMIT
        $sqlLO = "SELECT count(id_form1) as cantidad
        FROM form1
        WHERE id_persona=:id ";

        if ($filtro != ''){
            $sql = $sql . " AND (
                upper(objeto_viaje) like :filtro OR 
                upper(antecedente) like :filtro OR 
                upper(cronograma) like :filtro OR 
                upper(resultados) like :filtro OR 
                to_char( fecha_elaboracion, 'DD/MM/YYYY') like :filtro OR
                to_char( fecha_salida, 'DD/MM/YYYY') like :filtro
                ) ";
            $sqlLO = $sqlLO . " AND (
                upper(objeto_viaje) like :filtro OR 
                upper(antecedente) like :filtro OR 
                upper(cronograma) like :filtro OR 
                upper(resultados) like :filtro OR 
                to_char( fecha_elaboracion, 'DD/MM/YYYY') like :filtro OR
                to_char( fecha_salida, 'DD/MM/YYYY') like :filtro
                ) ";
        }
        if ($estado != '') {
            $sql = $sql . " AND estado=:estado";
            $sqlLO = $sqlLO . " AND estado=:estado";
        }
        $sql = $sql . ' ORDER BY fecha_elaboracion DESC ';
        $sql = $sql . ' LIMIT :cantidad OFFSET :inicio ';

        $res = $conexion->prepare($sql);
        $res->bindParam(':id', $id, PDO::PARAM_STR);
        if ($filtro != '') {
            $res->bindParam(':filtro', $filtro, PDO::PARAM_STR);
        }
        if ($estado != '') {
            $res->bindParam(':estado', $estado, PDO::PARAM_STR);
        }
  
        $res->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $res->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    
        $res->execute();

        $resLO = $conexion->prepare($sqlLO);  
        $resLO->bindParam(':id', $id, PDO::PARAM_STR);
        if ($estado != '') {
            $resLO->bindParam(':estado', $estado, PDO::PARAM_STR);
        }  
        if ($filtro != '') {
            $resLO->bindParam(':filtro', $filtro, PDO::PARAM_STR);
        }
        $resLO->execute();
        $resLO = $resLO->fetchAll(PDO::FETCH_ASSOC);
        $resLO = $resLO[0];
        $total = $resLO['cantidad']; 
    } catch (Exception $ex) {
        return array('success' => false, 'message' => 'Error al imgresar los datos');
    }

    $datos = [];
    $resgistro= [];
     if($res->rowCount()>0){
         $i = 0;
        // $res = $res->fetchAll(PDO::FETCH_ASSOC)
         while ($reg = $res->fetch(PDO::FETCH_ASSOC))
         {
             $i++;
               foreach ($reg as $clave => $valor){
                $resgistro[$clave] =$valor;
             }
             $datos[] = $resgistro;
         }
         $res = null;
         $resLO = null;
         $conexion = null;
         //return $datos;
         $tabla = array('total' => $total, 'listado' => $datos);
         return array('success' => true, 'message' => 'Listado de registros', 'data' => $tabla);
     }
     else {
         return array('error' => false, 'message' => 'No hay formularios registrados', 'data' =>null );
     }
      
    /*--------------------------------------*/
      
  }


}