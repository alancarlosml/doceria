# Como Capturar Logs de Impressão - PRODUÇÃO

## ⚠️ IMPORTANTE: Driver Daily

O Laravel usa o driver `daily` que cria arquivos com data no nome. O arquivo **NÃO** será `printer.log`, mas sim:

```
storage/logs/printer-2025-11-05.log
storage/logs/printer-2025-11-06.log
```

Onde a data muda automaticamente a cada dia.

## 📍 Localização dos Logs no Servidor

Os logs estão em:
```
storage/logs/printer-AAAA-MM-DD.log
```

**Exemplo:** Se hoje é 05/11/2025, o arquivo será:
```
storage/logs/printer-2025-11-05.log
```

## 🔍 Como Encontrar o Arquivo Correto

### **Opção 1: Via PowerShell no Servidor Windows**

```powershell
# Listar todos os arquivos de log da impressora
Get-ChildItem storage\logs\printer*.log

# Ver o arquivo de hoje (formato: printer-2025-11-05.log)
$hoje = Get-Date -Format "yyyy-MM-dd"
Get-Content "storage\logs\printer-$hoje.log" -Tail 50

# Ver últimos 50 logs
Get-Content "storage\logs\printer-$hoje.log" -Tail 50

# Ver apenas erros
Get-Content "storage\logs\printer-$hoje.log" | Select-String "ERRO|ERROR|Exception"

# Ver arquivo mais recente automaticamente
Get-Content (Get-ChildItem storage\logs\printer*.log | Sort-Object LastWriteTime -Descending | Select-Object -First 1).FullName -Tail 50
```

### **Opção 2: Via SSH/Terminal Linux**

```bash
# Listar todos os arquivos de log da impressora
ls -la storage/logs/printer*.log

# Ver o arquivo de hoje
cat storage/logs/printer-$(date +%Y-%m-%d).log

# Ver últimos 50 logs
tail -50 storage/logs/printer-$(date +%Y-%m-%d).log

# Ver apenas erros
grep -i "ERRO\|ERROR\|Exception" storage/logs/printer-$(date +%Y-%m-%d).log
```

### **Opção 2: Via FTP/File Manager**

1. Acesse o servidor via FTP ou painel de controle
2. Navegue até: `storage/logs/`
3. Procure por arquivos que começam com `printer-` e terminam com `.log`
4. Abra o arquivo do dia atual (mais recente)

### **Opção 3: Via Painel de Controle do Hosting**

1. Acesse o File Manager do seu hosting
2. Vá até `public_html/storage/logs/` (ou `htdocs/storage/logs/`)
3. Procure por arquivos `printer-2025-XX-XX.log`

## 📋 Onde Ver os Logs Mais Recentes

### **Windows PowerShell:**

```powershell
# Ver arquivo mais recente
Get-ChildItem storage\logs\printer*.log | Sort-Object LastWriteTime -Descending | Select-Object -First 1

# Ver conteúdo do arquivo mais recente
Get-Content (Get-ChildItem storage\logs\printer*.log | Sort-Object LastWriteTime -Descending | Select-Object -First 1).FullName -Tail 100

# Ver apenas erros do arquivo mais recente
Get-Content (Get-ChildItem storage\logs\printer*.log | Sort-Object LastWriteTime -Descending | Select-Object -First 1).FullName | Select-String "ERRO|ERROR|Exception"
```

### **Linux/Bash:**

```bash
# Ver arquivo mais recente
ls -t storage/logs/printer*.log | head -1

# Ver conteúdo do arquivo mais recente
tail -100 $(ls -t storage/logs/printer*.log | head -1)

# Ver apenas erros do arquivo mais recente
grep -i "ERRO\|ERROR\|Exception" $(ls -t storage/logs/printer*.log | head -1)
```

## 🔎 O que Procurar nos Logs

Quando você tentar finalizar uma venda e der erro "impressora não localizada", procure por:

### 1. **Nome da impressora sendo usado:**
```
Usando WindowsPrintConnector {"printer_name":"EPSON TM-T20X Receipt6"}
```

### 2. **Impressoras disponíveis no sistema:**
```
Impressoras disponíveis no sistema: {"printers":["EPSON TM-T20X", "Outra Impressora"]}
```

### 3. **Aviso de impressora não encontrada:**
```
⚠️ IMPRESSORA NÃO ENCONTRADA NA LISTA!
procurada: "EPSON TM-T20X Receipt6"
disponiveis: ["EPSON TM-T20X"]
```

### 4. **Erro de conexão:**
```
❌ ERRO AO CONECTAR:
message: "Impressora não encontrada"
file: "...WindowsPrintConnector.php"
```

## 🛠️ Comandos Úteis para Diagnóstico

### **Windows PowerShell:**

