<?php
  require __DIR__ . "/../config.php";
?>

<aside class="sidebar">
  <div class="sidebar-logo">
    <span class="logo-icon">🍽️</span>
    <h1>Bistrot POO</h1>
    <p>Sistema de Restaurante</p>
  </div>

  <nav class="sidebar-nav">
    <span class="nav-section-label">Menu</span>

    <a href="<?= $view ?>index.php" class="nav-link <?= $dsPaginaAtual == "dashboard" ? "active" : "" ?>">
      <span class="nav-icon">📊</span> Dashboard
    </a>

    <a href="<?= $view ?>pages/pedidos.php" class="nav-link <?= $dsPaginaAtual == "pedidos" ? "active" : "" ?>">
      <span class="nav-icon">🧾</span> Pedidos
    </a>

    <span class="nav-section-label">Cadastros</span>

    <a href="<?= $view ?>pages/clientes.php" class="nav-link <?= $dsPaginaAtual == "clientes" ? "active" : "" ?>">
      <span class="nav-icon">👥</span> Clientes
    </a>

    <a href="<?= $view ?>pages/produtos.php" class="nav-link <?= $dsPaginaAtual == "produtos" ? "active" : "" ?>">
      <span class="nav-icon">🍕</span> Cardápio
    </a>

    <a href="<?= $view ?>pages/funcionarios.php" class="nav-link <?= $dsPaginaAtual == "funcionarios" ? "active" : "" ?>">
      <span class="nav-icon">👨‍🍳</span> Funcionários
    </a>
  </nav>

  <div class="sidebar-footer">
    <strong>Trabalho POO — PHP</strong><br>
    <p>Herança · Composição · Associação</p>
  </div>
</aside>