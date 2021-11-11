<?php  namespace App\Controllers;
use PDO;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use App\Verificaciones\Verificacion;

class AccesoBD_Param {
    protected $container;
    public function __construct (ContainerInterface $c)  //esto es un constructor de clase
    {  
        $this -> container = $c;
    }
   

 public function verEstados($body){
        $conexion = $this->container->get('bd');
        $id = $body['id'];
        $datos = [];
        
        $sql="
        SELECT DISTINCT(estado)  
        FROM form1
        WHERE id_persona=:id
        ORDER BY estado";

        $res = $conexion->prepare($sql);
        $res->bindParam(':id', $id, PDO::PARAM_STR);
        $res->execute();
        if($res->rowCount()>0){

          $i=0;  
         while ($reg = $res->fetch(PDO::FETCH_ASSOC))
         {
             $i++;
               foreach ($reg as $clave => $valor){
                switch($valor){
                    case 'BOR': $datos['BORRADOR'] =$valor; break;
                    case 'ACE': $datos['ACEPTAR'] =$valor; break;
                    //case 'REV': $resgistro['REVISADO'] =$valor; break; 
                    case 'OBS': $datos['OBSERVADO'] =$valor; break; 
                    case 'REC': $datos['RECHAZADO'] =$valor; break; 
                        
                }
                
             }
             
         }
         $res = null;
        
         $conexion = null;
         
         return array($datos);
         //return array('success' => true, 'message' => 'Estados', 'data' => $datos);
     }
     else {
         return array('error' => false, 'message' => 'No hay formularios registrados', 'data' =>null );
     }
      
    /*--------------------------------------*/
      
  }


}