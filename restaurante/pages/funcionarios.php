<?php

  require_once __DIR__ . "/../config.php";
  include      __DIR__ . "/../includes/nav.php";

  $dsPaginaAtual      = "funcionarios";
  $dsMsg              = "";
  $dsErro             = "";
  $vlBonus            = null;
  $arrFuncionarioBonus = null;

  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    $dsAcao = $_POST["acao"];

    if ($dsAcao == "cadastrar")
    {
      $nmPessoa  = trim($_POST["nmPessoa"]);
      $dsCpf     = trim($_POST["dsCpf"]);
      $dsEmail   = trim($_POST["dsEmail"]);
      $dsCargo   = trim($_POST["dsCargo"]);
      $vlSalario = (float) str_replace(",", ".", $_POST["vlSalario"]);

      if (!$nmPessoa || !$dsCpf || !$dsEmail || !$dsCargo || $vlSalario <= 0)
        $dsErro = "Preencha todos os campos com valores válidos.";
      else
      {
        (new Funcionario($nmPessoa, $dsCpf, $dsEmail, $dsCargo, $vlSalario))->salvarFuncionario();
        $dsMsg = "Funcionário <strong>{$nmPessoa}</strong> cadastrado com sucesso!";
      }
    }

    if ($dsAcao == "bonus")
    {
      $idFuncionario = (int) $_POST["idFuncionario"];
      $prBonus       = (float) str_replace(",", ".", $_POST["prBonus"]);

      try
      {
        $vlBonus             = Funcionario::calcularBonus($idFuncionario, $prBonus);
        $arrFuncionarioBonus = Funcionario::buscarFuncionario($idFuncionario);
      }
      catch (InvalidArgumentException $e)
      {
        $dsErro = $e->getMessage();
      }
      catch (RuntimeException $e)
      {
        $dsErro = $e->getMessage();
      }
    }
  }

  $arrFuncionarios = Funcionario::buscarFuncionario();

  $dsTabAtiva = match(true)
  {
    $dsErro != "" && ($_POST["acao"] ?? "") == "cadastrar" => "cadastro",
    $dsMsg  != ""                                          => "lista",
    default                                                => "lista"
  };
