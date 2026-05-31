<?php
  define("BASE_PATH", __DIR__);

  require_once BASE_PATH . "/classes/Pessoa.php";
  require_once BASE_PATH . "/classes/Cliente.php";
  require_once BASE_PATH . "/classes/Funcionario.php";
  require_once BASE_PATH . "/classes/Produto.php";
  require_once BASE_PATH . "/classes/ItemPedido.php";
  require_once BASE_PATH . "/classes/Pedido.php";
  require_once BASE_PATH . "/includes/DataStore.php";

  $idArquivoPages  = str_contains($_SERVER["SCRIPT_FILENAME"], "/pages/");
  $view            = $idArquivoPages ? "../" : "";