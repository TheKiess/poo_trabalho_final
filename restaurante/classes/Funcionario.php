<?php
/**
 * Representa um funcionário do restaurante.
 * Herda de Pessoa e adiciona cargo, salário e persistência.
 */
class Funcionario extends Pessoa
{
  private const DS_ARQUIVO = "funcionarios.json";
  private string $dsCargo;
  private float $vlSalario;

  /**
   * @param string $nmPessoa
   * @param string $dsCpf
   * @param string $dsEmail
   * @param string $dsCargo
   * @param float  $vlSalario
   */
  public function __construct(string $nmPessoa, string $dsCpf, string $dsEmail, string $dsCargo, float $vlSalario)
  {
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
   * @param  float $prBonus Percentual entre 0 e 100
   * @throws InvalidArgumentException
   * @return float
   */
  public function calcularBonusSalario(float $prBonus): float
  {
    if ($prBonus <= 0 || $prBonus > 100)
      throw new InvalidArgumentException("Percentual de bônus inválido: {$prBonus}%. Deve estar entre 0 e 100.");

    return $this->vlSalario * ($prBonus / 100);
  }

  /**
   * Persiste o funcionário no arquivo JSON e retorna o registro salvo.
   *
   * @return array
   */
  public function salvarFuncionario(): array
  {
    $arrRegistro = array_merge(["id" => DataStore::incrementarProximoId(self::DS_ARQUIVO)], $this->toArray());
    $arrLista    = self::buscarFuncionario();
    $arrLista[]  = $arrRegistro;
    DataStore::salvarConteudo(self::DS_ARQUIVO, $arrLista);

    return $arrRegistro;
  }

  /**
   * Caso não passado ID, buscará todos!
   * 
   * @param int $idFuncionario
   * @return array
   */
  public static function buscarFuncionario(int $idFuncionario = 0): array
  {
    if ($idFuncionario > 0)
    {
      foreach (self::buscarFuncionario() as $arrFuncionario)
        if ((int) $arrFuncionario["id"] == $idFuncionario)
          return $arrFuncionario;

      return [];
    }

    return DataStore::carregarArquivo(self::DS_ARQUIVO);
  }

  /**
   * @param  int   $idFuncionario
   * @param  float $prBonus
   * @throws RuntimeException
   * @throws InvalidArgumentException
   * @return float
   */
  public static function calcularBonus(int $idFuncionario, float $prBonus): float
  {
    $arrDados = self::buscarFuncionario($idFuncionario);

    if (!$arrDados)
      throw new RuntimeException("Funcionário #{$idFuncionario} não encontrado.");

    return self::fromArray($arrDados)->calcularBonusSalario($prBonus);
  }

  /**
   * Serializa o funcionário para array (sem o id persistido).
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
<<<<<<< HEAD
}

/*@startuml

class Pessoa

class Funcionario {
    - dsCargo : string
    - vlSalario : float

    + getDsTipo() : string
    + getDsCargo() : string
    + getVlSalario() : float
    + calcularBonusSalario(prBonus : float) : float
    + toArray() : array
    + fromArray(arrFuncionario : array) : Funcionario
    + __toString() : string
}

Funcionario --|> Pessoa

@enduml */
=======
}
>>>>>>> df0ee3b059bcf3fc6950e8b11cb4eddcc946c976