?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Funcionários — Bistrot POO</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="main">
      <div class="topbar">
        <div class="topbar-inner">
          <div>
            <h2 class="page-title">Funcionários</h2>
            <p class="page-subtitle">Cadastro e cálculo de bônus salarial</p>
          </div>
        </div>
      </div>
      <div class="content">
        <?php if (!empty($dsMsg)): ?>
          <div class="alert alert-success">✔ <?= $dsMsg ?></div>
        <?php endif ?>

        <?php if (!empty($dsErro) && ($_POST["acao"] ?? "") != "bonus"): ?>
          <div class="alert alert-error">✖ <?= htmlspecialchars($dsErro) ?></div>
        <?php endif ?>

        <div class="tabs">
          <button class="tab-btn <?= $dsTabAtiva == "lista" ? "active" : "" ?>" onclick="showTab('lista', this)">
            👨‍🍳 Funcionários (<?= count($arrFuncionarios) ?>)
          </button>
          <button class="tab-btn <?= $dsTabAtiva == "cadastro" ? "active" : "" ?>" onclick="showTab('cadastro', this)">
            ＋ Cadastrar
          </button>
          <button class="tab-btn <?= ($_POST["acao"] ?? "") == "bonus" ? "active" : "" ?>" onclick="showTab('bonus', this)">
            🧮 Calcular Bônus
          </button>
        </div>

        <div id="tab-lista" class="tab-pane <?= $dsTabAtiva == "lista" ? "active" : "" ?>">
          <div class="card">
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>E-mail</th>
                    <th>Cargo</th>
                    <th>Salário</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($arrFuncionarios)): ?>
                    <tr>
                      <td colspan="6" class="table-empty">
                        Nenhum funcionário cadastrado ainda.<br>
                        <button class="btn btn-outline btn-sm mt-4" onclick="showTab('cadastro', document.querySelectorAll('.tab-btn')[1])">
                          Cadastrar primeiro funcionário
                        </button>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($arrFuncionarios as $arrFunc): ?>
                      <tr>
                        <td class="mono">#<?= $arrFunc["id"] ?></td>
                        <td><strong><?= htmlspecialchars($arrFunc["nmPessoa"]) ?></strong></td>
                        <td class="mono"><?= htmlspecialchars($arrFunc["dsCpf"]) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($arrFunc["dsEmail"]) ?></td>
                        <td>
                          <span class="badge badge-muted"><?= htmlspecialchars($arrFunc["dsCargo"]) ?></span>
                        </td>
                        <td class="price">
                          R$ <?= number_format((float) $arrFunc["vlSalario"], 2, ",", ".") ?>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  <?php endif ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div id="tab-cadastro" class="tab-pane <?= $dsTabAtiva == "cadastro" ? "active" : "" ?>">
          <div class="card" style="max-width:640px">
            <div class="card-header">
              <span class="card-title">👨‍🍳 Novo Funcionário</span>
            </div>
            <div class="card-body">
              <form method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-grid cols-2">
                  <div class="form-group full">
                    <label for="nmPessoa">Nome Completo *</label>
                    <input type="text" id="nmPessoa" name="nmPessoa" placeholder="Ex: Carlos Lima"
                      value="<?= htmlspecialchars($_POST["nmPessoa"] ?? "") ?>" required
                    >
                  </div>

                  <div class="form-group">
                    <label for="dsCpf">CPF *</label>
                    <input type="text" id="dsCpf" name="dsCpf" placeholder="000.000.000-00"
                      maxlength="14" value="<?= htmlspecialchars($_POST["dsCpf"] ?? "") ?>" required
                    >
                  </div>

                  <div class="form-group">
                    <label for="dsEmail">E-mail *</label>
                    <input type="email" id="dsEmail" name="dsEmail" placeholder="funcionario@bistrot.com"
                      value="<?= htmlspecialchars($_POST["dsEmail"] ?? "") ?>" required
                    >
                  </div>

                  <div class="form-group">
                    <label for="dsCargo">Cargo *</label>
                    <input type="text" id="dsCargo" name="dsCargo" placeholder="Ex: Garçom, Cozinheiro..."
                      list="cargos-list" value="<?= htmlspecialchars($_POST["dsCargo"] ?? "") ?>" required
                    >
                    <datalist id="cargos-list">
                      <option value="Gerente">
                      <option value="Cozinheiro">
                      <option value="Garçom">
                      <option value="Caixa">
                      <option value="Auxiliar de Cozinha">
                      <option value="Barman">
                      <option value="Recepcionista">
                    </datalist>
                  </div>

                  <div class="form-group">
                    <label for="vlSalario">Salário (R$) *</label>
                    <input type="number" id="vlSalario" name="vlSalario" placeholder="0.00"
                      step="0.01" min="0.01" value="<?= htmlspecialchars($_POST["vlSalario"] ?? "") ?>" required>
                  </div>
                </div>

                <div style="margin-top:20px; display:flex; gap:10px;">
                  <button type="submit" class="btn btn-primary">Cadastrar Funcionário</button>
                  <button type="button" class="btn btn-outline" onclick="showTab('lista', document.querySelectorAll('.tab-btn')[0])">
                    Cancelar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div id="tab-bonus" class="tab-pane <?= ($_POST["acao"] ?? "") == "bonus" ? "active" : "" ?>">
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">
            <div class="card">
              <div class="card-header">
                <span class="card-title">🧮 Calcular Bônus Salarial</span>
              </div>
              <div class="card-body">
                <?php if (empty($arrFuncionarios)): ?>
                  <div class="alert alert-warning">
                    ⚠ Cadastre funcionários antes de calcular bônus.
                  </div>
                <?php else: ?>
                  <form method="POST">
                    <input type="hidden" name="acao" value="bonus">

                    <div class="form-group mb-4">
                      <label for="idFuncionario">Funcionário *</label>
                      <select id="idFuncionario" name="idFuncionario" required>
                        <option value="">— Selecione —</option>
                        <?php foreach ($arrFuncionarios as $arrFunc): ?>
                          <option value="<?= $arrFunc["id"] ?>" data-salario="<?= $arrFunc["vlSalario"] ?>"
                            <?= (int) ($_POST["idFuncionario"] ?? 0) == $arrFunc["id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($arrFunc["nmPessoa"]) ?> — <?= htmlspecialchars($arrFunc["dsCargo"]) ?>
                            (R$ <?= number_format((float) $arrFunc["vlSalario"], 2, ",", ".") ?>)
                          </option>
                        <?php endforeach ?>
                      </select>
                    </div>

                    <div class="form-group mb-4">
                      <label for="prBonus">Percentual de Bônus (%) *</label>
                      <input type="number" id="prBonus" name="prBonus" placeholder="Ex: 10" step="0.1" min="0.1"
                        max="100" value="<?= htmlspecialchars($_POST["prBonus"] ?? "") ?>" required oninput="visualizarBonus()"
                      >
                    </div>

                    <div id="preview-bonus" class="bonus-result" style="display:none; margin-bottom:16px;">
                      Preview: <strong id="preview-valor"></strong>
                    </div>

                    <?php if (!empty($dsErro) && ($_POST["acao"] ?? "") == "bonus"): ?>
                      <div class="alert alert-error" style="margin-bottom:16px;">
                        ✖ <?= htmlspecialchars($dsErro) ?>
                      </div>
                    <?php endif ?>

                    <button type="submit" class="btn btn-accent w-full">Calcular Bônus</button>
                  </form>
                <?php endif ?>
              </div>
            </div>

            <div>
              <?php if ($vlBonus != null && $arrFuncionarioBonus != null): ?>
                <div class="card">
                  <div class="card-header">
                    <span class="card-title">✔ Resultado</span>
                  </div>
                  <div class="card-body">
                    <table style="width:100%">
                      <tbody>
                        <tr>
                          <td class="text-muted text-small" style="padding:8px 0;">Funcionário</td>
                          <td style="padding:8px 0; font-weight:600;">
                            <?= htmlspecialchars($arrFuncionarioBonus["nmPessoa"]) ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-muted text-small" style="padding:8px 0;">Cargo</td>
                          <td style="padding:8px 0;">
                            <span class="badge badge-muted">
                              <?= htmlspecialchars($arrFuncionarioBonus["dsCargo"]) ?>
                            </span>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-muted text-small" style="padding:8px 0;">Salário Base</td>
                          <td class="price" style="padding:8px 0;">
                            R$ <?= number_format((float) $arrFuncionarioBonus["vlSalario"], 2, ",", ".") ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-muted text-small" style="padding:8px 0;">Percentual</td>
                          <td style="padding:8px 0; color:var(--text);">
                            <?= htmlspecialchars($_POST["prBonus"] ?? "") ?>%
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <hr class="divider">

                    <div style="text-align:center; padding:12px 0;">
                      <div style="font-size:.8rem; color:var(--text-muted); margin-bottom:6px;">
                        Valor do Bônus
                      </div>
                      <div style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; font-weight:700; color:var(--accent);">
                        R$ <?= number_format($vlBonus, 2, ",", ".") ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php else: ?>
                <div class="card" style="text-align:center; padding:40px; opacity:.5;">
                  <div style="font-size:2rem; margin-bottom:12px;">🧮</div>
                  <div style="color:var(--text-muted); font-size:.88rem;">
                    Selecione um funcionário e informe o percentual para calcular.
                  </div>
                </div>
              <?php endif ?>
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

      function visualizarBonus()
      {
        var prBonus         = parseFloat(document.getElementById('prBonus').value);
        var opIdFuncionario = document.getElementById('idFuncionario').selectedOptions[0];
        var objPrevisao     = document.getElementById('preview-bonus');

        if (!opIdFuncionario.dataset.salario || prBonus <= 0)
        {
          objPrevisao.style.display = 'none';
          return;
        }

        var bonusFormatado = 'R$ ' + (parseFloat(opIdFuncionario.dataset.salario) * (prBonus / 100)).toFixed(2).replace('.', ',');

        document.getElementById('preview-valor').textContent = bonusFormatado;
        objPrevisao.style.display = 'block';
      }

      document.getElementById('idFuncionario').addEventListener('change', visualizarBonus);
    </script>
  </body>
</html>