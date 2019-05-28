<?php
use \siap\nota\models\Nota;
use siap\cadastro\models\Fornecedor;
$app->get('/novo', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $notas = Nota::getAll();
  return $this->renderer->render($response, 'nota_novo.html', array('notas'=>$notas, 'classe' => $msg[0], 'texto' => $msg[1]
  ));
})->setName('ViewNota');

$app->post('/novo', function($request, $response, $args) {
  $postParam = $request->getParams();
  $doc = soDigitos($postParam['cpf_cnpj']);
  if (!validaCPF_CNPJ($doc)){
    $this->flash->addMessage('danger', 'CPF/CNPJ '.$postParam['cpf_cnpj'].' invalido.');
    return $response->withStatus(301)->withHeader('Location', '../nota-fiscal/novo');
  }
  $fornecedor = Fornecedor::getByCPF_CNPJ($doc);
  if (!$fornecedor){
    $this->flash->addMessage('danger', 'Não encontrado fornecedor com o CPF/CNPJ '.$postParam['cpf_cnpj']. ' informado.');
    return $response->withStatus(301)->withHeader('Location', '../nota-fiscal/novo');
  }
  $msg = Nota::create($fornecedor->getFornecedor_codigo(), $postParam['nota'], $postParam['valor'], $postParam['descricao']);
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
    $this->flash->addMessage('success', 'Produto cadastrado com sucesso.');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../nota-fiscal/novo');
})->setName('SalvarNota');


$app->get('/excluir/{nota_codigo}', function($request, $response, $args) {
  $msg = Nota::delete($args['nota_codigo']);
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
    $this->flash->addMessage('success', 'Produto cadastrado com sucesso.');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../novo');
})->setName('SalvarNota');

