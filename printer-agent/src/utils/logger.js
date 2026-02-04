const fs = require('fs');
const path = require('path');
const os = require('os');

const LOG_DIR = path.join(os.homedir(), 'AppData', 'Local', 'Doceria', 'PrinterAgent', 'logs');
const LOG_FILE = path.join(LOG_DIR, 'app.log');
const MAX_LOG_SIZE = 5 * 1024 * 1024; // 5MB
const MAX_LOG_FILES = 5;

// Garantir que o diretório existe
if (!fs.existsSync(LOG_DIR)) {
  fs.mkdirSync(LOG_DIR, { recursive: true });
}

function rotateLogs() {
  if (!fs.existsSync(LOG_FILE)) return;

  const stats = fs.statSync(LOG_FILE);
  if (stats.size > MAX_LOG_SIZE) {
    // Rotacionar logs antigos
    for (let i = MAX_LOG_FILES - 1; i >= 1; i--) {
      const oldFile = `${LOG_FILE}.${i}`;
      const newFile = `${LOG_FILE}.${i + 1}`;
      if (fs.existsSync(oldFile)) {
        fs.renameSync(oldFile, newFile);
      }
    }
    
    // Mover log atual para .1
    fs.renameSync(LOG_FILE, `${LOG_FILE}.1`);
  }
}

function writeLog(level, message, data = null) {
  rotateLogs();
  
  const timestamp = new Date().toISOString();
  const dataStr = data ? ` ${JSON.stringify(data)}` : '';
  const logLine = `[${timestamp}] [${level}] ${message}${dataStr}\n`;
  
  try {
    fs.appendFileSync(LOG_FILE, logLine, 'utf8');
  } catch (error) {
    console.error('Erro ao escrever log:', error);
  }
}

class Logger {
  static info(message, data = null) {
    console.log(`[INFO] ${message}`, data || '');
    writeLog('INFO', message, data);
  }

  static error(message, data = null) {
    console.error(`[ERROR] ${message}`, data || '');
    writeLog('ERROR', message, data);
  }

  static warn(message, data = null) {
    console.warn(`[WARN] ${message}`, data || '');
    writeLog('WARN', message, data);
  }

  static debug(message, data = null) {
    console.debug(`[DEBUG] ${message}`, data || '');
    writeLog('DEBUG', message, data);
  }

  static getRecentLogs(lines = 100) {
    try {
      if (!fs.existsSync(LOG_FILE)) {
        return [];
      }

      const content = fs.readFileSync(LOG_FILE, 'utf8');
      const logLines = content.split('\n').filter(line => line.trim());
      return logLines.slice(-lines);
    } catch (error) {
      Logger.error('Erro ao ler logs', { error: error.message });
      return [];
    }
  }

  static clearLogs() {
    try {
      if (fs.existsSync(LOG_FILE)) {
        fs.writeFileSync(LOG_FILE, '', 'utf8');
      }
      Logger.info('Logs limpos');
    } catch (error) {
      Logger.error('Erro ao limpar logs', { error: error.message });
    }
  }

  static getLogPath() {
    return LOG_FILE;
  }
}

module.exports = Logger;
