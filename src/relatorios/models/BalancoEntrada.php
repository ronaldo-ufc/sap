<?php
namespace siap\relatorios\models;

use siap\relatorios\models\Balanco;
use siap\models\DBSiap;
use siap\material\models\Produto;
use siap\produto\models\Movimentaca;
use siap\relatorios\relatorio\Relatorio;

use Dompdf\Dompdf;

class BalancoEntrada{
  private $relatorio;

  function __construct(Relatorio $relatorio) {
    $this->relatorio = $relatorio;
  }
            
  function criar($_produtos, $data_ini, $data_fim) {
    
    $this->relatorio->setTitulo('Relatório de Movimentação de Entrada de Materiais');   
    $produtos = $_produtos == 'TODOS'? Produto::getAllCodigos():$_produtos;
    
    foreach ($produtos as $produto){
      $i = 0;
      $balanco = new Balanco();
      $_balanco = $balanco->getAll($data_ini, $data_fim, $produto, 'E');
      $encontrou = false;
      foreach ($_balanco as $p){
        if (++$i == 1) {
          $head = "<h2>".$p->getNome()."</h2>";
          $tblincio = "<table class='tabela'>
                                            <tr>
                                            <th>DATA</th><th>SETOR</th><th  align=right> QTDE"." ( ".strtolower($p->getUnidade()).") </th>
                                            </tr>";
          $linhas = "";
          $total = 0;
        }
        $linhas .=  "<tr style='font-weight: normal'> 
                                        <td>".$p->getData()."</td><td>PREFEITURA</td><td  align=right>".$p->getquantidade()."</td>
                                      </tr> ";
        $total += $p->getQuantidade();
        $encontrou = true;
      }
      if ($encontrou){
        $linha_total =  "<p style='text-align: right'> TOTAL ".$total."</p> ";
        $linha_total .=  "<p style='text-align: right'><strong> Em Estoque: ".$p->getEstoque()."</strong></p> ";
        $tabela .= $head.$tblincio.$linhas."</table>".$linha_total;
      }
    }
    
    $this->relatorio->setContent($tabela);
    $this->relatorio->setHeader(array('Sucess', $this->relatorio->montaRelatorio(), NULL));
  }
  function imprimir($dompdf){
    $this->relatorio->imprimir($dompdf);
  }
}


