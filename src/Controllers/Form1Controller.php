<?php  namespace App\Controllers;

//use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Form1Controller extends AccesoBD{
    public function insertarForm1( Request $request,Response $response, $args){
      
        $body = json_decode($request->getBody());
        //crear nuevo
        $datos = $this->guardarForm1($body);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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
    public function editarForm1( Request $request,Response $response, $args){
      
        $body = json_decode($request->getBody());
        //crear nuevo
        $datos = $this->editForm1($body);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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
      public function eliminarForm1( Request $request,Response $response,$args){
        //$form=$args['form'];
        $idForm=$args['id'];
        $datos = $this->eliminarForm_1($idForm);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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
    public function verFormulario( Request $request,Response $response,$args){
        $idForm=$args['id'];
        $datos = $this->verFormularioImprimir($idForm);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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
    public function verForm1( Request $request,Response $response,$args){
        $idForm=$args['id'];
        $s=0;
        $datos = $this->verFormulario1($idForm,$s);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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
    public function verTramos( Request $request,Response $response,$args){
        $idForm=$args['id'];
        $s=1;
        $datos = $this->verFormulario1($idForm,$s);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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

    public function cambioEstados( Request $request,Response $response, $args){
        $estado=$args['estado'];
        $idForm=$args['id'];
        $datos = $this->cambioEstado($idForm,$estado);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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
      public function observarForm1( Request $request,Response $response, $args){
        $body = json_decode($request->getBody());
        $datos = $this->observarFormulario($body);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
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

    public function listarForm1PorSolicitante( Request $request,Response $response,$args){
        $indice=$args['index'];
        $limit=$args['limit'];
        $idSolicitante = $args['idSol'];
        $datos = $this->listarForm1Solicitante($indice,$limit,$idSolicitante);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
      
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

      public function listarForm1( Request $request,Response $response,$args){
        
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $queryParam=$request->getQueryParams();
        
        $datos = $this->listarFormularios($queryParam);
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

public function listarParam( Request $request,Response $response,$args){
        
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $queryParam=$request->getQueryParams();
        
        $datos = $this->listarParametros($queryParam);
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
  
    

     public function insertarTramoForm1( Request $request,Response $response, $args){
      
        $body = json_decode($request->getBody());
        //crear nuevo
        $datos = $this->guardarTramosForm1($body);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
       

     }
     public function buscarTramoForm1( Request $request,Response $response,$args){
        $idForm=$args['id'];
        $datos = $this->buscarTramosForm1($idForm);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
     public function buscarForm1Nombre( Request $request,Response $response,$args){
        $indice=$args['indice'];
        $limit=$args['limit'];
        $nombre=$args['nombre'];
        $datos = $this->buscarForm1Nombres($indice,$limit,$nombre);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }
    public function buscarForm1Estado( Request $request,Response $response,$args){
        $indice=$args['indice'];
        $limit=$args['limit'];
        $estado=$args['estado'];
        $datos = $this->buscarForm1Estados($indice,$limit,$estado);
        $status = sizeof($datos) > 0 ? 200 : 204;
        $response->getBody()->write(json_encode($datos));
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }

    

}


