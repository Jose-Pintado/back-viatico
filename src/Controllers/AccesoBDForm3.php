<?php  namespace App\Controllers;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use App\Verificaciones\Verificacion;
use \DateTime;

class AccesoBDForm3 {
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
    public function definirDestino($idForm2,$fecha,$c,$sw){
        $conexion = $this->container->get('bd');

        if($sw==1)
        {
            switch($c){
                case 1:
               $sql = "
               SELECT *
                  FROM form1_tramos 
               WHERE pais_salida!='BOLIVIA' AND id_form1=:idForm2 AND tramo_fecha_salida = :fecha ;";
                  break;
               case 2:
                   $sql = "
                   SELECT *
                      FROM form1_tramos f2, frontera f
                   WHERE f2.dpto_salida=f.departamento AND f2.poblacion_salida=f.poblacion AND f2.id_form1=:idForm2 AND f2.tramo_fecha_salida = :fecha ;";
           
               break;
               case 3:
                   $sql = "
                   SELECT *
                   FROM form1_tramos 
                   WHERE dpto_salida = 'LA PAZ'  AND  id_form1=:idForm2 AND tramo_fecha_salida = :fecha ;";
               break;
               }
        }else {
            switch($c){
                case 1:
               $sql = "
               SELECT *
                  FROM form2_tramos 
               WHERE pais_salida!='BOLIVIA' AND id_form2=:idForm2 AND tramo_fecha_salida = :fecha ;";
                  break;
               case 2:
                   $sql = "
                   SELECT *
                      FROM form2_tramos f2, frontera f
                   WHERE f2.dpto_salida=f.departamento AND f2.poblacion_salida=f.poblacion AND f2.id_form2=:idForm2 AND f2.tramo_fecha_salida = :fecha ;";
           
               break;
               case 3:
                   $sql = "
                   SELECT *
                   FROM form2_tramos 
                   WHERE dpto_salida = 'LA PAZ'  AND  id_form2=:idForm2 AND tramo_fecha_salida = :fecha ;";
               break;
               }
        }
       
       
    try {
       $consulta = $conexion->prepare($sql);
       $consulta->bindValue(':fecha',$fecha,PDO::PARAM_STR);
       $consulta->bindValue(':idForm2',$idForm2,PDO::PARAM_STR);
       $consulta->execute();
       if($consulta->rowCount()>0){
        $consulta = null;
        $conexion = null;
        return true;
       }
        else {
            $consulta = null;
            $conexion = null;
            return false;
        }
       
     
       } catch (Exception $e) {
       return 0;
     }

    }
    public function montoDiario1 ($intra,$inter,$fron,$categoria){
        $conexion = $this->container->get('bd');
       
            $sql = "
            SELECT *
               FROM viaticos_interior 
            WHERE categoria = :categoria ;";
        try {
           $consulta = $conexion->prepare($sql);
           $consulta->bindValue(':categoria',$categoria,PDO::PARAM_STR);
           $consulta->execute();
           $reg = $consulta->fetch(PDO::FETCH_ASSOC);
           if($intra == true) $monto = $reg['intradepartamental'];
           if($inter == true) $monto = $reg['interdepartamental'];
           if($fron == true)  $monto = $reg['franja_frontera'];
           
           $consulta = null;
           $conexion = null;
           return $monto;
           } catch (Exception $e) {
           return 0;
       }
     
     }
     public function montoDiario2 ($pais,$categoria){
        $conexion = $this->container->get('bd');
        $monto=0;
        try {
            $sql="
            SELECT * FROM paises p,viaticos_exterior v 
            WHERE p.continente =v.pais and p.pais=:pais and v.categoria=:categoria ";
            $consulta = $conexion->prepare($sql);
            $consulta->bindValue(':pais',$pais,PDO::PARAM_STR);
             $consulta->bindValue(':categoria',$categoria,PDO::PARAM_STR);
 
            $consulta->execute();
            if($consulta->rowCount()>0){
                $reg = $consulta->fetch(PDO::FETCH_ASSOC);
                $monto = $reg['monto_bs'];
              }
           
           $consulta = null;
           $conexion = null;
           return $monto;
           } catch (Exception $e) {
           return 0;
       }
     
     }
               
