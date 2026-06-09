<?php

/**
 * Representa um pedido do restaurante.
 *
 * Associação → Cliente  (existe independentemente do pedido)
 * Composição → ItemPedido[] (itens pertencem exclusivamente ao pedido)
 */
class Pedido
{
  private const DS_ARQUIVO = "pedidos.json";
  private static int $nrContadorId = 1;

  private int $idPedido;
  private Cliente $cliente;
  private array $arrItens = [];
  private string $dsStatus;
  private DateTime $dtCriacao;
  private float $vlDesconto = 0.0;

  /**
   * @param Cliente $cliente
   */
  public function __construct(Cliente $cliente)
  {
    $this->idPedido  = self::$nrContadorId++;
    $this->cliente   = $cliente;
    $this->dsStatus  = "aberto";
    $this->dtCriacao = new DateTime();
  }

  public function getIdPedido(): int
  {
    return $this->idPedido;
  }

  public function getCliente(): Cliente
  {
    return $this->cliente;
  }

  public function getDsStatus(): string
  {
    return $this->dsStatus;
  }

  public function getArrItens(): array
  {
    return $this->arrItens;
  }

  public function getVlDesconto(): float
  {
    return $this->vlDesconto;
  }

  /**
   * Adiciona um produto ao pedido (composição com ItemPedido).
   * Valida disponibilidade do produto e status do pedido.
   *
   * @param  Produto $produto
   * @param  int     $qtItens
   * @throws RuntimeException
   */
  public function adicionarItem(Produto $produto, int $qtItens): void
  {
    if ($this->dsStatus != "aberto")
      throw new RuntimeException("Pedido #{$this->idPedido} com status \"{$this->dsStatus}\" não aceita novos itens.");

    if (!$produto->isIdDisponivel())
      throw new RuntimeException("Produto \"{$produto->getNmProduto()}\" está indisponível.");

    $this->arrItens[] = new ItemPedido($produto, $qtItens);
  }

  /**
   * Regra de negócio 1: calcula o total do pedido descontado.
   *
   * @return float
   */
  public function calcularTotalPedido(): float
  {
    $vlSubtotal = 0.0;

    foreach ($this->arrItens as $item)
      $vlSubtotal += $item->calcularSubtotal();

    return max(0.0, $vlSubtotal - $this->vlDesconto);
  }

  /**
   * Regra de negócio 2: aplica desconto conforme perfil do cliente.
   * Premium     → 10%
   * 100+ pontos →  5%
   * Demais      →  0%
   *
   * @return float Valor do desconto aplicado
   */
  public function aplicarDesconto(): float
  {
    
    $vlSubtotal = 0.0;

    foreach ($this->arrItens as $item)
      $vlSubtotal += $item->calcularSubtotal();

    $this->vlDesconto = match(true)
    {
      $this->cliente->isPremium()                    => $vlSubtotal * 0.10,
      $this->cliente->getNrPontosFidelidade() >= 100 => $vlSubtotal * 0.05,
      default                                        => 0.0,
    };

    return $this->vlDesconto;
  }

  /**
   * Confirma o pedido e credita pontos de fidelidade (1 pt a cada R$ 10).
   *
   * @throws RuntimeException
   */
  public function confirmar(): void
  {
    if (empty($this->arrItens))
      throw new RuntimeException("Não é possível confirmar um pedido sem itens.");

    $this->dsStatus = "confirmado";

    $nrPontos = (int) floor($this->calcularTotalPedido() / 10);
    $this->cliente->adicionarPontos($nrPontos);
  }

  /**
   * Persiste o pedido no arquivo JSON e atualiza os pontos do cliente.
   *
   * @param  int $idCliente
   * @return array
   */
  public function salvarPedido(int $idCliente): array
  {
    $arrNovoPedido = array_merge(
      ["id" => DataStore::incrementarProximoId(self::DS_ARQUIVO), "idCliente" => $idCliente],
      $this->toArray()
    );

    $arrLista   = self::buscarPedidos();
    $arrLista[] = $arrNovoPedido;
    DataStore::salvarConteudo(self::DS_ARQUIVO, $arrLista);

    Cliente::atualizarPontos($idCliente, (int) floor($this->calcularTotalPedido() / 10));

    return $arrNovoPedido;
  }

