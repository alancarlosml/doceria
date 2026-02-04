# Script para criar um ícone básico para o tray
# Execute este script antes de fazer o build

Write-Host "Criando ícone temporário para o tray..." -ForegroundColor Yellow

$buildDir = "build"
if (-not (Test-Path $buildDir)) {
    New-Item -ItemType Directory -Path $buildDir | Out-Null
}

# Verificar se há um ícone do Electron disponível
$electronIcon = "node_modules\electron\dist\resources\electron.ico"
$targetIcon = "$buildDir\icon.ico"

if (Test-Path $electronIcon) {
    Copy-Item $electronIcon $targetIcon -Force
    Write-Host "Ícone copiado de: $electronIcon" -ForegroundColor Green
    Write-Host "Ícone criado em: $targetIcon" -ForegroundColor Green
} else {
    Write-Host "Ícone do Electron não encontrado." -ForegroundColor Yellow
    Write-Host "Criando arquivo placeholder..." -ForegroundColor Yellow
    
    # Criar um arquivo vazio como placeholder
    # O Electron usará um ícone padrão do sistema
    New-Item -ItemType File -Path $targetIcon -Force | Out-Null
    Write-Host "Arquivo placeholder criado." -ForegroundColor Yellow
    Write-Host "Para criar um ícone personalizado:" -ForegroundColor Cyan
    Write-Host "1. Crie uma imagem 256x256 pixels (PNG)" -ForegroundColor White
    Write-Host "2. Converta para .ico em:" -ForegroundColor White
    Write-Host "   - https://convertio.co/png-ico/" -ForegroundColor White
    Write-Host "   - https://www.icoconverter.com/" -ForegroundColor White
    Write-Host "3. Salve como: $targetIcon" -ForegroundColor White
}

Write-Host "`nPronto! Agora você pode fazer o build novamente." -ForegroundColor Green
