# Introdução

Este é um repositório para um desafio de backend de uma plataforma de pagamentos simplificada.

## Requisitos e Regras de Negócio

### RFs (Requisitos Funcionais)

- [ ] Deve ser possível realizar transferências de dinheiro entre usuários

- [ ] Deve ser possível ter 2 tipos de usuários (comuns e lojistas)

- [ ] Deve ser possível usuários terem carteira com dinheiro e realizam transferências entre eles

- [ ] Deve ser possível usuários realizarem transferências (enviar dinheiro) entre eles

### RNs (Regras de Negócio)

- [ ] Para ambos tipos de usuário devem existir os campos:
    - [ ] Nome Completo
    - [ ] CPF_CNPJ
    - [ ] e-mail
    - [ ] Senha

- [ ] Os seguintes campos devem ser únicos:
    - [ ] CPF_CNPJ
    - [ ] e-mail

- [ ] Lojistas só devem receber transferências, não devem enviar dinheiro

- [ ] Deve ser validado se o usuário tem saldo antes da transferência

- [ ] Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo (API mock)

- [ ] A operação de transferência deve ser uma transação (revertida em caso de inconsistência)

- [ ] No recebimento de pagamento, o usuário precisa receber notificação (email, sms) enviada por um serviço de terceiro (API mock)

### RNFs (Requisitos Não-Funcionais)

- [ ] O serviço implementado deve ser RESTFul

- [ ] Deve ser utilizado token JWT para autenticação de usuários

- [ ] Devem ser utilizadas migrations para gerar as tabelas do banco relacional (MySQL)

- [ ] O envio de notificação (email, sms) deverá ser feito via background jobs

- [ ] Devem ser criadas as seguintes tabelas no banco relacional:
    - [ ] user
        - [ ] uuid
        - [ ] name
        - [ ] cpf_cnpj
        - [ ] email
        - [ ] password
        - [ ] created_at
        - [ ] updated_at

    - [ ] wallet
        - [ ] uuid
        - [ ] owner_id
        - [ ] balance
        - [ ] created_at
        - [ ] updated_at

    - [ ] transfer
        - [ ] uuid
        - [ ] payer_id
        - [ ] payee_id
        - [ ] value
        - [ ] authorized
        - [ ] notification_sent
        - [ ] created_at
        - [ ] updated_at

- [ ] Devem ser criados as seguintes endpoints:
    - [ ] POST /transfer (realizar transferência entre usuários)
    - [ ] GET /wallet (obter valor da carteira de um usuário)
    - [ ] GET /summary (obter histórico de transferências paginado de um usuário)
    - [ ] POST /register (cadastrar um novo usuário com uma carteira)
    - [ ] POST /login (efetuar login de um usuário)