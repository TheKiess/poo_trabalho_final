# 🍽️ Bistrot POO — Sistema de Gerenciamento de Restaurante

Trabalho prático da disciplina de **Programação Orientada a Objetos** desenvolvido em PHP 8.  
O sistema gerencia clientes, funcionários, cardápio e pedidos de um restaurante, aplicando os principais conceitos de POO.

---

## 📌 Estudo de Caso

Um restaurante precisa controlar seu cardápio, registrar clientes e funcionários, realizar pedidos e aplicar regras de desconto por perfil de cliente. O sistema foi implementado como uma aplicação web com backend em PHP puro e persistência em arquivos JSON.

---

## 🏗️ Diagrama de Classes

```

```

### Relacionamentos

| Tipo | Entre | Descrição |
|---|---|---|
| Herança | `Pessoa` → `Cliente` | Cliente é uma Pessoa |

---

## 🗂️ Estrutura do Projeto

```
restaurante/
├── index.php
├── config.php
├── classes/
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

### Pré-requisito
PHP 8.0 ou superior instalado.

### Passos

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

> ⚠️ O comando `php -S localhost:8000` deve ser executado **dentro da pasta `restaurante/`**, onde está o `index.php`.

---

## 🧠 Sobre a Linguagem — PHP 8

PHP é uma linguagem de script server-side com suporte robusto a POO desde a versão 5. A versão 8 introduziu melhorias como *typed properties*, *match expressions*, *named arguments* e *union types*. É amplamente utilizada no desenvolvimento web backend — presente em plataformas como WordPress, Laravel e Symfony.

---

## 👥 Grupo

| | Nome |
|---|---|
| Integrante 1 | Frank Kiess |
| Integrante 2 | Amanda Carniel |

**Disciplina:** Programação Orientada a Objetos  
**Linguagem:** PHP 8
