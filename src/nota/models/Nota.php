<?php

namespace siap\nota\models;
use siap\models\DBSiap;

class Nota {
  private $nota_codigo;
  private $numero;
  private $valor;
  private $descricao;
  private $fornecedor;
  private $fornecedor_codigo;
  
  private function bundle($row){
    $u = new Nota();
    $u->setNota_codigo($row['nota_codigo']);
    $u->setNumero($row['numero']);
    $u->setValor($row['valor']);
    $u->setFornecedor_codigo($row['fornecedor_codigo']);
    $u->setDescricao($row['descricao']);
    
    $u->setFornecedor(\siap\cadastro\models\Fornecedor::getById($row['fornecedor_codigo']));
    return $u;
  }
  
  static function getAll(){
    $sql = "select * from sap.nota order by numero";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array());
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getAllByCPF_CNPJFornecedor($doc){
    $sql = "select n.* from sap.nota n "
            . "inner join fornecedor f on n.fornecedor_codigo = f.fornecedor_codigo "
            . "where f.cpf_cnpj = ? order by n.numero";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($doc));
    $rows = $stmt->fetchAll();
    $result = array();
    foreach ($rows as $row){
      array_push($result, self::bundle($row));
    }
    return $result;
  }
  
  static function getByCodigo($codigo){
    $sql = "select * from sap.nota where nota_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($codigo));
    $row = $stmt->fetch();
    if ($row == null){
      return null;
    }
    return self::bundle($row);
  }
  
  static function getByNumero($numero){
    $sql = "select * from sap.nota where numero = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($numero));
    $row = $stmt->fetch();
    if ($row == null){
      return null;
    }
    return self::bundle($row);
  }
  
  static function create($fornecedor_codigo, $numero, $valor, $descricao){
    $sql = "INSERT INTO sap.nota (fornecedor_codigo, numero, valor, descricao) VALUES (?,?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($fornecedor_codigo, $numero, converteMoeda($valor), $descricao));
    return $stmt->errorInfo();
  }
  
  static function delete($nota_codigo){
    $sql = "DELETE FROM sap.nota WHERE nota_codigo = ?";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($nota_codigo));
    return $stmt->errorInfo();
  }
          
  function getNota_codigo() {
    return $this->nota_codigo;
  }

  function getNumero() {
    return $this->numero;
  }

  function getValor() {
    return $this->valor;
  }

  function setNota_codigo($nota_codigo) {
    $this->nota_codigo = $nota_codigo;
  }

  function setNumero($numero) {
    $this->numero = $numero;
  }

  function setValor($valor) {
    $this->valor = $valor;
  }
  function getDescricao() {
    return $this->descricao;
  }

  function getFornecedor() {
    return $this->fornecedor;
  }

  function getFornecedor_codigo() {
    return $this->fornecedor_codigo;
  }

  function setDescricao($descricao) {
    $this->descricao = $descricao;
  }

  function setFornecedor($fornecedor) {
    $this->fornecedor = $fornecedor;
  }

  function setFornecedor_codigo($fornecedor_codigo) {
    $this->fornecedor_codigo = $fornecedor_codigo;
  }



}
