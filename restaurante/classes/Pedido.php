<?php

/**
 * Representa um pedido do restaurante.
 *
 * Associação → Cliente  (existe independentemente do pedido)
 * Composição → ItemPedido[] (itens pertencem exclusivamente ao pedido)
 */
class Pedido
{
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
    if ($this->dsStatus !== "aberto")
    {
      throw new RuntimeException(
        "Pedido #{$this->idPedido} com status \"{$this->dsStatus}\" não aceita novos itens."
      );
    }

    if (!$produto->isIdDisponivel())
    {
      throw new RuntimeException(
        "Produto \"{$produto->getNmProduto()}\" está indisponível."
      );
    }

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
    {
      $vlSubtotal += $item->calcularSubtotal();
    }

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
    {
      $vlSubtotal += $item->calcularSubtotal();
    }

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
    {
      throw new RuntimeException("Não é possível confirmar um pedido sem itens.");
    }

    $this->dsStatus = "confirmado";

    $nrPontos = (int) floor($this->calcularTotalPedido() / 10);
    $this->cliente->adicionarPontos($nrPontos);
  }

  /**
   * @throws RuntimeException
   */
  public function cancelar(): void
  {
    if ($this->dsStatus === "entregue")
    {
      throw new RuntimeException("Pedido já entregue não pode ser cancelado.");
    }

    $this->dsStatus = "cancelado";
  }

  /**
   * Serializa o pedido para array.
   * Não possui fromArray pois a reconstrução depende de um Cliente já
   * instanciado externamente — responsabilidade do DataStore.
   *
   * @return array
   */
  public function toArray(): array
  {
    return [
      "nmCliente"     => $this->cliente->getNmPessoa(),
      "dsTipoCliente" => $this->cliente->getDsTipoCliente(),
      "arrItens"      => array_map(fn($item) => $item->toArray(), $this->arrItens),
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

    $out  = "\n╔══════════════════════════════════════════╗\n";
    $out .= "  Pedido #{$this->idPedido} — {$dtFmt}\n";
    $out .= "  Cliente : {$this->cliente->getNmPessoa()} ({$this->cliente->getDsTipoCliente()})\n";
    $out .= "  Status  : {$this->dsStatus}\n";
    $out .= "╠══════════════════════════════════════════╣\n";

    foreach ($this->arrItens as $item)
    {
      $out .= $item . "\n";
    }

    $out .= "╠══════════════════════════════════════════╣\n";

    if ($this->vlDesconto > 0)
    {
      $out .= "  Desconto : -R$ {$descFmt}\n";
    }

    $out .= "  TOTAL    :  R$ {$vlFmt}\n";
    $out .= "╚══════════════════════════════════════════╝\n";

    return $out;
  }
}

/*@startuml

class Pedido {
    - nrContadorId : int {static}
    - idPedido : int
    - cliente : Cliente
    - arrItens : ItemPedido[]
    - dsStatus : string
    - dtCriacao : DateTime
    - vlDesconto : float

    + getIdPedido() : int
    + getCliente() : Cliente
    + getDsStatus() : string
    + getArrItens() : array
    + getVlDesconto() : float
    + adicionarItem(produto : Produto, qtItens : int) : void
    + calcularTotalPedido() : float
    + aplicarDesconto() : float
    + confirmar() : void
    + cancelar() : void
    + toArray() : array
    + __toString() : string
}

Pedido "1" *-- "1..*" ItemPedido
Pedido --> Cliente
ItemPedido --> Produto

@enduml */