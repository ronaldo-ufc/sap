<?php
use siap\material\models\Produto;
use siap\relatorios\models\BalancoEntrada;
use siap\relatorios\models\BalancoSaida;
use siap\relatorios\relatorio\Relatorio;
use siap\relatorios\models\Balanco;

#########################  MOVIMENTAÇÃO DE PRODUTOS  #######################
$app->get('/movimentacao', function($request, $response, $args) {
  $produtos = Produto::getAllByParams(null, null, null, null, null);
  return $this->renderer->render($response, 'movimentacao.html', array('produtos'=>$produtos));            
})->setName('Relatorioconsumo-produto');

$app->post('/movimentacao', function($request, $response, $args) {

  $postParam = $request->getParams();
  switch ($postParam['operacao']){
    case 'E': $relatorio = new BalancoEntrada(new Relatorio());      break;
    case 'S': $relatorio = new BalancoSaida(new Relatorio());      break;
  }
  $produtos = $postParam['todos']? 'TODOS': $postParam['produtos'];
  switch (get_post_action('imprimir', 'exportar')) {
    case 'imprimir':
        $relatorio->criar($produtos, $postParam['data_ini'], $postParam['data_fim']);
        $relatorio->imprimir($this->DOMPDF);
        break;

    case 'exportar':
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=file.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $relatorio->exportar($produtos, $postParam['data_ini'], $postParam['data_fim']);
        die;
        break;

  } 
  

})->setName('Relatorioconsumo-produto');

###########################################################################