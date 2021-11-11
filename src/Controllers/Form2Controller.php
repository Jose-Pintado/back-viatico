<?php  namespace App\Controllers;

//use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Form2Controller extends AccesoBDForm2{
    public function insertarForm2( Request $request,Response $response, $args){
      
        $body = json_decode($request->getBody());
        //crear nuevo
        $datos = $this->guardarForm2($body);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
      }
    public function editarForm2( Request $request,Response $response, $args){
      
        $body = json_decode($request->getBody());
        //crear nuevo
        $datos = $this->editForm2($body);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
      }
      public function eliminarForm2( Request $request,Response $response,$args){
        //$form=$args['form'];
        $idForm=$args['id'];
        $datos = $this->eliminarForm_2($idForm);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
    public function verForm2( Request $request,Response $response,$args){
        $idForm=$args['idForm1'];
        //$s=0;
        $datos = $this->verFormulario2($idForm);
        //$datos = $this->verFormulario2($idForm,$s);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
    public function verTramos2( Request $request,Response $response,$args){
        $idForm=$args['id'];
        $s=1;
        $datos = $this->verFormulario2($idForm,$s);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }

   public function listarForm2PorSolicitante( Request $request,Response $response,$args){
        $indice=$args['index'];
        $limit=$args['limit'];
        $idSolicitante = $args['idSol'];
        $datos = $this->listarForm2Solicitante($indice,$limit,$idSolicitante);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
}


