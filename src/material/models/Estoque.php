<?php
namespace siap\material\models;
use siap\models\DBSiap;

class Estoque {
  
  static function entrada($produto, $quantidade, $usuario, $setor_id, $nota_codigo, $vencimento){
    $sql = "INSERT INTO sap.balanco (quantidade, produto_codigo, setor_id, usuario_cadastro_login, nota_codigo, vencimento) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($quantidade, $produto, $setor_id, $usuario, $nota_codigo, $vencimento));
    return $stmt->errorInfo();
  }
  
  
  static function saida($produto, $quantidade, $usuario, $solicitante, $origem, $destino, $os){
    $sql = "select * from sap.estoque_saida(?, ?, ?, ?, ?, ?, ?)";
    $stmt = DBSiap::getSiap()->prepare($sql);
    $stmt->execute(array($produto, $quantidade, $usuario, $solicitante, $origem, $destino, $os));
    return $stmt->errorInfo(); 
  }
}

