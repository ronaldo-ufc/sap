<?php
namespace siap\relatorios\models;

use siap\models\DBSiap;
use siap\material\models\Produto;
use siap\produto\models\Movimentacao;
use siap\relatorios\models\iMovimentacao;
use siap\relatorios\relatorio\Relatorio;

use Dompdf\Dompdf;

class Balanco implements iMovimentacao{
  private $data;
  private $nome;
  private $setor;
  private $quantidade;
  private $unidade;
  private $estoque;
  
  function bundle($row) {
    $u = new Balanco();
    $u->setData($row['data']);
    $u->setNome($row['nome']);
    $u->setUnidade($row['unidade']);
    $u->setSetor($row['setor']);
    $u->setQuantidade($row['quantidade']);
    $u->setEstoque($row['estoque']);
    
    return $u;
  }
  
  function getAll($data_ini, $data_fim, $produto, $operacao){
    $sql = "select data, p.nome, s.nome as setor, quantidade, u.nome as unidade, sap.item_quantidade(p.produto_codigo, p.setor_codigo) as estoque
                      from sap.balanco b
                      inner join sap.produto p on b.produto_codigo = p.produto_codigo
                      inner join unidade u on p.unidade_codigo = u.unidade_codigo
                      inner join setor s on s.setor_id = b.setor_id
                      where b.produto_codigo = ?
                            and data between ? and ?
                            and b.tipo = 'E' ";
    if ($operacao == 'E') {
      $sql .= " and b.setor_id = 32 ";
    }else{
      $sql .= " and b.setor_id <> 32 ";
    }
    $sql .= " order by data asc ";
                                              
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($produto, $data_ini, $data_fim));
    $rows = $stmt->fetchAll();
    
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
    
  }
          
  function getData() {
    return formatoDateToDataHora($this->data, 'DMY NNN');
  }

  function getNome() {
    return $this->nome;
  }
  function getEstoque() {
    return $this->estoque;
  }

  function setEstoque($estoque) {
    $this->estoque = $estoque;
  }

  
  function getSetor() {
    return $this->setor;
  }

  function getQuantidade() {
    return $this->quantidade;
  }

  function setData($data) {
    $this->data = $data;
  }

  function setNome($nome) {
    $this->nome = $nome;
  }
  function getUnidade() {
    return $this->unidade;
  }

  function setUnidade($unidade) {
    $this->unidade = $unidade;
  }

  
  function setSetor($setor) {
    $this->setor = $setor;
  }

  function setQuantidade($quantidade) {
    $this->quantidade = $quantidade;
  }

  public function criar($produtos, $data_ini, $data_fim) {
    
  }

  public function imprimir($dompdf) {
    
  }

}

