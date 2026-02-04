#!/bin/sh

set -e

echo "Aguardando serviços ficarem prontos..."

# Aguardar MySQL estar pronto
until php artisan db:monitor 2>/dev/null || nc -z db 3306; do
    echo "Aguardando MySQL..."
    sleep 2
done

echo "MySQL está pronto!"

# Executar migrações
echo "Executando migrações..."
php artisan migrate --force

# Limpar e otimizar cache
echo "Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Aplicação pronta!"

exec "$@"
