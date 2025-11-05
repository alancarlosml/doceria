# Como Capturar Logs de Impressão

Este documento explica como visualizar e analisar os logs da impressora para diagnosticar problemas.

## 📍 Localização dos Logs

Os logs da impressora são salvos em:
```
storage/logs/printer.log
```

O Laravel cria um arquivo diário automaticamente, então você também pode encontrar:
```
storage/logs/printer-2025-01-15.log
storage/logs/printer-2025-01-16.log
```

## 🔍 Como Visualizar os Logs

### **Opção 1: Via Terminal (PowerShell)**

```powershell
# Ver os últimos 50 logs
Get-Content storage\logs\printer.log -Tail 50

# Ver todos os logs em tempo real (monitorar)
Get-Content storage\logs\printer.log -Wait

# Ver logs filtrados por erro
Get-Content storage\logs\printer.log | Select-String "ERRO"

# Ver logs de conexão
Get-Content storage\logs\printer.log | Select-String "CONECTAR"
```

### **Opção 2: Via CMD**

```cmd
# Ver os últimos logs
type storage\logs\printer.log | more

# Ver e filtrar erros
findstr "ERRO" storage\logs\printer.log
```

### **Opção 3: Via Navegador (se tiver acesso)**

Acesse: `http://seu-dominio.com/storage/logs/printer.log`

**⚠️ IMPORTANTE:** Remova o acesso público aos logs em produção!

### **Opção 4: Via Editor de Texto**

Abra o arquivo diretamente:
- Notepad: `storage\logs\printer.log`
- VS Code: `code storage\logs\printer.log`

## 📋 O que os Logs Mostram

Os logs incluem informações detalhadas sobre:

1. **Configuração da Impressora**
   - Tipo de conexão (Windows/Rede/Arquivo)
   - Nome da impressora
   - Configurações aplicadas

2. **Tentativa de Conexão**
   - Configuração recebida
   - Tipo de conector usado
   - Sucesso ou falha na conexão

3. **Processo de Impressão**
   - Preparação dos dados
   - Cada etapa (cabeçalho, pedido, rodapé)
   - Cortes de papel

4. **Erros Detalhados**
   - Mensagem de erro completa
   - Arquivo e linha onde ocorreu
   - Stack trace completo

## 🔎 Exemplos de Logs

### Log de Sucesso:
```
[2025-01-15 14:30:25] local.INFO: === OBTENDO CONFIGURAÇÕES DA IMPRESSORA ===
[2025-01-15 14:30:25] local.INFO: Tipo de impressora configurado: {"type":null}
[2025-01-15 14:30:25] local.INFO: Usando configuração padrão Windows: {"windows_printer_name":"EPSON TM-T20X Receipt6"}
[2025-01-15 14:30:25] local.INFO: === TENTANDO CONECTAR À IMPRESSORA ===
[2025-01-15 14:30:25] local.INFO: Usando WindowsPrintConnector {"printer_name":"EPSON TM-T20X Receipt6"}
[2025-01-15 14:30:25] local.INFO: ✅ CONEXÃO ESTABELECIDA COM SUCESSO
```

### Log de Erro:
```
[2025-01-15 14:30:25] local.ERROR: ❌ ERRO AO CONECTAR: {
    "message": "Impressora não encontrada",
    "file": "C:\\xampp\\htdocs\\doceria\\vendor\\mike42\\escpos-php\\src\\Mike42\\Escpos\\PrintConnectors\\WindowsPrintConnector.php",
    "line": 45,
    "trace": "..."
}
```

## 🛠️ Comandos Úteis para Diagnóstico

### Ver apenas erros:
```powershell
Get-Content storage\logs\printer.log | Select-String "ERRO|ERROR|Exception"
```

### Ver tentativas de conexão:
```powershell
Get-Content storage\logs\printer.log | Select-String "CONECTAR|WindowsPrintConnector|NetworkPrintConnector"
```

### Ver configurações usadas:
```powershell
Get-Content storage\logs\printer.log | Select-String "CONFIGURAÇÃO|config"
```

### Ver impressões completas:
```powershell
Get-Content storage\logs\printer.log | Select-String "INICIANDO IMPRESSÃO|IMPRESSÃO CONCLUÍDA"
```

### Ver últimas 100 linhas:
```powershell
Get-Content storage\logs\printer.log -Tail 100
```

## 📊 Analisar Logs de Hoje

```powershell
# Ver logs de hoje apenas
Get-Content storage\logs\printer.log | Select-String "$(Get-Date -Format 'yyyy-MM-dd')"
```

## 🐛 Quando o Erro Acontece

Quando você tentar finalizar uma venda e der erro:

1. **Abra o PowerShell** no diretório do projeto
2. **Execute:**
   ```powershell
   Get-Content storage\logs\printer.log -Tail 50
   ```
3. **Procure por:**
   - `❌ ERRO AO CONECTAR`
   - `Exception`
   - O nome da impressora que está sendo usado
   - Mensagens sobre "Impressora não encontrada"

## 💡 Dicas Importantes

1. **Limpar logs antigos:** Os logs são mantidos por 30 dias. Para limpar manualmente:
   ```powershell
   Remove-Item storage\logs\printer-*.log
   ```

2. **Verificar permissões:** Certifique-se de que o PHP tem permissão para escrever em `storage/logs/`

3. **Logs em produção:** Em produção, considere reduzir o nível de log para `info` ou `warning` no `.env`:
   ```
   LOG_LEVEL=info
   ```

## 📝 Enviar Logs para Suporte

Se precisar enviar os logs para análise:

```powershell
# Copiar últimos 100 logs para um arquivo
Get-Content storage\logs\printer.log -Tail 100 | Out-File -FilePath printer_erro.txt
```

Ou simplesmente copie o conteúdo do arquivo `storage\logs\printer.log` após reproduzir o erro.

## ✅ Verificação Rápida

Para verificar se os logs estão sendo gerados:

```powershell
# Ver se o arquivo existe
Test-Path storage\logs\printer.log

# Ver tamanho do arquivo
(Get-Item storage\logs\printer.log).Length
```

Se o arquivo não existir ou estiver vazio, verifique as permissões do diretório `storage/logs/`.

