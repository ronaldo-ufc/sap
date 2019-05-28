<?php
use siap\material\models\Produto;
use siap\material\models\Requisicao;
use siap\cadastro\models\Unidade;
use siap\cadastro\models\Grupo;
use siap\auth\models\Autenticador;
use siap\usuario\models\Usuario;
use siap\material\models\MontaBuscasItens;
use siap\material\models\RequisicaoItens;
use siap\material\models\Estoque;
use siap\setor\models\Setor;
use siap\nota\models\Nota;
use siap\cadastro\models\Solicitante;

include_once 'public/uteis/funcoes.php';
include_once 'public/uteis/data.php';
$app->get('/produto', function($request, $response, $args) {
  $postParam = $request->getParams();
  $c_barras = $postParam['c_barras'];
  $nome = $postParam['nome'];
  $unidade = $postParam['unidade'];
  $grupo = $postParam['grupo'];
  $observacao = $postParam['observacao'];
  $notas = Nota::getAll();

  $produtos = Produto::getAllByParams($c_barras, $nome, $unidade, $grupo, $observacao);

  $msg = getMensagem($this->flash->getMessages());
  return $this->renderer->render($response, 'produto_main.html', array('unidades'=>Unidade::getAll(), 
      'grupos'=> Grupo::getAll(),
      'produtos'=>$produtos,
      'notas'=>$notas, 
      'classe'=> $msg[0], 'texto'=>$msg[1]
  ));
})->setName('visualizaProduto');

$app->get('/produto/editar/{produto_codigo}', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $_msg = $produto = Produto::getById($args['produto_codigo']);
  if (!$_msg) {
      $this->flash->addMessage('danger', 'Produto não encontrado para edição');
      return $response->withStatus(301)->withHeader('Location', '../../produto');
  } 
  $setores = \siap\setor\models\Setor::getAllById($produto->getSetor_codigo());
  $unidades = Unidade::getAllById($produto->getUnidade_codigo());
  $grupos = Grupo::getAllById($produto->getGrupo_codigo());
  return $this->renderer->render($response, 'produto_editar.html', array('unidades'=>$unidades, 'grupos'=> $grupos, 'setores'=>$setores,
      'produto'=>$produto,
      'classe' => $msg[0], 'texto' => $msg[1]
  ));
  
})->setName('NovoEditar');

$app->post('/produto/editar/{produto_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();
  $c_barras = $postParam['c_barras'];
  $nome = $postParam['nome'];
  $unidade = $postParam['unidade'];
  $grupo = $postParam['grupo'];
  $observacao = $postParam['observacao'];
  $quantidade_minima = $postParam['quantidade_minima'];
  $setor = $postParam['setor'];
  
  $msg = Produto::update($c_barras, $nome, $unidade, $grupo, $observacao, $quantidade_minima, $setor,  $args['produto_codigo']);
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
    $this->flash->addMessage('success', 'Produto atualizado com sucesso.');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../editar/'.$args['produto_codigo']);

  
})->setName('NovoEditar');

$app->get('/produto/novo', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $setores = \siap\setor\models\Setor::getAll();

  return $this->renderer->render($response, 'produto_novo.html', array('unidades'=>Unidade::getAll(), 'grupos'=> Grupo::getAll(), 'setores'=>$setores,
   
      'classe' => $msg[0], 'texto' => $msg[1]
  ));
})->setName('NovoProduto');

$app->post('/produto/novo', function($request, $response, $args) {
  $postParam = $request->getParams();
  $c_barras = $postParam['c_barras'];
  $nome = $postParam['nome'];
  $unidade = $postParam['unidade'];
  $grupo = $postParam['grupo'];
  $observacao = $postParam['observacao'];
  $quantidade_minima = $postParam['quantidade_minima'];
  $setor = $postParam['setor'];
  
  $msg = Produto::create($c_barras, $nome, $unidade, $grupo, $observacao, $quantidade_minima, $setor);
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
    $this->flash->addMessage('success', 'Produto cadastrado com sucesso.');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../produto');
})->setName('NovoProduto');



$app->map(['GET', 'DELETE'], '/excluir/{produto_codigo}', function($request, $response, $args) {
  $produto_codigo = $args['produto_codigo'];
  
  $msg = Produto::delete($produto_codigo);
  
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
      $this->flash->addMessage('success', 'Registro excluido com sucesso');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../produto');
  
})->setName('excluirProduto');

