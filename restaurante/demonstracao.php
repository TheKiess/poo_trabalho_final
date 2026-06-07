<?php
  require_once __DIR__ . "/config.php";

  $cliente = new Cliente("Maria Silva", "123.456.789-00", "maria@email.com", "premium");
  $pizza   = new Produto("Pizza Margherita", "Molho, mussarela", 45.90, "Pizza");
  $bebida  = new Produto("Suco de Laranja", "Natural 500ml", 12.00, "Bebida");

  $pedido = new Pedido($cliente);
  $pedido->adicionarItem($pizza,  1);
  $pedido->adicionarItem($bebida, 2);

  $vlDesconto = $pedido->aplicarDesconto();
  echo "Desconto aplicado: R$ " . number_format($vlDesconto, 2, ",", ".") . "\n";

  $pedido->confirmar();
  echo $pedido;

  $garcom = new Funcionario("Carlos", "111.222.333-44", "c@rest.com", "Garçom", 2500.00);
  $bonus  = $garcom->calcularBonusSalario(15);
  echo "Bônus: R$ " . number_format($bonus, 2, ",", ".") . "\n";