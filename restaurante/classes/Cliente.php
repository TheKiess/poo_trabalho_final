<?php

/**
 * Representa um cliente do restaurante.
 * Herda de Pessoa e adiciona tipo e programa de fidelidade.
 */
class Cliente extends Pessoa
{
  private string $dsTipoCliente;
  private int $nrPontosFidelidade;

  /**
   * @param string $nmPessoa
   * @param string $dsCpf
   * @param string $dsEmail
   * @param string $dsTipoCliente
   */
  public function __construct(string $nmPessoa, string $dsCpf, string $dsEmail, string $dsTipoCliente = "comum")
  {
    parent::__construct($nmPessoa, $dsCpf, $dsEmail);
    $this->dsTipoCliente      = $dsTipoCliente;
    $this->nrPontosFidelidade = 0;
  }

  public function getDsTipo(): string
  {
    return "Cliente";
  }

  public function getDsTipoCliente(): string
  {
    return $this->dsTipoCliente;
  }

  public function getNrPontosFidelidade(): int
  {
    return $this->nrPontosFidelidade;
  }

  public function isPremium(): bool
  {
    return $this->dsTipoCliente == "premium";
  }

  public function adicionarPontos(int $nrPontos): void
  {
    $this->nrPontosFidelidade += $nrPontos;
  }

  /**
   * Serializa o cliente para array (sem o id do DataStore).
   *
   * @return array
   */
  public function toArray(): array
  {
    return array_merge(parent::toArray(), [
      "dsTipoCliente"      => $this->dsTipoCliente,
      "nrPontosFidelidade" => $this->nrPontosFidelidade,
    ]);
  }

  /**
   * Reconstrói um Cliente a partir de um array persistido.
   *
   * @param  array $arrCliente
   * @return static
   */
  public static function fromArray(array $arrCliente): static
  {
    $Cliente = new static(
      $arrCliente["nmPessoa"],
      $arrCliente["dsCpf"],
      $arrCliente["dsEmail"],
      $arrCliente["dsTipoCliente"] ?? "comum"
    );

    $Cliente->adicionarPontos($arrCliente["nrPontosFidelidade"]);

    return $Cliente;
  }

  public function __toString(): string
  {
    return parent::__toString()
      . " | Tipo: {$this->dsTipoCliente}"
      . " | Pontos: {$this->nrPontosFidelidade}";
  }
}
