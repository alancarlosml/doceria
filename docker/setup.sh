#!/bin/bash

set -e

echo "🚀 Configurando ambiente Docker para Doceria..."

# Verificar se o arquivo .env existe
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env a partir do .env.example..."
    cp .env.example .env
fi

# Configurar variáveis de ambiente para Docker
echo "🔧 Configurando variáveis de ambiente para Docker..."

# Atualizar .env com configurações do Docker
sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env
sed -i 's/DB_HOST=127.0.0.1/DB_HOST=db/' .env
sed -i 's/DB_PORT=3306/DB_PORT=3306/' .env
sed -i 's/DB_DATABASE=laravel/DB_DATABASE=doceria/' .env
sed -i 's/DB_USERNAME=root/DB_USERNAME=doceria/' .env
sed -i 's/DB_PASSWORD=/DB_PASSWORD=root/' .env
sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/' .env
sed -i 's|APP_URL=http://localhost|APP_URL=http://localhost:8080|' .env

echo "✅ Configuração concluída!"
echo ""
echo "📋 Próximos passos:"
echo "1. Execute: docker-compose up -d"
echo "2. Execute: docker-compose exec app composer install"
echo "3. Execute: docker-compose exec app php artisan key:generate"
echo "4. Execute: docker-compose exec app php artisan migrate"
echo "5. Execute: docker-compose exec app php artisan db:seed (opcional)"
echo ""
echo "🌐 Acesse a aplicação em: http://localhost:8080"
