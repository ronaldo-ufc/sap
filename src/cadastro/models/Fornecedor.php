<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Fornecedor {
  const PESSOA_FISICA = 'F';
  const PESSOA_JURIDICA = 'J';

  private $fornecedor_codigo;
    private $nome;
    private $cpf_cnpj;
    private $tipo;

    private function bundle($row) {
        $u = new Fornecedor();
        $u->setFornecedor_codigo($row['fornecedor_codigo']);
        $u->setNome($row['nome']);
        $u->setCpf_cnpj($row['cpf_cnpj']);
        $u->setTipo($row['tipo']);
        return $u;
    }

    static function getAll() {
        $sql = "select * from fornecedor order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
    static function getAllById($id) {
      $sql = "select * from fornecedor order by fornecedor_codigo = ? desc, nome asc";
      $stmt = DBSiap::getSiap()->prepare($sql);
      $stmt->execute(array($id));
      $rows = $stmt->fetchAll();
      $result = array();
      foreach ($rows as $row) {
          array_push($result, self::bundle($row));
      }
      return $result;
    }

    static function getById($fornecedor_codigo) {
        $sql = "select * from fornecedor where fornecedor_codigo = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($fornecedor_codigo));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }
    
    static function getByCPF_CNPJ($doc) {
        $sql = "select * from fornecedor where cpf_cnpj = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($doc));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    static function create($nome, $doc, $tipo) {
        $sql = "INSERT INTO fornecedor (nome, cpf_cnpj, tipo) VALUES (?, ?, ?)";

        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array(strtoupper(tirarAcentos($nome)), $doc, $tipo));
        
        return $stmt->errorInfo();
    }

    static function delete($fornecedor_codigo) {
        $sql = 'DELETE FROM fornecedor WHERE fornecedor_codigo = ?';
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($fornecedor_codigo));
        return $stmt->errorInfo();
    }

    function getFornecedor_codigo() {
      return $this->fornecedor_codigo;
    }

    function getNome() {
      return $this->nome;
    }

    function setFornecedor_codigo($fornecedor_codigo) {
      $this->fornecedor_codigo = $fornecedor_codigo;
    }

    function setNome($nome) {
      $this->nome = $nome;
    }

    function getCpf_cnpj() {
      return $this->cpf_cnpj;
    }

    function getTipo() {
      return $this->tipo;
    }

    function setCpf_cnpj($cpf_cnpj) {
      $this->cpf_cnpj = $cpf_cnpj;
    }

    function setTipo($tipo) {
      $this->tipo = $tipo;
    }

    function getTipoNome(){
      switch ($this->tipo){
        case self::PESSOA_FISICA: return 'Pessoa Física';    
        case self::PESSOA_JURIDICA: return 'Pessoa Jurídica';
      }
    }

}
