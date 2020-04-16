<?php
namespace siap\relatorios\models;

interface iMovimentacao {
  function bundle($row);
  function criar($produtos, $data_ini, $data_fim);
  function imprimir($dompdf);
}
