<?php

  /**
   * DataStore
   *
   * Utilitário de persistência em arquivos JSON (Pasta ../data/).
   * Responsável apenas por leitura, escrita e geração de IDs.
   * As regras de negócio e operações de domínio pertencem às próprias classes.
   */
  class DataStore
  {
    /**
     * @return string
     */
    public static function dir(): string
    {
      $dsDiretorio = BASE_PATH . "/data/";

      if (!is_dir($dsDiretorio))
        mkdir($dsDiretorio, 0755, true);

      return $dsDiretorio;
    }

    /**
     * @param  string $dsArquivo
     * @return array
     */
    public static function carregarArquivo(string $dsArquivo): array
    {
      $dsCaminhoArquivo = self::dir() . $dsArquivo;

      if (!file_exists($dsCaminhoArquivo))
        return [];

      return json_decode(file_get_contents($dsCaminhoArquivo), true) ?? [];
    }

    /**
     * @param string $dsArquivo
     * @param array  $arrDados
     */
    public static function salvarConteudo(string $dsArquivo, array $arrDados): void
    {
      file_put_contents(
        self::dir() . $dsArquivo,
        json_encode($arrDados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
      );
    }

    /**
     * @param  string $dsArquivo
     * @return int
     */
    public static function incrementarProximoId(string $dsArquivo): int
    {
      $arrDados = self::carregarArquivo($dsArquivo);

      if (empty($arrDados))
        return 1;

      return max(array_column($arrDados, "id")) + 1;
    }

    /**
     * Agrega dados de múltiplas entidades para o dashboard.
     *
     * @return array
     */
    public static function getEstatisticas(): array
    {
      $arrPedidos            = Pedido::buscarPedidos();
      $arrPedidosConfirmados = [];

      foreach ($arrPedidos as $arrPedido)
        if ($arrPedido["dsStatus"] == "confirmado")
          $arrPedidosConfirmados[] = $arrPedido;

      return [
        "totalClientes"     => count(Cliente::buscarClientes()),
        "totalProdutos"     => count(Produto::buscarProduto()),
        "totalPedidos"      => count($arrPedidos),
        "totalFuncionarios" => count(Funcionario::buscarFuncionario()),
        "vlReceita"         => array_sum(array_column($arrPedidosConfirmados, "vlTotal")),
        "pedidosRecentes"   => array_slice(array_reverse($arrPedidos), 0, 6),
      ];
    }
  }