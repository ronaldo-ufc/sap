<?php

namespace siap\cadastro\models;

use siap\models\DBSiap;

class Solicitante {

    private $responsavel_recebimento_codigo;
    private $nome;

    private function bundle($row) {
        $u = new Solicitante();
        $u->setResponsavel_recebimento_codigo($row['responsavel_recebimento_codigo']);
        $u->setNome($row['nome']);
        return $u;
    }

    static function getAll() {
        $sql = "select * from responsavel_recebimento order by nome";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array());
        $rows = $stmt->fetchAll();
        $result = array();
        foreach ($rows as $row) {
            array_push($result, self::bundle($row));
        }
        return $result;
    }
    
        
    static function getById($responsavel_recebimento) {
        $sql = "select * from responsavel_recebimento where responsavel_recebimento_codigo = ?";
        $stmt = DBSiap::getSiap()->prepare($sql);
        $stmt->execute(array($responsavel_recebimento));
        $row = $stmt->fetch();
        if ($row == null) {
            return null;
        }
        return self::bundle($row);
    }

    
    function getResponsavel_recebimento_codigo() {
      return $this->responsavel_recebimento_codigo;
    }

    function getNome() {
      return $this->nome;
    }

    function setResponsavel_recebimento_codigo($responsavel_recebimento_codigo) {
      $this->responsavel_recebimento_codigo = $responsavel_recebimento_codigo;
    }

    function setNome($nome) {
      $this->nome = $nome;
    }



}
