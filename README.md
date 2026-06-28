# Sistema de Gestão de Pedidos Multicanal — API Back-end

Projeto acadêmico desenvolvido para a disciplina de Projeto Multidisciplinar (Trilha Back-End) — Uninter, 2026.

API REST em Laravel para gestão de pedidos de uma rede de lanchonetes com múltiplas unidades, suportando múltiplos
canais de atendimento (App, Totem, Balcão, Pickup, Web), controle de estoque por unidade, programa de fidelização,
integração simulada de pagamento e auditoria de ações sensíveis.

---

## Sumário

- [Visão Geral](#visão-geral)
- [Tecnologias](#tecnologias)
- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
- [Executando a Aplicação](#executando-a-aplicação)
- [Autenticação](#autenticação)
- [Perfis de Usuário](#perfis-de-usuário)
- [Principais Recursos da API](#principais-recursos-da-api)
- [Documentação da API](#documentação-da-api)
- [Testes](#testes)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Licença](#licença)

---

## Visão Geral

O sistema simula o back-end de uma rede de lanchonetes nordestinas em expansão, atendendo aos seguintes requisitos de
negócio:

- Cardápio configurável por unidade (preço e disponibilidade independentes)
- Controle de estoque por unidade, com movimentações registradas
- Pedidos com rastreabilidade de canal de origem (`canal_pedido`)
- Fluxo de status do pedido com transições controladas (não retrocede)
- Pagamento desacoplado, simulado via mock (sem gateway real)
- Programa de fidelização com acúmulo e resgate de pontos, condicionado a consentimento LGPD
- Log de auditoria para ações sensíveis (criação de pedido, mudança de status, cancelamento)

## Tecnologias

- PHP 8.2+
- Laravel 11
- SQLite
- Laravel Sanctum (autenticação via token)
- PHPUnit (testes automatizados)

## Requisitos

Antes de começar, garanta que você tem instalado:

- PHP >= 8.2
- Composer
- Extensão `sqlite3` habilitada no PHP

## Instalação

Clone o repositório e instale as dependências:

```bash
git clone https://github.com/quelipee/api-gestao-franquias.git
cd SEU_REPOSITORIO

composer install
```

Copie o arquivo de variáveis de ambiente:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

## Configuração do Banco de Dados

Este projeto usa **SQLite**, então não é necessário instalar um servidor de banco de dados separado.

1. Crie o arquivo do banco:

```bash
touch database/database.sqlite
```

2. No `.env`, mantenha:

```env
DB_CONNECTION=sqlite
```

> Não é necessário definir `DB_DATABASE`. No Laravel 11, quando a conexão é `sqlite` e essa variável não está definida,
> o framework usa `database/database.sqlite` automaticamente.

3. Execute as migrations:

```bash
php artisan migrate
```

4. (Opcional) Popule o banco com dados de exemplo:

```bash
php artisan db:seed
```

Os seeders criam:

- Usuários de cada perfil (ADMIN, GERENTE, ATENDENTE, COZINHA, CLIENTE)
- Unidades de exemplo
- Categorias e produtos do cardápio nordestino
- Vínculo de produtos às unidades (cardápio) com estoque inicial

## Executando a Aplicação

```bash
php artisan serve
```

A API estará disponível em:

```
http://127.0.0.1:8000
```

## Autenticação

A autenticação é feita via **Laravel Sanctum**, usando tokens (não sessão/cookie), adequada para consumo por App, Totem
e Web.

### Registro

```http
POST /api/register
Content-Type: application/json

{
    "nome": "Maria Silva",
    "email": "maria@exemplo.com",
    "senha": "Senha@123",
    "senha_confirmation": "Senha@123",
    "perfil": "CLIENTE",
    "consentimento_lgpd": true
}
```

### Login

```http
POST /api/login
Content-Type: application/json

{
    "email": "maria@exemplo.com",
    "senha": "Senha@123"
}
```

Resposta:

```json
{
    "accessToken": "1|abcdef123456...",
    "tokenType": "Bearer",
    "user": {
        "id": 1,
        "nome": "Maria Silva",
        "perfil": "CLIENTE"
    }
}
```

### Uso do token

Inclua o token retornado no header `Authorization` de todas as requisições autenticadas:

```http
Authorization: Bearer 1|abcdef123456...
```

### Logout

```http
POST /api/logout
Authorization: Bearer {token}
```

Revoga o token atual.

## Perfis de Usuário

| Perfil      | Descrição                                                                                           |
|-------------|-----------------------------------------------------------------------------------------------------|
| `ADMIN`     | Acesso total à rede. Cria unidades, produtos globais, gerencia usuários.                            |
| `GERENTE`   | Gerencia apenas a(s) unidade(s) à qual está vinculado (estoque, cardápio, cancelamento de pedidos). |
| `ATENDENTE` | Cria pedidos no balcão e atualiza status até "entregue".                                            |
| `COZINHA`   | Atualiza status do pedido entre "em preparo" e "pronto".                                            |
| `CLIENTE`   | Cria pedidos, acompanha status, participa da fidelização.                                           |

A relação entre `GERENTE`/`ATENDENTE`/`COZINHA` e a unidade onde atuam é controlada pela tabela `unidade_usuario`.

## Principais Recursos da API

### Autenticação

| Método | Rota        | Acesso      | Descrição                       |
|--------|-------------|-------------|---------------------------------|
| POST   | `/register` | Público     | Cadastro de novo usuário        |
| POST   | `/login`    | Público     | Autenticação e geração de token |
| POST   | `/logout`   | Autenticado | Revoga o token atual            |
| GET    | `/user`     | Autenticado | Retorna o usuário autenticado   |

### Unidades

| Método | Rota                  | Acesso      | Descrição               |
|--------|-----------------------|-------------|-------------------------|
| GET    | `/unidades`           | Autenticado | Lista unidades          |
| GET    | `/unidades/{unidade}` | Autenticado | Detalhe de uma unidade  |
| POST   | `/unidades`           | ADMIN       | Cria unidade            |
| PUT    | `/unidades/{unidade}` | ADMIN       | Atualiza unidade        |
| DELETE | `/unidades/{unidade}` | ADMIN       | Remove/desativa unidade |

### Cardápio por Unidade

| Método | Rota                                     | Acesso         | Descrição                              |
|--------|------------------------------------------|----------------|----------------------------------------|
| GET    | `/unidades/{unidade}/produtos`           | Autenticado    | Lista produtos disponíveis na unidade  |
| POST   | `/unidades/{unidade}/produtos`           | ADMIN, GERENTE | Vincula produto ao cardápio da unidade |
| DELETE | `/unidades/{unidade}/produtos/{produto}` | ADMIN, GERENTE | Remove produto do cardápio da unidade  |

### Produtos (catálogo global)

| Método | Rota                  | Acesso         | Descrição                  |
|--------|-----------------------|----------------|----------------------------|
| GET    | `/produtos`           | Público        | Lista produtos do catálogo |
| GET    | `/produtos/{produto}` | Público        | Detalhe de um produto      |
| POST   | `/produtos`           | ADMIN, GERENTE | Cria produto               |
| PUT    | `/produtos/{produto}` | ADMIN, GERENTE | Atualiza produto           |
| DELETE | `/produtos/{produto}` | ADMIN, GERENTE | Remove produto             |

### Estoque

| Método | Rota                    | Acesso         | Descrição                                           |
|--------|-------------------------|----------------|-----------------------------------------------------|
| GET    | `/estoque/{unidade}`    | ADMIN, GERENTE | Consulta estoque da unidade                         |
| POST   | `/estoque`              | ADMIN, GERENTE | Cria registro de estoque para um produto na unidade |
| POST   | `/estoque/movimentacao` | ADMIN, GERENTE | Registra entrada ou saída de estoque                |

### Pedidos

| Método | Rota                       | Acesso                               | Descrição                                                  |
|--------|----------------------------|--------------------------------------|------------------------------------------------------------|
| POST   | `/pedidos`                 | CLIENTE                              | Cria um novo pedido                                        |
| GET    | `/pedidos`                 | CLIENTE                              | Lista pedidos (aceita filtro `?canalPedido=` e `?status=`) |
| PATCH  | `/pedidos/{pedido}/status` | COZINHA, ATENDENTE, GERENTE, CLIENTE | Atualiza dados/status do pedido                            |

### Pagamento (mock)

| Método | Rota                          | Acesso  | Descrição                                      |
|--------|-------------------------------|---------|------------------------------------------------|
| POST   | `/pedidos/{pedido}/pagamento` | CLIENTE | Solicita pagamento mock e registra o resultado |

### Fidelização

| Método | Rota                                   | Acesso  | Descrição                                       |
|--------|----------------------------------------|---------|-------------------------------------------------|
| GET    | `/fidelizacoes/saldo`                  | CLIENTE | Consulta saldo de pontos do cliente autenticado |
| POST   | `/pedidos/{pedido}/fidelidade/resgate` | CLIENTE | Resgata pontos como desconto no pedido          |

### Multicanalidade

Todo pedido exige o campo `canal_pedido`, com os valores: `APP`, `TOTEM`, `BALCAO`, `PICKUP`, `WEB`.

A listagem de pedidos pode ser filtrada por canal:

```http
GET /api/pedidos?canalPedido=TOTEM
```

E por status:

```http
GET /api/pedidos?status=EM_PREPARO
```

### Fluxo de status do pedido

```
AGUARDANDO_PAGAMENTO → PAGO → EM_PREPARO → PRONTO → ENTREGUE
                                                  ↘
                                              CANCELADO (de qualquer status, exceto ENTREGUE)
```

O status nunca retrocede. Tentativas de regressão retornam erro `422`.

### Pagamento (mock)

O sistema não processa pagamento real. O fluxo simulado:

```http
POST /api/pedidos/{pedido}/pagamento
Content-Type: application/json
Authorization: Bearer {token}

{
    "forma_pagamento": "MOCK",
    "simular_resultado": "APROVADO"
}
```

- `APROVADO` → pedido avança para `PAGO`
- `RECUSADO` → pedido permanece em `AGUARDANDO_PAGAMENTO`, permitindo nova tentativa

### Fidelização e LGPD

- O cliente só acumula pontos se `consentimento_lgpd = true`
- Pontos são creditados apenas quando o pedido atinge o status `ENTREGUE`
- Pedidos cancelados não geram pontos
- O resgate de pontos é validado contra o saldo disponível e aplicado como desconto no pedido
- Consulta de saldo: `GET /api/fidelizacoes/saldo`
- Resgate: `POST /api/pedidos/{pedido}/fidelidade/resgate`

### Auditoria

Toda ação sensível (criação de pedido, mudança de status, cancelamento) gera um registro em `logs_auditoria`, contendo:
usuário responsável pela ação, ação executada, entidade afetada, snapshot do estado anterior e do novo estado.

## Documentação da API

> **FALTA FAZER AINDA**

## Testes

O projeto segue uma abordagem orientada a testes (feature tests), cobrindo fluxos positivos e negativos de cada módulo:
autenticação, unidades, produtos/cardápio, estoque, pedidos, pagamento, fidelização e auditoria.

Para rodar a suíte completa:

```bash
php artisan test
```

Para rodar um arquivo específico:

```bash
php artisan test --filter=PedidoTest
```

O ambiente de teste usa um banco SQLite em memória (`RefreshDatabase`), garantindo isolamento entre os testes.

## Estrutura do Projeto

O projeto segue uma organização por camadas, separando regras de negócio (`application`), contratos/interfaces (
`Contracts`), objetos de transferência de dados (`DTOs`), infraestrutura de persistência (`Infrastructure`) e a camada
HTTP (`Http`).

```
app/
├── application/             # Casos de uso / regras de negócio por domínio
│   ├── Auditoria/
│   ├── Authenticated/
│   ├── Estoque/
│   ├── Fidelizacao/
│   ├── Pagamento/
│   ├── Pedido/
│   ├── Produto/
│   ├── Unidade/
│   └── UnidadeProduto/
│
├── Contracts/                # Interfaces (contratos) implementadas pela infraestrutura
│   ├── Repository/
│   └── Services/
│
├── DTOs/                      # Objetos de transferência de dados, por domínio
│   ├── Estoque/
│   ├── Pagamento/
│   ├── Pedido/
│   ├── Produto/
│   ├── Unidade/
│   └── UnidadeProduto/
│
├── Enums/                     # OrderStatus, AuditoriaAcao, AuditoriaEntidade, UserRole...
├── Exceptions/                # Exceções customizadas de domínio
│
├── Http/
│   ├── Controllers/           # Controllers da API
│   ├── Middleware/             # Middlewares (ex: checagem de role)
│   ├── Requests/                # Form Requests de validação
│   └── Resources/                # API Resources (formatação de resposta)
│
├── Infrastructure/
│   └── Repository/              # Implementação concreta dos contratos de Repository
│
├── Models/                       # Models Eloquent
├── Policies/                     # Regras de autorização por perfil e por unidade
└── Providers/
    └── AppServiceProvider.php    # Bindings de Contracts → Infrastructure

database/
├── factories/                    # Factories usadas nos testes
├── migrations/                   # Estrutura das tabelas
├── seeders/                       # Dados de exemplo
└── database.sqlite               # Banco SQLite local

routes/
├── api.php                       # Rotas da API
├── console.php
└── web.php

tests/
├── Feature/                      # Testes de integração por módulo
└── TestCase.php
```

### Sobre a organização em camadas

- **`application/`**: concentra a lógica de negócio de cada domínio (ex: `Pedido`, `Pagamento`, `Fidelizacao`), isolada
  dos detalhes de Eloquent e HTTP.
- **`Contracts/`**: define interfaces para `Repository` e `Services`, permitindo que a camada de aplicação dependa de
  abstrações, não de implementações concretas.
- **`Infrastructure/Repository/`**: implementa os contratos de repositório, encapsulando o acesso ao Eloquent.
- **`DTOs/`**: padroniza os dados trafegados entre camadas (Controller → Application → Repository), evitando passar
  arrays soltos ou Models diretamente entre as camadas.
- **`Providers/AppServiceProvider.php`**: responsável por registrar os bindings entre os `Contracts` e suas
  implementações em `Infrastructure`.

Essa separação facilita testes unitários da camada de aplicação (mockando os contratos) e mantém a regra de negócio
independente do framework.

## Licença

Projeto de uso acadêmico, desenvolvido para fins avaliativos da disciplina de Projeto Multidisciplinar.
