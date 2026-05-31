<?php
  require_once __DIR__ . "/config.php";
  include      __DIR__ . "/includes/nav.php";

  $dsPaginaAtual   = "dashboard";
  $arrEstatisticas = DataStore::getEstatisticas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Bistrot POO</title>
    <link rel="stylesheet" href="<?= $view ?>pages/style.css">
  </head>
  <body>
    <div class="main">
      <div class="topbar">
        <div class="topbar-inner">
          <div>
            <h2 class="page-title">Dashboard</h2>
            <p class="page-subtitle">Visão geral do restaurante</p>
          </div>
          <a href="<?= $view ?>pages/pedidos.php" class="btn btn-accent">+ Novo Pedido</a>
        </div>
      </div>

      <div class="content">
        <div class="stats-grid">
          <div class="stat-card">
            <span class="stat-icon">👥</span>
            <div class="stat-value"><?= $arrEstatisticas["totalClientes"] ?></div>
            <div class="stat-label">Clientes</div>
          </div>
          <div class="stat-card">
            <span class="stat-icon">🍕</span>
            <div class="stat-value"><?= $arrEstatisticas["totalProdutos"] ?></div>
            <div class="stat-label">Produtos no Cardápio</div>
          </div>
          <div class="stat-card">
            <span class="stat-icon">🧾</span>
            <div class="stat-value"><?= $arrEstatisticas["totalPedidos"] ?></div>
            <div class="stat-label">Pedidos Realizados</div>
          </div>
          <div class="stat-card">
            <span class="stat-icon">👨‍🍳</span>
            <div class="stat-value"><?= $arrEstatisticas["totalFuncionarios"] ?></div>
            <div class="stat-label">Funcionários</div>
          </div>
          <div class="stat-card highlight">
            <span class="stat-icon">💰</span>
            <div class="stat-value">R$&nbsp;<?= number_format($arrEstatisticas["vlReceita"], 2, ",", ".") ?></div>
            <div class="stat-label">Receita Total (confirmados)</div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">🧾 Pedidos Recentes</span>
            <a href="<?= $view ?>pages/pedidos.php" class="btn btn-outline btn-sm">
              Ver todos
            </a>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Cliente</th>
                  <th>Tipo</th>
                  <th>Itens</th>
                  <th>Desconto</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Data</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($arrEstatisticas["pedidosRecentes"])): ?>
                  <tr><td colspan="8" class="table-empty">Nenhum pedido ainda. <a href="pages/pedidos.php" class="text-accent">Criar primeiro pedido →</a></td></tr>
                <?php else: ?>
                  <?php foreach ($arrEstatisticas["pedidosRecentes"] as $arrPedido): ?>
                    <?php
                      $statusBadge = match($arrPedido["dsStatus"])
                      {
                        "confirmado" => "badge-green",
                        "cancelado"  => "badge-red",
                        "entregue"   => "badge-gold",
                        default      => "badge-orange",
                      };
                    ?>
                    <tr>
                      <td class="mono">#<?= $arrPedido["id"] ?></td>
                      <td>
                        <?= htmlspecialchars($arrPedido["nmCliente"]) ?>
                      </td>
                      <td>
                        <span class="badge <?= $arrPedido["dsTipoCliente"] == "premium" ? "badge-gold" : "badge-muted" ?>">
                          <?= $arrPedido["dsTipoCliente"] == "premium" ? "★ " : "" ?>
                          <?= $arrPedido["dsTipoCliente"] ?>
                        </span>
                      </td>
                      <td>
                        <?= count($arrPedido["arrItens"]) ?> item(s)
                      </td>
                      <td class="price">
                        <?= $arrPedido["vlDesconto"] > 0 ? "-R$ " . number_format($arrPedido["vlDesconto"], 2, ",", ".") : "—" ?>
                      </td>
                      <td class="price">R$ <?= number_format($arrPedido["vlTotal"], 2, ",", ".") ?></td>
                      <td>
                        <span class="badge <?= $statusBadge ?>"><?= $arrPedido["dsStatus"] ?></span>
                      </td>
                      <td class="text-muted text-small">
                        <?= $arrPedido["dtCriacao"] ?>
                      </td>
                    </tr>
                  <?php endforeach ?>
                <?php endif ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>