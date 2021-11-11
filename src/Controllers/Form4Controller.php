<?php  namespace App\Controllers;

//use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Form4Controller extends AccesoBDForm4{
    public function insertarForm4( Request $request,Response $response, $args){
      
        $body = json_decode($request->getBody());
        //crear nuevo
        $datos = $this->guardarForm4($body);
        $status = sizeof($datos) > 0 ? 200 : 204 ;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
      }
    public function editarForm4( Request $request,Response $response, $args){
      
        $body = json_decode($request->getBody());
        //crear nuevo
        $datos = $this->editForm4($body);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
      }
      public function eliminarForm4( Request $request,Response $response,$args){
        //$form=$args['form'];
        $idForm=$args['id'];
        $datos = $this->eliminarForm_4($idForm);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
    public function verForm4( Request $request,Response $response,$args){
        $idForm=$args['idForm1'];
       
        $datos = $this->verFormulario4($idForm);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
    
 
}


