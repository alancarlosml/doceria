const { app, BrowserWindow, Tray, Menu, ipcMain, dialog, nativeImage } = require('electron');
const path = require('path');
const { startHttpServer } = require('./src/server/http-server');
const { initializePrinterManager } = require('./src/server/printer-manager');
const { initializeAutoUpdater } = require('./src/updater/auto-updater');
const Config = require('./src/utils/config');
const Logger = require('./src/utils/logger');

let mainWindow = null;
let tray = null;
let httpServer = null;

// Evitar múltiplas instâncias
const gotTheLock = app.requestSingleInstanceLock();

if (!gotTheLock) {
  app.quit();
} else {
  app.on('second-instance', () => {
    if (mainWindow) {
      if (mainWindow.isMinimized()) mainWindow.restore();
      mainWindow.focus();
    }
  });
}

function createWindow() {
  if (mainWindow && !mainWindow.isDestroyed()) {
    Logger.info('Janela já existe, retornando');
    return;
  }

  Logger.info('Criando nova janela...');
  
  // Tentar carregar ícone, se não existir usar null (padrão do sistema)
  let iconPath = path.join(__dirname, 'build/icon.ico');
  const fs = require('fs');
  if (!fs.existsSync(iconPath)) {
    iconPath = null; // Usar ícone padrão do Electron
  }

  mainWindow = new BrowserWindow({
    width: 600,
    height: 700,
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true
    },
    icon: iconPath,
    show: false, // Não mostrar automaticamente
    resizable: true,
    minimizable: true,
    maximizable: false,
    skipTaskbar: false // Aparecer na barra de tarefas quando visível
  });

  mainWindow.loadFile('src/ui/index.html');

  // Eventos da janela
  mainWindow.once('ready-to-show', () => {
    Logger.info('Janela pronta para ser exibida');
  });
  
  mainWindow.webContents.once('did-finish-load', () => {
    Logger.info('Conteúdo da janela carregado');
  });

  mainWindow.on('close', (event) => {
    if (!app.isQuitting) {
      event.preventDefault();
      mainWindow.hide();
      Logger.info('Janela ocultada (app continua rodando)');
      return false;
    }
  });

  mainWindow.on('closed', () => {
    Logger.info('Janela fechada');
    mainWindow = null;
  });
  
  mainWindow.on('show', () => {
    Logger.info('Janela sendo exibida');
  });
}

function createTray() {
  const fs = require('fs');
  
  Logger.info('Criando tray icon...');
  
  // Tentar várias fontes de ícone em ordem de prioridade
  let iconPath = null;
  let iconImage = null;
  
  // 1. Tentar usar o ícone do executável (quando compilado)
  if (app.isPackaged) {
    const exePath = process.execPath;
    Logger.info('Aplicativo empacotado, tentando usar ícone do executável', { exePath });
    try {
      // Tentar extrair o ícone do executável
      iconImage = nativeImage.createFromPath(exePath);
      if (iconImage && !iconImage.isEmpty()) {
        Logger.info('Ícone do executável carregado com sucesso');
        // Redimensionar para tamanho do tray (16x16 é padrão)
        const sizes = iconImage.getSize();
        if (sizes.width !== 16 || sizes.height !== 16) {
          iconImage = iconImage.resize({ width: 16, height: 16 });
        }
      } else {
        Logger.warn('Ícone do executável está vazio, tentando alternativas...');
        iconImage = null;
      }
    } catch (error) {
      Logger.warn('Não foi possível carregar ícone do executável', { error: error.message });
      iconImage = null;
    }
    
    // Se não conseguiu do executável, tentar recursos do app.asar
    if (!iconImage) {
      try {
        // Tentar carregar do diretório de recursos (quando empacotado)
        const resourcesPath = process.resourcesPath;
        const iconInResources = path.join(resourcesPath, 'app.asar', 'build', 'icon.ico');
        if (fs.existsSync(iconInResources)) {
          Logger.info('Tentando ícone em recursos', { iconInResources });
          iconImage = nativeImage.createFromPath(iconInResources);
          if (iconImage && !iconImage.isEmpty()) {
            const sizes = iconImage.getSize();
            if (sizes.width !== 16 || sizes.height !== 16) {
              iconImage = iconImage.resize({ width: 16, height: 16 });
            }
            Logger.info('Ícone carregado dos recursos');
          }
        }
      } catch (error) {
        Logger.warn('Erro ao carregar ícone dos recursos', { error: error.message });
      }
    }
  }
  
  // 2. Tentar arquivo de ícone personalizado (desenvolvimento ou se não empacotado)
  if (!iconImage) {
    // Quando empacotado, __dirname aponta para app.asar
    // Quando não empacotado, aponta para o diretório do projeto
    iconPath = path.join(__dirname, 'build/icon.ico');
    
    // Se empacotado, tentar caminho alternativo
    if (app.isPackaged && !fs.existsSync(iconPath)) {
      const resourcesPath = process.resourcesPath;
      iconPath = path.join(resourcesPath, 'app.asar', 'build', 'icon.ico');
    }
    
    if (fs.existsSync(iconPath)) {
      Logger.info('Usando ícone personalizado', { iconPath });
    } else {
      // 3. Tentar ícone do Electron (desenvolvimento)
      const electronIcon = path.join(__dirname, 'node_modules/electron/dist/resources/electron.ico');
      if (fs.existsSync(electronIcon)) {
        iconPath = electronIcon;
        Logger.info('Usando ícone do Electron', { iconPath });
      } else {
        iconPath = null;
      }
    }
  }
  
  // Criar o tray
  try {
    if (iconImage) {
      tray = new Tray(iconImage);
      Logger.info('Tray criado com ícone do executável');
    } else if (iconPath) {
      tray = new Tray(iconPath);
      Logger.info('Tray criado com ícone de arquivo', { iconPath });
    } else {
      // Criar ícone temporário melhorado
      Logger.warn('Nenhum ícone encontrado, criando ícone temporário melhorado');
      iconImage = createBetterTrayIcon();
      tray = new Tray(iconImage);
      Logger.info('Tray criado com ícone temporário');
    }
  } catch (error) {
    Logger.error('Erro ao criar tray', { error: error.message });
    // Último recurso: ícone mínimo
    try {
      iconImage = createBetterTrayIcon();
      tray = new Tray(iconImage);
      Logger.warn('Tray criado com fallback');
    } catch (finalError) {
      Logger.error('Erro fatal ao criar tray', { error: finalError.message });
      return;
    }
  }
  
  setupTrayMenu();
}