     public function otrosGastos($pasajes,$viaticos,$hospedaje,$alimentacion){
        $porcentaje=1;
        if($hospedaje == true && $pasajes == false && $viaticos==false && $alimentacion=false)
            $porcentaje = 0.7;
        if(($hospedaje == true && $alimentacion=false) || $pasajes == true || $viaticos == true )
            $porcentaje = 0.5;
        if(($hospedaje == true && $alimentacion=false) || ($pasajes == true && $alimentacion=false) )
            $porcentaje = 0.25;

        return $porcentaje;
     } 
    
   public function calcularDias($fecha_salida,$hora_salida, $fecha_retorno,$hora_retorno){
        $diaHora=[];
        $fecha_salida=new DateTime($fecha_salida);
        $fecha_retorno=new DateTime($fecha_retorno);
        $dias = $fecha_salida->diff($fecha_retorno);
        $diaHora['dias'] = $dias->d;
        $hora_salida = new DateTime($hora_salida);
        $hora_retorno = new DateTime($hora_retorno);
        $interval = $hora_salida->diff($hora_retorno);
        $diaHora['horas'] = $interval->format('%H');
        $diaHora['minutos'] = $interval->format('%i');

        $hora_salida1 = $hora_salida->format('%H');
        $hora_retorno1= $hora_retorno->format('%H');
        if ($hora_retorno1 > $hora_salida1 )
            if ($diaHora['horas'] > 4)  $diaHora['dias']  = $dias->d + 1;
        //echo $diaHora['dias'];
        return $diaHora;
    }

    
/*--------------------GENERAR FORMULARIO DE  PRE LIQUIDACION -------------------------- */
    public function guardarForm3Pre($id_form){
        $conexion = $this->container->get('bd');
       
        try {
            $sql = "
            SELECT *
            FROM form1 f1, form1_tramos f 
            WHERE f1.id_form1=f.id_form1 and f1.id_form1=:idForm ;";
            $consulta = $conexion->prepare($sql);
            $consulta->bindValue(':idForm',$id_form,PDO::PARAM_STR);
            $consulta->execute();
            $contador=$consulta->rowCount();
            


            $sw=1;
            $total_viaticos=0;
            $categoria="Tercera categoria";
            $cargo="";
            $i=0;
            while ($reg = $consulta->fetch(PDO::FETCH_ASSOC)){
               //Definir categoria 
               $i++;
               if ($sw==1) {
                   $cargo=$reg['cargo'];
                switch($reg['cargo']){
                    case 'Vicepresidente del Estado Plurinacional': $categoria="Primera categoria"; break;
                    case 'Secretario General': $categoria="Segunda categoria"; break;
                    default: $categoria="Tercera categoria"; break;
                    }
                
                if($reg['gastos_financiados'] == false){
                    $porcentaje = $this -> otrosGastos ($reg['otros_gastos_pasajes'],$reg['otros_gastos_viaticos'],$reg['otros_gastos_hospedaje'],$reg['otros_gastos_alimentacion']);
    
                }else
                {   $porcentaje = 1; }
                $sw=0;
                $fecha_salida = $reg['tramo_fecha_salida'];
                $hora_salida = $reg['tramo_hora_salida'];
               
               }
               else
               {
                $diaHora= $this->calcularDias($fecha_salida,$hora_salida,$reg['tramo_fecha_salida'],$reg['tramo_hora_salida']);
                $numeroDias = $diaHora['dias']; 
                $numeroHoras = $diaHora['horas'];
                
                $internacional=$this->definirDestino($reg['id_form1'],$reg['tramo_fecha_salida'],1,1);
                $frontera=$this->definirDestino($reg['id_form1'],$reg['tramo_fecha_salida'],2,1);
                $intraDeptal=$this->definirDestino($reg['id_form1'],$reg['tramo_fecha_salida'],3,1);
                if ($internacional==false && $frontera == false && $intraDeptal == false)   $interDeptal=true; 

                if ($internacional==false)
                    $viaticos_dia = $this->montoDiario1($intraDeptal,$interDeptal,$frontera,$categoria);   
                 else
                    $viaticos_dia = $this->montoDiario2($reg['pais_destino'],$categoria);

                 $lugar_destino = $reg['pais_salida']." - ".$reg['dpto_salida']." - ".$reg['municipio_salida']." - ".$reg['poblacion_salida'];
                 $monto_dia=$viaticos_dia * $porcentaje;
                 $sub_total=$monto_dia * $numeroDias;
                 $total_viaticos += $sub_total;
                 $uuid = Uuid::uuid6(); 
                $id_form3_pre_tramos = $uuid->toString();
                 $sql1="
                   INSERT INTO form3_pre_liq_tramos(
                    id_form3_pre_tramos, 
                    id_form1,
                    fecha_salida,
                    hora_salida,
                    fecha_retorno,
                    hora_retorno,
                    lugar_destino,
                    dias, 
                    horas,
                    porcentaje, 
                    viaticos_dia, 
                    monto_dia, 
                    sub_total)
            VALUES (
                    :id_form3_pre_tramos, 
                    :id_form1, 
                    :fecha_salida,
                    :hora_salida,
                    :fecha_retorno,
                    :hora_retorno,
                    :lugar_destino,
                    :dias, 
                    :horas,
                    :porcentaje, 
                    :viaticos_dia, 
                    :monto_dia, 
                    :sub_total);";
                    $consultat = $conexion->prepare($sql1);  
                    $consultat->bindValue(':id_form3_pre_tramos',$id_form3_pre_tramos,PDO::PARAM_STR);
                    $consultat->bindValue(':id_form1',$id_form,PDO::PARAM_STR);
                    $consultat->bindValue(':fecha_salida',$fecha_salida,PDO::PARAM_STR);
                    $consultat->bindValue(':hora_salida',$hora_salida,PDO::PARAM_STR);
                    $consultat->bindValue(':fecha_retorno',$reg['tramo_fecha_salida'],PDO::PARAM_STR);
                    $consultat->bindValue(':hora_retorno',$reg['tramo_hora_salida'],PDO::PARAM_STR);
                    $consultat->bindValue(':lugar_destino',$lugar_destino,PDO::PARAM_STR);
                    $consultat->bindValue(':dias',$numeroDias,PDO::PARAM_STR);
                    $consultat->bindValue(':horas',$numeroHoras,PDO::PARAM_STR);
                    $consultat->bindValue(':porcentaje',$porcentaje,PDO::PARAM_STR); 
                    $consultat->bindValue(':viaticos_dia',$viaticos_dia,PDO::PARAM_STR); 
                    $consultat->bindValue(':monto_dia',$monto_dia,PDO::PARAM_STR); 
                    $consultat->bindValue(':sub_total',$sub_total,PDO::PARAM_STR); 
                    $consultat->execute();
               }
                $fecha_salida = $reg['tramo_fecha_salida'];
                $hora_salida = $reg['tramo_hora_salida'];

                }


            //gastos de representacion
            if($categoria == "Primera categoria" && $reg['destino_viaje_internacional']==true)
                $gastos_repre= 0.25 * $sub_total;
            else 
                $gastos_repre=0;
            
            $total_viaticos_rep = $total_viaticos + $gastos_repre;
            //retencion RC-IVA
            $descargo = 0 ;
            if($cargo =='CONSULTOR DE LINEA') {
                $retencionIVA = $total_viaticos * 0.13;
                if( $reg['adjunto_form110']==true ) {
                    //$descargo = $reg['descargo_form110'] - $retencionIVA;
                    $descargo = $reg['descargo_form110'];
                    if ($descargo > $retencionIVA) $total_retencion = 0;
                    else $total_retencion = $retencionIVA - $descargo;
                }
                else{
                    
                    $total_retencion = $retencionIVA;
                }
            }else{
                $retencionIVA =0;
                
                $total_retencion = 0;
            }
            $total_autorizado = $total_viaticos;
            $total_deducciones = $total_retencion;
            $liquido_pagable =  $total_autorizado - $total_deducciones;

            $uuid = Uuid::uuid6(); 
            $id_form3_pre = $uuid->toString();

            $sql="
            INSERT INTO form3_pre_liq(
                id_form3_pre, 
                id_form1, 
                total_viaticos, 
                gastos_repre, 
                total_y_repre, 
                retencion_iva_13, 
                descargo, 
                total_retencion, 
                total_autorizado, 
                total_deducciones, 
                total_liquido)
            VALUES (
                :id_form3_pre, 
                :id_form1, 
                :total_viaticos, 
                :gastos_repre, 
                :total_y_repre, 
                :retencion_iva_13, 
                :descargo, 
                :total_retencion, 
                :total_autorizado, 
                :total_deducciones, 
                :total_liquido
                );";
                $consulta = $conexion->prepare($sql); 
               $consulta->bindValue(':id_form3_pre',$id_form3_pre,PDO::PARAM_STR);
                $consulta->bindValue(':id_form1',$id_form,PDO::PARAM_STR); 
                $consulta->bindValue(':total_viaticos',$total_viaticos,PDO::PARAM_STR); 
                $consulta->bindValue(':gastos_repre',$gastos_repre,PDO::PARAM_STR); 
                $consulta->bindValue(':total_y_repre',$total_viaticos_rep,PDO::PARAM_STR); 
                $consulta->bindValue(':retencion_iva_13',$retencionIVA,PDO::PARAM_STR); 
                $consulta->bindValue(':descargo',$descargo,PDO::PARAM_STR); 
                $consulta->bindValue(':total_retencion',$total_retencion,PDO::PARAM_STR); 
                $consulta->bindValue(':total_autorizado',$total_autorizado,PDO::PARAM_STR); 
                $consulta->bindValue(':total_deducciones',$total_deducciones,PDO::PARAM_STR); 
                $consulta->bindValue(':total_liquido',$liquido_pagable,PDO::PARAM_STR);
                 
                $consulta->execute();
                $reg = $consulta->fetch(PDO::FETCH_ASSOC);
                $sql = "
                UPDATE form1
                SET  pre_liquidacion = true 
                WHERE id_form1 = :idForm ;";
                $consulta = $conexion->prepare($sql);
                $consulta->bindValue(':idForm',$id_form,PDO::PARAM_STR);
                $consulta->execute();
           
            $consulta = null;
            $consultat = null;
            $conexion = null;
            //return   $diaHora;
            return array('success' => true,'message' => 'Se genero con exito form 3 de pre-liquidación');
            
          }
            catch (Exception $e) {
            return array('success' => false,'message' => 'Error ');
           }  
          
    } 

