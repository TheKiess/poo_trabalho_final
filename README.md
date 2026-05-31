# 🍽️ Sistema de Gerenciamento de Restaurante — POO em PHP

Trabalho prático de Programação Orientada a Objetos utilizando PHP 8.

---

## 📌 Estudo de Caso

Um restaurante precisa gerenciar seu cardápio, clientes, funcionários e pedidos.
O sistema permite registrar produtos, criar pedidos para clientes, aplicar descontos
por perfil e acompanhar o status dos pedidos.

---

## 🏗️ Estrutura de Classes

```
Pessoa (abstrata)
├── Cliente          ← herança
└── Funcionario      ← herança

Produto
ItemPedido           ← composição com Pedido
Pedido
  ├── → Cliente      ← associação
  └── ◆ ItemPedido[] ← composição
```

### Relacionamentos implementados

| Tipo          | Entre                     | Descrição                                      |
|---------------|---------------------------|------------------------------------------------|
| Herança       | Pessoa → Cliente          | Cliente é uma Pessoa                           |
| Herança       | Pessoa → Funcionario      | Funcionário é uma Pessoa                       |
| Composição    | Pedido → ItemPedido[]     | Itens não existem fora de um Pedido            |
| Associação    | Pedido → Cliente          | Cliente existe independentemente do Pedido     |

---

## ✅ Requisitos atendidos

- [x] **Herança** — `Cliente` e `Funcionario` herdam de `Pessoa`
- [x] **Composição** — `Pedido` contém `ItemPedido[]`
- [x] **Associação** — `Pedido` referencia `Cliente`
- [x] **5+ classes de domínio** — `Pessoa`, `Cliente`, `Funcionario`, `Produto`, `ItemPedido`, `Pedido`
- [x] **Construtores** em todas as classes
- [x] **Substituição de métodos** — `getTipo()` e `__toString()` em `Cliente` e `Funcionario`
- [x] **2+ regras de negócio**:
  - `calcularTotal()` — soma os subtotais dos itens menos desconto
  - `aplicarDesconto()` — desconto por perfil (premium 10%, 100+ pontos 5%)
  - `calcularBonus()` — bônus percentual do funcionário com validação
- [x] **Fluxo de execução em console** com instanciação, mensagens e tratamento de exceções

---

## ▶️ Como executar

### Pré-requisito
- PHP 8.0 ou superior instalado

### Comando
```bash
php index.php
```

---

## 📁 Estrutura de arquivos

```
restaurante/
├── index.php                  # Fluxo principal de execução
└── classes/
    ├── Pessoa.php             # Classe abstrata base
    ├── Cliente.php            # Herda Pessoa
    ├── Funcionario.php        # Herda Pessoa
    ├── Produto.php            # Item do cardápio
    ├── ItemPedido.php         # Composição com Pedido
    └── Pedido.php             # Entidade central com regras de negócio
```

---

## 🧠 Linguagem — PHP 8

PHP é uma linguagem de script de propósito geral com forte suporte a POO desde
a versão 5. A versão 8 trouxe melhorias como *typed properties*, *union types*,
*match expressions* e *named arguments*. É amplamente usada no desenvolvimento
web back-end (WordPress, Laravel, Symfony).

---

## 👥 Grupo

- Integrante 1: Frank Kiess
- Integrante 2: Amanda Carniel