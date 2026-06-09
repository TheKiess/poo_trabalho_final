<?php

  require_once __DIR__ . "/../config.php";
  include      __DIR__ . "/../includes/nav.php";

  $dsPaginaAtual = "clientes";
  $dsMsg         = "";
  $dsErro        = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["acao"] == "cadastrar")
  {
    $nmPessoa      = trim($_POST["nmPessoa"]);
    $dsCpf         = trim($_POST["dsCpf"]);
    $dsEmail       = trim($_POST["dsEmail"]);
    $dsTipoCliente = $_POST["dsTipoCliente"];

    if (!$nmPessoa || !$dsCpf || !$dsEmail)
      $dsErro = "Preencha todos os campos obrigatórios.";
    else
    {
      (new Cliente($nmPessoa, $dsCpf, $dsEmail, $dsTipoCliente))->salvarCliente();
      $dsMsg = "Cliente <strong>{$nmPessoa}</strong> cadastrado com sucesso!";
    }
  }

  $arrClientes = Cliente::buscarClientes();
?>

<!DOCTYPE html>
  <html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clientes — Bistrot POO</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>

  <div class="main">
    <div class="topbar">
      <div class="topbar-inner">
        <div>
          <h2 class="page-title">Clientes</h2>
          <p class="page-subtitle">Cadastro e gerenciamento de clientes</p>
        </div>
      </div>
    </div>

    <div class="content">

      <?php if ($dsMsg): ?>
        <div class="alert alert-success">✔ <?= $dsMsg ?></div>
      <?php endif ?>
      <?php if ($dsErro): ?>
        <div class="alert alert-error">✖ <?= htmlspecialchars($dsErro) ?></div>
      <?php endif ?>

      <div class="tabs">
        <button class="tab-btn active" onclick="showTab('lista', this)">👥 Lista de Clientes (<?= count($arrClientes) ?>)</button>
        <button class="tab-btn" onclick="showTab('cadastro', this)">＋ Novo Cliente</button>
      </div>

      <div id="tab-lista" class="tab-pane active">
        <div class="card">
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nome</th>
                  <th>CPF</th>
                  <th>E-mail</th>
                  <th>Tipo</th>
                  <th>Pontos Fidelidade</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($arrClientes)): ?>
                  <tr><td colspan="6" class="table-empty">
                    Nenhum cliente cadastrado ainda.<br>
                    <button class="btn btn-outline btn-sm mt-4" onclick="showTab('cadastro', document.querySelectorAll('.tab-btn')[1])">
                      Cadastrar primeiro cliente
                    </button>
                  </td></tr>
                <?php else: ?>
                  <?php foreach ($arrClientes as $arrCliente): ?>
                    <tr>
                      <td class="mono">#<?= $arrCliente["id"] ?></td>
                      <td><strong><?= htmlspecialchars($arrCliente["nmPessoa"]) ?></strong></td>
                      <td class="mono"><?= htmlspecialchars($arrCliente["dsCpf"]) ?></td>
                      <td class="text-muted"><?= htmlspecialchars($arrCliente["dsEmail"]) ?></td>
                      <td>
                        <?php if ($arrCliente["dsTipoCliente"] == "premium"): ?>
                          <span class="badge badge-gold">★ Premium</span>
                        <?php else: ?>
                          <span class="badge badge-muted">Comum</span>
                        <?php endif ?>
                      </td>
                      <td>
                        <span style="font-family:'JetBrains Mono', monospace; font-size:.85rem; color:var(--accent);">
                          <?= $arrCliente["nrPontosFidelidade"] ?? 0 ?> pts
                        </span>
                      </td>
                    </tr>
                  <?php endforeach ?>
                <?php endif ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="tab-cadastro" class="tab-pane">
        <div class="card" style="max-width:640px">
          <div class="card-header">
            <span class="card-title">👤 Novo Cliente</span>
          </div>
          <div class="card-body">
            <form method="POST">
              <input type="hidden" name="acao" value="cadastrar">
              <div class="form-grid cols-2">
                <div class="form-group full">
                  <label for="nmPessoa">Nome Completo *</label>
                  <input type="text" id="nmPessoa" name="nmPessoa"
                        placeholder="Ex: Maria da Silva" required>
                </div>
                <div class="form-group">
                  <label for="dsCpf">CPF *</label>
                  <input type="text" id="dsCpf" name="dsCpf"
                        placeholder="000.000.000-00" maxlength="14" required>
                </div>
                <div class="form-group">
                  <label for="dsEmail">E-mail *</label>
                  <input type="email" id="dsEmail" name="dsEmail"
                        placeholder="cliente@email.com" required>
                </div>
                <div class="form-group full">
                  <label for="dsTipoCliente">Tipo de Cliente</label>
                  <select id="dsTipoCliente" name="dsTipoCliente">
                    <option value="comum">Comum</option>
                    <option value="premium">⭐ Premium (10% de desconto nos pedidos)</option>
                  </select>
                </div>
              </div>
              <div style="margin-top:20px; display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Cadastrar Cliente</button>
                <button type="button" class="btn btn-outline" onclick="showTab('lista', document.querySelectorAll('.tab-btn')[0])">Cancelar</button>
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
      document.querySelectorAll(".tab-pane").forEach(function(p) { p.classList.remove("active"); });
      document.querySelectorAll(".tab-btn").forEach(function(b) { b.classList.remove("active"); });
      document.getElementById("tab-" + nmTab).classList.add("active");

      if (objBotao)
        objBotao.classList.add("active");
    }

    <?php if ($dsMsg): ?>
      document.querySelectorAll(".tab-btn")[0].classList.add("active");
      document.querySelectorAll(".tab-btn")[1].classList.remove("active");
    <?php endif ?>
  </script>

  </body>
</html>