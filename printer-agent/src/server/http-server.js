const express = require('express');
const cors = require('cors');
const Config = require('../utils/config');
const Logger = require('../utils/logger');
const { printReceipt, listPrinters, getDefaultPrinter, setDefaultPrinter } = require('./printer-manager');

let server = null;

function createHttpServer() {
  const app = express();
  const config = Config.getConfig();
  
  // Middleware
  app.use(express.json({ limit: '10mb' }));
  app.use(express.urlencoded({ extended: true }));
  
  // CORS - permitir apenas localhost e domínios configurados
  app.use(cors({
    origin: (origin, callback) => {
      // Permitir requisições sem origin (ex: Postman, aplicações desktop)
      if (!origin) {
        return callback(null, true);
      }
      
      // Permitir localhost em qualquer porta
      if (origin.startsWith('http://localhost') || origin.startsWith('http://127.0.0.1')) {
        return callback(null, true);
      }
      
      // Aqui você pode adicionar domínios permitidos no futuro
      // Por enquanto, apenas localhost
      callback(null, true);
    },
    credentials: true
  }));
  
  // Middleware de autenticação simples (opcional)
  const authenticate = (req, res, next) => {
    const authToken = req.headers['x-auth-token'] || req.query.token;
    const configToken = config.authToken;
    
    // Se não há token configurado, permitir (primeira execução)
    if (!configToken) {
      return next();
    }
    
    // Se há token configurado, validar
    if (authToken && authToken === configToken) {
      return next();
    }
    
    // Permitir requisições de localhost sem token (para desenvolvimento)
    const origin = req.headers.origin || '';
    if (origin.startsWith('http://localhost') || origin.startsWith('http://127.0.0.1')) {
      return next();
    }
    
    // Rejeitar outras requisições sem token válido
    res.status(401).json({ error: 'Token de autenticação inválido' });
  };
  
  // Rotas
  app.get('/status', (req, res) => {
    const printerName = getDefaultPrinter();
    res.json({
      status: 'running',
      version: require('../../package.json').version,
      printer: printerName || null,
      printerConfigured: !!printerName,
      timestamp: new Date().toISOString()
    });
  });
  
  app.get('/printers', authenticate, async (req, res) => {
    try {
      const printers = await listPrinters();
      res.json({ success: true, printers });
    } catch (error) {
      Logger.error('Erro ao listar impressoras via HTTP', { error: error.message });
      res.status(500).json({ success: false, error: error.message });
    }
  });
  
  app.post('/print', authenticate, async (req, res) => {
    try {
      const receiptData = req.body;
      
      if (!receiptData) {
        return res.status(400).json({ success: false, error: 'Dados do recibo não fornecidos' });
      }
      
      Logger.info('Recebendo comando de impressão via HTTP', { 
        order: receiptData.order_number || 'N/A' 
      });
      
      const result = await printReceipt(receiptData);
      res.json({ success: true, ...result });
    } catch (error) {
      Logger.error('Erro ao processar impressão via HTTP', { error: error.message });
      res.status(500).json({ success: false, error: error.message });
    }
  });
  
  app.post('/config', authenticate, async (req, res) => {
    try {
      const { printerName, serverPort } = req.body;
      
      if (printerName) {
        setDefaultPrinter(printerName);
      }
      
      if (serverPort && serverPort !== config.serverPort) {
        Config.set('serverPort', serverPort);
        res.json({ 
          success: true, 
          message: 'Configuração salva. Reinicie o aplicativo para aplicar a nova porta.' 
        });
        return;
      }
      
      res.json({ success: true, message: 'Configuração salva' });
    } catch (error) {
      Logger.error('Erro ao salvar configuração via HTTP', { error: error.message });
      res.status(500).json({ success: false, error: error.message });
    }
  });
  
  app.get('/config', authenticate, (req, res) => {
    const currentConfig = Config.getConfig();
    res.json({
      success: true,
      config: {
        printerName: currentConfig.printerName,
        serverPort: currentConfig.serverPort,
        autoStart: currentConfig.autoStart,
        checkUpdates: currentConfig.checkUpdates
      }
    });
  });
  
  // Rota de teste
  app.get('/test', (req, res) => {
    res.json({ 
      message: 'Doceria Printer Agent está rodando!',
      timestamp: new Date().toISOString()
    });
  });
  
  // Error handler
  app.use((err, req, res, next) => {
    Logger.error('Erro no servidor HTTP', { error: err.message, stack: err.stack });
    res.status(500).json({ success: false, error: 'Erro interno do servidor' });
  });
  
  return app;
}

async function startHttpServer(port = 8080) {
  return new Promise((resolve, reject) => {
    try {
      const app = createHttpServer();
      
      server = app.listen(port, '127.0.0.1', () => {
        Logger.info(`Servidor HTTP iniciado na porta ${port}`);
        resolve(server);
      });
      
      server.on('error', (error) => {
        if (error.code === 'EADDRINUSE') {
          Logger.error(`Porta ${port} já está em uso`, { port });
          reject(new Error(`Porta ${port} já está em uso`));
        } else {
          Logger.error('Erro no servidor HTTP', { error: error.message });
          reject(error);
        }
      });
    } catch (error) {
      Logger.error('Erro ao iniciar servidor HTTP', { error: error.message });
      reject(error);
    }
  });
}

function stopHttpServer() {
  return new Promise((resolve) => {
    if (server) {
      server.close(() => {
        Logger.info('Servidor HTTP encerrado');
        server = null;
        resolve();
      });
    } else {
      resolve();
    }
  });
}

module.exports = {
  startHttpServer,
  stopHttpServer,
  createHttpServer
};
