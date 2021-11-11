<?php  namespace App\Controllers;

//use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ParamController extends AccesoBD_Param{


public function listarEstados( Request $request,Response $response,$args){
        
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $queryParam=$request->getQueryParams();
        
        $datos = $this->verEstados($queryParam);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        //$response->$datos;
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Content-Type','application/json')          
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
            ->withHeader('Pragma', 'no-cache')
            ->withStatus($status);
        return $response;
    }
  
    
}


