# 🍽️ Bistrot POO — Sistema de Gerenciamento de Restaurante

Trabalho prático da disciplina de **Programação Orientada a Objetos** desenvolvido em PHP 8.  
O sistema gerencia clientes, funcionários, cardápio e pedidos de um restaurante aplicando os principais conceitos de POO.

---

## 🏗️ Diagrama de Classes

```
Pessoa  (abstrata)
├── Cliente       → herança
│     getDsTipo() · toArray() · __toString()  [substituição]
└── Funcionario   → herança
      getDsTipo() · toArray() · __toString()  [substituição]

Produto
ItemPedido        → composição com Pedido

Pedido
  ├── → Cliente       (associação — Cliente existe fora do Pedido)
  └── ◆ ItemPedido[]  (composição — ItemPedido só existe dentro do Pedido)
```

### Relacionamentos

| Tipo | Entre | Descrição |
|---|---|---|
| Herança | `Pessoa` → `Cliente` | Cliente é uma Pessoa |
| Herança | `Pessoa` → `Funcionario` | Funcionário é uma Pessoa |
| Composição | `Pedido` ◆ `ItemPedido[]` | Itens são criados e pertencem exclusivamente ao Pedido |
| Associação | `Pedido` → `Cliente` | Cliente existe independentemente do Pedido |

---

## O quê há em nossa estrutura?

### Classes de domínio

- [x] **6 classes relacionadas** — `Pessoa`, `Cliente`, `Funcionario`, `Produto`, `ItemPedido`, `Pedido`
- [x] **Herança** — `Cliente` e `Funcionario` herdam de `Pessoa` abstrata
- [x] **Composição** — `Pedido` cria `ItemPedido[]` via `adicionarItem()`
- [x] **Associação** — `Pedido` referencia `Cliente`

### Construtores e inicialização

- [x] Todas as classes possuem `__construct()`
- [x] `Cliente` e `Funcionario` chamam `parent::__construct()` para inicializar os campos de `Pessoa`

### Substituição de métodos

- [x] `getDsTipo()` — abstrato em `Pessoa`, implementado em `Cliente` e `Funcionario`
- [x] `toArray()` — definido como `protected` em `Pessoa`, sobrescrito em cada subclasse com `array_merge(parent::toArray(), [...])`
- [x] `__toString()` — definido em `Pessoa`, estendido em `Cliente` e `Funcionario`

> **Sobre sobrecarga:** PHP não suporta sobrecarga de assinatura como Java. O método `buscarClientes(int $id = 0)` usa parâmetro opcional para simular o comportamento — sem argumento retorna todos; com ID retorna um. O mesmo padrão é aplicado em `buscarFuncionario()`, `buscarProduto()` e `buscarPedidos()`.

### Regras de negócio

- [x] `Pedido::calcularTotalPedido()` — soma os subtotais de todos os itens e subtrai o desconto aplicado
- [x] `Pedido::aplicarDesconto()` — Premium → 10%, 100+ pontos de fidelidade → 5%, demais → 0%
- [x] `Funcionario::calcularBonusSalario()` — calcula bônus percentual sobre o salário com validação de parâmetro
- [x] `ItemPedido::calcularSubtotal()` — preço unitário (snapshot) × quantidade

### Fluxo de execução

- [x] Interface web demonstra instanciação, mensagens entre objetos e execução das regras de negócio
- [x] `demonstracao.php` em console exemplifica os objetos em memória de forma explícita (ideal para apresentação)

---

## 💡 Destaques Técnicos

**Active Record** — cada classe conhece e gerencia sua própria persistência. Não existe um repositório central: `Cliente`, `Funcionario`, `Produto` e `Pedido` carregam seus próprios métodos `salvar*()`, `buscar*()` e a constante `DS_ARQUIVO` com o nome do arquivo JSON correspondente.

```php
// Cada classe sabe onde e como se persistir
private const DS_ARQUIVO = "clientes.json";

public function salvarCliente(): array { ... }
public static function buscarClientes(int $id = 0): array { ... }
```

**`toArray()` / `fromArray()`** — padrão de serialização implementado em todas as classes. Permite transformar qualquer objeto em array para persistir em JSON e reconstruí-lo depois sem acoplamento externo.

```php
// Serializar
$arr = $cliente->toArray();

// Reconstruir
$cliente = Cliente::fromArray($arr);
```

**Snapshot de preço** — `ItemPedido` captura `getVlPreco()` no momento da criação. Alterações futuras no produto não afetam pedidos já registrados.

**`DataStore`** funciona como utilitário puro de I/O — apenas `carregarArquivo()`, `salvarConteudo()`, `incrementarProximoId()` e `dir()`. Toda a lógica de domínio vive nas classes.

---

## 🗂️ Estrutura do Projeto

```
restaurante/
├── index.php
├── config.php
├── demonstracao.php
├── classes/
│   ├── Pessoa.php
│   ├── Cliente.php
│   ├── Funcionario.php
│   ├── Produto.php
│   ├── ItemPedido.php
│   └── Pedido.php
├── includes/
│   ├── DataStore.php
│   └── nav.php
├── pages/
│   ├── style.css
│   ├── clientes.php
│   ├── produtos.php
│   ├── pedidos.php
│   └── funcionarios.php
└── data/
    ├── clientes.json
    ├── funcionarios.json
    ├── produtos.json
    └── pedidos.json
```

---

## ▶️ Como Executar

**Pré-requisito:** PHP 8.0 ou superior.

```bash
# 1. Clone o repositório
git clone https://github.com/TheKiess/poo_trabalho_final.git

# 2. Entre na pasta do projeto
cd poo_trabalho_final/restaurante

# 3. Suba o servidor embutido do PHP
php -S localhost:8000

# 4. Acesse no navegador
http://localhost:8000/
```

> ⚠️ O comando `php -S localhost:8000` deve ser executado **dentro da pasta `restaurante/`**, onde está o `index.php`. Executar de outra pasta retorna "resource not found".

**Para executar o demo em console:**

```bash
php demonstracao.php
```

---

## 🧠 Sobre a Linguagem — PHP 8

PHP é uma linguagem server-side com suporte robusto a POO desde a versão 5. A versão 8 introduziu *typed properties*, *match expressions*, *named arguments*, *union types* e *constructor property promotion*. É amplamente usada em plataformas como WordPress, Laravel e Symfony.

Neste projeto foram aplicados recursos do PHP 8: tipagem estrita em todos os parâmetros e retornos, `match` com expressões na `aplicarDesconto()`, constantes de classe (`private const DS_ARQUIVO`), `static` em `fromArray()` para preservar o tipo concreto em subclasses, e PHPDoc em todos os métodos públicos.

---

## 👥 Grupo

| | Nome |
|---|---|
| Integrante 1 | Frank Kiess |
| Integrante 2 | Amanda Carniel |

**Disciplina:** Programação Orientada a Objetos  
**Linguagem:** PHP 8