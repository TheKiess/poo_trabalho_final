<?php

/**
 * Representa um funcionário do restaurante.
 * Herda de Pessoa e adiciona cargo e salário.
 */
class Funcionario extends Pessoa
{
  private string $dsCargo;
  private float $vlSalario;

  /**
   * @param string $nmPessoa
   * @param string $dsCpf
   * @param string $dsEmail
   * @param string $dsCargo
   * @param float  $vlSalario
   */
  public function __construct(
    string $nmPessoa,
    string $dsCpf,
    string $dsEmail,
    string $dsCargo,
    float $vlSalario
  ) {
    parent::__construct($nmPessoa, $dsCpf, $dsEmail);
    $this->dsCargo   = $dsCargo;
    $this->vlSalario = $vlSalario;
  }

  public function getDsTipo(): string
  {
    return "Funcionário";
  }

  public function getDsCargo(): string
  {
    return $this->dsCargo;
  }

  public function getVlSalario(): float
  {
    return $this->vlSalario;
  }

  /**
   * Calcula o valor do bônus sobre o salário.
   *
   * @param  float $prBonus Percentual entre 0 e 100
   * @throws InvalidArgumentException
   * @return float
   */
  public function calcularBonusSalario(float $prBonus): float
  {
    if ($prBonus <= 0 || $prBonus > 100)
    {
      throw new InvalidArgumentException(
        "Percentual de bônus inválido: {$prBonus}%. Deve estar entre 0 e 100."
      );
    }

    return $this->vlSalario * ($prBonus / 100);
  }

  /**
   * Serializa o funcionário para array (sem o id do DataStore).
   *
   * @return array
   */
  public function toArray(): array
  {
    return array_merge(parent::toArray(), [
      "dsCargo"   => $this->dsCargo,
      "vlSalario" => $this->vlSalario,
    ]);
  }

  /**
   * Reconstrói um Funcionario a partir de um array persistido.
   *
   * @param  array $arrFuncionario
   * @return static
   */
  public static function fromArray(array $arrFuncionario): static
  {
    return new static(
      $arrFuncionario["nmPessoa"],
      $arrFuncionario["dsCpf"],
      $arrFuncionario["dsEmail"],
      $arrFuncionario["dsCargo"],
      (float) $arrFuncionario["vlSalario"]
    );
  }

  public function __toString(): string
  {
    $vlFmt = number_format($this->vlSalario, 2, ",", ".");

    return parent::__toString() . " | Cargo: {$this->dsCargo} | Salário: R$ {$vlFmt}";
  }
}
