<?php

/**
 * Representa um item dentro de um pedido.
 * Composição com Pedido — não existe fora dele.
 * Armazena snapshot do preço no momento da criação.
 */
class ItemPedido
{
  private Produto $produto;
  private int $qtItens;
  private float $vlUnitario;

  /**
   * @param  Produto $produto
   * @param  int     $qtItens
   * @throws InvalidArgumentException
   */
  public function __construct(Produto $produto, int $qtItens)
  {
    if ($qtItens <= 0)
      throw new InvalidArgumentException("Quantidade deve ser maior que zero.");

    $this->produto    = $produto;
    $this->qtItens    = $qtItens;
    $this->vlUnitario = $produto->getVlPreco();
  }

  public function getProduto(): Produto
  {
    return $this->produto;
  }

  public function getQtItens(): int
  {
    return $this->qtItens;
  }

  public function getVlUnitario(): float
  {
    return $this->vlUnitario;
  }

  /**
   * Regra de negócio: calcula o subtotal do item (preço × quantidade).
   *
   * @return float
   */
  public function calcularSubtotal(): float
  {
    return $this->vlUnitario * $this->qtItens;
  }

  /**
   * Serializa o item para array com snapshot dos valores.
   * Não possui fromArray pois ItemPedido só existe dentro de um Pedido,
   * sempre criado via adicionarItem().
   *
   * @return array
   */
  public function toArray(): array
  {
    return [
      "nmProduto"  => $this->produto->getNmProduto(),
      "qtItens"    => $this->qtItens,
      "vlUnitario" => $this->vlUnitario,
      "vlSubtotal" => $this->calcularSubtotal(),
    ];
  }

  public function __toString(): string
  {
    $vlUnit  = number_format($this->vlUnitario, 2, ",", ".");
    $vlTotal = number_format($this->calcularSubtotal(), 2, ",", ".");

    return "  • {$this->produto->getNmProduto()} x{$this->qtItens}"
      . " @ R$ {$vlUnit} = R$ {$vlTotal}";
  }
}
/*@startuml

class ItemPedido {
    - produto : Produto
    - qtItens : int
    - vlUnitario : float

    + getProduto() : Produto
    + getQtItens() : int
    + getVlUnitario() : float
    + calcularSubtotal() : float
    + toArray() : array
    + __toString() : string
}

@enduml*/