    public function mostrarForm3($idForm,$c){
        $conexion = $this->container->get('bd');
        if ($c == 1){
            $sql =  "
             SELECT *
             FROM  form3_liquidacion f3
             WHERE id_form1 = :idForm ;";

             $sql1="
             SELECT *
             FROM form3_liq_tramos
             WHERE  id_form1 = :idForm ;";
             
        }
             
        else{
            $sql =  "
            SELECT *
             FROM form3_pre_liq 
             WHERE id_form1 = :idForm ;";
             $sql1="
             SELECT *
             FROM form3_pre_liq_tramos
             WHERE id_form1 = :idForm ;";
        }
            
       try {
            $consulta = $conexion->prepare($sql);
            $consulta->bindParam(':idForm',$idForm,PDO::PARAM_STR);
            $consulta->execute();
            $datos = [];
            
            $reg = $consulta->fetch(PDO::FETCH_ASSOC);
              foreach ($reg as $clave => $valor){
                    $datos[$clave]=$valor;
                }
            $consulta = $conexion->prepare($sql1);
            $consulta->bindParam(':idForm',$idForm,PDO::PARAM_STR);
            $consulta->execute();
            $i=0;
            while($reg = $consulta->fetch(PDO::FETCH_ASSOC))
            {   $i++;
                foreach ($reg as $clave => $valor){
                    $datos[$i][$clave]=$valor;
                }
            }
            
        } 
        catch (Exception $e) {
            return array('success' => false,'message' => 'Error en ver informacion');
        }
        $consulta = null;
        $conexion = null;
        return $datos;
                
    } 
  