$app->post('/entrada', function($request, $response, $args) {
  $postParam = $request->getParams();
  $produto = $postParam['item'];
  $quantidade = $postParam['quantidade'];
  $setor_id = $postParam['setor_id'];
  $nota_codigo = $postParam['nota'];
  $vencimento = $postParam['dta_venc']?formatoYMD_SH($postParam['dta_venc']):null;

  $aut = Autenticador::instanciar();
  $msg = Estoque::entrada($produto, $quantidade, $aut->getUsuario(), $setor_id, $nota_codigo, $vencimento);
 
 if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
      $this->flash->addMessage('success', 'Entrada realizada com sucesso');
  }
  
  return $response->withStatus(301)->withHeader('Location', 'produto');
})->setName('NovoProduto');

$app->get('/saida/{produto_codigo}', function($request, $response, $args) {
  $msg = getMensagem($this->flash->getMessages());
  $produto = Produto::getById($args['produto_codigo']);
  $setores = Setor::getAll();
  $responsaveis = Solicitante::getAll();
 
  return $this->renderer->render($response, 'produto_saida.html', array('produto'=>$produto, 'responsaveis'=>$responsaveis, 'setores'=>$setores, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('SaidaProduto');

$app->post('/saida/{produto_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();
  $produto = Produto::getById($args['produto_codigo']);
  if (!$produto){
    $this->flash->addMessage('danger', 'Produto não encontrado');
    return $response->withStatus(301)->withHeader('Location', '../saida/'.$args['produto_codigo']);
  }
  
  if ($postParam['quantidade'] <= 0){
    $this->flash->addMessage('danger', 'Informe uma quantidade');
    return $response->withStatus(301)->withHeader('Location', '../saida/'.$args['produto_codigo']);
  }

  if ($produto->getQuantidade() < $postParam['quantidade']){
    $this->flash->addMessage('danger', 'Quantidade informada é superior ao estoque: Estoque -> '.$produto->getQuantidade().' Saída -> '.$postParam['quantidade']);
    return $response->withStatus(301)->withHeader('Location', '../saida/'.$args['produto_codigo']);
  }
  if ($produto->getSetor_codigo() == $postParam['setor']){
    $this->flash->addMessage('danger', 'Não é permitido uma saída para a mesma localização que o produto se encontra '. 'Localização: '.$produto->getSetor()->getNome());
    return $response->withStatus(301)->withHeader('Location', '../saida/'.$args['produto_codigo']);
  }
  $aut = Autenticador::instanciar();
  $responsavel = $postParam['responsavel'] != 0? $postParam['responsavel'] : null; 
  $msg = Estoque::saida($args['produto_codigo'], $postParam['quantidade'], $aut->getUsuario(), $responsavel, $produto->getSetor_codigo(), $postParam['setor'], $postParam['os']);
  if ($msg[2]) {
      $this->flash->addMessage('danger', $msg[2]);
  } else {
      $this->flash->addMessage('success', 'Entrada do produto '.$produto->getNome().' registrada com sucesso.');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../produto');
  
})->setName('SaidaProduto');






/******************* SOLICITAÇÕES ********************************/
$app->get('/solicitacoes', function($request, $response, $args) {
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  $requisicoes = Requisicao::getAllBySetor($usuario->getSetor());
  $msg = getMensagem($this->flash->getMessages());
  return $this->renderer->render($response, 'solicitacao_main.html', array('setor_nome'=>$usuario->getSetorNome(), 'solicitacoes'=>$requisicoes, 'classe'=> $msg[0], 'texto'=>$msg[1]));
})->setName('visualizaSolicitacoes');

$app->get('/solicitacoes/novo', function($request, $response, $args) {
  
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  #Verifica se tem Requisição em aberto no ano corrente
  $requisicao = new Requisicao();
  $solicitacao_aberta = $requisicao->haveRequisicaoAberta($usuario->getSetor());
  if ($solicitacao_aberta){
    $this->flash->addMessage('danger', 'A solicitação de número <strong>'.$solicitacao_aberta->getNumero().'</strong> ainda não foi enviada.');
  }else{
    Requisicao::create($aut->getUsuario(), COD_ALMOXARIFADO, $usuario->getSetor());
  }
  return $response->withStatus(301)->withHeader('Location', '../solicitacoes');
})->setName('NovaSolicitacao');

$app->get('/solicitacoes/{requisicao_codigo}', function($request, $response, $args) {
  $requisicao_codigo = $args['requisicao_codigo'];
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  $requisicao = Requisicao::getByCodigo($requisicao_codigo);
  $itens = RequisicaoItens::getByRequisicao($requisicao_codigo);
  return $this->renderer->render($response, 'solicitacao_itens.html', array('setor_nome'=>$usuario->getSetorNome(), 'requisicao'=>$requisicao, 'itens'=>$itens));
})->setName('ItensSolicitacoes');

$app->post('/solicitacoes/{requisicao_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();
  $produto = $postParam['produto'];
  $quantidade = $postParam['quantidade'];
  
  $msg = RequisicaoItens::create($args['requisicao_codigo'], $produto, $quantidade);
    
  return $response->withStatus(301)->withHeader('Location', $args['requisicao_codigo']);
})->setName('SalvarItensSolicitacoes');

$app->post('/seach/{nome}', function($request, $response, $args) {
  $p = Produto::getAllByNome($args['nome']);
  $tabela = new MontaBuscasItens($p);
  echo $tabela->getTabela();
});

$app->post('/seach/itens/{codigo}', function($request, $response, $args) {
  $p = Produto::getById($args['codigo']);
  
  return $this->renderer->render($response, 'resChoice.html', array('produto'=>$p));
});

$app->map(['GET', 'DELETE'],'/solicitacoes/item/excluir/{solicitacao}/{produto_codigo}', function($request, $response, $args) {
  RequisicaoItens::delete($args['produto_codigo']);
  return $response->withStatus(301)->withHeader('Location', '../../../'.$args['solicitacao']);
});

$app->map(['GET', 'DELETE'],'/solicitacoes/excluir/{solicitacao}', function($request, $response, $args) {
  Requisicao::delete($args['solicitacao']);
  return $response->withStatus(301)->withHeader('Location', '../../solicitacoes');
});

$app->get('/solicitacoes/enviar/{codigo}', function($request, $response, $args) {
  if (Requisicao::isItens($args['codigo'])){
    $msg = Requisicao::enviar($args['codigo']);
    if ($msg[2]) {
     $this->flash->addMessage('danger', $msg[2]);
    }
  }else{
    $this->flash->addMessage('danger', 'Não é possível enviar a solicitação sem itens cadastrados');
  }
  
  return $response->withStatus(301)->withHeader('Location', '../../solicitacoes');
});





/********************************  GERENCIAR ************************************/

$app->get('/gerenciar', function($request, $response, $args) {
  $requisicoes = Requisicao::getAllEnviadas();
  $msg = getMensagem($this->flash->getMessages());
  return $this->renderer->render($response, 'gerenciar_solicitacao_main.html', array('solicitacoes'=>$requisicoes, 'classe'=> $msg[0], 'texto'=>$msg[1]));
});

$app->post('/gerenciar', function($request, $response, $args) {
  $postParam = $request->getParams();

  $requisicoes = Requisicao::getAllByFiltro($postParam['numero'], $postParam['status']);
  $msg = getMensagem($this->flash->getMessages());
  return $this->renderer->render($response, 'gerenciar_solicitacao_main.html', array('solicitacoes'=>$requisicoes, 'classe'=> $msg[0], 'texto'=>$msg[1]));
});

$app->get('/gerenciar/{requisicao_codigo}', function($request, $response, $args) {
  $requisicao_codigo = $args['requisicao_codigo'];
  $aut = Autenticador::instanciar();
  $usuario = Usuario::getByLogin($aut->getUsuario());
  $requisicao = Requisicao::getByCodigo($requisicao_codigo);
  $itens = RequisicaoItens::getByRequisicao($requisicao_codigo);
  return $this->renderer->render($response, 'gerenciar_solicitacao_itens.html', array('setor_nome'=>$usuario->getSetorNome(), 'requisicao'=>$requisicao, 'itens'=>$itens));
})->setName('ItensSolicitacoes');

$app->post('/gerenciar/{requisicao_codigo}', function($request, $response, $args) {
  $postParam = $request->getParams();
  $solicitante = $postParam['solicitante'];
  define('ALMOXARIFADO', 22);
  
  //echo $args['requisicao_codigo']; return;
  
  $aut = Autenticador::instanciar();

  $itens = $postParam['quantidade'];
  try {
    foreach($itens as $produto => $quantidade)
    {
      $msg = Estoque::saida($args['requisicao_codigo'], $produto, $quantidade, $aut->getUsuario(), $solicitante, ALMOXARIFADO, $postParam['destino']);
      if ($msg[2]){
         throw new \Exception($msg[2]);
      }
      
    }
    $msg = Requisicao::Aprovar($args['requisicao_codigo']);
    if ($msg[2]){
      throw new \Exception($msg[2]);
    }
    return $response->withStatus(301)->withHeader('Location', '../gerenciar');
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
    
})->setName('ItensSolicitacoes');



