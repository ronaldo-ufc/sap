<?php
namespace siap\material\models;
use siap\models\DBSiap;
use siap\cadastro\models\Unidade;
use siap\cadastro\models\Grupo;
use siap\setor\models\Setor;
class Produto {
  private $produto_codigo;
  private $nome; 
  private $observacao;
  private $status;
  private $unidade_codigo; 
  private $grupo_codigo;
  private $codigo_barras;
  private $quantidade_minima;
  private $fornecedor_codigo;
  private $setor_codigo;
  private $unidade;
  private $grupo;
  private $setor;
  
  public static $quantidade = 0;
  
  private function bundle($row){
    $u = new Produto();
    $u->setProduto_codigo($row['produto_codigo']);
    $u->setNome($row['nome']);
    $u->setObservacao($row['observacao']);
    $u->setStatus($row['status']);
    $u->setUnidade_codigo($row['unidade_codigo']);
    $u->setGrupo_codigo($row['grupo_codigo']);
    $u->setCodigo_barras($row['codigo_barras']);
    $u->setSetor_codigo($row['setor_codigo']);
    $u->setQuantidade_minima($row['quantidade_minima']);

    #Objetos de referência
    $u->setUnidade(Unidade::getById($row['unidade_codigo']));
    $u->setGrupo(Grupo::getById($row['grupo_codigo']));
    $u->setSetor(Setor::getById($row['setor_codigo']));

    return $u;
  }
  
  static function getAllByParams($c_barras, $nome, $unidade, $grupo, $observacao){
    $nome = "%".$nome."%";
    $observacao = "%".$observacao."%";
    $unidade = "%".$unidade."%";
    $grupo = "%".$grupo."%";
    $barras = $c_barras? "%".$c_barras."%": "%";
    $sql = "select * from sap.produto where  "
            . "codigo_barras like ? and "
            . "nome ilike ? and "
            . "cast (unidade_codigo as text) like ? and "
            . "cast (grupo_codigo as text) like ? and "
            . "observacao ilike ? order by nome ";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($barras, $nome, $unidade, $grupo, $observacao));
    $rows = $stmt->fetchAll();
   //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getById($id){
    $sql = "select * from sap.produto where produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($id));
    $row = $stmt->fetch();
    if ($row == null){
      return false;
    }
    return self::bundle($row);
  }
  
  static function getAllByNome($nome){
    $nome = "%".$nome."%";
    $sql = "select * from sap.produto where nome ilike ? order by nome asc limit 6";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($nome));
    $rows = $stmt->fetchAll();
   //return $stmt->errorInfo();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }

  static function create($c_barras, $nome, $unidade, $grupo, $observacao, $quantidade_minima, $setor){
    $sql = "INSERT INTO sap.produto (nome, observacao, unidade_codigo, grupo_codigo, codigo_barras, quantidade_minima, setor_codigo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(strtoupper(tirarAcentos($nome)), strtoupper(tirarAcentos($observacao)), $unidade, $grupo, $c_barras, $quantidade_minima, $setor));
    return $stmt->errorInfo();
  }
  
  static function update($c_barras, $nome, $unidade, $grupo, $observacao, $quantidade_minima, $setor, $produto_codigo){
    $sql = "UPDATE sap.produto SET nome = ?, observacao=?, unidade_codigo=?, grupo_codigo=?, codigo_barras=?, quantidade_minima=?, setor_codigo=? WHERE produto_codigo=?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array(strtoupper(tirarAcentos($nome)), strtoupper(tirarAcentos($observacao)), $unidade, $grupo, $c_barras, $quantidade_minima, $setor, $produto_codigo));
    return $stmt->errorInfo();
  }
  
  static function delete($produto_codigo){
    $sql = "DELETE FROM sap.produto WHERE produto_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($produto_codigo));
    return $stmt->errorInfo();
  }
  
  static function getAll(){
    $sql = "select * from sap.produto order by nome";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row) {
        array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getAllCodigos(){
    $produtos = Produto::getAll();
    $result = array();
    foreach ($produtos as $produto){
      array_push($result, $produto->getProduto_codigo());
    }
    return $result;
  }


  public function getProduto_codigo() {
    return $this->produto_codigo;
  }

  
  public function getNome() {
    return $this->nome;
  }

  public function getObservacao() {
    return $this->observacao;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getUnidade_codigo() {
    return $this->unidade_codigo;
  }

  public function getGrupo_codigo() {
    return $this->grupo_codigo;
  }

  public function setProduto_codigo($produto_codigo) {
    $this->produto_codigo = $produto_codigo;
  }
   
  public function setNome($nome) {
    $this->nome = $nome;
  }

  public function setObservacao($observacao) {
    $this->observacao = $observacao;
  }
  
  function getSetor() {
    return $this->setor;
  }

  function setSetor($setor) {
    $this->setor = $setor;
  }

  
  public function setStatus($status) {
    $this->status = $status;
  }

  public function setUnidade_codigo($unidade_codigo) {
    $this->unidade_codigo = $unidade_codigo;
  }

  public function setGrupo_codigo($grupo_codigo) {
    $this->grupo_codigo = $grupo_codigo;
  }
  
  public function getUnidade() {
    return $this->unidade;
  }

  public function getGrupo() {
    return $this->grupo;
  }

  public function setUnidade($unidade) {
    $this->unidade = $unidade;
  }
  function getSetor_codigo() {
    return $this->setor_codigo;
  }

  function setSetor_codigo($setor_codigo) {
    $this->setor_codigo = $setor_codigo;
  }

  function getFornecedor_codigo() {
    return $this->fornecedor_codigo;
  }

  function getNota_codigo() {
    return $this->nota_codigo;
  }

  function setFornecedor_codigo($fornecedor_codigo) {
    $this->fornecedor_codigo = $fornecedor_codigo;
  }

  function setNota_codigo($nota_codigo) {
    $this->nota_codigo = $nota_codigo;
  }

    
  public function setGrupo($grupo) {
    $this->grupo = $grupo;
  }
  public function getCodigo_barras() {
    return $this->codigo_barras;
  }

  public function setCodigo_barras($codigo_barras) {
    $this->codigo_barras = $codigo_barras;
  }
  
  public function get_Quantidade(){
    $sql = "select * from sap.item_quantidade(?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($this->produto_codigo, $this->setor_codigo));
    $row = $stmt->fetch();
    if ($row == null){
      return 0;
    }
    
    return $row['item_quantidade'];
  }

  public function getQuantidade() {
    if ($this->quantidade == 0) {
      $this->quantidade = $this->get_Quantidade();
    }
    return $this->quantidade;
  }
       
  public function getStatusNome(){
    switch ($this->status){
      case 'A': return 'Ativo';
      case 'I': return 'Inativo';  
    }
  }
  public function getQuantidade_minima() {
    return $this->quantidade_minima;
  }

  public function setQuantidade_minima($quantidade_minima) {
    $this->quantidade_minima = $quantidade_minima;
  }

  public function getCorClassQuantidadeMinima(){
    if ($this->getQuantidade() < $this->quantidade_minima){
      return 'danger';
    }
    if ($this->getQuantidade() == $this->quantidade_minima){
      return 'warning';
    }
  }
}

