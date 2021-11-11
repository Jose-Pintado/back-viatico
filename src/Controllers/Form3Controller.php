<?php  namespace App\Controllers;

//use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Form3Controller extends AccesoBDForm3{
    public function generarPreLiquidacion( Request $request,Response $response,$args){
        $idForm=$args['id'];
        $datos = $this->guardarForm3Pre($idForm);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
    public function mostrarForm3Pre( Request $request,Response $response,$args){
      $idForm=$args['id'];
      $datos = $this->mostrarForm3($idForm,0);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
  }
  public function mostrarForm3Liquidacion( Request $request,Response $response,$args){
    $idForm=$args['id'];
    $datos = $this->mostrarForm3($idForm,1);
      $status = sizeof($datos) > 0 ? 200 : 204;
      $response->getBody()->write(json_encode($datos));
      return $response
      ->withHeader('Content-Type','application/json')
      ->withStatus($status);
}
  public function generarLiquidacion( Request $request,Response $response,$args){
    $idForm=$args['id'];
    $datos = $this->guardarForm3Liquidacion($idForm);
    $status = sizeof($datos) > 0 ? 200 : 204;
    $response->getBody()->write(json_encode($datos));
    return $response
    ->withHeader('Content-Type','application/json')
    ->withStatus($status);
}


    
    
 
}


