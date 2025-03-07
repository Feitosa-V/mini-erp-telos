# Projeto Mini ERP - TELOS

## Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Configuração do Ambiente

### 1. Clonar o repositório
```sh
git clone https://github.com/Feitosa-V/mini-erp-telos.git
cd mini-erp-telos
```

### 2. Construir e subir os containers
```sh
docker-compose up --build -d
```
⚠️ Isso já instala as dependências do Laravel automaticamente.


### 3. Configurar o ambiente
Crie o arquivo `.env` com base no `.env.example` e configure as variáveis de ambiente conforme necessário.
```sh
cp .env.example .env
```

### 4. Rodar migrations e seeders
```sh
docker exec -it mini-erp-app bash
php artisan migrate:fresh --seed
```

### 5. Instalar dependências do frontend (Vue.js)
```sh
npm install
```

### 6. Rodar o frontend
```sh
npm run dev
```

## Acesso ao sistema
- O sistema estará rodando em: `http://localhost:8000/login`
- O acesso ao Mailpit estará disponível em: `http://localhost:8025/`

### Credenciais Padrão

**Admin:**
- ✉️ Email: `admin@example.com`
- 🔑 Senha: `password`

**Vendedor:**
- ✉️ Email: `seller@example.com`
- 🔑 Senha: `password`

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

**Nota:** O ambiente já está configurado para funcionar automaticamente com Docker. Basta rodar os comandos indicados acima!
