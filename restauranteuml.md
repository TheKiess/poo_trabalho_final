@startuml Bistrot_POO

abstract class Pessoa {
  # nmPessoa : string
  # dsCpf : string
  # dsEmail : string
  + getNmPessoa() : string
  + getDsCpf() : string
  + getDsEmail() : string
  + {abstract} getDsTipo() : string
  # toArray() : array
  + __toString() : string
}

class Cliente {
  - dsTipoCliente : string
  - nrPontosFidelidade : int
  + getDsTipo() : string
  + getDsTipoCliente() : string
  + getNrPontosFidelidade() : int
  + isPremium() : bool
  + adicionarPontos(nrPontos : int) : void
  + salvarCliente() : array
  + {static} buscarClientes(idCliente : int) : array
  + {static} atualizarPontos(idCliente : int, nrPontos : int) : void
  + toArray() : array
  + {static} fromArray(arr : array) : static
  + __toString() : string
}

class Funcionario {
  - dsCargo : string
  - vlSalario : float
  + getDsTipo() : string
  + getDsCargo() : string
  + getVlSalario() : float
  + calcularBonusSalario(prBonus : float) : float
  + salvarFuncionario() : array
  + {static} buscarFuncionario(idFuncionario : int) : array
  + {static} calcularBonus(idFuncionario : int, prBonus : float) : float
  + toArray() : array
  + {static} fromArray(arr : array) : static
  + __toString() : string
}

class Produto {
  - {static} nrContadorId : int
  - idProduto : int
  - nmProduto : string
  - dsProduto : string
  - vlPreco : float
  - dsCategoria : string
  - idDisponivel : bool
  + getIdProduto() : int
  + getNmProduto() : string
  + getVlPreco() : float
  + isIdDisponivel() : bool
  + setIdDisponivel(idDisponivel : bool) : void
  + salvarProduto() : array
  + {static} buscarProduto(idProduto : int) : array
  + {static} mudarDisponibilidade(idProduto : int) : void
  + toArray() : array
  + {static} fromArray(arr : array) : static
  + __toString() : string
}

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

class Pedido {
  - {static} nrContadorId : int
  - idPedido : int
  - cliente : Cliente
  - arrItens : ItemPedido[]
  - dsStatus : string
  - dtCriacao : DateTime
  - vlDesconto : float
  + getIdPedido() : int
  + getCliente() : Cliente
  + getDsStatus() : string
  + getVlDesconto() : float
  + adicionarItem(produto : Produto, qtItens : int) : void
  + calcularTotalPedido() : float
  + aplicarDesconto() : float
  + confirmar() : void
  + salvarPedido(idCliente : int) : array
  + {static} criarPedido(idCliente : int, arrItens : array) : array
  + {static} buscarPedidos(idPedido : int) : array
  + {static} cancelar(idPedido : int) : bool
  + {static} fromArray(arr : array, idCliente : int) : static
  + toArray() : array
  + __toString() : string
}

' ── Relacionamentos ──────────────────────────
Cliente    --|> Pessoa       : herança
Funcionario --|> Pessoa      : herança
Pedido "1" *-- "1..*" ItemPedido : composição
Pedido      --> Cliente      : associação
ItemPedido  --> Produto      : usa

@enduml