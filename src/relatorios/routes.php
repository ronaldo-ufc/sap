<?php
use siap\material\models\Produto;
use siap\relatorios\models\BalancoEntrada;
use siap\relatorios\models\BalancoSaida;
use siap\relatorios\relatorio\Relatorio;

#########################  MOVIMENTAÇÃO DE PRODUTOS  #######################
$app->get('/movimentacao', function($request, $response, $args) {
  $produtos = Produto::getAllByParams(null, null, null, null, null);
  return $this->renderer->render($response, 'movimentacao.html', array('produtos'=>$produtos));            
})->setName('Relatorioconsumo-produto');

$app->post('/movimentacao', function($request, $response, $args) {
//  ini_set('display_errors',1);
//ini_set('display_startup_erros',1);
//error_reporting(E_ALL);
  $postParam = $request->getParams();
  switch ($postParam['operacao']){
    case 'E': $relatorio = new BalancoEntrada(new Relatorio());      break;
    case 'S': $relatorio = new BalancoSaida(new Relatorio());      break;
  }
  $produtos = $postParam['todos']? 'TODOS': $postParam['produtos'];
  $relatorio->criar($produtos, $postParam['data_ini'], $postParam['data_fim']);
  $relatorio->imprimir($this->DOMPDF);
})->setName('Relatorioconsumo-produto');

###########################################################################