    /***LIQUIDACION FINAL***** */
    public function guardarForm3Liquidacion($id_form){
        $conexion = $this->container->get('bd');
       
        try {
            $sql = "
            SELECT *
            FROM form1 f1, form2 f2, form2_tramos f 
            WHERE f1.id_form1 = f2.id_form1 and f2.id_form2=f.id_form2 and f1.id_form1=:idForm ;";
            $consulta = $conexion->prepare($sql);
            $consulta->bindValue(':idForm',$id_form,PDO::PARAM_STR);
            $consulta->execute();
            $contador=$consulta->rowCount();
            


            $sw=1;
            $total_viaticos=0;
            $categoria="Tercera categoria";
            $cargo="";
            $i=0;
            while ($reg = $consulta->fetch(PDO::FETCH_ASSOC)){
               //Definir categoria 
               $i++;
               if ($sw==1) {
                   $cargo=$reg['cargo'];
                switch($reg['cargo']){
                    case 'Vicepresidente del Estado Plurinacional': $categoria="Primera categoria"; break;
                    case 'Secretario General': $categoria="Segunda categoria"; break;
                    default: $categoria="Tercera categoria"; break;
                    }
                
                if($reg['gastos_financiados'] == false){
                    $porcentaje = $this -> otrosGastos ($reg['otros_gastos_pasajes'],$reg['otros_gastos_viaticos'],$reg['otros_gastos_hospedaje'],$reg['otros_gastos_alimentacion']);
    
                }else
                {   $porcentaje = 1; }
                $sw=0;
                $fecha_salida = $reg['tramo_fecha_salida'];
                $hora_salida = $reg['tramo_hora_salida'];
               
               }
               else
               {
                $diaHora= $this->calcularDias($fecha_salida,$hora_salida,$reg['tramo_fecha_salida'],$reg['tramo_hora_salida']);
                $numeroDias = $diaHora['dias']; 
                $numeroHoras = $diaHora['horas'];
                
                $internacional=$this->definirDestino($reg['id_form1'],$reg['tramo_fecha_salida'],1,1);
                $frontera=$this->definirDestino($reg['id_form1'],$reg['tramo_fecha_salida'],2,1);
                $intraDeptal=$this->definirDestino($reg['id_form1'],$reg['tramo_fecha_salida'],3,1);
                if ($internacional==false && $frontera == false && $intraDeptal == false)   $interDeptal=true; 

                if ($internacional==false)
                    $viaticos_dia = $this->montoDiario1($intraDeptal,$interDeptal,$frontera,$categoria);   
                 else
                    $viaticos_dia = $this->montoDiario2($reg['pais_destino'],$categoria);

                 $lugar_destino = $reg['pais_salida']." - ".$reg['dpto_salida']." - ".$reg['municipio_salida']." - ".$reg['poblacion_salida'];
                 $monto_dia=$viaticos_dia * $porcentaje;
                 $sub_total=$monto_dia * $numeroDias;
                 $total_viaticos += $sub_total;
                 $uuid = Uuid::uuid6(); 
                $id_form3_pre_tramos = $uuid->toString();
                 $sql1="
                   INSERT INTO form3_liq_tramos(
                    id_form3_liq_tramos, 
                    id_form1,
                    liq_fecha_salida,
                    liq_hora_salida,
                    liq_fecha_retorno,
                    liq_hora_retorno,
                    liq_lugar_destino,
                    liq_dias, 
                    liq_horas,
                    liq_porcentaje, 
                    liq_viaticos_dia, 
                    liq_monto_dia, 
                    liq_sub_total)
            VALUES (
                    :id_form3_pre_tramos, 
                    :id_form1, 
                    :fecha_salida,
                    :hora_salida,
                    :fecha_retorno,
                    :hora_retorno,
                    :lugar_destino,
                    :dias, 
                    :horas,
                    :porcentaje, 
                    :viaticos_dia, 
                    :monto_dia, 
                    :sub_total);";
                    $consultat = $conexion->prepare($sql1);  
                    $consultat->bindValue(':id_form3_pre_tramos',$id_form3_pre_tramos,PDO::PARAM_STR);
                    $consultat->bindValue(':id_form1',$id_form,PDO::PARAM_STR);
                    $consultat->bindValue(':fecha_salida',$fecha_salida,PDO::PARAM_STR);
                    $consultat->bindValue(':hora_salida',$hora_salida,PDO::PARAM_STR);
                    $consultat->bindValue(':fecha_retorno',$reg['tramo_fecha_salida'],PDO::PARAM_STR);
                    $consultat->bindValue(':hora_retorno',$reg['tramo_hora_salida'],PDO::PARAM_STR);
                    $consultat->bindValue(':lugar_destino',$lugar_destino,PDO::PARAM_STR);
                    $consultat->bindValue(':dias',$numeroDias,PDO::PARAM_STR);
                    $consultat->bindValue(':horas',$numeroHoras,PDO::PARAM_STR);
                    $consultat->bindValue(':porcentaje',$porcentaje,PDO::PARAM_STR); 
                    $consultat->bindValue(':viaticos_dia',$viaticos_dia,PDO::PARAM_STR); 
                    $consultat->bindValue(':monto_dia',$monto_dia,PDO::PARAM_STR); 
                    $consultat->bindValue(':sub_total',$sub_total,PDO::PARAM_STR); 
                    $consultat->execute();
               }
                $fecha_salida = $reg['tramo_fecha_salida'];
                $hora_salida = $reg['tramo_hora_salida'];

                }


            //gastos de representacion
            if($categoria == "Primera categoria" && $reg['destino_viaje_internacional']==true)
                $gastos_repre= 0.25 * $sub_total;
            else 
                $gastos_repre=0;
            
            $total_viaticos_rep = $total_viaticos + $gastos_repre;
            //retencion RC-IVA
            $descargo = 0 ;
            if($cargo =='CONSULTOR DE LINEA') {
                $retencionIVA = $total_viaticos * 0.13;
                if( $reg['adjunto_form110']==true ) {
                    //$descargo = $reg['descargo_form110'] - $retencionIVA;
                    $descargo = $reg['descargo_form110'];
                    if ($descargo > $retencionIVA) $total_retencion = 0;
                    else $total_retencion = $retencionIVA - $descargo;
                }
                else{
                    
                    $total_retencion = $retencionIVA;
                }
            }else{
                $retencionIVA =0;
                
                $total_retencion = 0;
            }
            $total_autorizado = $total_viaticos;
            $total_deducciones = $total_retencion;
            $liquido_pagable =  $total_autorizado - $total_deducciones;

            $uuid = Uuid::uuid6(); 
            $id_form3_pre = $uuid->toString();

            $sql="
            INSERT INTO form3_liquidacion(
                id_form3_liq, 
                id_form1, 
                liq_total_viaticos, 
                liq_gastos_repre, 
                liq_total_y_repre, 
                liq_retencion_iva_13, 
                liq_descargo, 
                liq_total_retencion, 
                liq_total_autorizado, 
                liq_total_deducciones, 
                liq_total_liquido)
            VALUES (
                :id_form3_pre, 
                :id_form1, 
                :total_viaticos, 
                :gastos_repre, 
                :total_y_repre, 
                :retencion_iva_13, 
                :descargo, 
                :total_retencion, 
                :total_autorizado, 
                :total_deducciones, 
                :total_liquido
                );";
                $consulta = $conexion->prepare($sql); 
               $consulta->bindValue(':id_form3_pre',$id_form3_pre,PDO::PARAM_STR);
                $consulta->bindValue(':id_form1',$id_form,PDO::PARAM_STR); 
                $consulta->bindValue(':total_viaticos',$total_viaticos,PDO::PARAM_STR); 
                $consulta->bindValue(':gastos_repre',$gastos_repre,PDO::PARAM_STR); 
                $consulta->bindValue(':total_y_repre',$total_viaticos_rep,PDO::PARAM_STR); 
                $consulta->bindValue(':retencion_iva_13',$retencionIVA,PDO::PARAM_STR); 
                $consulta->bindValue(':descargo',$descargo,PDO::PARAM_STR); 
                $consulta->bindValue(':total_retencion',$total_retencion,PDO::PARAM_STR); 
                $consulta->bindValue(':total_autorizado',$total_autorizado,PDO::PARAM_STR); 
                $consulta->bindValue(':total_deducciones',$total_deducciones,PDO::PARAM_STR); 
                $consulta->bindValue(':total_liquido',$liquido_pagable,PDO::PARAM_STR);
                 
                $consulta->execute();
                $reg = $consulta->fetch(PDO::FETCH_ASSOC);
                $sql = "
                UPDATE form1
                SET  liquidacion_final = true 
                WHERE id_form1 = :idForm ;";
                $consulta = $conexion->prepare($sql);
                $consulta->bindValue(':idForm',$id_form,PDO::PARAM_STR);
                $consulta->execute();
           
            $consulta = null;
            $consultat = null;
            $conexion = null;
            //return   $diaHora;
            return array('success' => true,'message' => 'Se genero con exito form 3 de pre-liquidación');
            
          }
            catch (Exception $e) {
            return array('success' => false,'message' => 'Error ');
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
