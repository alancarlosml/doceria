const Store = require('electron-store');
const { v4: uuidv4 } = require('uuid');
const path = require('path');
const os = require('os');

const store = new Store({
  name: 'config',
  defaults: {
    printerName: null,
    serverPort: 8080,
    authToken: null,
    autoStart: true,
    checkUpdates: true,
    updateUrl: 'https://seu-dominio.com/api/printer-agent/updates'
  }
});

class Config {
  static getConfig() {
    const config = store.store;
    
    // Gerar token se não existir
    if (!config.authToken) {
      config.authToken = uuidv4();
      store.set('authToken', config.authToken);
    }
    
    return config;
  }

  static saveConfig(newConfig) {
    Object.keys(newConfig).forEach(key => {
      store.set(key, newConfig[key]);
    });
  }

  static get(key, defaultValue = null) {
    return store.get(key, defaultValue);
  }

  static set(key, value) {
    store.set(key, value);
  }

  static getConfigPath() {
    return store.path;
  }
}

module.exports = Config;
