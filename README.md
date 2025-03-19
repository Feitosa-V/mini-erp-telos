# Projeto Mini ERP - TELOS

## Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Composer](https://getcomposer.org/download/)
- [Node e NPM](https://nodejs.org/pt)

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
E em seguida:

```sh
composer install
```

### 3. Configurar o ambiente
Crie o arquivo `.env` com base no `.env.example` e configure as variáveis de ambiente conforme necessário.
```sh
cp .env.example .env
```

### 4. Rodar migrations e seeders e Criar Chave
```sh
docker exec -it mini-erp-app bash
```
Criar chave de acesso:

```sh
php artisan key:generate
```
Criar migrations e seeders:

```sh
php artisan migrate:fresh --seed
```

⚠️ Caso ocorra um erro ao rodar as migrations:

Antes de continuar, verifique se o Composer está instalado corretamente no projeto:

```sh
composer install
```

Caso apareça um erro do laravel log:

```sh
docker exec -it mini-erp-app bash
chown www-data:www-data /var/www/storage/logs/laravel.log
```

### 5. Instalar dependências do frontend (Vue.js)
```sh
npm install
```

⚠️ Se aparecer o erro ERR_SSL_CIPHER_OPERATION_FAILED, tente forçar a instalação com:

```sh
npm install --force
```

Se mesmo assim der erro, tente esse comando:

```sh
export NODE_OPTIONS=--openssl-legacy-provider
```

E em seguida:

```sh
npm install --force
```

### 6. Rodar o frontend
```sh
npm run dev
```

## Acesso ao sistema
- O sistema estará rodando em: `http://localhost:8000/login`
- O acesso ao Mailpit estará disponível em: `http://localhost:8025/`

## Acesso ao banco de dados
```sh
docker exec -it mini-erp-mysql mysql -uuser -ppassword mini_erp
```

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

### Testar envio de e-mail
```sh
php artisan email:send-order-report
```

## Tecnologias Utilizadas
- **Backend:** Laravel
- **Frontend:** Vue.js + Inertia / Breeze
- **Banco de Dados:** MySQL
- **Docker** para gerenciamento de ambiente
- **Mailpit** para testes de e-mails

**Nota:** O ambiente já está configurado para funcionar automaticamente com Docker. Basta rodar os comandos indicados acima!
