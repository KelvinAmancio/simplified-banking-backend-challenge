
# Simplified Banking Backend Challenge

Este é um repositório para um desafio de backend de uma plataforma de pagamentos simplificada.


## Funcionalidades

### RFs (Requisitos Funcionais)

- Deve ser possível realizar transferências de dinheiro entre usuários

- Deve ser possível ter 2 tipos de usuários (comuns e lojistas)

- Deve ser possível usuários terem carteira com dinheiro e realizam transferências entre eles

- Deve ser possível usuários realizarem transferências (enviar dinheiro) entre eles

### RNs (Regras de Negócio)

- Para ambos tipos de usuário devem existir os campos:
    - nome
    - cpf_cnpj
    - email
    - senha

- Os seguintes campos devem ser únicos:
    - cpf_cnpj
    - email

- Lojistas só devem receber transferências, não devem enviar dinheiro

- Deve ser validado se o usuário tem saldo antes da transferência

- Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo (API mock)

- A operação de transferência deve ser uma transação (revertida em caso de inconsistência)

- No recebimento de pagamento, o usuário precisa receber notificação (email, sms) enviada por um serviço de terceiro (API mock)

- Um usuário não deve enviar uma transferência para ele mesmo

### RNFs (Requisitos Não-Funcionais)

- O serviço implementado deve ser RESTFul

- Deve ser utilizado token JWT para autenticação de usuários

- Devem ser utilizadas migrations para gerar as tabelas do banco relacional (MySQL)

- O envio de notificação (email, sms) deverá ser feito via event notification

- Devem ser criadas as seguintes tabelas no banco relacional:
    - user
    - wallet
    - transfer

## Documentação da API

#### Login de usuário

```http
  POST /login
  Content-Type: application/json
```

| Parâmetro   | Tipo       | Descrição                           |
| :---------- | :--------- | :---------------------------------- |
| `email` | `string` | **Obrigatório**. Email do Usuário |
| `password` | `string` | **Obrigatório**. Senha do Usuário |

```json
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"
}
```

#### Cadastro de usuário

```http
  POST /register
  Content-Type: application/json
```

| Parâmetro   | Tipo       | Descrição                           |
| :---------- | :--------- | :---------------------------------- |
| `name` | `string` | **Obrigatório**. Nome do Usuário |
| `email` | `string` | **Obrigatório**. Email do Usuário |
| `cpf_cnpj` | `string` | **Obrigatório**. Documento do Usuário |
| `password` | `string` | **Obrigatório**. Senha do Usuário |

```json
{
    "user": {
        "name": "User Name",
        "email": "email@email.com",
        "cpf_cnpj": "111.111.111-11",
        "password": "$2y$10$CvFaUTc\/FfnOFWowxu5S9eQwnPdd4PybAzDTBOrmI4BuFNCiTXFhG",
        "uuid": "01hxqy16pqfk79wgb88xv9n8nc",
        "updated_at": "2024-05-12 23:55:41",
        "created_at": "2024-05-12 23:55:41"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MTU1Njg5NDEsImV4cCI6MTcxNTU3MDc0MSwibmJmIjoxNzE1NTY4OTQwLCJkYXRhIjp7InVzZXJfdXVpZCI6IjAxaHhxeTE2cHFmazc5d2diODh4djluOG5jIiwidXNlcl90eXBlIjoiUEYifX0.wB5hyD9oDnLFskvn16_zOlIvrecsUz0ZR8qS9HDELMo",
    "wallet": {
        "balance": 0,
        "owner_id": "01hxqy16pqfk79wgb88xv9n8nc",
        "uuid": "01hxqy16qk2ceanhanqa9apgs4",
        "updated_at": "2024-05-12 23:55:41",
        "created_at": "2024-05-12 23:55:41"
    }
}
```

#### Transferir dinheiro entre usuáris
```http
  POST /request
  Content-Type: application/json
```

| Parâmetro   | Tipo       | Descrição                           |
| :---------- | :--------- | :---------------------------------- |
| `value` | `numeric` | **Obrigatório**. Valor da transferência |
| `payee` | `string` | **Obrigatório**. Id do usuário que receberá o dinheiro |
| `Authorization` | `string (header)` | **Obrigatório**. Token do usuário logado que enviará o dinheiro |

```json
{
    "transfer": {
        "authorized": true,
        "notification_sent": false,
        "payer_id": "01hxqy54krre0he63d7qphbgpg",
        "payee_id": "01hxqy54p514vvxn7mhvatbrc4",
        "value": 100,
        "uuid": "01hxqy54rgvbfhs4b0x5rkwk1r",
        "updated_at": "2024-05-12 23:57:50",
        "created_at": "2024-05-12 23:57:50"
    }
}
```

## Modelagem

![Tabelas do banco MySQL](https://github.com/KelvinAmancio/simplified-banking-backend-challenge/assets/25416440/996fbd6d-93fd-48df-b2bf-5716bf185724)
[Issue com imagem](https://github.com/KelvinAmancio/simplified-banking-backend-challenge/issues/1#issue-2291737699)

## Rodando localmente

Clone o projeto

```bash
  git clone https://github.com/KelvinAmancio/simplified-banking-backend-challenge
```

Entre no diretório do projeto

```bash
  cd simplified-banking-backend-challenge
```

Iniciar containers:

```bash
  make up
```

Com os containers inciados, rodar em outro terminal:

```bash
  make exec
  make install
  make migrate
  vendo/bin/phpunit
```

Para iniciar o servidor:

```bash
  make exec
  make install
  make start
```

Relatório de cobertura de teste:

```bash
  make exec
  make install
  make coverage-u
  make coverage-i
  make coverage-a
```


## Melhorias

Propostas de endpoint:

-  GET /wallet (obter valor da carteira de um usuário logado)
- GET /summary (obter histórico de transferências paginado de um usuário logado)
## Stack utilizada

- [Hyperf Framework](https://hyperf.io/)
- [Docker](https://www.docker.com/)
- [MySQL](https://www.mysql.com/)
- [Redis](https://redis.io/)

