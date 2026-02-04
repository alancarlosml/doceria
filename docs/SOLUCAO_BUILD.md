# Solução para Problemas de Build

## Problema

O electron-builder está falhando ao tentar criar symbolic links para arquivos macOS (que não são necessários para Windows). Mesmo executando como administrador, o Windows pode bloquear a criação de symbolic links.

## Soluções

### Solução 1: Habilitar Modo Desenvolvedor (Recomendado)

1. Abra **Configurações do Windows**
2. Vá em **Privacidade e Segurança** > **Para desenvolvedores**
3. Ative **Modo de Desenvolvedor**
4. Execute o build novamente:
   ```powershell
   cd C:\xampp\htdocs\doceria\printer-agent
   $env:CSC_IDENTITY_AUTO_DISCOVERY='false'
   npm run build:win
   ```

### Solução 2: Usar Build Sem Instalador

Para desenvolvimento/teste, você pode gerar apenas os arquivos executáveis sem criar o instalador:

```powershell
cd C:\xampp\htdocs\doceria\printer-agent
$env:CSC_IDENTITY_AUTO_DISCOVERY='false'
npm run build:win:dir
```

Isso criará os arquivos em `dist/win-unpacked/` que você pode executar diretamente.

### Solução 3: Testar Sem Build

Para testar o aplicativo sem fazer build:

```powershell
cd C:\xampp\htdocs\doceria\printer-agent
npm start
```

### Solução 4: Ignorar Erros de Symbolic Link

Os erros sobre `libcrypto.dylib` e `libssl.dylib` são apenas avisos sobre arquivos macOS que não são necessários. O build pode continuar mesmo com esses avisos. Verifique se o arquivo foi criado:

```powershell
Get-ChildItem dist\*.exe -ErrorAction SilentlyContinue
```

Se o arquivo `.exe` existir em `dist/`, o build foi bem-sucedido apesar dos avisos.

### Solução 5: Usar Versão Mais Antiga do Electron-Builder

Se nada funcionar, você pode tentar usar uma versão mais antiga:

```powershell
npm install electron-builder@23 --save-dev
npm run build:win
```

## Verificação

Após executar o build, verifique:

```powershell
# Verificar se arquivos foram criados
Get-ChildItem dist\ -Recurse | Select-Object Name, Length, LastWriteTime

# Verificar se executável existe
Test-Path "dist\Doceria Printer Agent Setup *.exe"
# ou
Test-Path "dist\win-unpacked\Doceria Printer Agent.exe"
```

## Notas Importantes

- Os erros de symbolic link são sobre arquivos macOS que **não são necessários** para Windows
- O build pode ser bem-sucedido mesmo com esses avisos
- Para produção, é recomendado habilitar o Modo Desenvolvedor
- Para testes rápidos, use `npm start` ou `npm run build:win:dir`
