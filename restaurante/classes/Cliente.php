<?php
/**
 * Representa um cliente do restaurante.
 * Herda de Pessoa e adiciona tipo, programa de fidelidade e persistência.
 */
class Cliente extends Pessoa
{
  private const DS_ARQUIVO = "clientes.json";
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
   * @return array
   */
  public function salvarCliente(): array
  {
    $arrRegistro = array_merge(["id" => DataStore::incrementarProximoId(self::DS_ARQUIVO)], $this->toArray());
    $arrLista    = self::buscarClientes();
    $arrLista[]  = $arrRegistro;

    DataStore::salvarConteudo(self::DS_ARQUIVO, $arrLista);

    return $arrRegistro;
  }

  /**
   * Caso não passado ID, buscará todos!
   * 
   * @param int $idCliente
   * @return array
   */
  public static function buscarClientes(int $idCliente = 0): array
  {
    if ($idCliente > 0)
    {
      foreach (self::buscarClientes() as $arrCliente)
        if ((int) $arrCliente["id"] == $idCliente)
          return $arrCliente;

      return [];
    }

    return DataStore::carregarArquivo(self::DS_ARQUIVO);
  }

  /**
   * Atualiza os pontos de fidelidade de um cliente no arquivo JSON.
   *
   * @param null|int $idCliente
   * @param int $nrPontos
   */
  public static function atualizarPontos(?int $idCliente, int $nrPontos = 0): void
  {
    $arrLista = self::buscarClientes();

    foreach ($arrLista as &$arrCliente)
    {
      if ((int) $arrCliente["id"] != $idCliente)
        continue;

      $arrCliente["nrPontosFidelidade"] = max(0, $arrCliente["nrPontosFidelidade"] + $nrPontos);
      break;
    }

    DataStore::salvarConteudo(self::DS_ARQUIVO, $arrLista);
  }

  /**
   * Serializa o cliente para array (sem o id persistido).
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
    $cliente = new static(
      $arrCliente["nmPessoa"],
      $arrCliente["dsCpf"],
      $arrCliente["dsEmail"],
      $arrCliente["dsTipoCliente"] ?? "comum"
    );

    $cliente->adicionarPontos($arrCliente["nrPontosFidelidade"]);

    return $cliente;
  }

  public function __toString(): string
  {
    return parent::__toString() . " | Tipo: {$this->dsTipoCliente}" . " | Pontos: {$this->nrPontosFidelidade}";
  }
<<<<<<< HEAD
}

//UML cliente 
/*@startuml

class Cliente {
    - dsTipoCliente : string
    - nrPontosFidelidade : int

    + getDsTipo() : string
    + getDsTipoCliente() : string
    + getNrPontosFidelidade() : int
    + isPremium() : bool
    + adicionarPontos(nrPontos : int) : void
    + toArray() : array
    + fromArray(arrCliente : array) : Cliente
    + __toString() : string
}

Cliente --|> Pessoa

@enduml */
=======
}
>>>>>>> df0ee3b059bcf3fc6950e8b11cb4eddcc946c976
