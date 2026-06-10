<?php

/**
 * Representa um produto do cardápio do restaurante.
 */
class Produto
{
  private const DS_ARQUIVO = "produtos.json";
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
  public function __construct(string $nmProduto, string $dsProduto, float $vlPreco, string $dsCategoria)
  {
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
   * Persiste o produto no arquivo JSON e retorna o registro salvo.
   *
   * @return array
   */
  public function salvarProduto(): array
  {
    $arrRegistro = array_merge(["id" => DataStore::incrementarProximoId(self::DS_ARQUIVO)], $this->toArray());

    $arrLista   = self::buscarProduto();
    $arrLista[] = $arrRegistro;
    DataStore::salvarConteudo(self::DS_ARQUIVO, $arrLista);

    return $arrRegistro;
  }

  /**
   * Caso não passado ID, buscará todos!
   * 
   * @return array
   */
  public static function buscarProduto(int $idProduto = 0): array
  {
    if ($idProduto > 0)
    {
      foreach (self::buscarProduto() as $arrProduto)
        if ((int) $arrProduto["id"] == $idProduto)
          return $arrProduto;

      return [];
    }

    return DataStore::carregarArquivo(self::DS_ARQUIVO);
  }

  /**
   * @param int $idProduto
   */
  public static function mudarDisponibilidade(int $idProduto): void
  {
    $arrLista = self::buscarProduto();

    foreach ($arrLista as &$arrProduto)
    {
      if ((int) $arrProduto["id"] != $idProduto)
        continue;

      $arrProduto["idDisponivel"] = !$arrProduto["idDisponivel"];
      break;
    }

    DataStore::salvarConteudo(self::DS_ARQUIVO, $arrLista);
  }

  /**
   * Serializa o produto para array (sem o id persistido).
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
   * @param  array $arrProduto
   * @return static
   */
  public static function fromArray(array $arrProduto): static
  {
    $produto = new static(
      $arrProduto["nmProduto"],
      $arrProduto["dsProduto"],
      (float) $arrProduto["vlPreco"],
      $arrProduto["dsCategoria"]
    );

    $produto->setIdDisponivel((bool) $arrProduto["idDisponivel"]);

    return $produto;
  }

  public function __toString(): string
  {
    $vlFmt  = number_format($this->vlPreco, 2, ",", ".");
    $status = $this->idDisponivel ? "✔ Disponível" : "✖ Indisponível";

    return "#{$this->idProduto} [{$this->dsCategoria}] {$this->nmProduto} — R$ {$vlFmt} | {$status}";
  }
}