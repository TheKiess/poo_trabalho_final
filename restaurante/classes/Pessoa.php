<?php

/**
 * Classe abstrata base para pessoas do sistema.
 * Subclasses devem implementar getDsTipo().
 */
abstract class Pessoa
{
  protected string $nmPessoa;
  protected string $dsCpf;
  protected string $dsEmail;

  /**
   * @param string $nmPessoa
   * @param string $dsCpf
   * @param string $dsEmail
   */
  protected function __construct(string $nmPessoa, string $dsCpf, string $dsEmail)
  {
    $this->nmPessoa = $nmPessoa;
    $this->dsCpf    = $dsCpf;
    $this->dsEmail  = $dsEmail;
  }

  public function getNmPessoa(): string
  {
    return $this->nmPessoa;
  }

  public function getDsCpf(): string
  {
    return $this->dsCpf;
  }

  public function getDsEmail(): string
  {
    return $this->dsEmail;
  }

  abstract protected function getDsTipo(): string;

  /**
   * Retorna os campos base de Pessoa como array.
   * Subclasses devem fazer array_merge com este resultado.
   *
   * @return array
   */
  protected function toArray(): array
  {
    return [
      "nmPessoa" => $this->nmPessoa,
      "dsCpf"    => $this->dsCpf,
      "dsEmail"  => $this->dsEmail,
    ];
  }

  public function __toString(): string
  {
    return "[{$this->getDsTipo()}] {$this->nmPessoa} | CPF: {$this->dsCpf} | E-mail: {$this->dsEmail}";
  }
}