  /**
   * Orquestra a criação completa de um pedido:
   * carrega cliente e produtos, aplica regras de negócio e persiste.
   *
   * @param  int
   * @param  array
   * @throws RuntimeException
   * @return array
   */
  public static function criarPedido(int $idCliente, array $arrItens): array
  {
    $arrDadosCliente = Cliente::buscarClientes($idCliente);

    if (!$arrDadosCliente)
      throw new RuntimeException("Cliente #{$idCliente} não encontrado.");

    $cliente = Cliente::fromArray($arrDadosCliente);
    $pedido  = new self($cliente);

    foreach ($arrItens as $arrItem)
    {
      $arrDadosProduto = Produto::buscarProduto((int) $arrItem["idProduto"]);

      if (!$arrDadosProduto || !(bool) $arrDadosProduto["idDisponivel"])
        continue;

      $pedido->adicionarItem(Produto::fromArray($arrDadosProduto), (int) $arrItem["quantidade"]);
    }

    if (empty($pedido->getArrItens()))
      throw new RuntimeException("Nenhum item válido no pedido.");

    $pedido->aplicarDesconto();
    $pedido->confirmar();

    return $pedido->salvarPedido($idCliente);
  }

  /**
   * Caso não passado ID, buscará todos!
   *
   * @param  int $idPedido
   * @return array
   */
  public static function buscarPedidos(int $idPedido = 0): array
  {
    if ($idPedido > 0)
    {
      foreach (self::buscarPedidos() as $arrPedido)
        if ((int) $arrPedido["id"] == $idPedido)
          return $arrPedido;

      return [];
    }

    return DataStore::carregarArquivo(self::DS_ARQUIVO);
  }

  /**
   * Cancela um pedido pelo id, desde que não esteja entregue.
   *
   * @param  int $idPedido
   * @return bool
   */
  public static function cancelar(int $idPedido): bool
  {
    $arrLista = self::buscarPedidos();

    foreach ($arrLista as &$arrPedido)
    {
      if ((int) $arrPedido["id"] != $idPedido || $arrPedido["dsStatus"] == "entregue")
        continue;

      $arrPedido["dsStatus"] = "cancelado";
      DataStore::salvarConteudo(self::DS_ARQUIVO, $arrLista);

      return true;
    }

    return false;
  }

  /**
   * Reconstrói um Pedido a partir de um array persistido.
   *
   * @param  array $arrPedido
   * @param  int   $idCliente
   * @return static
   */
  public static function fromArray(array $arrPedido, int $idCliente): static
  {
    $arrDadosCliente = Cliente::buscarClientes($idCliente);
    $Cliente         = Cliente::fromArray($arrDadosCliente);

    $Pedido             = new static($Cliente);
    $Pedido->vlDesconto = $arrPedido["vlDesconto"];
    $Pedido->dsStatus   = $arrPedido["dsStatus"];

    return $Pedido;
  }

  /**
   * @return array
   */
  public function toArray(): array
  {
    $arrItensPersistidos = [];

    foreach ($this->arrItens as $item)
      $arrItensPersistidos[] = $item->toArray();

    return [
      "nmCliente"     => $this->cliente->getNmPessoa(),
      "dsTipoCliente" => $this->cliente->getDsTipoCliente(),
      "arrItens"      => $arrItensPersistidos,
      "vlDesconto"    => $this->vlDesconto,
      "vlTotal"       => $this->calcularTotalPedido(),
      "dsStatus"      => $this->dsStatus,
      "dtCriacao"     => $this->dtCriacao->format("d/m/Y H:i"),
    ];
  }

  public function __toString(): string
  {
    $dtFmt   = $this->dtCriacao->format("d/m/Y H:i");
    $vlFmt   = number_format($this->calcularTotalPedido(), 2, ",", ".");
    $descFmt = number_format($this->vlDesconto, 2, ",", ".");

    $dsRelatorio  = "\n╔══════════════════════════════════════════╗\n";
    $dsRelatorio .= "  Pedido #{$this->idPedido} — {$dtFmt}\n";
    $dsRelatorio .= "  Cliente : {$this->cliente->getNmPessoa()} ({$this->cliente->getDsTipoCliente()})\n";
    $dsRelatorio .= "  Status  : {$this->dsStatus}\n";
    $dsRelatorio .= "╠══════════════════════════════════════════╣\n";

    foreach ($this->arrItens as $item)
      $dsRelatorio .= $item . "\n";

    $dsRelatorio .= "╠══════════════════════════════════════════╣\n";

    if ($this->vlDesconto > 0)
      $dsRelatorio .= "  Desconto : -R$ {$descFmt}\n";

    $dsRelatorio .= "  TOTAL    :  R$ {$vlFmt}\n";
    $dsRelatorio .= "╚══════════════════════════════════════════╝\n";

    return $dsRelatorio;
  }
}