function createBetterTrayIcon() {
  // Criar um ícone melhor: quadrado roxo com "P" branco
  // PNG 16x16 com fundo roxo (#667eea) e letra "P" branca
  const canvas = Buffer.from(
    'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAAsTAAALEwEAmpwYAAAA' +
    'GXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHhJREFUeNpi/P//PwMlgImBQkAx' +
    'A0YGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYG' +
    'BgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYG' +
    'BgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYG' +
    'BgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYGBgYG',
    'base64'
  );
  
  // Criar um ícone simples usando SVG
  const svgIcon = `<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg">
    <rect width="16" height="16" fill="#667eea" rx="2"/>
    <text x="8" y="13" font-size="11" font-weight="bold" text-anchor="middle" fill="white" font-family="Arial, sans-serif">P</text>
  </svg>`;
  
  try {
    return nativeImage.createFromDataURL('data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgIcon));
  } catch (error) {
    // Fallback: criar ícone mínimo
    const minimalIcon = Buffer.from(
      'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
      'base64'
    );
    return nativeImage.createFromBuffer(minimalIcon).resize({ width: 16, height: 16 });
  }
}

function setupTrayMenu() {
  if (!tray) {
    Logger.error('Tray não foi criado, não é possível configurar menu');
    return;
  }

  Logger.info('Configurando menu de contexto do tray...');

  const contextMenu = Menu.buildFromTemplate([
    {
      label: 'Abrir Configurações',
      click: () => {
        Logger.info('Menu: Abrir Configurações clicado');
        showWindow();
      }
    },
    {
      label: 'Testar Impressão',
      click: async () => {
        Logger.info('Menu: Testar Impressão clicado');
        const { printTestReceipt } = require('./src/server/printer-manager');
        try {
          await printTestReceipt();
          dialog.showMessageBox(mainWindow || null, {
            type: 'info',
            title: 'Sucesso',
            message: 'Cupom de teste enviado para impressão!'
          });
        } catch (error) {
          Logger.error('Erro ao testar impressão', { error: error.message });
          dialog.showErrorBox('Erro', `Erro ao imprimir: ${error.message}`);
        }
      }
    },
    { type: 'separator' },
    {
      label: 'Sair',
      click: () => {
        Logger.info('Menu: Sair clicado');
        app.isQuitting = true;
        app.quit();
      }
    }
  ]);

  try {
    tray.setToolTip('Doceria Printer Agent\nDuplo clique ou botão direito > Abrir Configurações');
    tray.setContextMenu(contextMenu);
    Logger.info('Menu de contexto configurado com sucesso');
  } catch (error) {
    Logger.error('Erro ao configurar menu de contexto', { error: error.message });
    return;
  }

  // Duplo clique sempre abre a janela (funciona no Windows)
  tray.on('double-click', () => {
    Logger.info('Duplo clique no tray detectado');
    showWindow();
  });
  
  // Clique simples - No Windows geralmente abre o menu automaticamente
  // Mas vamos também tentar abrir a janela
  tray.on('click', (event, bounds) => {
    Logger.info('Clique no tray detectado', { 
      platform: process.platform,
      button: event.button || 'left'
    });
    
    // No Windows, clique esquerdo abre o menu automaticamente
    // Vamos também tentar abrir a janela após um delay
    setTimeout(() => {
      Logger.info('Tentando abrir janela após clique');
      showWindow();
    }, 250);
  });
  
  Logger.info('Eventos do tray configurados', {
    hasTray: !!tray,
    hasContextMenu: !!contextMenu
  });
}

function showWindow() {
  Logger.info('showWindow chamado');
  
  if (!mainWindow || mainWindow.isDestroyed()) {
    Logger.info('Criando nova janela...');
    createWindow();
    
    // Aguardar a janela estar completamente pronta antes de mostrar
    if (mainWindow) {
      mainWindow.once('ready-to-show', () => {
        Logger.info('Janela pronta, mostrando...');
        try {
          mainWindow.show();
          mainWindow.focus();
          mainWindow.setAlwaysOnTop(true);
          setTimeout(() => {
            if (mainWindow && !mainWindow.isDestroyed()) {
              mainWindow.setAlwaysOnTop(false);
            }
          }, 500);
          Logger.info('Janela exibida com sucesso');
        } catch (error) {
          Logger.error('Erro ao mostrar janela', { error: error.message });
        }
      });
    } else {
      Logger.error('mainWindow não foi criado');
    }
    return;
  }
  
  // Janela já existe
  try {
    if (mainWindow.isVisible()) {
      Logger.info('Janela já visível, focando...');
      mainWindow.focus();
      mainWindow.setAlwaysOnTop(true);
      setTimeout(() => {
        if (mainWindow && !mainWindow.isDestroyed()) {
          mainWindow.setAlwaysOnTop(false);
        }
      }, 200);
    } else {
      Logger.info('Mostrando janela existente...');
      mainWindow.show();
      mainWindow.focus();
      mainWindow.setAlwaysOnTop(true);
      setTimeout(() => {
        if (mainWindow && !mainWindow.isDestroyed()) {
          mainWindow.setAlwaysOnTop(false);
        }
      }, 500);
      Logger.info('Janela exibida');
    }
  } catch (error) {
    Logger.error('Erro ao manipular janela', { error: error.message });
    // Tentar recriar a janela
    mainWindow = null;
    createWindow();
  }
}

app.whenReady().then(async () => {
  Logger.info('Iniciando Doceria Printer Agent...');

  // Inicializar configurações
  const config = Config.getConfig();
  Logger.info('Configuração carregada:', config);

  // Inicializar gerenciador de impressoras
  await initializePrinterManager();

  // Inicializar servidor HTTP
  const port = config.serverPort || 8080;
  httpServer = await startHttpServer(port);
  Logger.info(`Servidor HTTP iniciado na porta ${port}`);

  // Criar tray icon
  createTray();

  // Criar janela (mas não mostrar ainda)
  createWindow();

  // Inicializar auto-updater
  if (config.checkUpdates !== false) {
    initializeAutoUpdater();
  }

  Logger.info('Doceria Printer Agent iniciado com sucesso!');
});

app.on('window-all-closed', (event) => {
  // Não fechar app quando todas as janelas são fechadas (manter em tray)
  event.preventDefault();
});

app.on('activate', () => {
  if (BrowserWindow.getAllWindows().length === 0) {
    createWindow();
  }
});

app.on('before-quit', () => {
  app.isQuitting = true;
  if (httpServer) {
    httpServer.close();
  }
});

// IPC handlers
ipcMain.handle('get-config', () => {
  return Config.getConfig();
});

ipcMain.handle('save-config', (event, newConfig) => {
  Config.saveConfig(newConfig);
  return { success: true };
});

ipcMain.handle('get-printers', async () => {
  const { listPrinters } = require('./src/server/printer-manager');
  return await listPrinters();
});

ipcMain.handle('test-print', async () => {
  const { printTestReceipt } = require('./src/server/printer-manager');
  try {
    await printTestReceipt();
    return { success: true, message: 'Cupom de teste enviado para impressão!' };
  } catch (error) {
    return { success: false, message: error.message };
  }
});

ipcMain.handle('get-logs', () => {
  return Logger.getRecentLogs(100);
});

ipcMain.handle('clear-logs', () => {
  Logger.clearLogs();
  return { success: true };
});
