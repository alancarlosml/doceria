@echo off
REM Script para build sem assinatura de código
REM Execute este arquivo como Administrador

set CSC_IDENTITY_AUTO_DISCOVERY=false
set SKIP_NOTARIZATION=true

echo Building Doceria Printer Agent...
echo.

npm run build:win

if %ERRORLEVEL% EQU 0 (
    echo.
    echo Build concluido com sucesso!
    echo Arquivo instalador em: dist\
) else (
    echo.
    echo Build falhou. Verifique os erros acima.
    echo.
    echo Se os erros forem apenas sobre symbolic links (libcrypto.dylib, libssl.dylib),
    echo isso pode ser ignorado se o arquivo .exe foi criado em dist\
    echo.
    pause
)
