<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;
use App\Controllers\Form1Controller;
use App\Controllers\Form2Controller;
use App\Controllers\Form4Controller;
use App\Controllers\Form3Controller;
use App\Controllers\ParamController;

//FORM 1
$app->group('/api/v1/form1',function(RouteCollectorProxy $group){
    //SOLICITANTE
   $group->post('',Form1Controller::class.':insertarForm1');
    $group->put('',Form1Controller::class.':editarForm1');
    $group->delete('/{id}',Form1Controller::class.':eliminarForm1');
    $group->get('/{id}',Form1Controller::class.':verForm1');//ver datos del formulario
    $group->get('/formulario/{id}',Form1Controller::class.':verFormulario'); //imprimir
    $group->get('/tramos/{id}',Form1Controller::class.':verTramos');
    $group->put('/{estado}/{id}',Form1Controller::class.':cambioEstados');//estado= revisado, generado,aceptado,archivar
    $group->put('/observacion',Form1Controller::class.':observarForm1'); //estado = RECH u OBS
    $group->GET('/{index}/{limit}/{idSol}',Form1Controller::class.':listarForm1PorSolicitante');

    
    $group->get('',Form1Controller::class.':listarForm1');
    
    
    //$group->get('/solicitante/{indice}/{limit}/{idSolicitante}',Form1Controller::class.':listarForm1PorSolicitante');
   
   // $group->post('/TramoForm1',Form1Controller::class.':insertarTramoForm1');
    //$group->get('/Tramo/{id}',Form1Controller::class.':buscarTramoForm1');
   // $group->get('/estado/{indice}/{limit}/{estado}/{idSolicitante}',Form1Controller::class.':listarForm1PorEstadoSolicitante');

    //CONTABILIDAD
    //$group->get('/formulario1/{indice}/{limit}',Form1Controller::class.':listar');
   // $group->get('/nombre/{indice}/{limit}/{nombre}',Form1Controller::class.':buscarForm1Nombre');
   // $group->get('/estado/{indice}/{limit}/{estado}',Form1Controller::class.':buscarForm1Estado');
    
    //ADMIN
  //  $group->delete('/formulario1/{form}/{id}',Form1Controller::class.':eliminarForm1');
});
//FORM 2
$app->group('/api/v1/form2',function(RouteCollectorProxy $group){
  $group->post('',Form2Controller::class.':insertarForm2');
  $group->put('',Form2Controller::class.':editarForm2');
  $group->delete('/{id}',Form2Controller::class.':eliminarForm2');
  $group->get('/{idForm1}',Form2Controller::class.':verForm2');// para imprimir form2
  //$group->get('/tramos/{idForm2}',Form2Controller::class.':verTramos2');
  
});
//FORM 4
$app->group('/api/form4',function(RouteCollectorProxy $group){
  $group->post('',Form4Controller::class.':insertarForm4');
  $group->put('',Form4Controller::class.':editarForm4');
  $group->delete('/{id}',Form4Controller::class.':eliminarForm4');
  $group->get('/{idForm1}',Form4Controller::class.':verForm4'); // para imprimir informe final 
  
});
//FORM 3
$app->group('/api/v1/form3',function(RouteCollectorProxy $group){
  $group->put('/{id}',Form3Controller::class.':generarPreLiquidacion');
  $group->put('/liquidacion/{id}',Form3Controller::class.':generarLiquidacion');
  $group->get('/liquidacion/{id}',Form3Controller::class.':mostrarForm3Liquidacion');
  $group->get('/preliquidacion/{id}',Form3Controller::class.':mostrarForm3Pre');
});

$app->group('/api/v1/param',function(RouteCollectorProxy $group){
   $group->get('/estados',ParamController::class.':listarEstados');
});