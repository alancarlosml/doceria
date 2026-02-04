# Instruções de Build - Doceria Printer Agent

## Problema Conhecido

O electron-builder pode mostrar erros sobre symbolic links ao tentar extrair arquivos macOS (que não são necessários para Windows). Esses erros podem ser ignorados se o build continuar.

## Solução 1: Executar como Administrador (Recomendado)

1. Abra o PowerShell **como Administrador**
2. Navegue até a pasta do projeto:
   ```powershell
   cd C:\xampp\htdocs\doceria\printer-agent
   ```
3. Execute o build:
   ```powershell
   $env:CSC_IDENTITY_AUTO_DISCOVERY='false'
   npm run build:win
   ```

## Solução 2: Build sem Assinatura (Desenvolvimento)

Se você não precisa de assinatura de código (para desenvolvimento/teste):

```powershell
cd C:\xampp\htdocs\doceria\printer-agent
$env:CSC_IDENTITY_AUTO_DISCOVERY='false'
npx electron-builder --win --config.win.sign=null
```

## Solução 3: Limpar Cache e Tentar Novamente

```powershell
# Limpar cache do electron-builder
Remove-Item -Recurse -Force "$env:LOCALAPPDATA\electron-builder\Cache\winCodeSign" -ErrorAction SilentlyContinue

# Executar build
$env:CSC_IDENTITY_AUTO_DISCOVERY='false'
npm run build:win
```

## Verificar se o Build Foi Bem-Sucedido

Após executar o build, verifique se o arquivo foi criado:

```powershell
Test-Path "dist\Doceria Printer Agent Setup *.exe"
```

Se o arquivo existir em `dist\`, o build foi bem-sucedido mesmo com os avisos de symbolic link.

## Testar o Aplicativo Sem Build

Para testar o aplicativo sem fazer o build:

```powershell
cd C:\xampp\htdocs\doceria\printer-agent
npm start
```

Isso iniciará o aplicativo Electron diretamente para testes.

## Notas

- Os erros sobre `libcrypto.dylib` e `libssl.dylib` são arquivos macOS que não são necessários para Windows
- O build pode continuar mesmo com esses avisos
- Para produção, execute como administrador para evitar qualquer problema
