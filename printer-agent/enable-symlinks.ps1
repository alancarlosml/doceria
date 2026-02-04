# Script para habilitar criação de symbolic links no Windows
# Execute como Administrador

Write-Host "Habilitando modo desenvolvedor para symbolic links..." -ForegroundColor Yellow

# Tentar habilitar via política de grupo (requer admin)
try {
    # Verificar se já está habilitado
    $currentPolicy = Get-ItemProperty -Path "HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\AppModelUnlock" -Name "AllowDevelopmentWithoutDevLicense" -ErrorAction SilentlyContinue
    
    if ($currentPolicy -eq $null -or $currentPolicy.AllowDevelopmentWithoutDevLicense -ne 1) {
        Write-Host "Habilitando modo desenvolvedor..." -ForegroundColor Yellow
        New-ItemProperty -Path "HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\AppModelUnlock" -Name "AllowDevelopmentWithoutDevLicense" -Value 1 -PropertyType DWORD -Force | Out-Null
        Write-Host "Modo desenvolvedor habilitado!" -ForegroundColor Green
    } else {
        Write-Host "Modo desenvolvedor já está habilitado." -ForegroundColor Green
    }
} catch {
    Write-Host "Erro ao habilitar modo desenvolvedor: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Tentando método alternativo..." -ForegroundColor Yellow
}

# Método alternativo: usar mklink diretamente (pode não funcionar)
Write-Host "`nNota: Se os erros persistirem, você pode:" -ForegroundColor Cyan
Write-Host "1. Habilitar 'Modo Desenvolvedor' manualmente:" -ForegroundColor Cyan
Write-Host "   Configurações > Privacidade e Segurança > Para desenvolvedores > Modo de Desenvolvedor" -ForegroundColor Cyan
Write-Host "2. Ou usar o build sem instalador: npm run build:win:dir" -ForegroundColor Cyan
Write-Host "3. Ou testar diretamente: npm start" -ForegroundColor Cyan
