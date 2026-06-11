<?php

  require_once __DIR__ . "/../config.php";
  include      __DIR__ . "/../includes/nav.php";

  $dsPaginaAtual = "pedidos";
  $dsMsg         = "";
  $dsErro        = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["acao"] == "cancelar")
  {
    $id = (int) $_POST["idPedido"];

    if (Pedido::cancelarPedido($id))
      $dsMsg = "Pedido #{$id} cancelado.";
    else
      $dsErro = "Não foi possível cancelar o pedido #{$id}.";
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["acao"] == "criar")
  {
    $idCliente  = (int) ($_POST["idCliente"] ?? 0);
    $arrIdsProd = $_POST["idProduto"]        ?? [];
    $arrQtProd  = $_POST["qtProduto"]        ?? [];
    $arrItens   = [];

    foreach ($arrIdsProd as $i => $idProduto)
    {
      $qtItens = (int) ($arrQtProd[$i] ?? 0);

      if ($idProduto && $qtItens > 0)
        $arrItens[] = ["idProduto" => (int) $idProduto, "quantidade" => $qtItens];
    }

    if (!$idCliente)
      $dsErro = "Selecione um cliente para o pedido.";
    elseif (empty($arrItens))
      $dsErro = "Adicione pelo menos um item ao pedido.";
    else
    {
      try
      {
        $arrPedidoCriado = Pedido::criarPedido($idCliente, $arrItens);

        $dsMsg  = "Pedido <strong>#{$arrPedidoCriado["id"]}</strong> criado! ";
        $dsMsg .= "Total: <strong>R$ " . number_format($arrPedidoCriado["vlTotal"], 2, ",", ".") . "</strong>";

        if ($arrPedidoCriado["vlDesconto"] > 0)
          $dsMsg .= " (desconto de R$ " . number_format($arrPedidoCriado["vlDesconto"], 2, ",", ".") . " aplicado)";
      }
      catch (Exception $e)
      {
        $dsErro = $e->getMessage();
      }
    }
  }

  $arrPedidos  = array_reverse(Pedido::buscarPedidos());
  $arrClientes = Cliente::buscarClientes();
  $arrProdutos = [];

  foreach (Produto::buscarProduto() as $arrProduto)
  {
    if ($arrProduto["idDisponivel"])
      $arrProdutos[] = $arrProduto;
  }
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedidos — Bistrot POO</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="main">
      <div class="topbar">
        <div class="topbar-inner">
          <div>
            <h2 class="page-title">Pedidos</h2>
            <p class="page-subtitle">Criação e acompanhamento de pedidos</p>
          </div>
        </div>
      </div>

      <div class="content">

        <?php if (!empty($dsMsg)): ?>
          <div class="alert alert-success">✔ <?= $dsMsg ?></div>
        <?php endif ?>

        <?php if (!empty($dsErro)): ?>
          <div class="alert alert-error">✖ <?= htmlspecialchars($dsErro) ?></div>
        <?php endif ?>

        <div class="tabs">
          <button class="tab-btn active" onclick="showTab('lista', this)">🧾 Histórico (<?= count($arrPedidos) ?>)</button>
          <button class="tab-btn" onclick="showTab('novo', this)">＋ Novo Pedido</button>
        </div>

        <div id="tab-lista" class="tab-pane active">
          <div class="card">
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
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($arrPedidos)): ?>
                    <tr>
                      <td colspan="9" class="table-empty">
                        Nenhum pedido registrado.<br>
                        <button class="btn btn-accent btn-sm mt-4" onclick="showTab('novo', document.querySelectorAll('.tab-btn')[1])">
                          Criar primeiro pedido
                        </button>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($arrPedidos as $arrPedido): ?>
                      <?php
                        $dsBadgeStatus = match($arrPedido["dsStatus"])
                        {
                          "confirmado" => "badge-green",
                          "cancelado"  => "badge-red",
                          "entregue"   => "badge-gold",
                          default      => "badge-orange"
                        };
                      ?>
                      <tr>
                        <td class="mono">#<?= $arrPedido["id"] ?></td>
                        <td><strong><?= htmlspecialchars($arrPedido["nmCliente"]) ?></strong></td>
                        <td>
                          <span class="badge <?= $arrPedido["dsTipoCliente"] == "premium" ? "badge-gold" : "badge-muted" ?>">
                            <?= $arrPedido["dsTipoCliente"] == "premium" ? "★ " : "" ?><?= $arrPedido["dsTipoCliente"] ?>
                          </span>
                        </td>
                        <td>
                          <ul class="itens-list">
                            <?php foreach ($arrPedido["arrItens"] as $arrItem): ?>
                              <li><?= htmlspecialchars($arrItem["nmProduto"]) ?> ×<?= $arrItem["qtItens"] ?></li>
                            <?php endforeach ?>
                          </ul>
                        </td>
                        <td class="price">
                          <?php if ($arrPedido["vlDesconto"] > 0): ?>
                            -R$ <?= number_format($arrPedido["vlDesconto"], 2, ",", ".") ?>
                          <?php else: ?>
                            <span style="color:var(--text-faint)">—</span>
                          <?php endif ?>
                        </td>
                        <td class="price">R$ <?= number_format($arrPedido["vlTotal"], 2, ",", ".") ?></td>
                        <td><span class="badge <?= $dsBadgeStatus ?>"><?= $arrPedido["dsStatus"] ?></span></td>
                        <td class="text-muted text-small"><?= $arrPedido["dtCriacao"] ?></td>
                        <td>
                          <?php if ($arrPedido["dsStatus"] == "confirmado"): ?>
                            <form method="POST" style="display:inline"
                              onsubmit="return confirm('Cancelar pedido #<?= $arrPedido["id"] ?>?')">
                              <input type="hidden" name="acao" value="cancelar">
                              <input type="hidden" name="idPedido" value="<?= $arrPedido["id"] ?>">
                              <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                            </form>
                          <?php endif ?>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  <?php endif ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div id="tab-novo" class="tab-pane">
          <?php if (empty($arrClientes)): ?>
            <div class="alert alert-warning">
              ⚠ Cadastre pelo menos um cliente antes de criar pedidos.
              <a href="clientes.php" class="btn btn-outline btn-sm" style="margin-left:12px">Ir para Clientes</a>
            </div>
          <?php elseif (empty($arrProdutos)): ?>
            <div class="alert alert-warning">
              ⚠ Adicione produtos disponíveis ao cardápio antes de criar pedidos.
              <a href="produtos.php" class="btn btn-outline btn-sm" style="margin-left:12px">Ir para Cardápio</a>
            </div>
          <?php else: ?>
            <div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

              <div class="card">
                <div class="card-header">
                  <span class="card-title">🧾 Novo Pedido</span>
                </div>
                <div class="card-body">
                  <form method="POST" id="form-pedido">
                    <input type="hidden" name="acao" value="criar">

                    <div class="form-group mb-4">
                      <label for="idCliente">Cliente *</label>
                      <select id="idCliente" name="idCliente" required onchange="recalcular()">
                        <option value="">— Selecione —</option>
                        <?php foreach ($arrClientes as $arrCliente): ?>
                          <option value="<?= $arrCliente["id"] ?>"
                            data-tipo="<?= $arrCliente["dsTipoCliente"] ?>"
                            data-pontos="<?= $arrCliente["nrPontosFidelidade"] ?? 0 ?>">
                            <?= htmlspecialchars($arrCliente["nmPessoa"]) ?>
                            <?= $arrCliente["dsTipoCliente"] == "premium" ? "★" : "" ?>
                            (<?= $arrCliente["nrPontosFidelidade"] ?? 0 ?> pts)
                          </option>
                        <?php endforeach ?>
                      </select>
                    </div>

                    <hr class="divider">

                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                      <label style="text-transform:uppercase; font-size:.78rem; font-weight:600; color:var(--text-muted);">
                        Itens do Pedido
                      </label>
                      <button type="button" class="btn btn-outline btn-sm" onclick="adicionarItem()">
                        ＋ Item
                      </button>
                    </div>

                    <div id="itens-container"></div>

                    <div id="pedido-summary" class="pedido-summary" style="display:none">
                      <div class="pedido-summary-row">
                        <span>Subtotal</span>
                        <span id="sum-subtotal">R$ 0,00</span>
                      </div>
                      <div class="pedido-summary-row" id="sum-desconto-row" style="display:none">
                        <span>Desconto</span>
                        <span id="sum-desconto" style="color:var(--success)"></span>
                      </div>
                      <div class="pedido-summary-row total">
                        <span>Total Estimado</span>
                        <span id="sum-total">R$ 0,00</span>
                      </div>
                    </div>

                    <div style="margin-top:20px;">
                      <button type="submit" class="btn btn-primary w-full">Confirmar Pedido</button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="card">
                <div class="card-header">
                  <span class="card-title">🍕 Cardápio</span>
                </div>
                <div style="max-height:500px; overflow-y:auto;">
                  <table>
                    <tbody>
                      <?php foreach ($arrProdutos as $arrProduto): ?>
                        <tr style="cursor:pointer"
                          onclick="adicionarItemProduto(<?= $arrProduto["id"] ?>, <?= htmlspecialchars(json_encode($arrProduto["nmProduto"])) ?>, <?= $arrProduto["vlPreco"] ?>)">
                          <td>
                            <div style="font-size:.88rem; font-weight:500;"><?= htmlspecialchars($arrProduto["nmProduto"]) ?></div>
                            <div style="font-size:.75rem; color:var(--text-muted);"><?= htmlspecialchars($arrProduto["dsCategoria"]) ?></div>
                          </td>
                          <td class="price" style="white-space:nowrap;">
                            R$ <?= number_format($arrProduto["vlPreco"], 2, ",", ".") ?>
                          </td>
                        </tr>
                      <?php endforeach ?>
                    </tbody>
                  </table>
                </div>
                <div style="padding:10px 14px; font-size:.75rem; color:var(--text-faint); border-top:1px solid var(--border);">
                  Clique em um item para adicioná-lo
                </div>
              </div>

            </div>
          <?php endif ?>
        </div>

      </div>
    </div>

    <script>
      var arrProdutos  = <?= json_encode(array_values($arrProdutos), JSON_UNESCAPED_UNICODE) ?>;
      var objProdutos  = {};
      var nrContador   = 0;

      arrProdutos.forEach(function(p) { objProdutos[p.id] = p; });

      function adicionarItem(idProduto, nmProduto, vlPreco)
      {
        idProduto = idProduto || "";
        vlPreco   = vlPreco   || 0;

        var objContainer = document.getElementById("itens-container");
        var nrIdx        = nrContador++;

        var dsOptions = arrProdutos.map(function(p)
        {
          return "<option value=\"" + p.id + "\" data-preco=\"" + p.vlPreco + "\" " + (p.id == idProduto ? "selected" : "") + ">"
            + p.nmProduto + " — R$ " + parseFloat(p.vlPreco).toFixed(2).replace(".", ",")
            + "</option>";
        }).join("");

        var objLinha       = document.createElement("div");
        objLinha.className = "pedido-item-row";
        objLinha.id        = "item-" + nrIdx;
        objLinha.innerHTML =
          "<select name=\"idProduto[]\" onchange=\"recalcular()\" required>"
            + "<option value=\"\">— Produto —</option>"
            + dsOptions
          + "</select>"
          + "<input type=\"number\" name=\"qtProduto[]\" value=\"1\" min=\"1\" max=\"99\" placeholder=\"Qtd\" oninput=\"recalcular()\">"
          + "<button type=\"button\" class=\"remove-item-btn\" onclick=\"removerItem(" + nrIdx + ")\">✕</button>";

        objContainer.appendChild(objLinha);
        recalcular();
      }

      function adicionarItemProduto(idProduto, nmProduto, vlPreco)
      {
        adicionarItem(idProduto, nmProduto, vlPreco);
        document.getElementById("form-pedido").scrollIntoView({ behavior: "smooth", block: "end" });
      }

      function removerItem(nrIdx)
      {
        var objLinha = document.getElementById("item-" + nrIdx);

        if (objLinha)
          objLinha.remove();

        recalcular();
      }

      function recalcular()
      {
        var arrSelects    = document.querySelectorAll("[name=\"idProduto[]\"]");
        var arrQuantidades = document.querySelectorAll("[name=\"qtProduto[]\"]");
        var vlSubtotal    = 0;

        arrSelects.forEach(function(objSelect, i)
        {
          var idProduto = parseInt(objSelect.value);
          var qtItens   = parseInt(arrQuantidades[i] ? arrQuantidades[i].value : 0);

          if (idProduto && objProdutos[idProduto] && qtItens > 0)
            vlSubtotal += objProdutos[idProduto].vlPreco * qtItens;
        });

        var objClienteSel = document.getElementById("idCliente");
        var opSelecionada = objClienteSel.selectedOptions[0];
        var vlDesconto    = 0;

        if (opSelecionada)
        {
          var dsTipo   = opSelecionada.dataset.tipo;
          var nrPontos = parseInt(opSelecionada.dataset.pontos || 0);

          if (dsTipo == "premium")
            vlDesconto = vlSubtotal * 0.10;
          else if (nrPontos >= 100)
            vlDesconto = vlSubtotal * 0.05;
        }

        var vlTotal = Math.max(0, vlSubtotal - vlDesconto);
        var fmtBR   = function(v) { return "R$ " + v.toFixed(2).replace(".", ","); };

        document.getElementById("sum-subtotal").textContent = fmtBR(vlSubtotal);
        document.getElementById("sum-total").textContent    = fmtBR(vlTotal);

        var objDescontoRow = document.getElementById("sum-desconto-row");

        objDescontoRow.style.display = "none";

        if (vlDesconto > 0)
        {
          objDescontoRow.style.display = "flex";
          document.getElementById("sum-desconto").textContent = "−" + fmtBR(vlDesconto);
        }

        document.getElementById("pedido-summary").style.display = arrSelects.length > 0 ? "block" : "none";
      }

      function showTab(nmTab, objBotao)
      {
        document.querySelectorAll(".tab-pane").forEach(function(p) { p.classList.remove("active"); });
        document.querySelectorAll(".tab-btn").forEach(function(b) { b.classList.remove("active"); });
        document.getElementById("tab-" + nmTab).classList.add("active");

        if (objBotao)
          objBotao.classList.add("active");

        if (nmTab == "novo" && document.getElementById("itens-container").children.length == 0)
          adicionarItem();
      }

      document.addEventListener("DOMContentLoaded", function()
      {
        if (document.getElementById("tab-novo").classList.contains("active"))
          adicionarItem();
      });
    </script>
  </body>
</html>