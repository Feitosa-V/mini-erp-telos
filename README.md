# Projeto

## Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Node.js](https://nodejs.org/)
- [Composer](https://getcomposer.org/)

## Configuração do Ambiente

### 1. Clonar o repositório
```sh
git clone https://github.com/Feitosa-V/mini-erp-telos.git
```

### 2. Construir e subir os containers
```sh
docker-compose up --build -d
```

### 3. Instalar dependências do backend (Laravel)
```sh
docker-compose exec app composer install
```

### 4. Configurar o ambiente
Crie o arquivo `.env` com base no `.env.example` e configure as variáveis de ambiente conforme necessário.
```sh
cp .env.example .env
```

### 5. Rodar migrations e seeders
```sh
docker exec -it mini-erp-app bash
php artisan migrate:fresh --seed
```

### 6. Instalar dependências do frontend (Vue.js)
```sh
npm install
```

### 7. Rodar o frontend
```sh
npm run dev
```

## Acesso ao sistema
- O sistema estará rodando em: `http://localhost:8000/login`
- O acesso ao Mailpit estará disponível em: `http://localhost:8025/`

Login Admin: 

Email: admin@example.com
Senha: password

Login Vendedor: 

Email: seller@example.com
Senha: password

## Funcionalidades e Regras

### 1. Módulo de Fornecedores
- Apenas administradores podem acessar este módulo.

### 2. Restrições para vendedores
- Vendedores só podem fazer pedidos para fornecedores aos quais têm acesso.

### 3. Relatórios e Notificações
- Relatório diário de pedidos enviado automaticamente por e-mail às 08:00.
- O relatório é separado por status e apresenta um layout melhorado com cores distintas para cada status:
  - **Concluído** → Verde
  - **Pendente** → Amarelo
  - **Cancelado** → Vermelho

## Tecnologias Utilizadas
- **Backend:** Laravel
- **Frontend:** Vue.js + Inertia / Breeze
- **Banco de Dados:** MySQL
- **Docker** para gerenciamento de ambiente
- **Mailpit** para testes de e-mails

