<?php  
namespace App\Verificaciones;
use Psr\Container\ContainerInterface;
class Verificacion {
    protected $container;
   
    public function verificaFechaFinSemanaFestivo($fechaInicio, $fechaFinal)
    {   $respuesta=0;
        $fechaInicio=strtotime($fechaInicio);
        $fechaFin=strtotime($fechaFinal);
        $DiasFestivos[0] = '01/01'; // 1 de enero
        $DiasFestivos[1] = '22/01'; // Estado plurinacional de Bolivia
        $DiasFestivos[2] = '28/02'; // Carnaval
        $DiasFestivos[3] = '01/03'; // Carnaval
        $DiasFestivos[4] = '15/04'; // Viernes Santo
        $DiasFestivos[5] = '01/05'; // 1 de mayo
        $DiasFestivos[6] = '16/06'; // Corpus cristi
        $DiasFestivos[7] = '21/06'; // Año nuevo aymara
        $DiasFestivos[8] = '06/08'; // Dia de la independencia
        $DiasFestivos[9] = '02/11'; // Dia de los difuntos
        $DiasFestivos[10] = '25/12'; // Navidad

        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            if (date('N', strtotime(date("Y-m-d", $i))) >= 6)
              {
                $respuesta=1;
                break;
              }
              else
              {
                $fecha=date("Y-m-d", $i);
                //echo $fecha;

                for ($j=0; $j < count($DiasFestivos); $j++) {
                    $dia = date('d', strtotime($fecha));
                    $mes = date('m', strtotime($fecha));
                    $tmp_date=$dia."/".$mes;
                    if ($tmp_date==$DiasFestivos[$j]) 
                        {$respuesta=1;
                            break;}
                    }
                    if ($respuesta == 1) break;
              }
            
            }
        return $respuesta;
    }
       
}