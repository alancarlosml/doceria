# Script de configuração Docker para Windows PowerShell

Write-Host "🚀 Configurando ambiente Docker para Doceria..." -ForegroundColor Cyan

# Verificar se o arquivo .env existe
if (-not (Test-Path .env)) {
    Write-Host "📝 Criando arquivo .env a partir do .env.example..." -ForegroundColor Yellow
    Copy-Item .env.example .env
}

# Ler conteúdo do .env
$envContent = Get-Content .env -Raw

# Configurar variáveis de ambiente para Docker
Write-Host "🔧 Configurando variáveis de ambiente para Docker..." -ForegroundColor Yellow

# Atualizar configurações
$envContent = $envContent -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql'
$envContent = $envContent -replace 'DB_HOST=127\.0\.0\.1', 'DB_HOST=db'
$envContent = $envContent -replace 'DB_PORT=3306', 'DB_PORT=3306'
$envContent = $envContent -replace 'DB_DATABASE=laravel', 'DB_DATABASE=doceria'
$envContent = $envContent -replace 'DB_USERNAME=root', 'DB_USERNAME=doceria'
$envContent = $envContent -replace 'DB_PASSWORD=$', 'DB_PASSWORD=root'
$envContent = $envContent -replace 'REDIS_HOST=127\.0\.0\.1', 'REDIS_HOST=redis'
$envContent = $envContent -replace 'APP_URL=http://localhost', 'APP_URL=http://localhost:8080'

# Salvar arquivo .env atualizado
Set-Content -Path .env -Value $envContent

Write-Host "✅ Configuração concluída!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Próximos passos:" -ForegroundColor Cyan
Write-Host "1. Execute: docker-compose up -d --build"
Write-Host "2. Execute: docker-compose exec app composer install"
Write-Host "3. Execute: docker-compose exec app php artisan key:generate"
Write-Host "4. Execute: docker-compose exec app php artisan migrate"
Write-Host "5. Execute: docker-compose exec app php artisan db:seed (opcional)"
Write-Host ""
Write-Host "🌐 Acesse a aplicação em: http://localhost:8080" -ForegroundColor Green
