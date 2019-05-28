<?php
#Rota para os serviços do template
use siap\usuario\models\Permicao;
use siap\cadastro\models\Modelo;
use siap\nota\models\Nota;

#Tetorma Menus Pais
$app->map(['GET', 'POST'], '/menu/{privilegio}', function($request, $response, $args){
  
  $_menu = Permicao::getMenuPaisByPrivilegio($args['privilegio']);
  $menu = [];
  foreach($_menu as $val){
    array_push($menu, array($val->getMenu_codigo(), $val->getMenu()->getMenu_nome(), $val->getPrivilegio_habilitado()));
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($menu, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Retorma SubMenus
$app->map(['GET', 'POST'], '/submenu/{privilegio}/{menu}', function($request, $response, $args){
  
  $_menu = Permicao::getsubMenusByPrivilegioAndMenu($args['privilegio'], $args['menu']);
  $menu = [];
  foreach($_menu as $val){
    array_push($menu, array($val->getMenu_codigo(), $val->getMenu()->getMenu_nome(), $val->getPrivilegio_habilitado()));
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($menu, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Retorma Modelos
$app->map(['GET', 'POST'], '/modelos/{fabricante_id}', function($request, $response, $args){
  
  $_modelo = Modelo::getByFabricante($args['fabricante_id']);
  $modelo = [];
  foreach($_modelo as $val){
    array_push($modelo, array($val->getModelo_Id(), $val->getnome()));
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($modelo, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Retorma Notas Fiscais
$app->map(['GET', 'POST'], '/notas/{fornecedor_cpf_cnpj}', function($request, $response, $args){
  
  $_notas = Nota::getAllByCPF_CNPJFornecedor($args['fornecedor_cpf_cnpj']);
  $notas = [];
  foreach($_notas as $val){
    array_push($notas, array($val->getNota_codigo(), $val->getNumero(), $val->getFornecedor()->getNome()));
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($notas, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Retorma Razão Social 
$app->map(['GET', 'POST'], '/fornecedor/{fornecedor_cpf_cnpj}', function($request, $response, $args){
  
  $fornecedor = siap\cadastro\models\Fornecedor::getByCPF_CNPJ($args['fornecedor_cpf_cnpj']);

  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($fornecedor->getNome(), null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

});

#Salvar Itens
$app->post('/salvar/item', function($request, $response, $args){
  $postParam = $request->getParams();
  switch ($postParam['item']){
    case 'marca': $msg = siap\cadastro\models\Fabricante::create($postParam['nome']);
      break;
              
    case 'modelo': $msg = Modelo::create($postParam['nome'], $postParam['marca']);
      break;
              
    case 'tipo_de_aquisicao': $msg = siap\cadastro\models\Aquisicao::create($postParam['nome']);
      break;
              
    case 'status': $msg = siap\cadastro\models\Status::create($postParam['nome']);
      break;
              
    case 'setor': $msg = siap\setor\models\Setor::create($postParam['nome'],$postParam['sigla']);
      break;
              
    case 'fornecedor': $msg = siap\cadastro\models\Fornecedor::create($postParam['nome']);
      break;
    
    case 'categoria': $msg = siap\cadastro\models\Categoria::create($postParam['nome']);
      break;
    
    case 'unidade': $msg = siap\cadastro\models\Unidade::create($postParam['nome']);
      break;
    
    case 'grupo': $msg = siap\cadastro\models\Grupo::create($postParam['nome']);
      break;
  }
  return ($msg[2])? $msg[2] : "Item :".$postParam['nome']." foi adicionado com sucesso.";
});

#Recebe Itens
$app->get('/receber/item/{item}', function($request, $response, $args){
  $item = [];
    
  switch ($args['item']){
    case 'unidade': $objeto = siap\cadastro\models\Unidade::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getUnidade_codigo(), $val->getnome()));
                      }
                      break;
    case 'fornecedor': $objeto = siap\cadastro\models\Fornecedor::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getFornecedor_codigo(), $val->getNome()));
                      }
                      break;

    case 'setor': $objeto = siap\setor\models\Setor::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getSetor_id(), $val->getnome()));
                      }
                      break;
    case 'grupo': $objeto = siap\cadastro\models\Grupo::getAll();
                      foreach($objeto as $val){
                        array_push($item, array($val->getGrupo_codigo(), $val->getnome()));
                      }
                      break;
  }
  return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->withJson($item, null, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
});