# Doceria Printer Agent

Agente de impressão local para o sistema Doceria. Este aplicativo Electron roda no computador do cliente e gerencia impressoras térmicas diretamente, permitindo que o sistema web hospedado envie comandos de impressão.

## Instalação para Desenvolvimento

```bash
cd printer-agent
npm install
npm start
```

## Build para Produção

```bash
npm run build:win
```

O executável será gerado em `dist/`.

## Estrutura

- `main.js` - Processo principal do Electron
- `preload.js` - Script de pré-carregamento seguro
- `src/server/` - Servidor HTTP local e gerenciamento de impressoras
- `src/ui/` - Interface de configuração
- `src/utils/` - Utilitários (config, logger)
- `src/updater/` - Sistema de atualização automática

## Configuração

O aplicativo salva configurações em `%AppData%\Local\Doceria\PrinterAgent\`.

## Requisitos

- Windows 10 ou superior
- Node.js 18+ (apenas para desenvolvimento)
- Impressora térmica compatível com ESC/POS