```powershell
$hoje = Get-Date -Format "yyyy-MM-dd"

# Ver últimos 100 logs
Get-Content "storage\logs\printer-$hoje.log" -Tail 100

# Ver apenas tentativas de conexão
Get-Content "storage\logs\printer-$hoje.log" | Select-String "CONECTAR|WindowsPrintConnector"

# Ver configurações usadas
Get-Content "storage\logs\printer-$hoje.log" | Select-String "CONFIGURAÇÃO|config"

# Ver impressoras disponíveis
Get-Content "storage\logs\printer-$hoje.log" | Select-String "Impressoras disponíveis"

# Ver todas as impressões de hoje
Get-Content "storage\logs\printer-$hoje.log" | Select-String "INICIANDO IMPRESSÃO|IMPRESSÃO CONCLUÍDA"
```

### **Linux/Bash:**

```bash
# Ver últimos 100 logs
tail -100 storage/logs/printer-$(date +%Y-%m-%d).log

# Ver apenas tentativas de conexão
grep -i "CONECTAR\|WindowsPrintConnector" storage/logs/printer-$(date +%Y-%m-%d).log

# Ver configurações usadas
grep -i "CONFIGURAÇÃO\|config" storage/logs/printer-$(date +%Y-%m-%d).log

# Ver impressoras disponíveis
grep -i "Impressoras disponíveis" storage/logs/printer-$(date +%Y-%m-%d).log

# Ver todas as impressões de hoje
grep -i "INICIANDO IMPRESSÃO\|IMPRESSÃO CONCLUÍDA" storage/logs/printer-$(date +%Y-%m-%d).log
```

## 📝 Fallback: Log Principal

Se o arquivo `printer-*.log` não existir ou estiver vazio, os logs também são escritos no log principal:

```
storage/logs/laravel.log
```

Procure por linhas que começam com `[PRINTER]`:
```bash
grep "\[PRINTER\]" storage/logs/laravel.log
```

## 💡 Verificação Rápida

### Verificar se os logs estão sendo gerados:

```bash
# Verificar se existe algum arquivo printer
ls -la storage/logs/printer*.log

# Ver tamanho do arquivo de hoje
ls -lh storage/logs/printer-$(date +%Y-%m-%d).log

# Ver última modificação
stat storage/logs/printer-$(date +%Y-%m-%d).log
```

## 🐛 Quando o Erro Acontece

1. **Tente finalizar uma venda** normalmente
2. **Acesse o servidor** via SSH, FTP ou painel
3. **Execute:**

   **Windows PowerShell:**
   ```powershell
   $hoje = Get-Date -Format "yyyy-MM-dd"
   Get-Content "storage\logs\printer-$hoje.log" -Tail 100
   ```
   
   **Ou ver arquivo mais recente:**
   ```powershell
   Get-Content (Get-ChildItem storage\logs\printer*.log | Sort-Object LastWriteTime -Descending | Select-Object -First 1).FullName -Tail 100
   ```
   
   **Linux/Bash:**
   ```bash
   tail -100 storage/logs/printer-$(date +%Y-%m-%d).log
   ```
   
   **Ou se não tiver SSH**, baixe o arquivo `printer-2025-XX-XX.log` via FTP

4. **Procure por:**
   - `❌ ERRO AO CONECTAR`
   - `⚠️ IMPRESSORA NÃO ENCONTRADA`
   - `Impressoras disponíveis no sistema`
   - O nome exato que está sendo usado

## ✅ Verificar Permissões

Se o arquivo não está sendo criado, verifique permissões:

```bash
# Verificar permissões do diretório
ls -ld storage/logs/

# Dar permissão de escrita (se necessário)
chmod 775 storage/logs/
chmod 775 storage/logs/printer*.log
```

## ✅ Verificar Permissões

Se o arquivo não está sendo criado, verifique permissões:

**Windows:**
```powershell
# Verificar se o diretório existe e tem permissão
Test-Path storage\logs
Get-Acl storage\logs

# Verificar se pode criar arquivo
New-Item -Path "storage\logs\test.log" -ItemType File -Force
Remove-Item "storage\logs\test.log"
```

**Linux:**
```bash
# Verificar permissões do diretório
ls -ld storage/logs/

# Dar permissão de escrita (se necessário)
chmod 775 storage/logs/
chmod 775 storage/logs/printer*.log
```

## 📤 Enviar Logs para Análise

### **Windows PowerShell:**
```powershell
# Copiar últimos 100 logs para um arquivo de texto
$hoje = Get-Date -Format "yyyy-MM-dd"
Get-Content "storage\logs\printer-$hoje.log" -Tail 100 | Out-File -FilePath printer_debug.txt
```

### **Linux/Bash:**
```bash
# Copiar últimos 100 logs para um arquivo de texto
tail -100 storage/logs/printer-$(date +%Y-%m-%d).log > printer_debug.txt
```

### Via FTP:
1. Baixe o arquivo `printer-2025-XX-XX.log` (do dia atual)
2. Abra e copie as últimas 100 linhas
3. Envie para análise

## 🔄 Limpar Cache de Configuração

Se os logs não aparecem, limpe o cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## 📊 Informações que Preciso

Quando enviar os logs, inclua:

1. **Nome da impressora configurada** (do código ou banco)
2. **Impressoras disponíveis** (do log)
3. **Mensagem de erro completa**
4. **Data/hora do erro**

Com essas informações, conseguiremos identificar o problema rapidamente!

