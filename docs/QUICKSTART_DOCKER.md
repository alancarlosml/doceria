# ⚡ Início Rápido com Docker

## 🚀 Passo a Passo Rápido

### 1. Configure o ambiente
```powershell
# Windows PowerShell
.\docker\setup.ps1
```

```bash
# Linux/Mac
chmod +x docker/setup.sh && ./docker/setup.sh
```

### 2. Inicie os containers
```bash
docker-compose up -d --build
```

### 3. Instale dependências e configure
```bash
# Instalar dependências PHP
docker-compose exec app composer install

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Executar migrações
docker-compose exec app php artisan migrate

# Criar link de storage
docker-compose exec app php artisan storage:link
```

### 4. Instale dependências Node.js (localmente)
```bash
npm install
npm run dev
```

### 5. Acesse a aplicação
🌐 **http://localhost:8080**

---

## 📚 Documentação Completa

Para mais detalhes, consulte [DOCKER.md](./DOCKER.md)

## 🆘 Problemas Comuns

### Porta 8080 já está em uso
Edite `docker-compose.yml` e altere `"8080:80"` para outra porta, ex: `"8081:80"`

### Erro de permissões
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Limpar tudo e começar de novo
```bash
docker-compose down -v
docker-compose up -d --build
```
