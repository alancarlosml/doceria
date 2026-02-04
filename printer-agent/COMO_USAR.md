# Como Usar o Doceria Printer Agent

## Iniciando o Aplicativo

Após instalar, o aplicativo iniciará automaticamente com o Windows (se configurado).

Você verá um ícone na **bandeja do sistema** (área de notificação, próximo ao relógio).

## Abrindo a Janela de Configurações

### Método 1: Duplo Clique
- **Duplo clique** no ícone na bandeja do sistema

### Método 2: Menu de Contexto
- **Clique direito** no ícone
- Selecione **"Abrir Configurações"**

### Método 3: Clique Simples (pode variar)
- **Clique simples** no ícone (pode abrir o menu ou a janela dependendo do Windows)

## Funcionalidades

### Menu de Contexto (Botão Direito)

- **Abrir Configurações**: Abre a janela principal de configurações
- **Testar Impressão**: Envia um cupom de teste para a impressora configurada
- **Sair**: Fecha o aplicativo completamente

### Janela de Configurações

A janela permite:

1. **Ver Status do Agente**
   - Status do servidor HTTP
   - Impressora configurada
   - Versão do aplicativo

2. **Configurar Impressora**
   - Listar impressoras disponíveis
   - Selecionar impressora padrão
   - Testar impressão

3. **Configurações do Servidor**
   - Alterar porta (padrão: 8080)
   - Habilitar/desabilitar auto-start
   - Habilitar/desabilitar atualizações

4. **Ver Logs**
   - Logs de impressão
   - Erros e avisos
   - Atividades do servidor

## Solução de Problemas

### Ícone não aparece

Se o ícone não aparecer na bandeja:
1. Verifique se o aplicativo está rodando (Processos do Windows)
2. Clique na seta "^" na bandeja para mostrar ícones ocultos
3. Arraste o ícone para a área visível

### Janela não abre

Se a janela não abrir ao clicar:
1. Tente **duplo clique** no ícone
2. Use **botão direito > Abrir Configurações**
3. Verifique os logs em `%AppData%\Local\Doceria\PrinterAgent\logs\app.log`

### Clique não funciona

No Windows, o comportamento pode variar:
- **Duplo clique** sempre funciona
- **Botão direito** sempre abre o menu
- **Clique simples** pode abrir menu ou janela dependendo da versão do Windows

## Verificar se Está Funcionando

1. Abra o navegador
2. Acesse: `http://localhost:8080/status`
3. Deve retornar um JSON com informações do agente

Se retornar dados, o agente está funcionando corretamente!
