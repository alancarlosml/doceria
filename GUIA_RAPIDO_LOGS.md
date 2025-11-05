# 🚀 GUIA RÁPIDO - Capturar Logs em Produção

## ⚡ Comando Rápido (Windows PowerShell)

```powershell
# Ver últimos 100 logs de hoje
$hoje = Get-Date -Format "yyyy-MM-dd"
Get-Content "storage\logs\printer-$hoje.log" -Tail 100

# OU ver arquivo mais recente automaticamente
Get-Content (Get-ChildItem storage\logs\printer*.log | Sort-Object LastWriteTime -Descending | Select-Object -First 1).FullName -Tail 100
```

## 📁 Onde Está o Arquivo?

**NÃO procure por `printer.log`!**

O arquivo tem o nome com a data:
```
storage/logs/printer-2025-11-05.log  ← Hoje é 05/11/2025
storage/logs/printer-2025-11-06.log  ← Amanhã será 06/11/2025
```

## 🔍 Como Encontrar

### Via FTP/File Manager:
1. Acesse `storage/logs/`
2. Procure arquivos que começam com `printer-` e terminam com `.log`
3. Abra o arquivo do dia atual (mais recente)

### Via PowerShell:
```powershell
# Listar todos os arquivos printer
Get-ChildItem storage\logs\printer*.log

# Ver o mais recente
Get-ChildItem storage\logs\printer*.log | Sort-Object LastWriteTime -Descending | Select-Object -First 1
```

## 🐛 Quando Der Erro

1. Tente finalizar uma venda
2. Execute:
   ```powershell
   $hoje = Get-Date -Format "yyyy-MM-dd"
   Get-Content "storage\logs\printer-$hoje.log" -Tail 100
   ```
3. Procure por:
   - `❌ ERRO AO CONECTAR`
   - `⚠️ IMPRESSORA NÃO ENCONTRADA`
   - `Impressoras disponíveis no sistema`

## 📤 Enviar Logs

```powershell
# Copiar últimos 100 logs
$hoje = Get-Date -Format "yyyy-MM-dd"
Get-Content "storage\logs\printer-$hoje.log" -Tail 100 | Out-File -FilePath printer_debug.txt
```

Envie o arquivo `printer_debug.txt` ou copie o conteúdo!

