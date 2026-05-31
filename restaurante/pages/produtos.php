<?php

  require_once __DIR__ . "/../config.php";
  include      __DIR__ . "/../includes/nav.php";

  $dsPaginaAtual = "produtos";
  $dsMsg         = "";
  $dsErro        = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["acao"] == "toggle")
  {
    $id = (int) ($_POST["idProduto"] ?? 0);

    if ($id > 0)
      DataStore::toggleDisponibilidade($id);

    header("Location: produtos.php");
    exit;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["acao"] == "cadastrar")
  {
    $nmProduto   = trim($_POST["nmProduto"]                          ?? "");
    $dsProduto   = trim($_POST["dsProduto"]                          ?? "");
    $vlPreco     = (float) str_replace(",", ".", $_POST["vlPreco"]   ?? "0");
    $dsCategoria = trim($_POST["dsCategoria"]                        ?? "");

    if (!$nmProduto || !$dsCategoria || $vlPreco <= 0)
      $dsErro = "Preencha nome, categoria e um preço válido.";
    else
    {
      DataStore::salvarProduto($nmProduto, $dsProduto, $vlPreco, $dsCategoria);
      $dsMsg = "Produto <strong>{$nmProduto}</strong> adicionado ao cardápio!";
    }
  }

  $arrProdutos    = DataStore::getProdutos();
  $arrCategorias  = array_unique(array_column($arrProdutos, "dsCategoria"));
  sort($arrCategorias);

  $arrPorCategoria = [];

  foreach ($arrProdutos as $arrProduto)
    $arrPorCategoria[$arrProduto["dsCategoria"]][] = $arrProduto;
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cardápio — Bistrot POO</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="main">
      <div class="topbar">
        <div class="topbar-inner">
          <div>
            <h2 class="page-title">Cardápio</h2>
            <p class="page-subtitle">Produtos disponíveis — <?= count($arrProdutos) ?> itens cadastrados</p>
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
          <button class="tab-btn active" onclick="showTab('cardapio', this)">🍕 Cardápio</button>
          <button class="tab-btn" onclick="showTab('form', this)">＋ Adicionar Produto</button>
        </div>

        <div id="tab-cardapio" class="tab-pane active">
          <?php if (empty($arrProdutos)): ?>
            <div class="card" style="text-align:center; padding:48px">
              <p style="color:var(--text-faint); font-size:1.1rem;">Cardápio vazio.</p>
              <button class="btn btn-accent mt-4" onclick="showTab('form', document.querySelectorAll('.tab-btn')[1])">
                Adicionar primeiro produto
              </button>
            </div>
          <?php else: ?>
            <?php foreach ($arrPorCategoria as $dsCategoria => $arrItens): ?>
              <div style="margin-bottom:28px;">
                <h3 style="color:var(--text-muted); margin-bottom:14px; letter-spacing:.05em; text-transform:uppercase; font-size:.85rem;">
                  📂 <?= htmlspecialchars($dsCategoria) ?>
                  <span style="color:var(--text-faint); font-size:.75rem; font-family:'Outfit',sans-serif;">
                    (<?= count($arrItens) ?>)
                  </span>
                </h3>
                <div class="produto-grid">
                  <?php foreach ($arrItens as $arrProduto): ?>
                    <div class="produto-card <?= !$arrProduto["idDisponivel"] ? "indisponivel" : "" ?>">
                      <div class="produto-card-top">
                        <div>
                          <div class="produto-card-name"><?= htmlspecialchars($arrProduto["nmProduto"]) ?></div>
                          <?php if (!empty($arrProduto["dsProduto"])): ?>
                            <div class="produto-card-desc"><?= htmlspecialchars($arrProduto["dsProduto"]) ?></div>
                          <?php endif ?>
                        </div>
                        <span class="badge badge-muted" style="margin-left:8px; flex-shrink:0;">
                          #<?= $arrProduto["id"] ?>
                        </span>
                      </div>
                      <div class="produto-card-bottom">
                        <span class="produto-price">R$ <?= number_format($arrProduto["vlPreco"], 2, ",", ".") ?></span>
                        <form method="POST" class="toggle-form">
                          <input type="hidden" name="acao" value="toggle">
                          <input type="hidden" name="idProduto" value="<?= $arrProduto["id"] ?>">
                          <button type="submit" class="toggle-btn <?= $arrProduto["idDisponivel"] ? "available" : "unavailable" ?>">
                            <?= $arrProduto["idDisponivel"] ? "✔ Disponível" : "✖ Indisponível" ?>
                          </button>
                        </form>
                      </div>
                    </div>
                  <?php endforeach ?>
                </div>
              </div>
            <?php endforeach ?>
          <?php endif ?>
        </div>

        <div id="tab-form" class="tab-pane">
          <div class="card" style="max-width:640px">
            <div class="card-header">
              <span class="card-title">🍕 Novo Produto</span>
            </div>
            <div class="card-body">
              <form method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-grid cols-2">

                  <div class="form-group full">
                    <label for="nmProduto">Nome do Produto *</label>
                    <input type="text" id="nmProduto" name="nmProduto"
                      placeholder="Ex: Pizza Margherita" required>
                  </div>

                  <div class="form-group full">
                    <label for="dsProduto">Descrição</label>
                    <textarea id="dsProduto" name="dsProduto"
                      placeholder="Ingredientes, preparo, detalhes..."></textarea>
                  </div>

                  <div class="form-group">
                    <label for="vlPreco">Preço (R$) *</label>
                    <input type="number" id="vlPreco" name="vlPreco"
                      placeholder="0.00" step="0.01" min="0.01" required>
                  </div>

                  <div class="form-group">
                    <label for="dsCategoria">Categoria *</label>
                    <input type="text" id="dsCategoria" name="dsCategoria"
                      placeholder="Ex: Pizza, Lanche, Bebida..." list="categorias-list" required>
                    <datalist id="categorias-list">
                      <?php foreach ($arrCategorias as $dsCategoria): ?>
                        <option value="<?= htmlspecialchars($dsCategoria) ?>">
                      <?php endforeach ?>
                      <option value="Pizza">
                      <option value="Lanche">
                      <option value="Bebida">
                      <option value="Sobremesa">
                      <option value="Entrada">
                      <option value="Prato Principal">
                    </datalist>
                  </div>

                </div>

                <div style="margin-top:20px; display:flex; gap:10px;">
                  <button type="submit" class="btn btn-primary">Adicionar ao Cardápio</button>
                  <button type="button" class="btn btn-outline" onclick="showTab('cardapio', document.querySelectorAll('.tab-btn')[0])">
                    Cancelar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>

    <script>
      function showTab(nmTab, objBotao)
      {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + nmTab).classList.add('active');

        if (objBotao)
          objBotao.classList.add('active');
      }
    </script>
  </body>
</html>