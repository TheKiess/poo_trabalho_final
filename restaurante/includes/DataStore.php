<?php

  /**
   * DataStore
   *
   * Armazena dados como arquivos JSON (Pasta ../data/).
   * Usa as classes de domínio (Cliente, Pedido, etc.) para aplicar
   * regras de negócio antes de persistir os resultados.
   */
  class DataStore
  {
    /**
     * Garante que a pasta de dados exista e retorna seu caminho.
     *
     * @return string
     */
    private static function dir(): string
    {
      $dsDiretorio = BASE_PATH . "/data/";

      if (!is_dir($dsDiretorio))
        mkdir($dsDiretorio, 0755, true);

      return $dsDiretorio;
    }

    /**
     * @param string $dsArquivo
     * @return array
     */
    private static function carregarArquivo(string $dsArquivo = ""): array
    {
      $dsCaminhoArquivo = self::dir() . $dsArquivo;

      if (!file_exists($dsCaminhoArquivo))
        return [];

      return json_decode(file_get_contents($dsCaminhoArquivo), true);
    }

    /**
     * @param string $dsArquivo
     * @param array $arrDados
     * @return void
     */
    private static function salvarConteudo(string $dsArquivo, array $arrDados): void
    {
      file_put_contents(
        self::dir() . $dsArquivo,
        json_encode($arrDados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
      );
    }

    /**
     * @param string $dsArquivo
     * @return int
     */
    private static function incrementarProximoId(string $dsArquivo = ""): int
    {
      if (empty($dsArquivo))
        return 1;

      $arrDados = self::carregarArquivo($dsArquivo);

      if (empty($arrDados))
        return 1;

      return max(array_column($arrDados, "id")) + 1;
    }

    /**
     * @return array
     */
    public static function getClientes(): array
    {
      return self::carregarArquivo("clientes.json");
    }

    /**
     * @param mixed $idCliente
     */
    public static function getClienteById(?int $idCliente): ?array
    {
      foreach (self::getClientes() as $arrCliente)
        if ((int) $arrCliente["id"] == $idCliente)
          return $arrCliente;

      return null;
    }

    /**
     * @param string $nmPessoa
     * @param string $dsCpf
     * @param string $dsEmail
     * @param string $dsTipoCliente
     * @return array
     */
    public static function salvarCliente(string $nmPessoa, string $dsCpf, string $dsEmail, string $dsTipoCliente): array
    {
      $cliente = new Cliente($nmPessoa, $dsCpf, $dsEmail, $dsTipoCliente);

      $arrRegistroCliente = array_merge(["id" => self::incrementarProximoId("clientes.json")], $cliente->toArray());

      $arrClientes   = self::getClientes();
      $arrClientes[] = $arrRegistroCliente;
      self::salvarConteudo("clientes.json", $arrClientes);

      return $arrRegistroCliente;
    }

    /**
     * @param null|int $idCliente
     * @param null|int $nrPontos
     * @return void
     */
    public static function atualizarPontosCliente(?int $idCliente, ?int $nrPontos): void
    {
      $arrClientes = self::getClientes();

      foreach ($arrClientes as &$arrCliente)
      {
        if ((int) $arrCliente["id"] != $idCliente)
          continue;

        $arrCliente["nrPontosFidelidade"] = max(0, $arrCliente["nrPontosFidelidade"] + $nrPontos);
        break;
      }

      self::salvarConteudo("clientes.json", $arrClientes);
    }

    /**
     * @return array
     */
    public static function getFuncionarios(): array
    {
      return self::carregarArquivo("funcionarios.json");
    }

    /**
     * @param string $nmPessoa
     * @param string $dsCpf
     * @param string $dsEmail
     * @param string $dsCargo
     * @param float $vlSalario
     * @return array
     */
    public static function salvarFuncionario(string $nmPessoa, string $dsCpf, string $dsEmail, string $dsCargo, float $vlSalario): array
    {
      $funcionario = new Funcionario($nmPessoa, $dsCpf, $dsEmail, $dsCargo, $vlSalario);

      $arrRegistroFuncionario = array_merge(["id" => self::incrementarProximoId("funcionarios.json")], $funcionario->toArray());

      $arrFuncionarios   = self::getFuncionarios();
      $arrFuncionarios[] = $arrRegistroFuncionario;
      self::salvarConteudo("funcionarios.json", $arrFuncionarios);

      return $arrRegistroFuncionario;
    }

    /**
     * @param null|int $idFuncionario
     * @param float $prBonus
     * @throws RuntimeException
     * @return float
     */
    public static function calcularBonusFuncionario(?int $idFuncionario, float $prBonus = 0.0): float
    {
      $arrDadosFuncionario = [];

      foreach (self::getFuncionarios() as $arrFuncionario)
      {
        if ((int) $arrFuncionario["id"] != $idFuncionario)
          continue;

        $arrDadosFuncionario = $arrFuncionario;
        break;
      }

      if (empty($arrDadosFuncionario))
        throw new RuntimeException("Funcionário #{$idFuncionario} não encontrado.");

      return Funcionario::fromArray($arrDadosFuncionario)->calcularBonusSalario($prBonus);
    }

    /**
     * @return array
     */
    public static function getProdutos(): array
    {
      return self::carregarArquivo("produtos.json");
    }

    /**
     * @param null|int $idProduto
     */
    public static function getProdutoById(?int $idProduto): ?array
    {
      foreach (self::getProdutos() as $arrProduto)
        if ((int) $arrProduto["id"] == $idProduto)
          return $arrProduto;

      return null;
    }

    /**
     * @param string $nmProduto
     * @param string $dsProduto
     * @param float $vlPreco
     * @param string $dsCategoria
     * @return array
     */
    public static function salvarProduto(string $nmProduto, string $dsProduto, float $vlPreco, string $dsCategoria): array
    {
      $Produto = new Produto($nmProduto, $dsProduto, $vlPreco, $dsCategoria);

      $arrRegistroProduto = array_merge(["id" => self::incrementarProximoId("produtos.json")], $Produto->toArray());

      $arrProdutos   = self::getProdutos();
      $arrProdutos[] = $arrRegistroProduto;
      self::salvarConteudo("produtos.json", $arrProdutos);

      return $arrRegistroProduto;
    }

    /**
     * @param int $idProduto
     * @return void
     */
    public static function toggleDisponibilidade(int $idProduto): void
    {
      $arrProdutos = self::getProdutos();

      foreach ($arrProdutos as &$arrProduto)
      {
        if ((int) $arrProduto["id"] != $idProduto)
          continue;

        $arrProduto["idDisponivel"] = !$arrProduto["idDisponivel"];
        break;
      }

      self::salvarConteudo("produtos.json", $arrProdutos);
    }

    /**
     * @return array
     */
    public static function getPedidos(): array
    {
      return self::carregarArquivo("pedidos.json");
    }

    /**
     * @param null|int $idCliente
     * @param array $arrProdutos
     * @throws RuntimeException
     * @return array
     */
    public static function criarPedido(?int $idCliente, array $arrProdutos = []): array
    {
      $arrDadosCliente = self::getClienteById($idCliente);

      if (!$arrDadosCliente)
        throw new RuntimeException("Cliente #{$idCliente} não encontrado.");

      $Cliente = Cliente::fromArray($arrDadosCliente);
      $Pedido  = new Pedido($Cliente);

      foreach ($arrProdutos as $arrProduto)
      {
        $arrDadosProduto = self::getProdutoById((int) $arrProduto["idProduto"]);

        if (!$arrDadosProduto || !(bool) $arrDadosProduto["idDisponivel"])
          continue;

        $Pedido->adicionarItem(Produto::fromArray($arrDadosProduto), (int) $arrProduto["quantidade"]);
      }

      if (empty($Pedido->getArrItens()))
        throw new RuntimeException("Nenhum item válido no pedido.");

      $Pedido->aplicarDesconto();
      $Pedido->confirmar();

      $arrNovoPedido = array_merge(["id" => self::incrementarProximoId("pedidos.json"), "idCliente" => $idCliente], $Pedido->toArray());

      $arrPedidos   = self::getPedidos();
      $arrPedidos[] = $arrNovoPedido;
      self::salvarConteudo("pedidos.json", $arrPedidos);

      self::atualizarPontosCliente($idCliente, (int) floor($Pedido->calcularTotalPedido() / 10));

      return $arrNovoPedido;
    }

    /**
     * @param int $idPedido
     * @return bool
     */
    public static function cancelarPedido(int $idPedido): bool
    {
      $arrPedidos = self::getPedidos();

      foreach ($arrPedidos as &$arrPedido)
      {
        if ((int) $arrPedido["id"] != $idPedido || $arrPedido["dsStatus"] == "entregue")
          continue;

        $arrPedido["dsStatus"] = "cancelado";
        self::salvarConteudo("pedidos.json", $arrPedidos);

        return true;
      }

      return false;
    }

    /**
     * @return array
     */
    public static function getEstatisticas(): array
    {
      $arrPedidos            = self::getPedidos();
      $arrPedidosConfirmados = [];

      foreach ($arrPedidos as $arrPedido)
        if ($arrPedido["dsStatus"] == "confirmado")
          $arrPedidosConfirmados[] = $arrPedido;

      return [
        "totalClientes"     => count(self::getClientes()),
        "totalProdutos"     => count(self::getProdutos()),
        "totalPedidos"      => count($arrPedidos),
        "totalFuncionarios" => count(self::getFuncionarios()),
        "vlReceita"         => array_sum(array_column($arrPedidosConfirmados, "vlTotal")),
        "pedidosRecentes"   => array_slice(array_reverse($arrPedidos), 0, 6),
      ];
    }
  }