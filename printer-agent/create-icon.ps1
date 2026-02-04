# Script para criar um ícone simples usando recursos do Windows
# Este é um workaround temporário até você criar um ícone personalizado

Write-Host "Criando ícone temporário..." -ForegroundColor Yellow

# Usar ícone padrão do Electron como fallback
$electronIcon = "node_modules\electron\dist\resources\electron.ico"
$targetIcon = "build\icon.ico"

if (Test-Path $electronIcon) {
    Copy-Item $electronIcon $targetIcon -Force
    Write-Host "Ícone copiado de: $electronIcon" -ForegroundColor Green
    Write-Host "Para criar um ícone personalizado:" -ForegroundColor Cyan
    Write-Host "1. Crie uma imagem 256x256 pixels" -ForegroundColor White
    Write-Host "2. Converta para .ico usando:" -ForegroundColor White
    Write-Host "   - https://convertio.co/png-ico/" -ForegroundColor White
    Write-Host "   - https://www.icoconverter.com/" -ForegroundColor White
    Write-Host "3. Salve como build\icon.ico" -ForegroundColor White
} else {
    Write-Host "Ícone do Electron não encontrado. Execute 'npm install' primeiro." -ForegroundColor Red
}
