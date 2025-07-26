
# Simplified Banking Backend Challenge
This is a repo for a Simplified Banking Backend Challenge.

## Requirements

### FRs (Functional Requirements)

- It should be possible to make money transfers between users

- It should be possible to have two types of users (regular and merchant)

- It should be possible for users to have a wallet with money and make transfers between them

- It should be possible for users to make transfers (send money) between them

### BRs (Business Rules)

- For both user types, the following fields must be present:
    - name
    - cpf_cnpj (document identification)
    - email
    - password

- The following fields must be unique:
    - cpf_cnpj (document identification)
    - email

- Merchants should only receive transfers, not send money

- It must be validated whether the user has a balance before the transfer

- Before completing the transfer, an external authorizing service (mock API) must be consulted

- The transfer operation must be a transaction (reversed in case of inconsistency)

- Upon receipt of payment, the user must receive notification (email, SMS) sent by a third-party service (mock API)

- A user should not send a transfer to themselves

### NFRs (Non Functional Requirements)

- The implemented service must be RESTful

- A JWT token must be used for user authentication

- Migrations must be used to generate relational database tables (MySQL)

- Notifications (email, SMS) must be sent via event notification

- The following tables must be created in the relational database:
    - user
    - wallet
    - transfer

## API Documentation

#### User Login

```http
  POST /login
  Content-Type: application/json
```

| Param   | Type       | Description                           |
| :---------- | :--------- | :---------------------------------- |
| `email` | `string` | **Required**. User Email |
| `password` | `string` | **Required**. User Password |

```json
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"
}
```

#### User Signup

```http
  POST /register
  Content-Type: application/json
```

| Param   | Type       | Description                           |
| :---------- | :--------- | :---------------------------------- |
| `name` | `string` | **Required**. User Name |
| `email` | `string` | **Required**. User Email |
| `cpf_cnpj` | `string` | **Required**. User Doc |
| `password` | `string` | **Required**. User Password |

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

#### Make money transfers between users
```http
  POST /request
  Content-Type: application/json
```

| Param   | Type       | Description                           |
| :---------- | :--------- | :---------------------------------- |
| `value` | `numeric` | **Required**. Transfer Value |
| `payee` | `string` | **Required**. ID of the user who will receive the money |
| `Authorization` | `string (header)` | **Required**. Token of the logged-in user who will send the money |

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

## Database Modeling

![MySQL Database Tables](https://github.com/KelvinAmancio/simplified-banking-backend-challenge/assets/25416440/996fbd6d-93fd-48df-b2bf-5716bf185724)
[GitHub Issue containing the image](https://github.com/KelvinAmancio/simplified-banking-backend-challenge/issues/1#issue-2291737699)

## Running Locally

Cloning the project

```bash
  git clone https://github.com/KelvinAmancio/simplified-banking-backend-challenge
```

Navigating to the project directory

```bash
  cd simplified-banking-backend-challenge
```

Starting containers:

```bash
  make up
```

With containers running, run in another terminal:

```bash
  make exec
  make install
  make migrate
  vendo/bin/phpunit
```

Starting server:

```bash
  make exec
  make install
  make start
```

Running tests and obtaining coverage report:

```bash
  make exec
  make install
  make coverage-u
  make coverage-i
  make coverage-a
```

## Improvements

Endpoint proposals:

-  GET /wallet (get wallet value for a logged in user)
- GET /summary (get paginated transfer history of a logged in user)

## Tech Stack

- [Hyperf Framework](https://hyperf.io/)
- [Docker](https://www.docker.com/)
- [MySQL](https://www.mysql.com/)
- [Redis](https://redis.io/)
