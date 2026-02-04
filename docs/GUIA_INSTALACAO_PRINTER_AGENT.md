# Guia de Instalação - Doceria Printer Agent

## Visão Geral

O **Doceria Printer Agent** é um executável Windows que roda localmente no computador do cliente e gerencia impressoras térmicas diretamente. Ele funciona como uma ponte entre o sistema web (hospedado na nuvem) e as impressoras locais.

## Requisitos

- Windows 10 ou superior
- Impressora térmica compatível com ESC/POS (ex: EPSON TM-T20X)
- Conexão com a internet (para atualizações)
- Permissões de administrador para instalação

## Instalação

### Passo 1: Baixar o Instalador

1. Acesse a página de download no sistema: **Configurações > Impressora > Baixar Instalador**
2. Clique em "Baixar Instalador"
3. Salve o arquivo `Doceria-Printer-Agent-Setup.exe` no seu computador

### Passo 2: Executar o Instalador

1. Localize o arquivo baixado e execute-o
2. Se o Windows solicitar permissões de administrador, clique em "Sim"
3. Siga as instruções do instalador:
   - Escolha o diretório de instalação (padrão: `C:\Program Files\Doceria\PrinterAgent\`)
   - Aceite os termos de uso
   - Clique em "Instalar"

### Passo 3: Verificar Instalação

Após a instalação:
1. O agente iniciará automaticamente
2. Um ícone aparecerá na bandeja do sistema (próximo ao relógio)
3. Clique com o botão direito no ícone para acessar o menu

## Configuração

### Configurar Impressora Padrão

1. Acesse **Configurações > Impressora** no sistema web
2. Na seção "Doceria Printer Agent", clique em "Verificar"
3. Se o agente estiver rodando, você verá o status "Rodando"
4. Clique em "Atualizar Lista" para ver as impressoras disponíveis
5. Selecione sua impressora térmica na lista
6. A impressora será configurada automaticamente

### Configurações Avançadas

Para acessar as configurações avançadas do agente:
1. Clique com o botão direito no ícone do agente na bandeja do sistema
2. Selecione "Abrir Configurações"
3. Na janela que abrir, você pode:
   - Alterar a impressora padrão
   - Configurar a porta do servidor (padrão: 8080)
   - Habilitar/desabilitar auto-start
   - Habilitar/desabilitar atualizações automáticas
   - Ver logs de impressão

## Uso

### Impressão Automática

Após configurado, o agente funciona automaticamente:
1. Quando uma venda é finalizada no sistema web
2. O sistema detecta o agente rodando
3. Envia o comando de impressão para o agente
4. O agente imprime o recibo diretamente na impressora configurada

### Teste de Impressão

Para testar se a configuração está funcionando:
1. Acesse **Configurações > Impressora**
2. Na seção do Printer Agent, clique em "Testar Impressão"
3. Um cupom de teste será impresso

## Solução de Problemas

### Agente não inicia automaticamente

**Solução:**
1. Abra o menu Iniciar
2. Procure por "Doceria Printer Agent"
3. Execute o aplicativo manualmente
4. Para configurar auto-start permanente:
   - Abra as configurações do agente
   - Marque "Iniciar automaticamente com o Windows"

### Impressora não aparece na lista

**Possíveis causas:**
- Impressora não está instalada no Windows
- Impressora está desligada
- Driver da impressora não está instalado

**Solução:**
1. Verifique se a impressora está ligada e conectada
2. Abra "Dispositivos e Impressoras" no Windows
3. Certifique-se de que a impressora aparece na lista
4. Se não aparecer, instale o driver da impressora
5. Clique em "Atualizar Lista" nas configurações do agente

### Erro ao imprimir

**Possíveis causas:**
- Impressora não configurada
- Impressora offline ou sem papel
- Problema de comunicação

**Solução:**
1. Verifique os logs do agente (acessível pelo ícone na bandeja)
2. Certifique-se de que a impressora está configurada corretamente
3. Teste a impressão diretamente pelo Windows primeiro
4. Verifique se há papel na impressora
5. Reinicie o agente se necessário

### Porta 8080 já está em uso

**Solução:**
1. Abra as configurações do agente
2. Altere a porta do servidor para outra (ex: 8081)
3. Reinicie o agente
4. **Nota:** Se alterar a porta, você precisará atualizar a configuração no sistema web também

### Agente não é detectado pelo sistema web

**Possíveis causas:**
- Agente não está rodando
- Firewall bloqueando conexão
- Porta incorreta

**Solução:**
1. Verifique se o agente está rodando (ícone na bandeja)
2. Verifique se o firewall do Windows não está bloqueando
3. Teste acessando `http://localhost:8080/status` no navegador
4. Se não funcionar, verifique a porta configurada

## Desinstalação

Para desinstalar o Printer Agent:

1. Abra "Adicionar ou Remover Programas" no Windows
2. Procure por "Doceria Printer Agent"
3. Clique em "Desinstalar"
4. Siga as instruções do desinstalador

**Nota:** Os logs e configurações serão mantidos em `%AppData%\Local\Doceria\PrinterAgent\` caso você queira reinstalar.

## Atualizações

O agente verifica atualizações automaticamente:
- Na inicialização
- A cada 4 horas enquanto está rodando

Quando uma atualização está disponível:
1. O agente baixa a atualização em segundo plano
2. Uma notificação aparece quando a atualização está pronta
3. Você pode escolher instalar agora ou depois
4. Após instalar, o agente reinicia automaticamente

## Suporte

Para mais ajuda:
1. Verifique os logs do agente (acessível pelo ícone na bandeja)
2. Consulte a documentação do sistema
3. Entre em contato com o suporte técnico

## Arquivos e Localizações

- **Instalação:** `C:\Program Files\Doceria\PrinterAgent\`
- **Configurações:** `%AppData%\Local\Doceria\PrinterAgent\config.json`
- **Logs:** `%AppData%\Local\Doceria\PrinterAgent\logs\app.log`
- **Atalho:** Menu Iniciar > Doceria Printer Agent

## Segurança

- O agente roda apenas em `localhost` (127.0.0.1)
- Não aceita conexões externas
- Usa autenticação por token (gerado automaticamente)
- Logs são armazenados localmente

## Próximos Passos

Após instalar e configurar:
1. Teste a impressão usando o botão "Testar Impressão"
2. Finalize uma venda de teste no sistema
3. Verifique se o recibo foi impresso corretamente
4. Configure outras impressoras se necessário (múltiplas lojas)
