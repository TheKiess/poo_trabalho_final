<?php

/**
 * Representa um produto do cardápio do restaurante.
 */
class Produto
{
  private static int $nrContadorId = 1;

  private int $idProduto;
  private string $nmProduto;
  private string $dsProduto;
  private float $vlPreco;
  private string $dsCategoria;
  private bool $idDisponivel;

  /**
   * @param string $nmProduto
   * @param string $dsProduto
   * @param float  $vlPreco
   * @param string $dsCategoria
   */
  public function __construct(
    string $nmProduto,
    string $dsProduto,
    float $vlPreco,
    string $dsCategoria
  ) {
    $this->idProduto    = self::$nrContadorId++;
    $this->nmProduto    = $nmProduto;
    $this->dsProduto    = $dsProduto;
    $this->vlPreco      = $vlPreco;
    $this->dsCategoria  = $dsCategoria;
    $this->idDisponivel = true;
  }

  public function getIdProduto(): int
  {
    return $this->idProduto;
  }

  public function getNmProduto(): string
  {
    return $this->nmProduto;
  }

  public function getDsProduto(): string
  {
    return $this->dsProduto;
  }

  public function getVlPreco(): float
  {
    return $this->vlPreco;
  }

  public function getDsCategoria(): string
  {
    return $this->dsCategoria;
  }

  public function isIdDisponivel(): bool
  {
    return $this->idDisponivel;
  }

  public function setIdDisponivel(bool $idDisponivel): void
  {
    $this->idDisponivel = $idDisponivel;
  }

  /**
   * Serializa o produto para array (sem o id do DataStore).
   *
   * @return array
   */
  public function toArray(): array
  {
    return [
      "nmProduto"    => $this->nmProduto,
      "dsProduto"    => $this->dsProduto,
      "vlPreco"      => $this->vlPreco,
      "dsCategoria"  => $this->dsCategoria,
      "idDisponivel" => $this->idDisponivel,
    ];
  }

  /**
   * Reconstrói um Produto a partir de um array persistido.
   *
   * @param  array $arr
   * @return static
   */
  public static function fromArray(array $arr): static
  {
    $obj = new static(
      $arr["nmProduto"],
      $arr["dsProduto"],
      (float) $arr["vlPreco"],
      $arr["dsCategoria"]
    );

    $obj->setIdDisponivel((bool) $arr["idDisponivel"]);

    return $obj;
  }

  public function __toString(): string
  {
    $vlFmt  = number_format($this->vlPreco, 2, ",", ".");
    $status = $this->idDisponivel ? "✔ Disponível" : "✖ Indisponível";

    return "#{$this->idProduto} [{$this->dsCategoria}] {$this->nmProduto} — R$ {$vlFmt} | {$status}";
  }
}

/*@startuml

class Produto {
    - nrContadorId : int {static}
    - idProduto : int
    - nmProduto : string
    - dsProduto : string
    - vlPreco : float
    - dsCategoria : string
    - idDisponivel : bool

    + getIdProduto() : int
    + getNmProduto() : string
    + getDsProduto() : string
    + getVlPreco() : float
    + getDsCategoria() : string
    + isIdDisponivel() : bool
    + setIdDisponivel(idDisponivel : bool) : void
    + toArray() : array
    + fromArray(arr : array) : Produto
    + __toString() : string
}

@enduml */