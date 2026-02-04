# 📁 Diretório Docker

Este diretório contém todas as configurações e scripts relacionados ao Docker.

## 📂 Estrutura

```
docker/
├── nginx/
│   └── default.conf          # Configuração do Nginx
├── php/
│   └── local.ini             # Configurações PHP
├── mysql/
│   └── my.cnf                # Configurações MySQL
├── setup.sh                  # Script de configuração (Linux/Mac)
├── setup.ps1                 # Script de configuração (Windows)
└── entrypoint.sh             # Script de inicialização do container
```

## 🔧 Arquivos de Configuração

### Nginx (`nginx/default.conf`)
Configuração do servidor web Nginx que faz proxy reverso para o PHP-FPM.

### PHP (`php/local.ini`)
Configurações personalizadas do PHP, incluindo limites de upload e memória.

### MySQL (`mysql/my.cnf`)
Configurações do MySQL para melhor performance e compatibilidade.

## 🚀 Scripts

### `setup.sh` / `setup.ps1`
Scripts que configuram automaticamente o arquivo `.env` para uso com Docker.

### `entrypoint.sh`
Script executado quando o container PHP inicia. Pode ser usado para executar migrações e outras tarefas de inicialização.

## 📝 Uso

Execute os scripts de setup antes de iniciar os containers:

**Windows:**
```powershell
.\docker\setup.ps1
```

**Linux/Mac:**
```bash
chmod +x docker/setup.sh
./docker/setup.sh
```
