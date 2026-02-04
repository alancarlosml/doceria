# 🐳 Guia de Docker - Sistema Doceria

Este guia explica como executar o sistema Doceria usando Docker.

## 📋 Pré-requisitos

- Docker Desktop instalado e rodando
- Docker Compose instalado (geralmente vem com Docker Desktop)

## 🚀 Início Rápido

### 1. Configurar ambiente

**Windows (PowerShell):**
```powershell
.\docker\setup.ps1
```

**Linux/Mac:**
```bash
chmod +x docker/setup.sh
./docker/setup.sh
```

**Ou configure manualmente o arquivo `.env`:**

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=doceria
DB_USERNAME=doceria
DB_PASSWORD=root

REDIS_HOST=redis
REDIS_PORT=6379

APP_URL=http://localhost:8080
```

### 2. Construir e iniciar containers

```bash
docker-compose up -d --build
```

### 3. Instalar dependências

```bash
# Instalar dependências PHP
docker-compose exec app composer install

# Instalar dependências Node.js localmente (recomendado para desenvolvimento)
# Ou use o container Node.js descomentando no docker-compose.yml
npm install
```

### 4. Configurar aplicação Laravel

```bash
# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Executar migrações
docker-compose exec app php artisan migrate

# Popular banco de dados (opcional)
docker-compose exec app php artisan db:seed

# Criar link simbólico para storage
docker-compose exec app php artisan storage:link
```

### 5. Compilar assets (desenvolvimento)

```bash
docker-compose exec node npm run dev
```

Ou para produção:

```bash
docker-compose exec node npm run build
```

## 🌐 Acessar Aplicação

- **Aplicação Web**: http://localhost:8080
- **Vite Dev Server**: http://localhost:5173 (desenvolvimento)

## 📦 Serviços Disponíveis

### App (PHP-FPM)
- Container: `doceria_app`
- Porta interna: 9000
- Comandos úteis:
  ```bash
  docker-compose exec app php artisan [comando]
  docker-compose exec app composer [comando]
  ```

### Nginx
- Container: `doceria_nginx`
- Porta: 8080 (mapeada para 80 interno)

### MySQL
- Container: `doceria_db`
- Porta: 3306
- Credenciais:
  - Database: `doceria`
  - Username: `doceria`
  - Password: `root`
  - Root Password: `root`

### Redis
- Container: `doceria_redis`
- Porta: 6379

### Node.js (Vite) - Opcional
- Container: `doceria_node` (comentado por padrão)
- Porta: 5173
- **Nota:** Para desenvolvimento, recomenda-se executar `npm run dev` localmente

## 🛠️ Comandos Úteis

### Ver logs
```bash
# Todos os serviços
docker-compose logs -f

# Serviço específico
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Parar containers
```bash
docker-compose stop
```

### Parar e remover containers
```bash
docker-compose down
```

### Parar e remover volumes (⚠️ apaga dados do banco)
```bash
docker-compose down -v
```

### Reconstruir containers
```bash
docker-compose up -d --build
```

### Acessar shell do container
```bash
docker-compose exec app bash
docker-compose exec db bash
```

### Executar comandos Artisan
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Executar testes
```bash
docker-compose exec app php artisan test
```

## 🔧 Configurações

### Alterar porta do Nginx

Edite `docker-compose.yml`:
```yaml
nginx:
  ports:
    - "8080:80"  # Altere 8080 para a porta desejada
```

### Alterar credenciais do MySQL

Edite `docker-compose.yml`:
```yaml
db:
  environment:
    MYSQL_DATABASE: doceria
    MYSQL_ROOT_PASSWORD: sua_senha_root
    MYSQL_PASSWORD: sua_senha
    MYSQL_USER: seu_usuario
```

E atualize o `.env` correspondente.

### Usar SQLite ao invés de MySQL

1. Edite `.env`:
```env
DB_CONNECTION=sqlite
# Comente ou remova as linhas do MySQL
```

2. Remova o serviço `db` do `docker-compose.yml` ou comente-o

3. Remova `depends_on: db` dos serviços que dependem dele

## 🐛 Troubleshooting

### Erro de permissões no storage

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Limpar cache do Laravel

```bash
docker-compose exec app php artisan optimize:clear
```

### Resetar banco de dados

```bash
docker-compose exec app php artisan migrate:fresh
docker-compose exec app php artisan db:seed
```

### Reinstalar dependências

```bash
docker-compose exec app rm -rf vendor
docker-compose exec app composer install

docker-compose exec node rm -rf node_modules
docker-compose exec node npm install
```

### Verificar status dos containers

```bash
docker-compose ps
```

### Verificar logs de erro

```bash
docker-compose logs app | grep ERROR
docker-compose logs nginx | grep error
```

## 📝 Notas

- Os volumes são persistidos, então dados do banco e arquivos não são perdidos ao parar os containers
- Para desenvolvimento, os arquivos são sincronizados via volumes
- Para produção, considere usar imagens otimizadas e multi-stage builds

## 🔒 Segurança em Produção

Antes de fazer deploy em produção:

1. Altere todas as senhas padrão
2. Configure SSL/TLS
3. Use variáveis de ambiente seguras
4. Configure firewall adequadamente
5. Use imagens específicas de versão (não `latest`)
6. Configure backups automáticos do banco de dados
