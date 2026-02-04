const { autoUpdater } = require('electron-updater');
const { dialog } = require('electron');
const Config = require('../utils/config');
const Logger = require('../utils/logger');

let updateAvailable = false;
let updateDownloaded = false;

function initializeAutoUpdater() {
  const config = Config.getConfig();
  
  if (config.checkUpdates === false) {
    Logger.info('Auto-updater desabilitado nas configurações');
    return;
  }
  
  // Configurar URL de atualizações (se configurado)
  if (config.updateUrl) {
    autoUpdater.setFeedURL({
      provider: 'generic',
      url: config.updateUrl
    });
  }
  
  // Eventos do auto-updater
  autoUpdater.on('checking-for-update', () => {
    Logger.info('Verificando atualizações...');
  });
  
  autoUpdater.on('update-available', (info) => {
    updateAvailable = true;
    Logger.info('Atualização disponível', { version: info.version });
    
    // Notificar usuário (opcional - pode ser silencioso)
    dialog.showMessageBox(null, {
      type: 'info',
      title: 'Atualização Disponível',
      message: `Uma nova versão (${info.version}) está disponível.`,
      detail: 'A atualização será baixada em segundo plano.',
      buttons: ['OK']
    });
  });
  
  autoUpdater.on('update-not-available', (info) => {
    Logger.info('Nenhuma atualização disponível', { version: info.version });
  });
  
  autoUpdater.on('error', (err) => {
    // Se for erro 404 ou URL não configurada, apenas avisar (não é crítico)
    const is404 = err.message && err.message.includes('404');
    const isUrlNotSet = err.message && (err.message.includes('url') || err.message.includes('URL'));
    
    if (is404 || isUrlNotSet || !config.updateUrl || config.updateUrl.includes('seu-dominio.com')) {
      Logger.info('Servidor de atualizações não configurado ou indisponível (isso é normal)', { 
        url: config.updateUrl,
        error: is404 ? '404 - Servidor não encontrado' : err.message 
      });
    } else {
      Logger.error('Erro ao verificar atualizações', { error: err.message });
    }
    // Não mostrar erro ao usuário - falha silenciosa
  });
  
  autoUpdater.on('download-progress', (progressObj) => {
    const percent = Math.round(progressObj.percent);
    Logger.info(`Download da atualização: ${percent}%`);
  });
  
  autoUpdater.on('update-downloaded', (info) => {
    updateDownloaded = true;
    Logger.info('Atualização baixada', { version: info.version });
    
    // Perguntar ao usuário se deseja instalar agora
    dialog.showMessageBox(null, {
      type: 'info',
      title: 'Atualização Pronta',
      message: `A versão ${info.version} foi baixada e está pronta para instalação.`,
      detail: 'Deseja reiniciar o aplicativo agora para instalar a atualização?',
      buttons: ['Reiniciar Agora', 'Depois'],
      defaultId: 0,
      cancelId: 1
    }).then((result) => {
      if (result.response === 0) {
        autoUpdater.quitAndInstall(false, true);
      }
    });
  });
  
  // Verificar atualizações na inicialização
  checkForUpdates();
  
  // Verificar atualizações periodicamente (a cada 4 horas)
  setInterval(() => {
    checkForUpdates();
  }, 4 * 60 * 60 * 1000);
}

function checkForUpdates() {
  const config = Config.getConfig();
  
  // Não tentar verificar se URL não está configurada ou é placeholder
  if (!config.updateUrl || config.updateUrl.includes('seu-dominio.com')) {
    Logger.info('Verificação de atualizações desabilitada (URL não configurada)');
    return;
  }
  
  try {
    autoUpdater.checkForUpdates().catch(err => {
      // Silenciar erros 404 ou de URL não configurada
      const is404 = err.message && err.message.includes('404');
      if (!is404) {
        Logger.error('Erro ao verificar atualizações', { error: err.message });
      }
    });
  } catch (error) {
    Logger.error('Erro ao iniciar verificação de atualizações', { error: error.message });
  }
}

module.exports = {
  initializeAutoUpdater,
  checkForUpdates
};
